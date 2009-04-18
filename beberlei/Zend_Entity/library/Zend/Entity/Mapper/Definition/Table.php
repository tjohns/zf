<?php
/**
 * Mapper
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 * 
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to kontakt@beberlei.de so we can send you a copy immediately.
 *
 * @category   Zend
 * @category   Zend_Entity
 * @copyright  Copyright (c) 2009 Benjamin Eberlei
 * @license    New BSD License
 */

abstract class Zend_Entity_Mapper_Definition_Table
{
    protected $_fetch;

    protected $_tableName;

    protected $_properties = array();

    public function __construct($tableName=null, array $options=array())
    {
        $this->setTable($tableName);
        foreach($options AS $k => $v) {
            $method = "set".ucfirst($k);
            if(method_exists($this, $method)) {
                call_user_func_array(array($this, $method), array($v));
            }
        }
    }

    public function getFetch()
    {
        return $this->_fetch;
    }

    public function setFetch($fetch)
    {
        $this->_fetch = $fetch;
    }

    public function getTable()
    {
        if($this->_tableName == null) {
            throw new Exception("No table has been set for the definition.");
        }
        return $this->_tableName;
    }

    public function setTable($tableName)
    {
        $this->_tableName = $tableName;
    }

    public function __call($method, $args)
    {
        if(substr($method, 0, 3) == "add") {
            $propertyType = substr($method, 3);

            if(!isset($args[0]) || !is_string($args[0])) {
                throw new Exception("First argument of '".$propertyType."' has to be a Property Name of type string.");
            } else {
                $propertyName = $args[0];
            }
            if(!isset($args[1]) || !is_array($args[1])) {
                $options = array();
            } else {
                $options = $args[1];
            }
            return $this->_add($propertyType, $propertyName, $options);
        } else {
            throw new Exception("Unknown method '".$method."' called.");
        }
    }

    protected function _add($propertyType, $propertyName, $options)
    {
        if(isset($this->_properties[$propertyName])) {
            throw new Exception("Property '".$propertyName."' already exists! Cannot have the same property twice.");
        }
        $this->_properties[$propertyName] = Zend_Entity_Mapper_Definition_Utility::loadDefinition($propertyType, $propertyName, $options);
        return $this->_properties[$propertyName];
    }

    public function getProperties()
    {
        return $this->_properties;
    }

    public function getPropertyByName($propertyName)
    {
        return $this->_properties[$propertyName];
    }
}