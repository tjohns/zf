<?php

interface Zend_CardSpace_Xml_Assertion_Interface {
	public function getAssertionID();
	public function getAttributes();
	public function getAssertionURI();
	public function getConditions();
	public function validateConditions($conditions);
}