<?php

namespace Estoque\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Estoque\Model\ItemSaidaTable;
use Estoque\Model\ItemSaida;
use Estoque\Form\ItemSaidaForm;
use Auth\Model\Auth;

/**
 * Description of ItemSaidaController
 *
 * @author Jessé Rafael das Neves
 */
class ItemSaidaController extends AbstractActionController {

    private $table;

    public function __construct(ItemSaidaTable $table) {
        $this->table = $table;
    }

    /**
     * @return ItemSaidaForm
     */
    private function getInstanceForm() {
        $form = new ItemSaidaForm();
        $this->carregaProdutosFormulario($form);
        return $form;
    }

    private function carregaProdutosFormulario(ItemSaidaForm $form) {
        $produtoTable = new \Produto\Model\ProdutoTable(\Produto\Module::newTableGatewayProduto($this->table->getAdapter()));
        $aOptions = [];
        foreach ($produtoTable->fetchAll() as /* @var $produto \Produto\Model\Produto */ $produto) {
            $aOptions[$produto->id] = $produto->nome;
        }

        $form->get('idproduto')->setValueOptions($aOptions);
    }

    public function indexAction() {
        if(!Auth::check()) {
            return $this->redirect()->toRoute('auth');
        }

        $idSaida = (int) $this->params()->fromRoute('idsaida', 0);
        $oSaida = $this->getSaidaById($idSaida);

        return new ViewModel(['saida' => $oSaida, 'idSaida' => $idSaida, 'itensSaida' => $this->table->allBySaida($idSaida)]);
    }

    public function addAction() {
        if(!Auth::check()) {
            return $this->redirect()->toRoute('auth');
        }

        $idSaida = (int) $this->params()->fromRoute('idsaida', 0);
        $form = $this->getInstanceForm();
        $form->get('submit')->setValue('Adicionar');

        $request = $this->getRequest();

        if (! $request->isPost()) {
            return ['form' => $form, 'idSaida' => $idSaida];
        }

        $itensSaida = new ItemSaida();
        $form->setInputFilter($itensSaida->getInputFilter());
        $form->setData($request->getPost());

        if (!$form->isValid()) {
            return ['form' => $form, 'idSaida' => $idSaida];
        }

        $itensSaida->exchangeArray($form->getData());
        $itensSaida->idsaida = $idSaida;
        $this->table->saveItemSaida($itensSaida);
        return $this->redirect()->toRoute('item-saida', ['idsaida' => $itensSaida->idsaida]);
    }

    public function editAction() {
        if(!Auth::check()) {
            return $this->redirect()->toRoute('auth');
        }

        $id        = (int) $this->params()->fromRoute('id'       , 0);
        $idSaida = (int) $this->params()->fromRoute('idsaida', 0);

        if (0 === $id) {
            return $this->redirect()->toRoute('item-saida', ['action' => 'add', 'idsaida' => $idSaida]);
        }

        try {
            $itensSaida = $this->table->getItemSaida($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('item-saida', ['action' => 'index', 'idsaida' => $idSaida]);
        }

        $qtdeEstoque = $this->getQtdeEstoqueByProduto($itensSaida->produto());
        $form = $this->getInstanceForm();
        $form->bind($itensSaida);
        $form->get('qtde_atual_estoque')->setValue($qtdeEstoque);
        $form->get('quantidade')->setAttribute('max', $qtdeEstoque);
        $form->get('submit')->setAttribute('value', 'Alterar');

        $request = $this->getRequest();
        $viewData = ['id' => $id, 'idSaida' => $idSaida, 'form' => $form];

        if (! $request->isPost()) {
            return $viewData;
        }

        $form->setInputFilter($itensSaida->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return $viewData;
        }

        $itensSaida->idsaida = $idSaida;
        $this->table->saveItemSaida($itensSaida);

        // Redirect to itensSaida list
        return $this->redirect()->toRoute('item-saida', ['action' => 'index', 'idsaida' => $itensSaida->idsaida]);
    }

    public function deleteAction() {
        if(!Auth::check()) {
            return $this->redirect()->toRoute('auth');
        }

        $id        = (int) $this->params()->fromRoute('id'       , 0);
        $idSaida = (int) $this->params()->fromRoute('idsaida', 0);

        if (!$id) {
            return $this->redirect()->toRoute('item-saida', ['idsaida' => $idSaida]);
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->table->deleteItemSaida($id);


            // Redirect to list of itensSaida
            return $this->redirect()->toRoute('item-saida', ['idsaida' => $idSaida]);
        }

        try {
            $itensSaida = $this->table->getItemSaida($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('item-saida', ['action' => 'index', 'idsaida' => $itensSaida->idsaida]);
        }

        $form = $this->getInstanceForm();
        $form->bind($itensSaida);
        $form->get('submit')->setAttribute('value', 'Excluir');
        $viewData = ['id' => $id, 'idSaida' => $idSaida,  'form' => $form];

        return $viewData;
    }

    private function getSaidaById(int $id) {
        $saidaTable = new \Estoque\Model\SaidaTable(\Estoque\Module::newTableGatewaySaida($this->table->getAdapter()));
        $saida      = $saidaTable->getSaida($id);
        return $saida;
    }

    private function getEstoqueByProduto(\Produto\Model\Produto $oProduto) {
        $estoqueTable = new \Estoque\Model\EstoqueTable(\Estoque\Module::newTableGatewayEstoque($this->table->getAdapter()));
        $oEstoque     = $estoqueTable->firstEstoqueByProduto($oProduto->id);
        return $oEstoque;
    }

    private function getQtdeEstoqueByProduto(\Produto\Model\Produto $oProduto) {
        $oEstoque     = $this->getEstoqueByProduto($oProduto);
        return $oEstoque ? $oEstoque->quantidade : 0;
    }

}
