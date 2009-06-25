<?php

abstract class Zend_Entity_Fixture_Abstract
{
    protected $resourceMap = null;

    protected $definitionCreationMethods = array();

    public function __construct()
    {
        $this->setUp();
    }

    public function setUp()
    {
        $definitions = array();
        $this->resourceMap = new Zend_Entity_Resource_Testing();
        foreach($this->definitionCreationMethods AS $method) {
            $definition = call_user_func_array(array($this, $method), array());
            $this->resourceMap->addDefinition($definition);
            $definitions[] = $definition;
        }
        foreach($definitions AS $def) {
            $def->compile($this->resourceMap);
        }
    }

    /**
     * @return Zend_Entity_Resource_Testing
     */
    public function getResourceMap()
    {
        return $this->resourceMap;
    }

    /**
     * @param  string $entityName
     * @return Zend_Entity_Mapper_Definition_Entity
     */
    public function getEntityDefinition($entityName)
    {
        return $this->getResourceMap()->getDefinitionByEntityName($entityName);
    }

    /**
     * @param string $entityName
     * @param string $propertyName
     * @return Zend_Entity_Mapper_Definition_Property_Abstract
     */
    public function getEntityPropertyDef($entityName, $propertyName)
    {
        return $this->getEntityDefinition($entityName)->getPropertyByName($propertyName);
    }
}