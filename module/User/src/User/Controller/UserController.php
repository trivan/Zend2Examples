<?php

namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Session\Container; // We need this when using sessions


class UserController extends AbstractActionController {

	protected $storage;
	protected $authservice;
    public function getAuthService()
    {
        if (! $this->authservice) {
            $this->authservice = $this->getServiceLocator()->get('AuthService');
        }
        return $this->authservice;
    }

    public function getSessionStorage()
    {
        if (! $this->storage) {
            $this->storage = $this->getServiceLocator()->get('User\Model\MyAuthStorage');
        }
        return $this->storage;
    }

    public function loginAction() {

    	if ($this->getServiceLocator()->get('AuthService')->hasIdentity())
    	{
    		return $this->redirect()->toRoute('profile');
    	}

        $loginMsg = "";
        $request = $this->getRequest();

        if ($request->isPost()) {

            // Set the input credential values (e.g., from a login form)
            $this->getAuthService()->getAdapter()
            ->setIdentity($_POST['username'])
            ->setCredential($_POST['password']);

            $result = $this->getAuthService()->authenticate();

            $request = $this->getRequest();
            if ($result->isValid()) {

            	if ($request->getPost('rememberme') == 'on') {
            		$this->getSessionStorage()->setRememberMe(1);
            		//set storage again
            		$this->getAuthService()->setStorage($this->getSessionStorage());
            	}

                // set id as identifier in session
                $userId = $this->getAuthService()->getAdapter()->getResultRowObject('id')->id;
                $this->getAuthService()->getStorage()->write(array($userId,$_POST['username']));

                //set permission
                $userSession = new Container('permisson');
                $userSession->permisson = "superguest";

                return $this->redirect()->toRoute('profile');

            } else {
                $loginMsg = $result->getMessages();
            }

            // Print the identity
//             echo $result->getIdentity() . "\n\n";
//             echo "<pre>";print_r($loginMsg);echo "</pre>";
//             echo "<pre>";print_r($this->getAuthService()->getAdapter()->getResultRowObject());echo "</pre>";die;
        }

        return new ViewModel(array(
                    'rs' => $loginMsg,
                ));
    }

    public function logoutAction() {
        if ($this->getAuthService()->hasIdentity()) {
            $this->getSessionStorage()->forgetMe();
            $this->getAuthService()->clearIdentity();

            //clear session permission
            $session_user = new Container('permisson');
            $session_user->getManager()->getStorage()->clear();

            $this->flashmessenger()->addMessage("You've been logged out");
        }
    	return $this->redirect()->toRoute("tuser");
    }

    public function profileAction() {
        if (! $this->getServiceLocator()->get('AuthService')->hasIdentity())
        {
            return $this->redirect()->toRoute('tuser');
        }
        $users = $this->getServiceLocator()->get('AuthService')->getIdentity();
//         echo "<pre>";print_r($users);echo "</pre>";die;

                return new ViewModel(array(
                    'users' => $users,
                ));
    }
}
