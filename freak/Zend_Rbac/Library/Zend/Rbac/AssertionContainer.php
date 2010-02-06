<?php

class Zend_Rbac_AssertionContainer extends ArrayObject {
    
    /**
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet ($key, $value)
    {
    	if(is_string($value)) {
    		if(!Zend_Loader_Autoloader::autoload($value)) {
    			throw new Zend_Rbac_Exception(
                    'Could not load the class specified'
    			);
    		}

            $value = new $value();
    	}

    	if(!is_object($value) || !$value instanceof Zend_Rbac_Assert_Interface) {
    		throw new Zend_Rbac_Exception(
    		  'Given value is no object or does not implement Zend_Rbac_Assert_Interface'
    	   );
    	}
    	
    	$key = get_class($value);
    	if(isset($this[$key])) {
    		throw new Zend_Rbac_Exception(
    		  'Assertion was already registered to this object. Cannot register twice.'
    		);
    	}
    	
    	return parent::offsetSet(get_class($value), $value);
    }
    
}
