<?php

require_once 'Zend/Tool/CodeGenerator/Php/Member/Abstract.php';

class Zend_Tool_CodeGenerator_Php_Property extends Zend_Tool_CodeGenerator_Php_Member_Abstract 
{

    protected $_isConst = null;
    protected $_defaultValue = null;

    public static function fromReflection(Zend_Reflection_Property $reflectionProperty) {
        $property = new self();
        $property->setSourceDirty(false);
        
        return $property;
    }
    
    public function setConst($const)
    {
        $this->_isConst = $const;
        return $this;
    }
    
    public function isConst()
    {
        return ($this->_isConst) ? true : false;
    }
    
    public function setDefaultValue($defaultValue)
    {
        $this->_defaultValue = $defaultValue;
        return $this;
    }
    
    public function getDefaultValue()
    {
        return $this->_defaultValue;
    }
    
    public function generate()
    {
        $string = '    ' . $this->_visibility . ' $' . $this->_name . ' = ' . (isset($this->_defaultValue) ? '\'' . $this->_defaultValue . '\'' : 'null') . ';';
        return $string; 
    }
    
}