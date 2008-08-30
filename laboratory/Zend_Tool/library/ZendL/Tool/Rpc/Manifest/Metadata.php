<?php

class ZendL_Tool_Rpc_Manifest_Metadata
{
    
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
}