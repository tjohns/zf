<?php

require_once 'ZendL/Tool/CodeGenerator/Php/Docblock/Tag.php';

class ZendL_Tool_CodeGenerator_Php_Docblock_Tag_Param extends ZendL_Tool_CodeGenerator_Php_Docblock_Tag
{
    
    protected $_datatype = null;
    protected $_paramName = null;
    protected $_description = null;
    
    public static function fromReflection(ZendL_Reflection_Docblock_Tag $reflectionTagParam)
    {
        $paramTag = new self();

        $paramTag->setName('param');
        $paramTag->setDatatype($reflectionTagParam->getType()); // @todo rename
        $paramTag->setParamName($reflectionTagParam->getVariableName());
        $paramTag->setDescription($reflectionTagParam->getDescription());
        
        return $paramTag;
    }

    
    public function setDatatype($datatype)
    {
        $this->_datatype = $datatype;
        return $this;
    }
    
    public function getDatatype()
    {
        return $this->_datatype;
    }
    
    public function setParamName($paramName)
    {
        $this->_paramName = $paramName;
        return $this;
    }
    
    public function getParamName()
    {
        return $this->_paramName;
    }
    
    public function generate()
    {
        $output = $this->getName() . ' ' . $this->_datatype . ' ' . $this->_paramname . ' ' . $this->_description . PHP_EOL;
        return $output;
    }
    
}
