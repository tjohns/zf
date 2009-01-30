<?php

class Zend_Tool_Project_Profile_Resource_SearchConstraints
{
    
    protected $_constraints = array();
    
    public function __construct($options = null)
    {
        if (is_string($options)) {
            $this->addConstraint($options);
        } elseif (is_array($options)) {
            $this->setOptions($options);
        }
    }
    
    public function setOptions(Array $option)
    {
        foreach ($option as $optionName => $optionValue) {
            if (is_int($optionName)) {
                $this->addConstraint($optionValue);
            } elseif (is_string($optionName)) {
                $this->addConstraint(array('name' => $optionName, 'params' => $optionValue));
            }
        }

    }
    
    public function addConstraint($constraint)
    {
        if (is_string($constraint)) {
            $name   = $constraint;
            $params = array();
        } elseif (is_array($constraint)) {
            $name   = $constraint['name'];
            $params = $constraint['params'];
        }
        
        $constraint = $this->_makeConstraint($name, $params);
        
        array_push($this->_constraints, $constraint);
        return $this;
    }
    
    public function getConstraint()
    {
        return array_shift($this->_constraints);
    }
            
    protected function _makeConstraint($name, $params)
    {
        $value = array('name' => $name, 'params' => $params);
        return new ArrayObject($value, ArrayObject::ARRAY_AS_PROPS);
    }
    
}