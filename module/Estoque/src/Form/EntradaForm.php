<?php

namespace Estoque\Form;

use Zend\Form\Form;
use Estoque\Model\Entrada;

/**
 * Description of EntradaForm
 *
 * @author Jessé Rafael das Neves
 */
class EntradaForm extends Form {

    public function __construct() {
        parent::__construct('entrada');
        $this->criaComponentes();
    }

    private function criaComponentes() {
        $this->addCampoId();
        $this->addCampoData();
        $this->addCampoHora();
        $this->addCampoSelectSituacao();
        $this->addCampoNumeroNota();
        $this->addCampoObservacao();
        $this->addCampoSelectFornecedor();
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

    private function addCampoData() {
        $date = new \Zend\Form\Element\Date('data');
        $date->setLabel('Data:');
        $date->setAttributes([
            'min'       => '2019-01-01',
            'max'       => '2019-12-31',
            'step'      => '1', // days; default step interval is 1 day
            'class'     => 'form-control form-control-sm',
            'autofocus' => 'true',
            'required'  => 'true',
        ]);
        $date->setOptions([
            'format' => 'Y-m-d',
        ]);
        $this->add($date);
    }

    private function addCampoHora() {
        $time = new \Zend\Form\Element\Time('hora');
        $time->setLabel('Hora:');
        $time->setAttributes([
            'min'      => '00:00:00',
            'max'      => '23:59:59',
            'step'     => '30', // seconds; default step interval is 60 seconds
            'class'    => 'form-control form-control-sm',
            'required' => 'true',
        ]);
        $time->setOptions([
            'format' => 'H:i:s',
        ]);

        $this->add($time);
    }

    private function addCampoSelectSituacao() {
        $select = new \Zend\Form\Element\Select('situacao');
        $select->setLabel('Situação:');
        $select->setValueOptions(Entrada::getListaSituacao());
        $select->setEmptyOption('Selecione...');
        $select->setAttribute('class', 'form-control form-control-sm');
        $select->setAttribute('required', 'true');
        $this->add($select);
    }

    private function addCampoNumeroNota() {
        $number = new \Zend\Form\Element\Number('numero_nota');
        $number->setLabel('Nº Nota:');
        $number->setAttributes([
            'min'  => '1',
            'max'  => '999999999',
            'step' => '1', // default step interval is 1
            'class'    => 'form-control form-control-sm',
            'required' => 'true',
        ]);
        $this->add($number);
    }

    private function addCampoObservacao() {
        $textarea = new \Zend\Form\Element\Textarea('observacao');
        $textarea->setLabel('Obsevação:');
        $textarea->setAttributes([
            'class' => 'form-control form-control-sm',
        ]);
        $this->add($textarea);
    }

    private function addCampoSelectFornecedor() {
        $select = new \Zend\Form\Element\Select('idfornecedor');
        $select->setLabel('Fornecedor:');
        $select->setEmptyOption('Selecione...');
        $select->setAttribute('class', 'form-control form-control-sm');
        $select->setAttribute('required', 'true');
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
