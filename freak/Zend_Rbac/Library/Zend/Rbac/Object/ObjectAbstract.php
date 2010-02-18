<?php
abstract class Zend_Rbac_Object_ObjectAbstract implements Zend_Rbac_Object {
	protected $_name;

	public function __construct($name) {
		$this->_name = (string)$name;
	}
	
	public function __toString() {
		return $this->_name;
	}
}
