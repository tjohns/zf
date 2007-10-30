<?php

class Zend_Filter_RegexReplace implements Zend_Filter_Interface
{
    
    protected $_match = null;
    protected $_replace = null;
    
    public function __construct($match, $replace)
    {
        $this->_match = $match;
        $this->_replace = $replace;
    }
    
    public function filter($value)
    {
        return preg_replace($this->_match, $this->_replace, $value);
    }
}