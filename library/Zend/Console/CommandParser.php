<?php

class Zend_Console_CommandParser
{
    protected $_cliArgs = array();
    protected $_parsers = array();
    
    public function __construct()
    {
        /** hack **/
        $this->_cliArgs = $_SERVER['argv'];
    }
    
    public function appendParser($name, Zend_Console_CommandParser_Interface $parser, $completionCallback = null)
    {
        if (isset($completionCallback) && !is_callable($completionCallback)) {
            throw new Exception('cannot callback to supplied callback.');
        }
        
        $this->_parsers[] = array(
            'name' => $name,
            'parser' => $parser,
            'callback' => $completionCallback
            );
    }
    
    public function getUsage()
    {
        
    }
    
    public function getResults()
    {
        return $this->_results;
    }
    
    public function getResult($parserName)
    {
        return (isset($this->_results[$parserName])) ? $this->_results[$parserName] : null;
    }
    
    public function parse()
    {
        while (!empty($this->_parsers)) {
            $parserEntry = array_shift($this->_parsers);
            $parserEntry['parser']->parse();
            $results = $parserEntry['parser']->getResults();
            $this->_cliArgs = $parserEntry['parser']->getRemainingArguments();
            Zend_Debug::dump($this->_cliArgs);
            $this->_results[$parserEntry['name']] = $results;

            if (isset($parserEntry['callback'])) {
                call_user_func_array($parserEntry['callback'], array($this, $results));
            }
            
        }

    }
    
    
    
}
