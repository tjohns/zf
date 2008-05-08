<?php

class Zend_Tool_CodeGenerator_Php_Class extends Zend_Tool_CodeGenerator_CodeGeneratorAbstract
{
    
    protected $_classDocblock = null;
    
    protected $_className = array();
    protected $_isAbstract = false;
    
    protected $_extendedClassName = null;
    protected $_implementedClassNames = array();
    
    protected $_properties = array();
    protected $_methods = array();
    
    public function setClassDocblock(Zend_Tool_CodeGenerator_Php_Docblock_Class $classDocblock)
    {
        $this->_classDocblock = $classDocblock;
        return $this;
    }
    
    public function getClassDocblock()
    {
        return $this->_classDocblock;
    }
    
    public function setClassName($className)
    {
        $this->_className = $className;
        return $this;
    }
    
    public function getClassName()
    {
        return $this->_className;
    }

    public function setAbstract($isAbstract)
    {
        $this->_isAbstract = ($isAbstract) ? true : false;
        return $this;
    }
    
    public function isAbstract()
    {
        return $this->_isAbstract;
    }
    
    public function setExtendedClassName($extendedClassName)
    {
        $this->_extendedClassName = $extendedClassName;
        return $this;
    }
    
    public function getExtendedClassName()
    {
        return $this->_extendedClassName;
    }
    
    public function setImplementedClassNames(Array $implementedClassNames)
    {
        $this->_implementedClassNames = $implementedClassNames;
        return $this;
    }
    
    public function getImplementedClassNames()
    {
        return $this->_implementedClassNames;
    }
    
    public function setProperties(Array $properties)
    {
        $this->_properties = $properties;
        return $this;
    }
    
    public function getProperties()
    {
        return $this->_properties;
    }
    
    public function setMethods(Array $methods)
    {
        $this->_methods = array();
        foreach ($methods as $method) {
            $this->addMethod($method);
        }
        return $this;
    }
    
    public function addMethod(Zend_Tool_CodeGenerator_Php_Method $method)
    {
        $this->_methods[] = $method;
        return $this;
    }
    
    public function getMethods()
    {
        return $this->_methods;
    }
    
    public function toString()
    {
        
        if ($this->_classDocblock) {
            $output .= $this->_classDocblock->toString() . PHP_EOL;
        }
        
        if ($this->_isAbstract) {
            $output .= 'abstract ';
        }
        
        $output .= 'class ' . $this->_className;
        
        if ($this->_extendedClassName) {
            $output .= ' extends ' . $this->_extendedClassName;
        }
        
        if ($this->_implementedClassNames) {
            $output .= ' implements ' . implode(', ', $this->_implementedClassNames);
        }
        
        $output .= PHP_EOL . '{' . PHP_EOL . PHP_EOL;
        
        if ($this->_properties) {
            foreach ($this->_properties as $property) {
                $output .= $property->toString() . PHP_EOL . PHP_EOL;
            }
        }
        
        if ($this->_methods) {
            foreach ($this->_methods as $method) {
                $output .= $method->toString() . PHP_EOL . PHP_EOL;
            }
        }
        
        $output .= PHP_EOL . '}' . PHP_EOL;
        
        return $output;
    }
    
}