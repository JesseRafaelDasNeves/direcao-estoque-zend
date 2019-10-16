<?php

namespace Produto\Form;

use Zend\Form\Form;
use Produto\Model\Produto;

/**
 * Description of ProdutoForm
 *
 * @author Jessé Rafael das Neves
 */
class ProdutoForm extends Form {

    public function __construct() {
        parent::__construct('produto');
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
        $this->add([
            'name' => 'nome',
            'type' => 'text',
            'options' => [
                'label' => 'Nome:',
            ],
            'attributes' => [
                'class'     => 'form-control form-control-sm',
                'autofocus' => 'true',
                'required'  => 'true',
            ],
        ]);
        $this->addCampoSelectCategoria();
        $this->addCampoSelectUnidade();

        $this->add([
            'name' => 'descricao',
            'type' => 'textarea',
            'options' => [
                'label' => 'Descrição:',
            ],
            'attributes' => [
                'class' => 'form-control form-control-sm',
            ],
        ]);

        $this->addSubmit();
    }

    private function addCampoSelectCategoria() {
        $select = new \Zend\Form\Element\Select('categoria');
        $select->setLabel('Categoria:');
        $select->setValueOptions(Produto::getListaCategoria());
        $select->setEmptyOption('Selecione...');
        $select->setAttribute('class', 'form-control form-control-sm');
        $select->setAttribute('required', 'true');
        $this->add($select);
    }

    private function addCampoSelectUnidade() {
        $select = new \Zend\Form\Element\Select('unidade');
        $select->setLabel('Unidade:');
        $select->setValueOptions(Produto::getListaUnidade());
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
