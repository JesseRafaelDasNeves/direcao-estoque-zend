<?php

namespace Auth\Model;

/**
 * Description of User
 *
 * @author Jessé Rafael das Neves
 */
class User {

    public $id;
    public $name;
    public $email;
    public $password;

    public function exchangeArray(Array $data) {
        $this->id       = !empty($data['id'])       ? $data['id']       : null;
        $this->name     = !empty($data['name'])     ? $data['name']     : null;
        $this->email    = !empty($data['email'])    ? $data['email']    : null;
        $this->password = !empty($data['password']) ? $data['password'] : null;
    }

    public function getArrayCopy() {
        return [
            'id'       => $this->id,
            'name'     => $this->name,
            'email'    => $this->email,
            'password' => $this->password,
        ];
    }

}
