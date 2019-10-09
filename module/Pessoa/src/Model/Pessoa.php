<?php

namespace Pessoa\Model;

/**
 * @author Jessé Rafael das Neves
 */
class Pessoa {

    const TIPO_FISICA   = 1,
          TIPO_JURIDICA = 2;

    public $id;
    public $nome;
    public $cpfcnpj;
    public $tipo;

    public function exchangeArray(Array $data) {
        $this->id      = !empty($data['id'])      ? $data['id']      : null;
        $this->tipo    = !empty($data['tipo'])    ? $data['tipo']    : null;
        $this->nome    = !empty($data['nome'])    ? $data['nome']    : null;
        $this->cpfcnpj = !empty($data['cpfcnpj']) ? $data['cpfcnpj'] : null;
    }

    public function getArrayCopy()
    {
        return [
            'id'      => $this->id,
            'tipo'    => $this->tipo,
            'nome'    => $this->nome,
            'cpfcnpj' => $this->cpfcnpj,
        ];
    }

    public static function getListaTipo(int $iTipo = null) {
        return Array(
            self::TIPO_FISICA   => 'Física',
            self::TIPO_JURIDICA => 'Jurídica'
        );
    }

}
