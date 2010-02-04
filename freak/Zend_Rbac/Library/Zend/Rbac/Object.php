<?php

abstract class Zend_Rbac_Object implements Zend_Rbac_ObjectInterface {
    protected $_name;
    
    protected $_parents = array();
    
    protected $_childs = array();
   
    public function __construct($name) {
        $this->_name = $name;
    }
    
    public function __toString() {
        return $this->getName();
    }
    
    public function getName() {
        return $this->_name;
    }
    
    public function addParent(Zend_Rbac_Object $object) {
    	$this->_parents[] = $object;
    	$object->addChild($this);
        return $this;
    }
    
    public function addChild(Zend_Rbac_Object $object) {
    	$this->_childs[] = $object;
    	return $this;
    }
    
    public function getParents() {
    	return $this->_parents;
    }
}
