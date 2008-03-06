<?php

require_once 'Zend/Tool/Cli/Context/Abstract.php';
require_once 'Zend/Build/Manifest.php';
require_once 'Zend/Console/Getopt.php';

class Zend_Tool_Cli_Context_Global extends Zend_Tool_Cli_Context_Abstract
{

	public function parse()
	{
	    
	    try {
        $getopt = new Zend_Console_Getopt($this->_globalGetoptRules(), $this->_arguments, array('parseAll' => false));
        $getopt->parse();
	    } catch (Exception $e) {
	        die($e->getMessage());
	    }
        
        if (in_array('help', $getopt->getOptions())) {
            $this->setShortCircuited(true);
            $this->_displayHelp();
            return;
        }
        
        
        $this->setExecutable();
        
        // check if global switches are supported
        return;
	}
	
	public function execute()
	{
	    // TODO not sure what we do here.
		return;
	}
	
	protected function _globalGetoptRules()
	{
	    return array(
	       'help|h' => 'Help.'
	       );
	}
	
	protected function _displayHelp()
	{
	    echo "Help Message" . PHP_EOL;
	}

}
