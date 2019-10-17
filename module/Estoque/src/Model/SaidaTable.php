<?php

namespace Estoque\Model;

use Zend\Db\TableGateway\TableGatewayInterface;

/**
 * Description of SaidaTable
 *
 * @author Jessé Rafael das Neves
 */
class SaidaTable {

    private $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll() {
        return $this->tableGateway->select();
    }

    public function getSaida($id) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row = $rowset->current();
        if (! $row) {
            throw new \Zend\Db\Sql\Exception\RuntimeException(sprintf(
                'Could not find row with identifier %d',
                $id
            ));
        }

        return $row;
    }

    public function saveSaida(Saida $saida) {
        $data = [
            'data'       => $saida->data,
            'hora'       => $saida->hora,
            'situacao'   => $saida->situacao,
            'observacao' => $saida->observacao,
            'idpessoa'   => $saida->idpessoa,
        ];

        $id = (int) $saida->id;

        if ($id === 0) {
            $data['situacao'] = Saida::SITUACAO_EM_ELABORACAO;
            $this->tableGateway->insert($data);
            return;
        }

        try {
            $this->getSaida($id);
        } catch (\Zend\Db\Sql\Exception\RuntimeException $e) {
            throw new \Zend\Db\Sql\Exception\RuntimeException(sprintf(
                'Cannot update album with identifier %d; does not exist',
                $id
            ));
        }

        $this->tableGateway->update($data, ['id' => $id]);
    }

    public function deleteSaida($id) {
        $this->tableGateway->delete(['id' => (int) $id]);
    }

    /**
     * @return \Zend\Db\Adapter\AdapterInterface
     */
    public function getAdapter() {
        return $this->tableGateway->getAdapter();
    }

}
