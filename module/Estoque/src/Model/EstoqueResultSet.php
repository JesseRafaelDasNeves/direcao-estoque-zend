<?php

namespace Estoque\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

/**
 * Description of EstoqueResultSet
 *
 * @author Jessé Rafael das Neves
 */
class EstoqueResultSet extends ResultSet {

    private $DbAdapter;

    public function __construct(AdapterInterface $dbAdapter) {
        parent::__construct();
        $this->DbAdapter = $dbAdapter;
    }

    public function current() {
        $Estoque =  parent::current();
        $this->loadProduto($Estoque);
        return $Estoque;
    }

    private function loadProduto(Estoque $estoque) {
        $oProdutoTable = new \Produto\Model\ProdutoTable(\Produto\Module::newTableGatewayProduto($this->DbAdapter));
        $oProduto      = $oProdutoTable->getProduto($estoque->idproduto);
        $estoque->setProduto($oProduto);
    }

}
