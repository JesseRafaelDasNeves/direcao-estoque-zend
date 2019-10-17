<?php

namespace Estoque\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Fornecedor\Model\FornecedorTable;

/**
 * Description of EntradaResultSet
 *
 * @author Jessé Rafael das Neves
 */
class EntradaResultSet extends ResultSet {

    private $DbAdapter;

    public function __construct(AdapterInterface $dbAdapter) {
        parent::__construct();
        $this->DbAdapter = $dbAdapter;
    }

    public function current() {
        $oEntrada =  parent::current();
        $this->loadFornecedor($oEntrada);
        return $oEntrada;
    }

    private function loadFornecedor(Entrada $Entrada) {
        $oFornecedorTable = new FornecedorTable(\Fornecedor\Module::newTableGatewayFornecedor($this->DbAdapter));
        $oFornecedor      = $oFornecedorTable->getFornecedor($Entrada->idfornecedor);
        $Entrada->setFornecedor($oFornecedor);
    }

}
