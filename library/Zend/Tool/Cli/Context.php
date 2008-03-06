<?php

require_once 'Zend/Tool/Cli/Context/Global.php';
require_once 'Zend/Tool/Cli/Context/Action.php';
require_once 'Zend/Tool/Cli/Context/Resource.php';

class Zend_Tool_Cli_Context 
{
    
    protected $_isExecutable = false;
    
    /**
     * @var Zend_Tool_Cli_Context_Global
     */
	protected $_globalContext = null;
	
	/**
	 * @var Zend_Tool_Cli_Context_Action
	 */
	protected $_actionContext = null;
	
	/**
	 * @var Zend_Tool_Cli_Context_Resource
	 */
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
	
    public function setExecutable($isExecutable = true)
    {
        $this->_isExecutable = $isExecutable;
        return $this;
    }
    
    public function isExecutable()
    {
        return $this->_isExecutable;
    }	
	
	public function parse(Array $arguments)
	{
		$this->_globalContext->setArguments($arguments);

		$this->_globalContext->parse();
        if ($this->_globalContext->isShortCircuited()) {
            $this->setExecutable($this->_globalContext->isExecutable());
            return;
        }
        
        $this->_actionContext->setArguments($this->_globalContext->getRemainingArguments());

        $this->_actionContext->parse();
        if ($this->_actionContext->isShortCircuited()) {
            $this->setExecutable($this->_actionContext->isExecutable());
            return;
        }
        
        $this->_resourceContext->setArguments($this->_actionContext->getRemainingArguments());
        $this->_resourceContext->parse();
        
        $this->setExecutable($this->_resourceContext->isExecutable());
	}
	
	public function execute()
	{
	    if (!$this->isExecutable()) {
	        throw new Zend_Tool_Exception('Not executable');
	    }
	    
	    // reset short cicuits
	    $this->_globalContext->setShortCircuited(false);
	    $this->_actionContext->setShortCircuited(false);
	    
	    // execute global context
	    $this->_globalContext->execute();
	    
        if ($this->_globalContext->isShortCircuited()) {
            return;
        }
        
        // execution action context
        $this->_actionContext->execute();
        
        if ($this->_actionContext->isShortCircuited()) {
            return;
        }
        
        // execute resource context with action context
        $this->_resourceContext->execute($this->_actionContext);

	}
	
}
