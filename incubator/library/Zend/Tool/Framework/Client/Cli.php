<?php

require_once 'Zend/Tool/Framework/Client/Abstract.php';
require_once 'Zend/Tool/Framework/Client/Cli/ArgumentParser.php';

class Zend_Tool_Framework_Client_Cli extends Zend_Tool_Framework_Client_Abstract
{

    public static function main()
    {
        $cliEndpoint = new self();
        $cliEndpoint->handle();
    }

    protected function _init()
    {
        // Nothing here... yet
    }

    protected function _preHandle()
    {
        $this->_response->setContentCallback(array($this, 'printOutput'));

        $optParser = new Zend_Tool_Framework_Client_Cli_ArgumentParser($_SERVER['argv'], $this->_request, $this->_response);
        $optParser->parse();
    }

    protected function _postHandle()
    {
        if ($this->_response->isException()) {
            echo PHP_EOL . 'An error has occured:' . PHP_EOL;
            echo $this->_response->getException()->getMessage() . PHP_EOL;
        }
    }

    public function printOutput($output)
    {
        echo $output . PHP_EOL;
    }
}
