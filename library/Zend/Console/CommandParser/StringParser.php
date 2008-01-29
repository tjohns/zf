<?php

require_once 'Zend/Console/CommandParser/Interface.php';

class Zend_Console_CommandParser_StringParser implements Zend_Console_CommandParser_Interface
{
    protected $_options = array();
    protected $_arguments = array();
    
    protected $_parsed = false;
    
    protected $_results = array();
    
    public function __construct(Array $options)
    {
        $this->_options = $options;
    }
    
    public function setArguments(Array $arguments = array())
    {
        $this->_arguments = $arguments;
    }
    
    public function getRemainingArguments()
    {
        return $this->_arguments;
    }
    
    public function parse()
    {
        if ($this->_parsed) {
            return;
        }
        
        $optionValues = array_values($this->_options);
        $slice = 0;
        
        foreach ($this->_options as $index => $option) {
            if (is_string($this->_arguments[$index])) {
                $this->_results[$option] = $this->_arguments[$index];
                $slice++;
            } else {
                break;
            }
        }
        
        if ($slice) {
            $this->_arguments = array_slice($this->_arguments, $slice);
        }
        
        $this->_parsed = true;
        
    }
    
    public function getResults()
    {
        return $this->_results;
    }
    
    public function getResult($name)
    {
        return (isset($this->_results[$name])) ? $this->_results[$name] : null;
    }

}