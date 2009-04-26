<?php

class Zend_Entity_Resource_Testing implements Zend_Entity_Resource_Interface
{
    protected $_defMap = array();

    public function addDefinition(Zend_Entity_Mapper_Definition_Entity $entityDefinition)
    {
        $this->_defMap[$entityDefinition->getClass()] = $entityDefinition;
        $entityDefinition->compile($this);
    }

    /**
     * Get an Entity Mapper Definition by the name of the Entity
     *
     * @param  string $entityName
     * @return Zend_Entity_Mapper_Definition_Entity
     */
    public function getDefinitionByEntityName($entityName)
    {
        if(!isset($this->_defMap[$entityName])) {
            throw new Exception("No Definition for the Entity '".$entityName."' was set.");
        }

        return $this->_defMap[$entityName];
    }
}