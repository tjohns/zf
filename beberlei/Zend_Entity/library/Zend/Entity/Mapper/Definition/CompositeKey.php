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

    public function uniqueStringIdentifier($forValues)
    {
        if(is_array($forValues)) {
            $parts = array();
            foreach($this->_key AS $k => $field) {
                if(isset($forValues[$field])) {
                    $parts[$k] = $forValues[$field];
                } else {
                    throw new Exception("Cannot generate unique key identifier due to missing field '".$field."'.");
                }
            }
            sort($parts);
            return md5(implode("-", $parts));
        } else {
            throw new Exception("Cannot generate unique key identifier due to missing data.");
        }
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