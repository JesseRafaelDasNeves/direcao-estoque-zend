<?php

namespace Fornecedor\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Fornecedor\Model\FornecedorTable;
use Fornecedor\Model\Fornecedor;
use Fornecedor\Form\FornecedorForm;
use Auth\Model\Auth;

/**
 * Description of FornecedorController
 *
 * @author Jessé Rafael das Neves
 */
class FornecedorController extends AbstractActionController {

    private $table;

    public function __construct(FornecedorTable $table) {
        $this->table = $table;
    }

    /**
     * @return FornecedorForm
     */
    private function getInstanceForm() {
        $form = new FornecedorForm();
        $this->loadPessoasByForm($form);
        return $form;
    }

    private function loadPessoasByForm(FornecedorForm $form) {
        $pessoaTable = new \Pessoa\Model\PessoaTable(\Pessoa\Module::newTableGatewayPessoa($this->table->getAdapter()));
        $aOptions = [];
        foreach ($pessoaTable->fetchAll() as /* @var $pessoa \Pessoa\Model\Pessoa */ $pessoa) {
            $aOptions[$pessoa->id] = $pessoa->nome;
        }

        $form->get('idpessoa')->setValueOptions($aOptions);
    }

    public function indexAction() {
        if(!Auth::check()) {
            return $this->redirect()->toRoute('auth');
        }

        return new ViewModel(['fornecedores' => $this->table->fetchAll()]);
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

        $fornecedor = new Fornecedor();
        $form->setInputFilter($fornecedor->getInputFilter());
        $form->setData($request->getPost());

        if (!$form->isValid()) {
            return ['form' => $form];
        }

        $fornecedor->exchangeArray($form->getData());
        $this->table->saveFornecedor($fornecedor);
        return $this->redirect()->toRoute('fornecedor');
    }

    public function editAction() {
        if(!Auth::check()) {
            return $this->redirect()->toRoute('auth');
        }

        $id = (int) $this->params()->fromRoute('id', 0);

        if (0 === $id) {
            return $this->redirect()->toRoute('fornecedor', ['action' => 'add']);
        }

        try {
            $fornecedor = $this->table->getFornecedor($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('fornecedor', ['action' => 'index']);
        }

        $form = $this->getInstanceForm();
        $form->bind($fornecedor);
        $form->get('submit')->setAttribute('value', 'Alterar');

        $request = $this->getRequest();
        $viewData = ['id' => $id, 'form' => $form];

        if (! $request->isPost()) {
            return $viewData;
        }

        $form->setInputFilter($fornecedor->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return $viewData;
        }

        $this->table->saveFornecedor($fornecedor);

        // Redirect to fornecedor list
        return $this->redirect()->toRoute('fornecedor', ['action' => 'index']);
    }

    public function deleteAction() {
        if(!Auth::check()) {
            return $this->redirect()->toRoute('auth');
        }

        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('fornecedor');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->table->deleteFornecedor($id);


            // Redirect to list of fornecedores
            return $this->redirect()->toRoute('fornecedor');
        }

        try {
            $fornecedor = $this->table->getFornecedor($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('fornecedor', ['action' => 'index']);
        }

        $form = $this->getInstanceForm();
        $form->bind($fornecedor);
        $form->get('submit')->setAttribute('value', 'Excluir');
        $viewData = ['id' => $id, 'form' => $form];

        return $viewData;
    }

}
