<?php

require_once 'Zend/Tool/Cli/Context/Abstract.php';
require_once 'Zend/Build/Manifest.php';
require_once 'Zend/Console/Getopt.php';

class Zend_Tool_Cli_Context_Global extends Zend_Tool_Cli_Context_Abstract
{

	public function parse()
	{
	    echo 'Parsing globals' . PHP_EOL;
	    
        $getopt = null;//new Zend_Console_Getopt($this->_globalGetoptRules(), $this->_arguments, array());
        
        // check if global switches are supported
	}
	
	public function execute()
	{
		
	}
	
	protected function _globalGetoptRules()
	{
	    return array(
	       'help|h' => 'Help.'
	       );
	}
	
}
