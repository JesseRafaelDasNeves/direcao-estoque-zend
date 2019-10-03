<?php

namespace Pessoa\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Pessoa\Model\PessoaTable;

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

    }

    public function editAction() {

    }

    public function deleteAction() {

    }

}
