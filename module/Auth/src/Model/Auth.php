<?php

namespace Auth\Model;

use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable\CallbackCheckAdapter as AuthAdapter;

/**
 * Description of Auth
 *
 * @author Jessé Rafael das Neves
 */
class Auth {

    public static function check() {
        $authService = new AuthenticationService();
        return $authService->hasIdentity();
    }

    public static function authenticate(AuthAdapter $oAdapter) {
        $authService = new AuthenticationService();
        return $authService->authenticate($oAdapter);
    }

    public static function clear() {
        $authService = new AuthenticationService();
        return $authService->clearIdentity();
    }

    public static function identity() {
        $authService = new AuthenticationService();
        return $authService->getIdentity();
    }

}
