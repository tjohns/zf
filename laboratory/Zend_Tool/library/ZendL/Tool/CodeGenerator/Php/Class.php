<?php

require_once 'ZendL/Tool/CodeGenerator/Php/Abstract.php';
require_once 'ZendL/Tool/CodeGenerator/Php/Member/Container.php';
require_once 'ZendL/Tool/CodeGenerator/Php/Method.php';
require_once 'ZendL/Tool/CodeGenerator/Php/Property.php';
require_once 'ZendL/Tool/CodeGenerator/Php/Docblock.php';

class ZendL_Tool_CodeGenerator_Php_Class extends ZendL_Tool_CodeGenerator_Php_Abstract
{
    
    /**
     * @var ZendL_Tool_CodeGenerator_Php_Docblock
     */
    protected $_docblock = null;
    
    protected $_name = null;
    protected $_isAbstract = false;
    
    protected $_extendedClass = null;
    protected $_implementedInterfaces = array();
    
    /**
     * @var ZendL_Tool_CodeGenerator_Php_Property[]
     */
    protected $_properties = null;
    
    /**
     * @var ZendL_Tool_CodeGenerator_Php_Method[]
     */
    protected $_methods = null;

    
    public static function fromReflection(ZendL_Reflection_Class $reflectionClass)
    {
        $class = new self();
        
        $class->setSourceContent($class->getSourceContent());
        $class->setSourceDirty(false);
        
        if ($reflectionClass->getDocComment() != '') {
            $class->setDocblock(ZendL_Tool_CodeGenerator_Php_Docblock::fromReflection($reflectionClass->getDocblock()));
        }
        
        $class->setAbstract($reflectionClass->isAbstract());
        $class->setName($reflectionClass->getName());
        
        if ($parentClass = $reflectionClass->getParentClass()) {
            $class->setExtendedClass($parentClass->getName());
        }
        
        $class->setImplementedInterfaces($reflectionClass->getInterfaceNames());
        
        $properties = array();
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            if ($reflectionProperty->getDeclaringClass()->getName() == $class->getName()) {
                $properties[] = ZendL_Tool_CodeGenerator_Php_Property::fromReflection($reflectionProperty);
            }
        }
        $class->setProperties($properties);
        
        $methods = array();
        foreach ($reflectionClass->getMethods() as $reflectionMethod) {
            if ($reflectionMethod->getDeclaringClass()->getName() == $class->getName()) {
                $methods[] = ZendL_Tool_CodeGenerator_Php_Method::fromReflection($reflectionMethod);
            }
        }
        $class->setMethods($methods);
        
        return $class;
    }
    
    public function setDocblock(ZendL_Tool_CodeGenerator_Php_Docblock $docblock)
    {
        $this->_docblock = $docblock;
        return $this;
    }
    
    public function getDocblock()
    {
        return $this->_docblock;
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

    public function setAbstract($isAbstract)
    {
        $this->_isAbstract = ($isAbstract) ? true : false;
        return $this;
    }
    
    public function isAbstract()
    {
        return $this->_isAbstract;
    }
    
    public function setExtendedClass($extendedClass)
    {
        $this->_extendedClass = $extendedClass;
        return $this;
    }
    
    public function getExtendedClass()
    {
        return $this->_extendedClass;
    }
    
    public function setImplementedInterfaces(Array $implementedInterfaces)
    {
        $this->_implementedInterfaces = $implementedInterfaces;
        return $this;
    }
    
    public function getImplementedInterfaces()
    {
        return $this->_implementedInterfaces;
    }
    
    public function setProperties(Array $properties)
    {
        foreach ($properties as $property) {
            $this->setProperty($property);
        }
        
        return $this;
    }
    
    public function setProperty($property)
    {
        if (is_array($property)) {
            $property = new ZendL_Tool_CodeGenerator_Php_Property($property);
            $propertyName = $property->getName();
        } elseif ($property instanceof ZendL_Tool_CodeGenerator_Php_Property) {
            $propertyName = $property->getName();
        } else {
            require_once 'ZendL/Tool/CodeGenerator/Php/Exception.php';
            throw new ZendL_Tool_CodeGenerator_Php_Exception('setProperty() expects either an array of property options or an instance of ZendL_Tool_CodeGenerator_Php_Property');
        }
        
        if (isset($this->_properties[$propertyName])) {
            require_once 'ZendL/Tool/CodeGenerator/Php/Exception.php';
            throw new ZendL_Tool_CodeGenerator_Php_Exception('A property by name ' . $propertyName . ' already exists in this class.');
        }
        
        $this->_properties->append($property);
        return $this;
    }
    
    public function getProperties()
    {
        return $this->_properties;
    }
    
    public function getProperty($propertyName)
    {
        return (isset($this->_properties[$propertyName])) ? $this->_properties[$propertyName] : false;
    }
    
    public function setMethods(Array $methods)
    {
        foreach ($methods as $method) {
            $this->setMethod($method);
        }
        return $this;
    }
    
    public function setMethod($method)
    {
        if (is_array($method)) {
            $method = new ZendL_Tool_CodeGenerator_Php_Method($method);
            $methodName = $method->getName();
        } elseif ($method instanceof ZendL_Tool_CodeGenerator_Php_Method) {
            $methodName = $method->getName();
        } else {
            require_once 'ZendL/Tool/CodeGenerator/Php/Exception.php';
            throw new ZendL_Tool_CodeGenerator_Php_Exception('setMethod() expects either an array of method options or an instance of ZendL_Tool_CodeGenerator_Php_Method');
        }
        
        if (isset($this->_methods[$methodName])) {
            require_once 'ZendL/Tool/CodeGenerator/Php/Exception.php';
            throw new ZendL_Tool_CodeGenerator_Php_Exception('A method by name ' . $methodName . ' already exists in this class.');
        }
        
        $this->_methods->append($method);
        return $this;
    }
    
    public function getMethods()
    {
        return $this->_methods;
    }
    
    public function getMethod($methodName)
    {
        return (isset($this->_methods[$methodName])) ? $this->_methods[$methodName] : false;
    }
    
    public function isSourceDirty()
    {
        if (($docblock = $this->getDocblock()) && $docblock->isSourceDirty()) {
            return true;
        }
        
        foreach ($this->_properties as $property) {
            if ($property->isSourceDirty()) {
                return true;
            }
        }
        
        foreach ($this->_methods as $method) {
            if ($method->isSourceDirty()) {
                return true;
            }
        }
        
        return parent::isSourceDirty();
    }
    
    public function generate()
    {
        if (!$this->isSourceDirty()) {
            return $this->getSourceContent();
        }
        
        $output = ''; 
        
        if ($this->_docblock) {
            $output .= $this->_docblock->generate();
        }
        
        if ($this->_isAbstract) {
            $output .= 'abstract ';
        }
        
        $output .= 'class ' . $this->_name;
        
        if ($this->_extendedClass) {
            $output .= ' extends ' . $this->_extendedClass;
        }
        
        if ($this->_implementedInterfaces) {
            $output .= ' implements ' . implode(', ', $this->_implementedInterfaces);
        }
        
        $output .= PHP_EOL . '{' . PHP_EOL . PHP_EOL;
        
        if ($this->_properties) {
            foreach ($this->_properties as $property) {
                $output .= $property->generate() . PHP_EOL . PHP_EOL;
            }
        }
        
        if ($this->_methods) {
            foreach ($this->_methods as $method) {
                $output .= $method->generate() . PHP_EOL . PHP_EOL;
            }
        }
        
        $output .= PHP_EOL . '}' . PHP_EOL;
        
        return $output;
    }
    
    protected function _init()
    {
        $this->_properties = new ZendL_Tool_CodeGenerator_Php_Member_Container(ZendL_Tool_CodeGenerator_Php_Member_Container::TYPE_PROPERTY);
        $this->_methods = new ZendL_Tool_CodeGenerator_Php_Member_Container(ZendL_Tool_CodeGenerator_Php_Member_Container::TYPE_METHOD);
    }

}
