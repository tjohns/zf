<?php
/**
 * @package Zend_Entity
 * @subpackage Mapper
 */

class Zend_Entity_Mapper_StateTransformer_Property extends Zend_Entity_Mapper_StateTransformer_Abstract
{
    /**
     * @throws Zend_Entity_Mapper_StateTransformer_Exception
     * @param object
     * @return array
     */
    public function getState($object)
    {
        $state = array();
        foreach($this->_propertyNames AS $propertyName) {
            $accessorName = "get".ucfirst($propertyName);
            if(!method_exists($object, $accessorName)) {
                throw Zend_Entity_Mapper_StateTransformer_Exception(
                    "Getter '".$accessorName."()' is required to be present on entity '".get_class($object)."'"
                );
            }
            $state[$propertyName] = $object->$accessorName();
        }
        return $state;
    }

    /**
     * @throws Zend_Entity_Mapper_StateTransformer_Exception
     * @param object $object
     * @param array $state
     */
    public function setState($object, $state)
    {
        foreach($this->_propertyNames AS $propertyName) {
            $setterName = "set".ucfirst($propertyName);
            if(!method_exists($object, $setterName)) {
                throw Zend_Entity_Mapper_StateTransformer_Exception(
                    "Setter '".$setterName."()' is required to be present on entity '".get_class($object)."'"
                );
            }
            $object->$accessorName($state[$propertyName]);
        }
    }

    /**
     * @throws Zend_Entity_Mapper_StateTransformer_Exception
     * @param object $object
     * @param string $idPropertyName
     * @param string|int $id
     */
    public function setId($object, $idPropertyName, $id)
    {
        $setterName = "set".ucfirst($idPropertyName);
        if(!method_Exists($object, $setterName)) {
            throw Zend_Entity_Mapper_StateTransformer_Exception(
                "Identity Setter '".$setterName."()' is required to be present on entity '".get_class($object)."'"
            );
        }
        $object->$setterName($id);
    }
}
