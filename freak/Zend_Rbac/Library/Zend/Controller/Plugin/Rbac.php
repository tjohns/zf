<?php
class Zend_Controller_Plugin_Rbac extends Zend_Controller_Plugin_Abstract {
	
	protected $_methodGetSubject;
	
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $front = Zend_Controller_Front::getInstance();
        $resource = $this->getResourceName($request);

        $subject = call_user_func($this->getMethodGetSubject());
        $rbac = $front->getParam('bootstrap')->getResource('Rbac');
        if(!$rbac->isAllowed($subject, $resource)) {
            $this->_notAllowed($request, $subject, $resource);
        }
    }
    
    protected function _notAllowed(Zend_Controller_Request_Abstract $request, $subject, $resource)
    {
        $request->setActionName(null);
        $request->setControllerName(null);
        $request->setModuleName(null);
        throw new Zend_Rbac_Exception(
            (string)$subject. ' is not allowed to access resource '.(string)$resource,
            Zend_Rbac::NO_ACCESS
            
        );
    }

    public function getResourceName(Zend_Controller_Request_Abstract $request)
    {
    	return $request->getModuleName() .
    	       '_' .
    	       $request->getControllerName() .
    	       '_' .
               $request->getActionName();
    }
    
    public function setMethodGetSubject($method) {
    	$this->_methodGetSubject = $method;
    }
    
    public function getMethodGetSubject() {
    	if($this->_methodGetSubject == null) {
    		$this->_methodGetSubject = function() {
                return Zend_Auth::getInstance()->getIdentity();
    		};
    	}

    	return $this->_methodGetSubject;
    }

}
