<?php

namespace Produto\Model;

use Zend\Db\TableGateway\TableGatewayInterface;

/**
 * Description of ProdutoTable
 *
 * @author Jessé Rafael das Neves
 */
class ProdutoTable {

    private $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll() {
        return $this->tableGateway->select();
    }

    public function getProduto($id) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row = $rowset->current();
        if (! $row) {
            throw new RuntimeException(sprintf(
                'Could not find row with identifier %d',
                $id
            ));
        }

        return $row;
    }

    public function saveProduto(Produto $produto) {
        $data = [
            'nome'      => $produto->nome,
            'unidade'   => $produto->unidade,
            'categoria' => $produto->categoria,
            'descricao' => $produto->descricao,
        ];

        $id = (int) $produto->id;

        if ($id === 0) {
            $this->tableGateway->insert($data);
            return;
        }

        try {
            $this->getProduto($id);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
                'Cannot update album with identifier %d; does not exist',
                $id
            ));
        }

        $this->tableGateway->update($data, ['id' => $id]);
    }

    public function deleteProduto($id) {
        $this->tableGateway->delete(['id' => (int) $id]);
    }

    public function getAdapter() {
        return $this->tableGateway->getAdapter();
    }

}
