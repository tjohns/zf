<?php

require_once 'Zend/Tool/Cli/Context.php';

class Zend_Tool_Cli 
{

    protected $_arguments = array();

	public function __construct()
	{
		
	}
	
	public function setArguments($arguments)
	{
	   $this->_arguments = $arguments;
	   return $this;
	}
	
    public function run()
    {
        $cliContext = new Zend_Tool_Cli_Context();
        $cliContext->parse($this->_arguments);
        $cliContext->execute();
    }
	
}
