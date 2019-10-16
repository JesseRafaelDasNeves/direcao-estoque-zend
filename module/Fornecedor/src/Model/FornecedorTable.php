<?php

namespace Fornecedor\Model;

use Zend\Db\TableGateway\TableGatewayInterface;

/**
 * Description of FornecedorTable
 *
 * @author Jessé Rafael das Neves
 */
class FornecedorTable {

    private $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll() {
        return $this->tableGateway->select();
    }

    public function getFornecedor($id) {
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

    public function saveFornecedor(Fornecedor $fornecedor) {
        $data = [
            'tipo'              => $fornecedor->tipo,
            'inscricaoestadual' => $fornecedor->inscricaoestadual,
            'idpessoa'          => $fornecedor->idpessoa,
        ];

        $id = (int) $fornecedor->id;

        if ($id === 0) {
            $this->tableGateway->insert($data);
            return;
        }

        try {
            $this->getFornecedor($id);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
                'Cannot update album with identifier %d; does not exist',
                $id
            ));
        }

        $this->tableGateway->update($data, ['id' => $id]);
    }

    public function deleteFornecedor($id) {
        $this->tableGateway->delete(['id' => (int) $id]);
    }

    public function getAdapter() {
        return $this->tableGateway->getAdapter();
    }

}
