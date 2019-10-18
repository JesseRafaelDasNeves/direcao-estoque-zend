<?php

namespace Estoque\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Auth\Model\Auth;
use Estoque\Model\EstoqueTable;

/**
 * Description of EstoqueController
 *
 * @author Jessé Rafael das Neves
 */
class EstoqueController extends AbstractActionController {

    private $table;

    public function __construct(EstoqueTable $table) {
        $this->table = $table;
    }

    public function qtdeestoqueprodutoAction() {
        if(!Auth::check()) {
            return $this->redirect()->toRoute('auth');
        }

        $idProduro = $this->params()->fromRoute('idproduto', 0);
        $estoque  = $this->table->firstEstoqueByProduto($idProduro);

        echo $estoque ? $estoque->quantidade : 0;
        die();
    }

}
