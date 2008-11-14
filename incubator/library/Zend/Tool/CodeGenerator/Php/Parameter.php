<?php

require_once 'Zend/Tool/CodeGenerator/Php/Abstract.php';

class Zend_Tool_CodeGenerator_Php_Parameter extends Zend_Tool_CodeGenerator_Php_Abstract
{

    protected $_type = null;
    protected $_name = null;
    protected $_defaultValue = null;
    protected $_position = null;
    
    public static function fromReflection(Zend_Reflection_Parameter $reflectionParameter)
    {
        return new self();
    }
    
    public function setType($type)
    {
        $this->_type = $type;
        return $this;
    }
    
    public function getType()
    {
        return $this->_type;
    }
    
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }
    
    public function getName()
    {
        return $this->_name;
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
    
    public function setPosition($position)
    {
        $this->_position = $position;
        return $this;
    }
    
    public function generate()
    {
        
    }
    
}