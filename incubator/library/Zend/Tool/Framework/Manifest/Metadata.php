<?php

class Zend_Tool_Framework_Manifest_Metadata
{
    const ATTRIBUTES_ALL    = 'attributesAll';
    const ATTRIBUTES_PARENT = 'attributesParent';
    
    protected $_type        = 'Global';
    protected $_name        = null;
    protected $_value       = null;
    protected $_reference   = null;
    
    public function __construct(Array $options = array())
    {
        if ($options) {
            $this->setOptions($options);
        }
    }
    
    public function setOptions(Array $options)
    {
        foreach ($options as $optionName => $optionValue) {
            $setMethod = 'set' . $optionName;
            if (method_exists($this, $setMethod)) {
                $this->{$setMethod}($optionValue);
            }
        }
    }

    /**
     * @return unknown
     */
    public function getType()
    {
        return $this->_type;
    }
    
    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->_type = $type;
    }

    /**
     * @return unknown
     */
    public function getName()
    {
        return $this->_name;
    }
    
    /**
     * @param unknown_type $Name
     */
    public function setName($name)
    {
        $this->_name = $name;
    }
    
    /**
     * @return unknown
     */
    public function getValue()
    {
        return $this->_value;
    }
    
    /**
     * @param unknown_type $Value
     */
    public function setValue($value)
    {
        $this->_value = $value;
    }

    public function setReference($reference)
    {
        $this->_reference = $reference;
        return $this;
    }
    
    public function getReference()
    {
        return $this->_reference;
    }
    
    public function getAttributes($type = self::ATTRIBUTES_ALL)
    {
        $thisReflection = new ReflectionObject($this);
            
        $metadataPairValues = array();

        foreach (get_object_vars($this) as $varName => $varValue) {
            if ($type == self::ATTRIBUTES_PARENT && ($thisReflection->getProperty($varName)->getDeclaringClass()->getName() == 'Zend_Tool_Framework_Manifest_Metadata')) {
                continue;
            }
            
            if (is_object($varValue)) {
                $varValue = '(object)';
            }
            
            if (is_null($varValue)) {
                $varValue = '(null)';
            }
            
            $metadataPairValues[ltrim($varName, '_')] = $varValue;
        }
        
        return $metadataPairValues;
    }
    
    public function __toString()
    {
        return 'Type: ' . $this->_type . ', Name: ' . $this->_name . ', Value: ' . (is_array($this->_value) ? http_build_query($this->_value) : (string) $this->_value);
    }
}