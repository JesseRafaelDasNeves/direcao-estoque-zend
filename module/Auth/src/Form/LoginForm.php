<?php

namespace Auth\Form;

use Zend\Form\Form;

/**
 * Description of LoginForm
 *
 * @author Jessé Rafael das Neves
 */
class LoginForm extends Form {

    public function __construct() {
        parent::__construct('login');
        $this->criaComponentes();
    }

    private function criaComponentes() {
        $this->add([
            'name' => 'email',
            'type' => 'email',
            'id'   => 'inputEmail',
            'options' => [
                'label' => 'E-mail:',
            ],
            'attributes' => [
                'class'     => 'form-control',
                'required' => 'true',
                'autofocus' => 'true',
                'placeholder' => 'Endereço de Email',
            ],
        ]);

        $this->add([
            'name' => 'password',
            'type' => 'password',
            'id'   => 'inputPassword',
            'options' => [
                'label' => 'Senha:',
            ],
            'attributes' => [
                'class'       => 'form-control',
                'required'    => 'true',
                'placeholder' => 'Senha',
            ],
        ]);

        $this->add([
            'name' => 'rememberme',
            'type' => 'checkbox',
            'id'   => 'inputRemember',
            'options' => [
                'label' => 'Lembre de min:',
            ],
        ]);

        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Confirmar',
                'id'    => 'submitbutton',
                'class' => 'btn btn-sm btn-primary btn-block',
            ],
        ]);
    }

}
