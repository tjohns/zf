<?php

class Zend_Environment_ModuleRegistry extends FilterIterator
{
	protected $_regex;

	function __construct()
	{
		$this->_regex = '/(?<!Abstract|Interface)\.php$/';
		parent::__construct(new RecursiveDirectoryIterator(dirname(__FILE__) . '/Module'));
	}

	function accept()
	{
		return preg_match($this->_regex, $this->current()->getFilename());
	}
}
