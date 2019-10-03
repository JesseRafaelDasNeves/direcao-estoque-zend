<?php

namespace Pessoa\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;

/**
 * @author Jessé Rafael das Neves
 */
class PessoaTable {

    private $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll() {
        return $this->tableGateway->select();
    }

    public function getPessoa($id) {
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

    public function savePessoa(Pessoa $pessoa) {
        $data = [
            'nome'    => $pessoa->nome,
            'cpfcnpj' => $pessoa->cpfcnpj,
            'tipo'    => $pessoa->tipo,
        ];

        $id = (int) $pessoa->id;

        if ($id === 0) {
            $this->tableGateway->insert($data);
            return;
        }

        try {
            $this->getPessoa($id);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
                'Cannot update album with identifier %d; does not exist',
                $id
            ));
        }

        $this->tableGateway->update($data, ['id' => $id]);
    }

    public function deletePessoa($id) {
        $this->tableGateway->delete(['id' => (int) $id]);
    }

}
