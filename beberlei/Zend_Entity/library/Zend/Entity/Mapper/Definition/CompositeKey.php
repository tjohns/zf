<?php

class Zend_Entity_Mapper_Definition_CompositeKey extends Zend_Entity_Mapper_Definition_PrimaryKey
{
    protected $_key;

    public function __construct($name, $options)
    {
        $this->_key = $name;
    }

    public function getKey()
    {
        return $this->_key;
    }

    public function containValidPrimaryKey($values)
    {
        foreach($this->_key AS $key) {
            if(!isset($values[$key]) || $values[$key] === null) {
                return false;
            }
        }
        return true;
    }
}