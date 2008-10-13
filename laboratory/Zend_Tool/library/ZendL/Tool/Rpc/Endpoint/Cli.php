<?php

require_once 'ZendL/Tool/Rpc/Endpoint/Abstract.php';
require_once 'ZendL/Tool/Rpc/Endpoint/Cli/GetoptParser.php';

class ZendL_Tool_Rpc_Endpoint_Cli extends ZendL_Tool_Rpc_Endpoint_Abstract
{
    
    public static function main()
    {
        $cliEndpoint = new self();
        //$cliEndpoint->setDefaults();
        $cliEndpoint->handle();
    }
    
    protected function _init()
    {
        
    }
    
    protected function _preHandle()
    {
        
        
        $optParser = new ZendL_Tool_Rpc_Endpoint_Cli_GetoptParser($this, $_SERVER['argv']);
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
