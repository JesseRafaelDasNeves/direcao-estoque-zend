<?php

namespace Estoque\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Estoque\Model\EntradaTable;
use Estoque\Model\Entrada;
use Estoque\Model\Estoque;
use Estoque\Model\ItemEntrada;
use Estoque\Form\EntradaForm;
use Auth\Model\Auth;

/**
 * Description of EntradaController
 *
 * @author Jessé Rafael das Neves
 */
class EntradaController extends AbstractActionController {

    /** @var EntradaTable */
    private $table;

    public function __construct(EntradaTable $table) {
        $this->table = $table;
    }

    /**
     * @return EntradaForm
     */
    private function getInstanceForm() {
        $form = new EntradaForm();
        $this->carregaFornecedoresByFormulario($form);
        return $form;
    }

    private function carregaFornecedoresByFormulario(EntradaForm $form) {
        $fornecedorTable = new \Fornecedor\Model\FornecedorTable(\Fornecedor\Module::newTableGatewayFornecedor($this->table->getAdapter()));
        $aOptions = [];
        foreach ($fornecedorTable->fetchAll() as /* @var $fornecedor \Fornecedor\Model\Fornecedor */ $fornecedor) {
            $aOptions[$fornecedor->id] = $fornecedor->pessoa()->nome;
        }

        $form->get('idfornecedor')->setValueOptions($aOptions);
    }

    public function indexAction() {
        if(!Auth::check()) {
            return $this->redirect()->toRoute('auth');
        }

        return new ViewModel(['entradas' => $this->table->fetchAll()]);
    }

    public function addAction() {
        if(!Auth::check()) {
            return $this->redirect()->toRoute('auth');
        }

        $form = $this->getInstanceForm();
        $form->get('submit')->setValue('Adicionar');

        $request = $this->getRequest();

        if (! $request->isPost()) {
            return ['form' => $form];
        }

        $entrada = new Entrada();
        $form->setInputFilter($entrada->getInputFilter());
        $form->setData($request->getPost());

        if (!$form->isValid()) {
            return ['form' => $form];
        }

        $entrada->exchangeArray($form->getData());
        $this->table->saveEntrada($entrada);
        return $this->redirect()->toRoute('entrada');
    }

    public function editAction() {
        if(!Auth::check()) {
            return $this->redirect()->toRoute('auth');
        }

        $id = (int) $this->params()->fromRoute('id', 0);

        if (0 === $id) {
            return $this->redirect()->toRoute('entrada', ['action' => 'add']);
        }

        try {
            $entrada = $this->table->getEntrada($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('entrada', ['action' => 'index']);
        }

        $form = $this->getInstanceForm();
        $form->bind($entrada);
        $form->get('submit')->setAttribute('value', 'Alterar');

        $request = $this->getRequest();
        $viewData = ['id' => $id, 'form' => $form];

        if (! $request->isPost()) {
            return $viewData;
        }

        $form->setInputFilter($entrada->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return $viewData;
        }

        $this->table->saveEntrada($entrada);

        // Redirect to entrada list
        return $this->redirect()->toRoute('entrada', ['action' => 'index']);
    }

    public function deleteAction() {
        if(!Auth::check()) {
            return $this->redirect()->toRoute('auth');
        }

        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('entrada');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->table->deleteEntrada($id);


            // Redirect to list of entradas
            return $this->redirect()->toRoute('entrada');
        }

        try {
            $entrada = $this->table->getEntrada($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('entrada', ['action' => 'index']);
        }

        $form = $this->getInstanceForm();
        $form->bind($entrada);
        $form->get('submit')->setAttribute('value', 'Excluir');
        $viewData = ['id' => $id, 'form' => $form];

        return $viewData;
    }

    public function concluiAction() {
        if(!Auth::check()) {
            return $this->redirect()->toRoute('auth');
        }

        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('entrada');
        }

        /* @var $oEntrada Entrada */
        $oEntrada = $this->table->getEntrada($id);

        if(!$oEntrada->isPermitidoManutencao()) {
            return $this->redirect()->toRoute('entrada');
        }

        /* @var $aItens ItemEntrada[] */
        $aItens = $this->getItensByEntrada($oEntrada);
        $this->table->getAdapter()->getDriver()->getConnection()->beginTransaction();

        foreach ($aItens as $oItemEntrada) {
            /* @var $oEstoqueProduto Estoque */
            $oEstoqueProduto = $this->atualizaEstoqueByItemEntrada($oItemEntrada);
            $oItemEntrada->idestoque = $oEstoqueProduto->id;
            $this->salvaItemEntrada($oItemEntrada);
        }

        $oEntrada->situacao = Entrada::SITUACAO_CONCLUIDA;
        $this->table->saveEntrada($oEntrada);

        $this->table->getAdapter()->getDriver()->getConnection()->commit();

        return $this->redirect()->toRoute("entrada");
    }

    private function getItensByEntrada(Entrada $oEntrada) {
        $itemEntradaTable = new \Estoque\Model\ItemEntradaTable(\Estoque\Module::newTableGatewayItemEntrada($this->table->getAdapter()));
        return $itemEntradaTable->allByEntrada($oEntrada->id);
    }

    private function salvaItemEntrada(ItemEntrada $itemEntrada) {
        $itemEntradaTable = new \Estoque\Model\ItemEntradaTable(\Estoque\Module::newTableGatewayItemEntrada($this->table->getAdapter()));
        $itemEntradaTable->saveItemEntrada($itemEntrada);
    }

    private function atualizaEstoqueByItemEntrada(ItemEntrada $oItemEntrada) {
        /* @var $oEstoqueProduto Estoque */
        $oEstoqueProduto = $this->getEstoqueByProduto($oItemEntrada->produto());

        if($oEstoqueProduto) {
            $oEstoqueProduto->addQuantidade($oItemEntrada->quantidade);
            $this->salvaEstoque($oEstoqueProduto);
            return $oEstoqueProduto;
        }

        return $this->criaEstoqueNovoByItemEntrada($oItemEntrada);
    }

    private function getEstoqueByProduto(\Produto\Model\Produto $oProduto) {
        $estoqueTable = new \Estoque\Model\EstoqueTable(\Estoque\Module::newTableGatewayEstoque($this->table->getAdapter()));
        $oEstoque     = $estoqueTable->firstEstoqueByProduto($oProduto->id);
        return $oEstoque;
    }

    private function criaEstoqueNovoByItemEntrada(ItemEntrada $oItemEntrada) {
        $estoqueTable = new \Estoque\Model\EstoqueTable(\Estoque\Module::newTableGatewayEstoque($this->table->getAdapter()));
        $estoque = new Estoque();
        $estoque->quantidade = $oItemEntrada->quantidade;
        $estoque->idproduto  = $oItemEntrada->idproduto;
        $estoqueTable->saveEstoque($estoque);
        return $estoque;
    }

    private function salvaEstoque(Estoque $estoque) {
        $estoqueTable = new \Estoque\Model\EstoqueTable(\Estoque\Module::newTableGatewayEstoque($this->table->getAdapter()));
        $estoqueTable->saveEstoque($estoque);
        return $estoque;
    }

}
