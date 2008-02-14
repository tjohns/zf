<?php

require_once 'Zend/Tool/Cli/Context/Global.php';
require_once 'Zend/Tool/Cli/Context/Action.php';
require_once 'Zend/Tool/Cli/Context/Resource.php';

class Zend_Tool_Cli_Context 
{
    
	protected $_globalContext = null;
	protected $_actionContext = null;
	protected $_resourceContext = null;
	
	public function __construct()
	{
        $buildManifest = Zend_Build_Manifest::getInstance();
        $buildManifest->scanIncludePath();
        
        $this->_globalContext = new Zend_Tool_Cli_Context_Global();
        $this->_globalContext->setBuildManifest($buildManifest);
        $this->_actionContext = new Zend_Tool_Cli_Context_Action();
        $this->_actionContext->setBuildManifest($buildManifest);
        $this->_resourceContext = new Zend_Tool_Cli_Context_Resource();
        $this->_resourceContext->setBuildManifest($buildManifest);
        
	}
	
	public function parse(Array $arguments)
	{
		$this->_globalContext->setArguments($arguments);
		$this->_globalContext->parse();
		$this->_actionContext->setArguments($this->_globalContext->getRemainingArguments());
		$this->_actionContext->parse();
        $this->_resourceContext->setArguments($this->_actionContext->getRemainingArguments());
        $this->_resourceContext->parse();
	}
	
	public function execute()
	{
		$this->_globalContext->execute();
		$this->_actionContext->execute();
		$this->_resourceContext->execute();
	}
	
}
