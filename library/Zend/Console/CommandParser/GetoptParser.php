<?php

class Zend_Console_CommandParser_GetoptParser extends Zend_Console_Getopt implements Zend_Console_CommandParser_Interface
{
    protected $_results = array();
    
    
    
    public function __construct($options)
    {
        parent::__construct($options, null, array('stopOnUnknownOption' => true));
    }
    
    public function setArguments(Array $arguments = array())
    {
        parent::setArguments($argv);
    }
    
    public function getRemainingArguments()
    {
        return $this->getRemainingArgs();
    }
    
    public function parse()
    {
        parent::parse();
    }
    
    public function getResults()
    {
        return $this->_options;
    }
    
    public function getResult($name)
    {
        return (isset($this->_options[$name])) ? $this->_options[$name] : null;
    }
    
}