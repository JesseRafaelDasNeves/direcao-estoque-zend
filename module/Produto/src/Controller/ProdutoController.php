<?php

namespace Produto\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Produto\Model\ProdutoTable;
use Produto\Model\Produto;
use Produto\Form\ProdutoForm;
use Auth\Model\Auth;

/**
 * Description of ProdutoController
 *
 * @author Jessé Rafael das Neves
 */
class ProdutoController extends AbstractActionController {

    private $table;

    public function __construct(ProdutoTable $table) {
        $this->table = $table;
    }

    /**
     * @return ProdutoForm
     */
    private function getInstanceForm() {
        $form = new ProdutoForm();
        return $form;
    }

    public function indexAction() {
        if(!Auth::check()) {
            return $this->redirect()->toRoute('auth');
        }

        return new ViewModel(['produtos' => $this->table->fetchAll()]);
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

        $produto = new Produto();
        $form->setInputFilter($produto->getInputFilter());
        $form->setData($request->getPost());

        if (!$form->isValid()) {
            return ['form' => $form];
        }

        $produto->exchangeArray($form->getData());
        $this->table->saveProduto($produto);
        return $this->redirect()->toRoute('produto');
    }

    public function editAction() {
        if(!Auth::check()) {
            return $this->redirect()->toRoute('auth');
        }

        $id = (int) $this->params()->fromRoute('id', 0);

        if (0 === $id) {
            return $this->redirect()->toRoute('produto', ['action' => 'add']);
        }

        try {
            $produto = $this->table->getProduto($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('produto', ['action' => 'index']);
        }

        $form = $this->getInstanceForm();
        $form->bind($produto);
        $form->get('submit')->setAttribute('value', 'Alterar');

        $request = $this->getRequest();
        $viewData = ['id' => $id, 'form' => $form];

        if (! $request->isPost()) {
            return $viewData;
        }

        $form->setInputFilter($produto->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return $viewData;
        }

        $this->table->saveProduto($produto);

        // Redirect to produto list
        return $this->redirect()->toRoute('produto', ['action' => 'index']);
    }

    public function deleteAction() {
        if(!Auth::check()) {
            return $this->redirect()->toRoute('auth');
        }

        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('produto');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->table->deleteProduto($id);


            // Redirect to list of produtos
            return $this->redirect()->toRoute('produto');
        }

        try {
            $produto = $this->table->getProduto($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('produto', ['action' => 'index']);
        }

        $form = $this->getInstanceForm();
        $form->bind($produto);
        $form->get('submit')->setAttribute('value', 'Excluir');
        $viewData = ['id' => $id, 'form' => $form];

        return $viewData;
    }

}
