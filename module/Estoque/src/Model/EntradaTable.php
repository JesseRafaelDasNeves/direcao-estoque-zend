<?php

namespace Estoque\Model;

use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

/**
 * Description of EntradaTable
 *
 * @author Jessé Rafael das Neves
 */
class EntradaTable {

    private $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll() {
        return $this->tableGateway->select();
    }

    public function fetchAllPaginator() {
        $sql = new \Zend\Db\Sql\Sql($this->getAdapter(), $this->tableGateway->getTable());
        $select = $sql->select();
        $select->order(['id desc']);

        $resultSet = new \Zend\Db\ResultSet\HydratingResultSet(new \Zend\Hydrator\ReflectionHydrator(), new Entrada());
        $adSelect  = new DbSelect($select, $this->getAdapter(), $resultSet);
        $paginator = new Paginator($adSelect);
        $paginator->setItemCountPerPage(10);
        return $paginator;
    }

    public function getEntrada($id) {
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

    public function saveEntrada(Entrada $entrada) {
        $data = [
            'data'         => $entrada->data,
            'hora'         => $entrada->hora,
            'situacao'     => $entrada->situacao,
            'numero_nota'  => $entrada->numero_nota,
            'observacao'   => $entrada->observacao,
            'idfornecedor' => $entrada->idfornecedor,
        ];

        $id = (int) $entrada->id;

        if ($id === 0) {
            $data['situacao'] = Entrada::SITUACAO_EM_ELABORACAO;
            $this->tableGateway->insert($data);
            return;
        }

        try {
            $this->getEntrada($id);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
                'Cannot update album with identifier %d; does not exist',
                $id
            ));
        }

        $this->tableGateway->update($data, ['id' => $id]);
    }

    public function deleteEntrada($id) {
        $this->tableGateway->delete(['id' => (int) $id]);
    }

    /**
     * @return \Zend\Db\Adapter\AdapterInterface
     */
    public function getAdapter() {
        return $this->tableGateway->getAdapter();
    }

}
