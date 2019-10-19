<?php

namespace Estoque\Model;

use Produto\Model\Produto;

/**
 * Description of Estoque
 *
 * @author Jessé Rafael das Neves
 */
class Estoque {

    /** @var Produto */
    private $Produto;

    public $id;
    public $quantidade;
    public $idproduto;

    public function exchangeArray(Array $data) {
        $this->id         = !empty($data['id'])         ? $data['id']         : null;
        $this->quantidade = !empty($data['quantidade']) ? $data['quantidade'] : null;
        $this->idproduto  = !empty($data['idproduto'])  ? $data['idproduto']  : null;
    }

    public function getArrayCopy() {
        return [
            'id'         => $this->id,
            'quantidade' => $this->quantidade,
            'idproduto'  => $this->idproduto,
        ];
    }

    public function setProduto(Produto $produto) {
        $this->Produto = $produto;
    }

    public function produto(): Produto {
        if(!isset($this->Produto)) {
            $this->Produto = new Produto();
        }
        return $this->Produto;
    }

    public function addQuantidade(float $qtdeAdd) {
        $quantidadeAtual = $this->quantidade;
        $this->quantidade = ($quantidadeAtual + $qtdeAdd);
    }

    public function retiraQuantidade(float $qtde) {
        $quantidadeAtual = $this->quantidade;
        $this->quantidade = ($quantidadeAtual - $qtde);
    }

}
