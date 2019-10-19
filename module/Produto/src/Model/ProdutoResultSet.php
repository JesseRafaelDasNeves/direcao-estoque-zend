<?php

namespace Produto\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Produto\Model\ProdutoTable;

/**
 * Description of ProdutoResultSet
 *
 * @author Jessé Rafael das Neves
 */
class ProdutoResultSet extends ResultSet {

    public function __construct(AdapterInterface $dbAdapter) {
        parent::__construct();
        $this->DbAdapter = $dbAdapter;
    }

    public function current() {
        $oFornecedor =  parent::current();
        $this->loadQuantidadeTotalEstoque($oFornecedor);
        return $oFornecedor;
    }

    private function loadQuantidadeTotalEstoque(Produto $produto) {
        $estoqueTable = new \Estoque\Model\EstoqueTable(\Estoque\Module::newTableGatewayEstoque($this->DbAdapter, false));
        $estoque      = $estoqueTable->firstEstoqueByProduto($produto->id);

        if($estoque) {
            $produto->setQtdeTotalEstoque($estoque->quantidade);
        }
    }

}
