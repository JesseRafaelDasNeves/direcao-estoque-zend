<?php

namespace Auth\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Auth\Model\UserTable;

/**
 * Description of AuthController
 *
 * @author Jessé Rafael das Neves
 */
class AuthController extends AbstractActionController {

    private $table;

    public function __construct(UserTable $table) {
        $this->table = $table;
    }

    public function loginAction() {
        return new ViewModel();
    }

}
