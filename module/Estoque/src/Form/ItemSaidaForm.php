<?php

namespace Estoque\Form;

use Zend\Form\Form;

/**
 * Description of ItemSaidaForm
 *
 * @author Jessé Rafael das Neves
 */
class ItemSaidaForm extends Form {

    public function __construct() {
        parent::__construct('item-saida');
        $this->criaComponentes();
    }

    private function criaComponentes() {
        $this->addCampoId();
        $this->addCampoIdSaida();
        $this->addCampoSelectProduto();
        $this->addCampoQtdeAtualEstoque();
        $this->addCampoQuantidade();
        $this->addCampoVlrUnit();
        $this->addCampoVlrTotal();
        $this->addSubmit();
    }

    private function addCampoId() {
        $this->add([
            'name' => 'id',
            'type' => 'hidden',
            'options' => [
                'label' => 'Código:',
            ],
            'attributes' => [
                'class'    => 'form-control form-control-sm',
                'readonly' => 'true',
                'disabled' => 'false',
            ],
        ]);
    }

    private function addCampoIdSaida() {
        $this->add([
            'name' => 'idsaida',
            'type' => 'hidden',
            'options' => [
                'label' => 'Cód. Saida:',
            ],
            'attributes' => [
                'class'    => 'form-control form-control-sm',
                'readonly' => 'true',
            ],
        ]);
    }

    private function addCampoQuantidade() {
        $number = new \Zend\Form\Element\Number('quantidade');
        $number->setLabel('Quantidade:');
        $number->setAttributes([
            'min'  => '1',
            'max'  => '999999999',
            'step' => '1', // default step interval is 1
            'class'    => 'form-control form-control-sm',
            'required' => 'true',
        ]);
        $number->setAttribute('id'      , 'quantidade');
        $number->setAttribute('onChange', 'onChangeCampoQuantidadeItemSaida()');
        $this->add($number);
    }

    private function addCampoQtdeAtualEstoque() {
        $number = new \Zend\Form\Element\Number('qtde_atual_estoque');
        $number->setLabel('Qtde. Atual Estoque:');
        $number->setAttributes([
            'min'  => '1',
            'max'  => '999999999',
            'step' => '1', // default step interval is 1
            'class'    => 'form-control form-control-sm',
            'required' => 'true',
            'readonly' => 'true',
        ]);
        $number->setAttribute('id', 'qtde_atual_estoque');
        $this->add($number);
    }

    private function addCampoVlrUnit() {
        $number = new \Zend\Form\Element\Number('valorunitario');
        $number->setLabel('Vlr. Unitário:');
        $number->setAttributes([
            'min'  => '1',
            'max'  => '999999999',
            'step' => '1', // default step interval is 1
            'class'    => 'form-control form-control-sm',
            'required' => 'true',
        ]);
        $number->setAttribute('id'      , 'valorunitario');
        $number->setAttribute('onChange', 'onChangeCampoValorUnitarioItemSaida()');
        $this->add($number);
    }

    private function addCampoVlrTotal() {
        $number = new \Zend\Form\Element\Number('valortotal');
        $number->setLabel('Vlr Total:');
        $number->setAttributes([
            'min'  => '1',
            'max'  => '999999999',
            'step' => '1', // default step interval is 1
            'class'    => 'form-control form-control-sm',
            'required' => 'true',
            'readonly' => 'true',
        ]);

        $number->setAttribute('id', 'valortotal');
        $this->add($number);
    }

    private function addCampoSelectProduto() {
        $select = new \Zend\Form\Element\Select('idproduto');
        $select->setLabel('Produto:');
        $select->setEmptyOption('Selecione...');
        $select->setAttribute('class', 'form-control form-control-sm');
        $select->setAttribute('required', 'true');
        $select->setAttribute('autoFocus', 'true');
        $select->setAttribute('id', 'idproduto');
        $select->setAttribute('onChange', 'onChangeCampoProdutoItemSaida()');
        $this->add($select);
    }

    private function addSubmit() {
        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Confirmar',
                'id'    => 'submitbutton',
                'class' => 'btn btn-sm btn-primary',
            ],
        ]);
    }

}
