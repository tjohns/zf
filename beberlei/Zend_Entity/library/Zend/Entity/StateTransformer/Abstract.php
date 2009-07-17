<?php
/**
 * @package Zend_Entity
 * @subpackage Mapper
 */

abstract class Zend_Entity_StateTransformer_Abstract
{
    /**
     * @var array
     */
    protected $_propertyNames = array();

    /**
     * @param array $propertyNames
     */
    public function setPropertyNames(array $propertyNames)
    {
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