<?php

namespace Estoque\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Pessoa\Model\PessoaTable;

/**
 * Description of SaidaResultSet
 *
 * @author Jessé Rafael das Neves
 */
class SaidaResultSet extends ResultSet {

    private $DbAdapter;

    public function __construct(AdapterInterface $dbAdapter) {
        parent::__construct();
        $this->DbAdapter = $dbAdapter;
    }

    public function current() {
        $saida =  parent::current();
        $this->loadPessoa($saida);
        //$this->loadSomaValorTotalItensBySaida($saida);
        return $saida;
    }

    private function loadPessoa(Saida $Saida) {
        $oPessoaTable = new PessoaTable(\Pessoa\Module::newTableGatewayPessoa($this->DbAdapter));
        $oPessoa      = $oPessoaTable->getPessoa($Saida->idpessoa);
        $Saida->setPessoa($oPessoa);
    }

    private function loadSomaValorTotalItensBySaida(Saida $saida) {
        $itemSaidaTable = new \Estoque\Model\ItemSaidaTable(\Estoque\Module::newTableGatewayItemSaida($this->DbAdapter, false));
        $fValor = $itemSaidaTable->somaValorTotalBySaida($saida->id);
        $saida->setValorTotal($fValor);
    }

}
