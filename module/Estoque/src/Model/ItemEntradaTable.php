<?php

namespace Estoque\Model;

use Zend\Db\TableGateway\TableGatewayInterface;

/**
 * Description of ItemEntradaTable
 *
 * @author Jessé Rafael das Neves
 */
class ItemEntradaTable {

    private $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll() {
        return $this->tableGateway->select();
    }

    public function allByEntrada($idEntrada) {
        return $this->tableGateway->select("identrada = $idEntrada");
    }

    public function getItemEntrada($id) {
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

    public function saveItemEntrada(ItemEntrada $itemEntrada) {
        $data = [
            'quantidade'    => $itemEntrada->quantidade,
            'valorunitario' => $itemEntrada->valorunitario,
            'valortotal'    => $itemEntrada->valortotal,
            'identrada'     => $itemEntrada->identrada,
            'idproduto'     => $itemEntrada->idproduto,
            'idestoque'     => $itemEntrada->idestoque,
        ];

        $id = (int) $itemEntrada->id;

        if ($id === 0) {
            $this->tableGateway->insert($data);
            return;
        }

        try {
            $this->getItemEntrada($id);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
                'Cannot update album with identifier %d; does not exist',
                $id
            ));
        }

        $this->tableGateway->update($data, ['id' => $id]);
    }

    public function deleteItemEntrada($id) {
        $this->tableGateway->delete(['id' => (int) $id]);
    }

    public function getAdapter() {
        return $this->tableGateway->getAdapter();
    }

}
