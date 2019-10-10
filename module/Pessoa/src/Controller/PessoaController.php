<?php

namespace Pessoa\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Pessoa\Model\PessoaTable;
use Pessoa\Model\Pessoa;
use Pessoa\Form\PessoaForm;

/**
 * Controlador da Pessoa
 *
 * @author Jessé Rafael das Neves
 */
class PessoaController extends AbstractActionController {

    private $table;

    public function __construct(PessoaTable $table) {
        $this->table = $table;
    }

    public function indexAction() {
        return new ViewModel(['pessoas' => $this->table->fetchAll()]);
    }

    public function addAction() {
        $form = new PessoaForm();
        $form->get('submit')->setValue('Adicionar');

        $request = $this->getRequest();

        if (! $request->isPost()) {
            return ['form' => $form];
        }

        $pessoa = new Pessoa();
        $form->setInputFilter($pessoa->getInputFilter());
        $form->setData($request->getPost());

        if (!$form->isValid()) {
            return ['form' => $form];
        }

        $pessoa->exchangeArray($form->getData());
        $this->table->savePessoa($pessoa);
        return $this->redirect()->toRoute('pessoa');
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);

        if (0 === $id) {
            return $this->redirect()->toRoute('pessoa', ['action' => 'add']);
        }

        // Retrieve the pessoa with the specified id. Doing so raises
        // an exception if the pessoa is not found, which should result
        // in redirecting to the landing page.
        try {
            $pessoa = $this->table->getPessoa($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('pessoa', ['action' => 'index']);
        }

        $form = new PessoaForm();
        $form->bind($pessoa);
        $form->get('submit')->setAttribute('value', 'Alterar');

        $request = $this->getRequest();
        $viewData = ['id' => $id, 'form' => $form];

        if (! $request->isPost()) {
            return $viewData;
        }

        $form->setInputFilter($pessoa->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return $viewData;
        }

        $this->table->savePessoa($pessoa);

        // Redirect to pessoa list
        return $this->redirect()->toRoute('pessoa', ['action' => 'index']);
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('pessoa');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->table->deletePessoa($id);


            // Redirect to list of pessoas
            return $this->redirect()->toRoute('pessoa');
        }

        try {
            $pessoa = $this->table->getPessoa($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('pessoa', ['action' => 'index']);
        }

        $form = new PessoaForm();
        $form->bind($pessoa);
        $form->get('submit')->setAttribute('value', 'Excluir');
        $viewData = ['id' => $id, 'form' => $form];

        return $viewData;
    }

}
