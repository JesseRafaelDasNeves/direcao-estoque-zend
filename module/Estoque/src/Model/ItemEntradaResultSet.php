<?php

namespace Estoque\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

/**
 * Description of ItemEntradaResultSet
 *
 * @author Jessé Rafael das Neves
 */
class ItemEntradaResultSet extends ResultSet {

    private $DbAdapter;

    public function __construct(AdapterInterface $dbAdapter) {
        parent::__construct();
        $this->DbAdapter = $dbAdapter;
    }

    public function current() {
        $itemEntrada =  parent::current();
        $this->loadEntrada($itemEntrada);
        $this->loadProduto($itemEntrada);
        return $itemEntrada;
    }

    private function loadEntrada(ItemEntrada $itemEntrada) {
        $oEntradaTable = new EntradaTable(\Estoque\Module::newTableGatewayEntrada($this->DbAdapter));
        $oEntrada      = $oEntradaTable->getEntrada($itemEntrada->identrada);
        $itemEntrada->setEntrada($oEntrada);
    }

    private function loadProduto(ItemEntrada $itemEntrada) {
        $oProdutoTable = new \Produto\Model\ProdutoTable(\Produto\Module::newTableGatewayProduto($this->DbAdapter));
        $oProduto      = $oProdutoTable->getProduto($itemEntrada->idproduto);
        $itemEntrada->setProduto($oProduto);
    }

}
