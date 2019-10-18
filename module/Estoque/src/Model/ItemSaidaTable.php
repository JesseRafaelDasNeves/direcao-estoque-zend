<?php

namespace Estoque\Model;

use Zend\Db\TableGateway\TableGatewayInterface;

/**
 * Description of ItemSaidaTable
 *
 * @author Jessé Rafael das Neves
 */
class ItemSaidaTable {

    private $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll() {
        return $this->tableGateway->select();
    }

    public function allBySaida($idSaida) {
        return $this->tableGateway->select(['idsaida' => $idSaida]);
    }

    public function getItemSaida($id) {
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

    public function saveItemSaida(ItemSaida $itemSaida) {
        $data = [
            'quantidade'    => $itemSaida->quantidade,
            'valorunitario' => $itemSaida->valorunitario,
            'valortotal'    => $itemSaida->valortotal,
            'idsaida'       => $itemSaida->idsaida,
            'idproduto'     => $itemSaida->idproduto,
            'idestoque'     => $itemSaida->idestoque,
        ];

        $id = (int) $itemSaida->id;

        if ($id === 0) {
            $this->tableGateway->insert($data);
            return;
        }

        try {
            $this->getItemSaida($id);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
                'Cannot update album with identifier %d; does not exist',
                $id
            ));
        }

        $this->tableGateway->update($data, ['id' => $id]);
    }

    public function deleteItemSaida($id) {
        $this->tableGateway->delete(['id' => (int) $id]);
    }

    public function getAdapter() {
        return $this->tableGateway->getAdapter();
    }

    public function somaValorTotalBySaida($idSaida) {
        $fValorTotal = 0;
        foreach ($this->allBySaida($idSaida) as $oItemSaida) {
            $fValorTotal = ($fValorTotal + $oItemSaida->valortotal);
        }

        return $fValorTotal;
    }

}
