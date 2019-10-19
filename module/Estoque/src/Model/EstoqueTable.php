<?php

namespace Estoque\Model;

use Zend\Db\TableGateway\TableGatewayInterface;

/**
 * Description of EstoqueTable
 *
 * @author Jessé Rafael das Neves
 */
class EstoqueTable {

    private $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll() {
        return $this->tableGateway->select();
    }

    public function getEstoque($id) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row = $rowset->current();
        if (! $row) {
            throw new \Zend\Db\Exception\RuntimeException(sprintf(
                'Could not find row with identifier %d',
                $id
            ));
        }

        return $row;
    }

    /**
     * @param int $idProduro
     *
     * @return Estoque
     */
    public function firstEstoqueByProduto(int $idProduro) {
        $rowset = $this->tableGateway->select(['idproduto' => $idProduro]);
        $row = $rowset->current();
        return $row;
    }

    public function saveEstoque(Estoque $estoque) {
        $data = [
            'quantidade' => $estoque->quantidade,
            'idproduto'  => $estoque->idproduto,
        ];

        $id = (int) $estoque->id;

        if ($id === 0) {
            $this->tableGateway->insert($data);
            return;
        }

        try {
            $this->getEstoque($id);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
                'Cannot update album with identifier %d; does not exist',
                $id
            ));
        }

        $this->tableGateway->update($data, ['id' => $id]);
    }

    public function deleteEstoque($id) {
        $this->tableGateway->delete(['id' => (int) $id]);
    }

    public function getAdapter() {
        return $this->tableGateway->getAdapter();
    }

    public function getMovimentacoesByIdEstoque(int $id) {
        $sql = new \Zend\Db\Sql\Sql($this->getAdapter(), $this->tableGateway->getTable());
        $selectEntrada = $sql->select();
        $selectEntrada->columns([
            'estoques.*',
            'identrada',
            'idsaida' => null,
        ], false);
        $selectEntrada->join(
            'itensentrada',
            'itensentrada.idestoque = estoques.id',
            [
                'qtde_movimentada' => 'quantidade',
                'valorunitario',
                'valortotal',
            ],
            $selectEntrada::JOIN_INNER
        );
        $selectEntrada->join(
            'entradas',
            'entradas.id = itensentrada.identrada',
            [
                'data',
            ],
            $selectEntrada::JOIN_INNER
        );
        $selectEntrada->where(['estoques.id' => $id]);

        $selectSaida = $sql->select();
        $selectSaida->columns([
            'estoques.*',
            'identrada' => null,
            'idsaida',
        ], false);
        $selectSaida->join(
            'itens_saida',
            'itens_saida.idestoque = estoques.id' ,
            [
                'qtde_movimentada' => 'quantidade',
                'valorunitario',
                'valortotal',
            ],
            $selectSaida::JOIN_INNER
        );
        $selectSaida->join(
            'saidas',
            'saidas.id = itens_saida.idsaida',
            [
                'data',
            ],
            $selectSaida::JOIN_INNER
        );
        $selectSaida->where(['estoques.id' => $id]);

        $selectEntrada->combine($selectSaida);
        /* @var $adapter \Zend\Db\Adapter\Adapter */
        $adapter = $this->getAdapter();
        return $sql->prepareStatementForSqlObject($selectEntrada)->execute();
    }

}
