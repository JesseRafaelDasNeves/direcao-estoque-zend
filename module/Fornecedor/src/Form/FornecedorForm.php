<?php

namespace Fornecedor\Form;

use Zend\Form\Form;
use Fornecedor\Model\Fornecedor;

/**
 * Description of FornecedorForm
 *
 * @author Jessé Rafael das Neves
 */
class FornecedorForm extends Form {

    public function __construct() {
        parent::__construct('fornecedor');
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
        $this->addCampoSelectTipo();
        $this->add([
            'name' => 'inscricaoestadual',
            'type' => 'text',
            'options' => [
                'label' => 'Inscrição Estadual:',
            ],
            'attributes' => [
                'class'     => 'form-control form-control-sm',
            ],
        ]);
        $this->addCampoSelectPessoa();
        $this->addSubmit();
    }

    private function addCampoSelectTipo() {
        $select = new \Zend\Form\Element\Select('tipo');
        $select->setLabel('Tipo');
        $select->setValueOptions(Fornecedor::getListaTipo());
        $select->setEmptyOption('Selecione...');
        $select->setAttribute('class', 'form-control form-control-sm');
        $select->setAttribute('autofocus', 'true');
        $this->add($select);
    }

    private function addCampoSelectPessoa() {
        $select = new \Zend\Form\Element\Select('idpessoa');
        $select->setLabel('Pessoa:');
        $select->setEmptyOption('Selecione...');
        $select->setAttribute('class', 'form-control form-control-sm');
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
