<?php

class ZendL_Reflection_Docblock_Tag_Param extends ZendL_Reflection_Docblock_Tag
{
    
    protected $_type = null;
    protected $_variableName = null;
    
    
    public function __construct($tagDocblockLine)
    {
        if (!preg_match('#^@(\w+)\s(\w+)(?:\s(\$\S+))?(?:\s(.*))?#s', $tagDocblockLine, $matches)) {
            throw new ZendL_Reflection_Exception('Provided docblock line is does not contain a valid tag');
        }
        
        if ($matches[1] != 'param') {
            throw new ZendL_Reflection_Exception('Provided docblock line is does not contain a valid @param tag');
        }
        
        $this->_name = 'param';
        $this->_type = $matches[2];
        
        if (isset($matches[3])) {
            $this->_variableName = $matches[3];
        }
        
        if (isset($matches[4])) {
            $this->_description = preg_replace('#\s+#', ' ', $matches[4]);
        }
    }
    
    public function getType()
    {
        return $this->_type;
    }
    
    public function getVariableName()
    {
        return $this->_variableName;
    }
    
}