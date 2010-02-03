<?php
interface Zend_Rbac_ObjectInterface {
	public function __construct($options);
	
	public function __toString();
	
	public function getName();
}
