<?php
class Zend_Application_Resource_Rbac
    extends Zend_Application_Resource_ResourceAbstract
{
	protected $_resource;
	
	public function init() {
        return $this->getRbac();
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
