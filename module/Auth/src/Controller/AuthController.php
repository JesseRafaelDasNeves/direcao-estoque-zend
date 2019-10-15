<?php

namespace Auth\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Auth\Model\UserTable;
use Auth\Form\LoginForm;
use Auth\Model\User;
use Auth\Model\Auth;
use Zend\Authentication\Adapter\DbTable\CallbackCheckAdapter as AuthAdapter;
use Zend\Authentication\AuthenticationService;

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
        $form = new LoginForm();

        if(Auth::check()) {
            return $this->redirect()->toRoute('home');
        }

        if(!$this->getRequest()->isPost()) {
            return ['form' => $form];
        }

        $user = new User();
        //$form->setInputFilter($user->getInputFilter());
        $form->setData($this->getRequest()->getPost());

        if (!$form->isValid()) {
            return ['form' => $form];
        }

        $user->exchangeArray($form->getData());
        $hashPasswordForm = password_hash($user->password, PASSWORD_BCRYPT);

        $oAuthAdapter = new AuthAdapter($this->table->getAdapter());
        $oAuthAdapter->setTableName('users')
            ->setIdentityColumn('email')
            ->setCredentialColumn('password')
            ->setCredentialValidationCallback(function($hashPasswordForm, $password) {
                return password_verify($password, $hashPasswordForm);
            });


        $oAuthAdapter->setIdentity($user->email)
            ->setCredential($user->password);
        $result = $oAuthAdapter->authenticate();

        if($result->isValid()) {
            Auth::authenticate($oAuthAdapter);
            return $this->redirect()->toRoute('home');
        }

        return $this->redirect()->toRoute('auth', ['errors' => $result->getMessages()]);
    }

    public function logoutAction() {
        Auth::clear();
        return $this->redirect()->toRoute('auth');
    }

}
