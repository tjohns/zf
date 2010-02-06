<?php

abstract class Zend_Rbac_Object implements Zend_Rbac_ObjectInterface {
    protected $_name;
    
    protected $_parents = array();
    
    protected $_childs = array();
    
    protected $_assertions;
   
    public function __construct($name) {
        $this->_name = $name;
        $this->_assertions = new Zend_Rbac_AssertionContainer();
        $this->init();
    }
    
    public function init() { }
    
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
    
    public function getAssertions() {
    	return $this->_assertions;
    }
    
    public function addAssertion($value) {
    	$this->_assertions->offsetSet(null, $value);
    	return $this;
    }
    
    public function hasAssertions() {
    	return count($this->getAssertions()) > 0;
    }
}
