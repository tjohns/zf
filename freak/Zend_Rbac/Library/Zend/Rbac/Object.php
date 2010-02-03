<?php

abstract class Zend_Rbac_Object implements Zend_Rbac_ObjectInterface {
   protected $_name;
    
    public function __construct($name) {
        $this->_name = $name;
    }
    
    public function __toString() {
        return $this->getName();
    }
    
    public function getName() {
        return $this->_name;
    }
}
