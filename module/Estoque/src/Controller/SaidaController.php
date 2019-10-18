<?php

namespace Estoque\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Estoque\Model\SaidaTable;
use Estoque\Model\Saida;
use Estoque\Model\Estoque;
use Estoque\Model\ItemSaida;
use Estoque\Form\SaidaForm;
use Auth\Model\Auth;

/**
 * Description of SaidaController
 *
 * @author Jessé Rafael das Neves
 */
class SaidaController extends AbstractActionController {

    /** @var SaidaTable */
    private $table;

    public function __construct(SaidaTable $table) {
        $this->table = $table;
    }

    /**
     * @return SaidaForm
     */
    private function getInstanceForm() {
        $form = new SaidaForm();
        $this->carregaPessoasByFormulario($form);
        return $form;
    }

    private function carregaPessoasByFormulario(SaidaForm $form) {
        $fornecedorTable = new \Pessoa\Model\PessoaTable(\Pessoa\Module::newTableGatewayPessoa($this->table->getAdapter()));
        $aOptions = [];
        foreach ($fornecedorTable->fetchAll() as /* @var $pessoa \Pessoa\Model\Pessoa */ $pessoa) {
            $aOptions[$pessoa->id] = $pessoa->nome;
        }

        $form->get('idpessoa')->setValueOptions($aOptions);
    }

    public function indexAction() {
        if(!Auth::check()) {
            return $this->redirect()->toRoute('auth');
        }

        return new ViewModel(['saidas' => $this->table->fetchAll()]);
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

        $saida = new Saida();
        $form->setInputFilter($saida->getInputFilter());
        $form->setData($request->getPost());

        if (!$form->isValid()) {
            return ['form' => $form];
        }

        $saida->exchangeArray($form->getData());
        $this->table->saveSaida($saida);
        return $this->redirect()->toRoute('saida');
    }

    public function editAction() {
        if(!Auth::check()) {
            return $this->redirect()->toRoute('auth');
        }

        $id = (int) $this->params()->fromRoute('id', 0);

        if (0 === $id) {
            return $this->redirect()->toRoute('saida', ['action' => 'add']);
        }

        try {
            $saida = $this->table->getSaida($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('saida', ['action' => 'index']);
        }

        $form = $this->getInstanceForm();
        $form->bind($saida);
        $form->get('submit')->setAttribute('value', 'Alterar');

        $request = $this->getRequest();
        $viewData = ['id' => $id, 'form' => $form];

        if (! $request->isPost()) {
            return $viewData;
        }

        $form->setInputFilter($saida->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return $viewData;
        }

        $this->table->saveSaida($saida);

        // Redirect to saida list
        return $this->redirect()->toRoute('saida', ['action' => 'index']);
    }

    public function deleteAction() {
        if(!Auth::check()) {
            return $this->redirect()->toRoute('auth');
        }

        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('saida');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->table->deleteSaida($id);


            // Redirect to list of saidas
            return $this->redirect()->toRoute('saida');
        }

        try {
            $saida = $this->table->getSaida($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('saida', ['action' => 'index']);
        }

        $form = $this->getInstanceForm();
        $form->bind($saida);
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
            return $this->redirect()->toRoute('saida');
        }

        /* @var $oSaida Saida */
        $oSaida = $this->table->getSaida($id);

        if(!$oSaida->isPermitidoManutencao()) {
            return $this->redirect()->toRoute('saida');
        }

        /* @var $aItens ItemSaida[] */
        $aItens = $this->getItensBySaida($oSaida);
        $this->table->getAdapter()->getDriver()->getConnection()->beginTransaction();
        $ok = true;

        foreach ($aItens as $oItemSaida) {
            /* @var $oEstoqueProduto Estoque */
            $oEstoqueProduto = $this->atualizaEstoqueByItemSaida($oItemSaida);

            if(!$oEstoqueProduto) {
                $ok = false;
                break;
            }

            $oItemSaida->idestoque = $oEstoqueProduto->id;
            $this->salvaItemSaida($oItemSaida);
        }

        if(!$ok) {
            $this->table->getAdapter()->getDriver()->getConnection()->rollback();
            return $this->redirect()->toRoute("saida");
        }

        $oSaida->situacao = Saida::SITUACAO_CONCLUIDA;
        $this->table->saveSaida($oSaida);
        $this->table->getAdapter()->getDriver()->getConnection()->commit();
        return $this->redirect()->toRoute("saida");
    }

    private function getItensBySaida(Saida $oSaida) {
        $itemSaidaTable = new \Estoque\Model\ItemSaidaTable(\Estoque\Module::newTableGatewayItemSaida($this->table->getAdapter()));
        return $itemSaidaTable->allBySaida($oSaida->id);
    }

    private function salvaItemSaida(ItemSaida $itemSaida) {
        $itemSaidaTable = new \Estoque\Model\ItemSaidaTable(\Estoque\Module::newTableGatewayItemSaida($this->table->getAdapter()));
        $itemSaidaTable->saveItemSaida($itemSaida);
    }

    private function atualizaEstoqueByItemSaida(ItemSaida $oItemSaida) {
        /* @var $oEstoqueProduto Estoque */
        $oEstoqueProduto = $this->getEstoqueByProduto($oItemSaida->produto());

        if($oEstoqueProduto && $oEstoqueProduto->quantidade >=  $oItemSaida->quantidade) {
            $oEstoqueProduto->retiraQuantidade($oItemSaida->quantidade);
            $this->salvaEstoque($oEstoqueProduto);
            return $oEstoqueProduto;
        }

        return false;
    }

    private function getEstoqueByProduto(\Produto\Model\Produto $oProduto) {
        $estoqueTable = new \Estoque\Model\EstoqueTable(\Estoque\Module::newTableGatewayEstoque($this->table->getAdapter()));
        $oEstoque     = $estoqueTable->firstEstoqueByProduto($oProduto->id);
        return $oEstoque;
    }

    private function salvaEstoque(Estoque $estoque) {
        $estoqueTable = new \Estoque\Model\EstoqueTable(\Estoque\Module::newTableGatewayEstoque($this->table->getAdapter()));
        $estoqueTable->saveEstoque($estoque);
        return $estoque;
    }

}
