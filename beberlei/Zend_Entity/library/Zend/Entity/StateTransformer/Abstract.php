<?php
/**
 * @package Zend_Entity
 * @subpackage Mapper
 */

abstract class Zend_Entity_StateTransformer_Abstract
{
    protected $_className = null;

    /**
     * @var array
     */
    protected $_propertyNames = array();

    /**
     * @param string $className
     * @param array $propertyNames
     */
    public function setTargetClass($className, array $propertyNames)
    {
        $this->_className = $className;
        $this->_propertyNames = $propertyNames;
    }

    /**
     * @throws Zend_Entity_StateTransformer_Exception
     * @param object
     * @return array
     */
    abstract public function getState($object);

    /**
     * @throws Zend_Entity_StateTransformer_Exception
     * @param object $object
     * @param array $state
     */
    abstract public function setState($object, $state);

    /**
     * @throws Zend_Entity_StateTransformer_Exception
     * @param object $object
     * @param string $idPropertyName
     * @param string|int $id
     */
    abstract public function setId($object, $idPropertyName, $id);
}