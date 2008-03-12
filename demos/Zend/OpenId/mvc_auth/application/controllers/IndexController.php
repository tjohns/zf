<?php
require_once 'Zend/Controller/Action.php';
require_once 'Zend/Auth.php';

class IndexController extends Zend_Controller_Action
{
    public function indexAction() 
    {
    	$auth = Zend_Auth::getInstance();
		if (!$auth->hasIdentity()) {
			$this->_redirect('/index/login');
		} else {
			$this->_redirect('/index/welcome');
		}
    }

    public function welcomeAction() 
    {
    	$auth = Zend_Auth::getInstance();
		if (!$auth->hasIdentity()) {
			$this->_redirect('index/login');
		}
		$this->view->user = $auth->getIdentity();
    }

    public function loginAction()
    {
    	$this->view->status = "";
    	if (($this->_request->isPost() &&
    	     $this->_request->getPost('openid_action') == 'login' &&
    	     $this->_request->getPost('openid_identifier', '') !== '') ||
    	    ($this->_request->isPost() &&
    	     $this->_request->getPost('openid_mode') !== null) ||
    	    (!$this->_request->isPost() &&
    	     $this->_request->getQuery('openid_mode') != null)) {
			Zend_Loader::loadClass('Zend_Auth_Adapter_OpenId');
	    	$auth = Zend_Auth::getInstance();
			$result = $auth->authenticate(
    			new Zend_Auth_Adapter_OpenId($this->_request->getPost('openid_identifier')));
			if ($result->isValid()) {
				$this->_redirect('/index/welcome');
		    } else {
        		$auth->clearIdentity();
		        foreach ($result->getMessages() as $message) {
        		    $this->view->status .= "$message<br>\n";
		        }
		    }
		}
		$this->render();
    }

    public function logoutAction()
    {
		Zend_Auth::getInstance()->clearIdentity();
		$this->_redirect('/index/index');
    }
}
