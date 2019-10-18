<?php

namespace Estoque\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

/**
 * Description of ItemSaidaResultSet
 *
 * @author Jessé Rafael das Neves
 */
class ItemSaidaResultSet extends ResultSet {

    private $DbAdapter;

    public function __construct(AdapterInterface $dbAdapter) {
        parent::__construct();
        $this->DbAdapter = $dbAdapter;
    }

    public function current() {
        $itemSaida =  parent::current();
        $this->loadSaida($itemSaida);
        $this->loadProduto($itemSaida);
        return $itemSaida;
    }

    private function loadSaida(ItemSaida $itemSaida) {
        $oSaidaTable = new SaidaTable(\Estoque\Module::newTableGatewaySaida($this->DbAdapter));
        $oSaida      = $oSaidaTable->getSaida($itemSaida->idsaida);
        $itemSaida->setSaida($oSaida);
    }

    private function loadProduto(ItemSaida $itemSaida) {
        $oProdutoTable = new \Produto\Model\ProdutoTable(\Produto\Module::newTableGatewayProduto($this->DbAdapter));
        $oProduto      = $oProdutoTable->getProduto($itemSaida->idproduto);
        $itemSaida->setProduto($oProduto);
    }

}
