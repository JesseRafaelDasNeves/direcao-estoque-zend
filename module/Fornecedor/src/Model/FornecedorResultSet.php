<?php

namespace Fornecedor\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Pessoa\Model\PessoaTable;

/**
 * Description of FornecedorResultSet
 *
 * @author Jessé Rafael das Neves
 */
class FornecedorResultSet extends ResultSet {

    private $DbAdapter;

    public function __construct(AdapterInterface $dbAdapter) {
        parent::__construct();
        $this->DbAdapter = $dbAdapter;
    }

    public function current() {
        $oFornecedor =  parent::current();
        $this->loadPessoa($oFornecedor);
        return $oFornecedor;
    }

    private function loadPessoa(Fornecedor $oFornecedor) {
        $oPessoaTable = new PessoaTable(\Pessoa\Module::newTableGatewayPessoa($this->DbAdapter));
        $oPessoa      = $oPessoaTable->getPessoa($oFornecedor->idpessoa);
        $oFornecedor->setPessoa($oPessoa);
    }

}
