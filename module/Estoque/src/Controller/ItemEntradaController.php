<?php

namespace Estoque\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Estoque\Model\ItemEntradaTable;
use Estoque\Model\ItemEntrada;
use Estoque\Form\ItemEntradaForm;
use Auth\Model\Auth;

/**
 * Description of ItemEntradaController
 *
 * @author Jessé Rafael das Neves
 */
class ItemEntradaController extends AbstractActionController {

    private $table;

    public function __construct(ItemEntradaTable $table) {
        $this->table = $table;
    }

    /**
     * @return ItemEntradaForm
     */
    private function getInstanceForm() {
        $form = new ItemEntradaForm();
        $this->carregaProdutosFormulario($form);
        return $form;
    }

    private function carregaProdutosFormulario(ItemEntradaForm $form) {
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

        $idEntrada = (int) $this->params()->fromRoute('identrada', 0);
        $oEntrada = $this->getEntradaById($idEntrada);

        return new ViewModel(['entrada' => $oEntrada, 'idEntrada' => $idEntrada, 'itensEntrada' => $this->table->allByEntrada($idEntrada)]);
    }

    public function addAction() {
        if(!Auth::check()) {
            return $this->redirect()->toRoute('auth');
        }

        $idEntrada = (int) $this->params()->fromRoute('identrada', 0);
        $form = $this->getInstanceForm();
        $form->get('submit')->setValue('Adicionar');

        $request = $this->getRequest();

        if (! $request->isPost()) {
            return ['form' => $form, 'idEntrada' => $idEntrada];
        }

        $itensEntrada = new ItemEntrada();
        $form->setInputFilter($itensEntrada->getInputFilter());
        $form->setData($request->getPost());

        if (!$form->isValid()) {
            return ['form' => $form, 'idEntrada' => $idEntrada];
        }

        $itensEntrada->exchangeArray($form->getData());
        $itensEntrada->identrada = $idEntrada;
        $this->table->saveItemEntrada($itensEntrada);
        return $this->redirect()->toRoute('item-entrada', ['identrada' => $itensEntrada->identrada]);
    }

    public function editAction() {
        if(!Auth::check()) {
            return $this->redirect()->toRoute('auth');
        }

        $id        = (int) $this->params()->fromRoute('id'       , 0);
        $idEntrada = (int) $this->params()->fromRoute('identrada', 0);

        if (0 === $id) {
            return $this->redirect()->toRoute('item-entrada', ['action' => 'add', 'identrada' => $idEntrada]);
        }

        try {
            $itensEntrada = $this->table->getItemEntrada($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('item-entrada', ['action' => 'index', 'identrada' => $idEntrada]);
        }

        $form = $this->getInstanceForm();
        $form->bind($itensEntrada);
        $form->get('submit')->setAttribute('value', 'Alterar');

        $request = $this->getRequest();
        $viewData = ['id' => $id, 'idEntrada' => $idEntrada, 'form' => $form];

        if (! $request->isPost()) {
            return $viewData;
        }

        $form->setInputFilter($itensEntrada->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return $viewData;
        }

        $itensEntrada->identrada = $idEntrada;
        $this->table->saveItemEntrada($itensEntrada);

        // Redirect to itensEntrada list
        return $this->redirect()->toRoute('item-entrada', ['action' => 'index', 'identrada' => $itensEntrada->identrada]);
    }

    public function deleteAction() {
        if(!Auth::check()) {
            return $this->redirect()->toRoute('auth');
        }

        $id        = (int) $this->params()->fromRoute('id'       , 0);
        $idEntrada = (int) $this->params()->fromRoute('identrada', 0);

        if (!$id) {
            return $this->redirect()->toRoute('item-entrada', ['identrada' => $idEntrada]);
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->table->deleteItemEntrada($id);


            // Redirect to list of itensEntrada
            return $this->redirect()->toRoute('item-entrada', ['identrada' => $idEntrada]);
        }

        try {
            $itensEntrada = $this->table->getItemEntrada($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('item-entrada', ['action' => 'index', 'identrada' => $itensEntrada->identrada]);
        }

        $form = $this->getInstanceForm();
        $form->bind($itensEntrada);
        $form->get('submit')->setAttribute('value', 'Excluir');
        $viewData = ['id' => $id, 'idEntrada' => $idEntrada,  'form' => $form];

        return $viewData;
    }

    private function getEntradaById(int $id) {
        $entradaTable = new \Estoque\Model\EntradaTable(\Estoque\Module::newTableGatewayEntrada($this->table->getAdapter()));
        $entrada      = $entradaTable->getEntrada($id);
        return $entrada;
    }

}
