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

abstract class Zend_Entity_Definition_Table
{
    /**
     * @var string
     */
    protected $_fetch;

    /**
     * @var string
     */
    protected $_tableName;

    /**
     * @var array
     */
    protected $_properties = array();

    /**
     * Construct a table
     * 
     * @param string $tableName
     * @param array $options
     */
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

    /**
     * Get current tablename
     *
     * @return string
     */
    public function getTable()
    {
        if($this->_tableName == null) {
            throw new Exception("No table has been set for the definition.");
        }
        return $this->_tableName;
    }

    /**
     * Set table
     * 
     * @param string $tableName
     */
    public function setTable($tableName)
    {
        $this->_tableName = $tableName;
    }

    /**
     * Add new property via magic __call()
     *
     * @param  string $method
     * @param  array $args
     * @return object
     */
    public function __call($method, $args)
    {
        if(substr($method, 0, 3) == "add") {
            $propertyType = substr($method, 3);

            if(!isset($args[0]) || !is_string($args[0])) {
                require_once "Zend/Entity/Exception.php";
                throw new Zend_Entity_Exception(
                    "First argument of '".$propertyType."' has to be a Property Name of type string."
                );
            } else {
                $propertyName = $args[0];
            }
            if(!isset($args[1]) || !is_array($args[1])) {
                $options = array();
            } else {
                $options = $args[1];
            }
            return $this->add($propertyType, $propertyName, $options);
        } else {
            require_once "Zend/Entity/Exception.php";
            throw new Zend_Entity_Exception("Unknown method '".$method."' called.");
        }
    }

    /**
     * @param  string $propertyType
     * @param  string $propertyName
     * @param  array $options
     * @return Zend_Entity_Definition_Property_Abstract
     */
    public function add($propertyType, $propertyName, $options)
    {
        if($this->hasProperty($propertyName)) {
            throw new Zend_Entity_Exception("Property '".$propertyName."' already exists! Cannot have the same property twice.");
        }
        $this->_properties[$propertyName] = Zend_Entity_Definition_Utility::loadDefinition($propertyType, $propertyName, $options);
        return $this->_properties[$propertyName];
    }

    /**
     * Get all current properties of table
     * 
     * @return array
     */
    public function getProperties()
    {
        return $this->_properties;
    }

    /**
     * Does a property exist?
     * 
     * @param  string $propertyName
     * @return boolean
     */
    public function hasProperty($propertyName)
    {
        return isset($this->_properties[$propertyName]);
    }

    /**
     * Property Name
     *
     * @param  string $propertyName
     * @return Zend_Entity_Definition_Property_Interface
     */
    public function getPropertyByName($propertyName)
    {
        if($this->hasProperty($propertyName)) {
            return $this->_properties[$propertyName];
        } else {
            require_once "Zend/Entity/Exception.php";
            throw new Zend_Entity_Exception("Accessed property '".$propertyName."' does not exist for table '".$this->getTable()."'");
        }
    }
}