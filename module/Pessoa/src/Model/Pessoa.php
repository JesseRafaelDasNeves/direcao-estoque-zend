<?php

namespace Pessoa\Model;

/**
 * @author Jessé Rafael das Neves
 */
class Pessoa {

    public $id;
    public $nome;
    public $cpfcnpj;
    public $tipo;

    public function exchangeArray(Array $data) {
        $this->id      = !empty($data['id'])      ? $data['id']      : null;
        $this->nome    = !empty($data['nome'])    ? $data['nome']    : null;
        $this->cpfcnpj = !empty($data['cpfcnpj']) ? $data['cpfcnpj'] : null;
        $this->tipo    = !empty($data['tipo'])    ? $data['tipo']    : null;
    }

}
