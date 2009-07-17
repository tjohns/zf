<?php
/**
 * @package Zend_Entity
 * @subpackage Mapper
 */

class Zend_Entity_StateTransformer_Array extends Zend_Entity_StateTransformer_Abstract
{
    /**
     * @throws Zend_Entity_StateTransformer_Exception
     * @param object
     * @return array
     */
    public function getState($object)
    {
        if(!method_exists($object, 'getState')) {
            require_once "Zend/Entity/StateTransformer/Exception.php";
            throw new Zend_Entity_StateTransformer_Exception(
                "Array Transformer requires getState() method on entity '".get_class($object)."'."
            );
        }
        $state = $object->getState();
        $requiredState = array();
        foreach($this->_propertyNames AS $propertyName) {
            if(!array_key_exists($propertyName, $state)) {
                require_once "Zend/Entity/StateTransformer/Exception.php";
                throw new Zend_Entity_StateTransformer_Exception(
                    "Missing property '".$propertyName."' on object '".get_class($object)."'"
                );
            }
            $requiredState[$propertyName] = $state[$propertyName];
            unset($state[$propertyName]);
        }
        return $requiredState;
    }

    /**
     * @throws Zend_Entity_StateTransformer_Exception
     * @param object $object
     * @param array $state
     * @return void
     */
    public function setState($object, $state)
    {
        if(!method_exists($object, 'setState')) {
            require_once "Zend/Entity/StateTransformer/Exception.php";
            throw new Zend_Entity_StateTransformer_Exception(
                "Array Transformer requires setState() method on entity '".get_class($object)."'."
            );
        }
        $object->setState($state);
    }

    /**
     * @throws Zend_Entity_StateTransformer_Exception
     * @param object $object
     * @param string $idPropertyName
     * @param string|int $id
     */
    public function setId($object, $idPropertyName, $id)
    {
        if(!method_exists($object, 'setState')) {
            require_once "Zend/Entity/StateTransformer/Exception.php";
            throw new Zend_Entity_StateTransformer_Exception(
                "Array Transformer requires setState() method on entity '".get_class($object)."'."
            );
        }
        $object->setState(array($idPropertyName => $id));
    }
}