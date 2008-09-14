<?php

require_once 'ZendL/Tool/CodeGenerator/Php/Docblock/Tag.php';

class ZendL_Tool_CodeGenerator_Php_Docblock_Tag_Return extends ZendL_Tool_CodeGenerator_Php_Docblock_Tag 
{
    
    protected $_datatype = null;
    protected $_description = null;
    
    public static function fromReflection(ZendL_Reflection_Docblock_Tag $reflectionTagReturn)
    {
        $returnTag = new self();
        
        $returnTag->setName('return');
        $returnTag->setDatatype($reflectionTagReturn->getType()); // @todo rename
        $returnTag->setDescription($reflectionTagReturn->getDescription());
        
        return $returnTag;
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
    
    public function setDescription($description)
    {
        $this->_description = $description;
        return $this;
    }
    
    public function getDescription()
    {
        return $this->_description;
    }

    public function generate()
    {
        $output = $this->getName() . ' ' . $this->_datatype . ' ' . $this->_description . PHP_EOL;
        return $output;
    }
    
}