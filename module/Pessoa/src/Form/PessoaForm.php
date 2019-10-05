<?php

namespace Pessoa\Form;

use Zend\Form\Form;

class PessoaForm extends Form {
    public function __construct() {
        parent::__construct('pessoa');
        $this->criaComponentes();
    }

    private function criaComponentes() {
        $this->add([
            'name' => 'id',
            'type' => 'number',
            'options' => [
                'label' => 'Código',
            ],
        ]);
        $this->add([
            'name' => 'nome',
            'type' => 'text',
            'options' => [
                'label' => 'Nome',
            ],
        ]);
        $this->add([
            'name' => 'cpfcnpj',
            'type' => 'text',
            'options' => [
                'label' => 'CPF/CNPJ',
            ],
        ]);
        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Confirmar',
                'id'    => 'submitbutton',
            ],
        ]);
    }

}