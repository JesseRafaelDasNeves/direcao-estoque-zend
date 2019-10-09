<?php

namespace Pessoa\Form;

use Zend\Form\Form;
use Pessoa\Model\Pessoa;

class PessoaForm extends Form {

    public function __construct() {
        parent::__construct('pessoa');
        $this->criaComponentes();
    }

    private function criaComponentes() {
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
        $this->addCampoTipo();
        $this->add([
            'name' => 'nome',
            'type' => 'text',
            'options' => [
                'label' => 'Nome:',
            ],
            'attributes' => [
                'class' => 'form-control form-control-sm',
            ],
        ]);
        $this->add([
            'name' => 'cpfcnpj',
            'type' => 'text',
            'options' => [
                'label' => 'CPF/CNPJ:',
            ],
            'attributes' => [
                'class' => 'form-control form-control-sm',
            ],
        ]);
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

    private function addCampoTipo() {
        $select = new \Zend\Form\Element\Select('tipo');
        $select->setLabel('Tipo');
        $select->setValueOptions(Pessoa::getListaTipo());
        $select->setEmptyOption('Selecione...');
        $select->setAttribute('class', 'form-control form-control-sm');
        $this->add($select);
    }

}