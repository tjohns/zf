<?php
class Zend_Application_Resource_Rbac
    extends Zend_Application_Resource_ResourceAbstract
{
	protected $_resource;
	
	public function init() {
        $out = $this->getRbac();
        
        $nav = $this->getBootstrap()->view->getHelper('navigation')->navigation();
        $nav->setAcl(new Zend_Acl($out));
        $nav->setRole(Zend_Auth::getInstance()->getIdentity()); //@Todo: think of sth nice here

        return $out;
	}
	
	public function getRbac() {
		if($this->_resource == null) {
	        $options = $this->getOptions();
	        if(!isset($options['adapter'])) {
	            throw new Zend_Rbac_Exception('No adapter was set');
	        }
	        $adapter = $options['adapter'];
	        
	        $this->_resource = Zend_Rbac::factory($adapter, $options);
		}

		return $this->_resource;
	}

	public function __call($name, array $arguments = array()) {
		return call_user_func(array($this->getRbac(), $name), $arguments);
	}
}

class Zend_Acl {
	private $_rbac;
	
	public function __construct(Zend_Rbac $rbac) {
		$this->_rbac = $rbac;
	}
	
	public function __call($method, $arguments) {
		var_dump($method, $arguments);
//		return call_user_func(array($this->_rbac, $method), $arguments);
	}
	
    public function isAllowed($subject, $resource, $priv) {
        if($priv !== null) {
            throw new exception('No privileges(!)');
        }
        
//        var_dump($subject, $resource); exit;
        return $this->_rbac->isAllowed($subject, $resource);
    }
}

/**
 * 
class Zend_Acl {
    private $_rbac;
    
    public function __construct(Zend_Rbac $rbac) {
        $this->_rbac = $rbac;
    }
    
    public function isAllowed($subject, $resource, $priv) {
        if($priv !== null) {
            throw new exception('No privileges(!)');
        }
        
        var_dump($subject, $resource)
        return $this->_rbac->isAllowed($subject, $resource);
    }
    
    public function __call($method, $arguments) {
        throw new exception ('method not implemented');
    }
}
 */