<?php

class Zend_Reflection_Docblock_Tag_Return extends Zend_Reflection_Docblock_Tag
{
    
    protected $_type = null;
    
    public function __construct($tagDocblockLine)
    {
        if (!preg_match('#^@(\w+)\s(\w+)(?:\s(.*))?#', $tagDocblockLine, $matches)) {
            throw new Zend_Reflection_Exception('Provided docblock line is does not contain a valid tag');
        }
        
        if ($matches[1] != 'return') {
            throw new Zend_Reflection_Exception('Provided docblock line is does not contain a valid @return tag');
        }
        
        $this->_name = 'return';
        $this->_type = $matches[2];
        if (isset($matches[3])) {
            $this->_description = $matches[3];
        }

    }
    
    public function getType()
    {
        return $this->_type;
    }
    
}