<?php

abstract class Zend_Entity_Fixture_Abstract
{
    protected $resourceMap = null;

    protected $definitionCreationMethods = array();

    public $identityMap = null;

    public $testAdapter = null;

    public function __construct()
    {
        $this->setUp();
    }

    public function setUp()
    {
        $definitions = array();
        $this->resourceMap = new Zend_Entity_MetadataFactory_Testing();
        $this->resourceMap->setDefaultIdGeneratorClass("Zend_Db_Mapper_Id_AutoIncrement");
        foreach($this->definitionCreationMethods AS $method) {
            $definition = call_user_func_array(array($this, $method), array());
            $this->resourceMap->addDefinition($definition);
            $definitions[] = $definition;
        }
    }

    public function createTestEntityManager()
    {
        $this->identityMap = new Zend_Entity_IdentityMap();
        $this->testAdapter = new Zend_Test_DbAdapter();

        $mapper = new Zend_Db_Mapper_Mapper(array('adapter' => $this->testAdapter, 'metadataFactory' => $this->resourceMap));

        $em = new Zend_Entity_Manager(array('identityMap' => $this->identityMap));
        $em->setMetadataFactory($this->resourceMap);
        $em->setMapper($mapper);
        return $em;
    }

    /**
     * @return Zend_Entity_MetadataFactory_Testing
     */
    public function getResourceMap()
    {
        return $this->resourceMap;
    }

    /**
     * @param  string $entityName
     * @return Zend_Entity_Definition_Entity
     */
    public function getEntityDefinition($entityName)
    {
        return $this->getResourceMap()->getDefinitionByEntityName($entityName);
    }

    /**
     * @param string $entityName
     * @param string $propertyName
     * @return Zend_Entity_Definition_Property
     */
    public function getEntityPropertyDef($entityName, $propertyName)
    {
        return $this->getEntityDefinition($entityName)->getPropertyByName($propertyName);
    }
}