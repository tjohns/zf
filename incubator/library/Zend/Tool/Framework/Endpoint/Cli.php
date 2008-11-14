<?php

require_once 'Zend/Tool/Framework/Endpoint/Abstract.php';
require_once 'Zend/Tool/Framework/Endpoint/Cli/ArgumentParser.php';

class Zend_Tool_Framework_Endpoint_Cli extends Zend_Tool_Framework_Endpoint_Abstract
{
    
    public static function main()
    {
        $cliEndpoint = new self();
        $cliEndpoint->handle();
    }
    
    protected function _init()
    {
        // nothing yet
    }
    
    protected function _preHandle()
    {
        $optParser = new Zend_Tool_Framework_Endpoint_Cli_ArgumentParser($_SERVER['argv'], $this->_request, $this->_response);
        $optParser->parse();
    }
    
    protected function _postHandle()
    {
        
        if ($this->_response->isException()) {
            echo PHP_EOL . 'An error has occured:' . PHP_EOL;
            echo $this->_response->getException()->getMessage() . PHP_EOL;
        }
        
        echo $this->_response->getContent() . PHP_EOL;
    }
    
}
