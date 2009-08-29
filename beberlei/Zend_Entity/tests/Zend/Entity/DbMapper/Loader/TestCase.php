<?php

abstract class Zend_Entity_Mapper_Loader_TestCase extends Zend_Entity_TestCase
{
    /**
     * @var Zend_Entity_Manager
     */
    protected $entityManager = null;

    /**
     * @var Zend_Entity_Fixture_Abstract
     */
    protected $fixture = null;

    /**
     * @var Zend_Entity_IdentityMap
     */
    protected $identityMap = null;

    /**
     * @var Zend_Entity_Metadata_Interface
     */
    protected $resourceMap = null;

    /**
     * @var Zend_Test_DbAdapter
     */
    protected $adapter = null;

    /**
     * @var array
     */
    protected $mappings = array();

    protected $loader = null;

    final public function setUp()
    {
        $this->adapter = new Zend_Test_DbAdapter();
        $fixtureClassName = $this->getFixtureClassName();
        $this->fixture = new $fixtureClassName;
        $this->resourceMap = $this->fixture->getResourceMap();

        $this->identityMap = new Zend_Entity_IdentityMap();
        $this->entityManager = new Zend_Entity_Manager(array('identityMap' => $this->identityMap, 'adapter' => $this->adapter));
        $this->entityManager->setMetadataFactory($this->resourceMap);
    }

    abstract public function getFixtureClassName();

    abstract public function getLoaderClassName();

    /**
     *
     * @return Zend_Entity_Mapper_Loader_LoaderAbstract
     */
    public function createLoader()
    {
        if($this->loader == null) {
            $loaderClassName = $this->getLoaderClassName();
            $this->mappings = $this->fixture->getResourceMap()->transform('Zend_Entity_Mapper_Mapping');

            $this->loader = new $loaderClassName($this->entityManager, $this->mappings);
        }
        return $this->loader;
    }

    public function createEntityManager()
    {
        return $this->entityManager;
    }
}