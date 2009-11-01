<?php

require_once "TestMapper.php";

abstract class Zend_Entity_TestCase extends PHPUnit_Framework_TestCase
{
    private $_db;

    public function setUp()
    {
        $this->_db = null;
    }

    /**
     *
     * @param Zend_Db_Adapter_Abstract $db
     * @param Zend_Entity_Definition_Entity $entityDefinition
     * @param Zend_Entity_MetadataFactory_FactoryAbstract $metadataFactory
     * @param Zend_Db_Mapper_Loader_LoaderAbstract $loader
     * @param Zend_Db_Mapper_Persister_Interface $persister
     * @return Zend_Entity_MapperAbstract
     */
    public function createMapper($db=null, $entityDefinition=null, $metadataFactory=null, $loader=null, $persister=null)
    {
        if($db == null) {
            $db = $this->getDatabaseConnection();
        }
        if($entityDefinition == null) {
            $entityDefinition = $this->createSampleEntityDefinition();
        }
        if($metadataFactory == null) {
            $metadataFactory = new Zend_Entity_MetadataFactory_Testing();
        }
        if($metadataFactory instanceof Zend_Entity_MetadataFactory_Testing) {
            $metadataFactory->addDefinition($entityDefinition);
        }

        $mapper = new Zend_Entity_TestMapper(array('adapter' => $db));
        $mapper->initializeMappings($metadataFactory);
        if($loader !== null) {
            $mapper->setLoader($loader);
        }
        if($persister !== null && $entityDefinition !== null) {
            $mapper->setPersister($entityDefinition->getClass(), $persister);
        }

        return $mapper;
    }

    public function createMapperMock()
    {
        return $this->getMock('Zend_Db_Mapper_Mapper', array(), array(), '', false);
    }

    /**
     * @return Zend_Db_Mapper_Loader_LoaderAbstract
     */
    public function createLoaderMock()
    {
        $loader = $this->getMock('Zend_Db_Mapper_Loader_LoaderAbstract', array(), array(), '', false);
        return $loader;
    }

    /**
     * @return Zend_Db_Mapper_Loader_LoaderAbstract
     */
    public function createLoaderMockThatReturnsProccessedResultset($result)
    {
        $loader = $this->createLoaderMock();
        $loader->expects($this->once())
               ->method('processResultset')
               ->will($this->returnValue($result));
        return $loader;
    }

    /**
     * @return Zend_Db_Mapper_Persister_Interface
     */
    public function createPersisterMock()
    {
        return $this->getMock('Zend_Db_Mapper_Persister_Interface');
    }

    public function createResourceMapMock()
    {
        return $this->getMock('Zend_Entity_MetadataFactory_FactoryAbstract');
    }

    public function createSampleEntityDefinition($sampleEntityName="Sample")
    {
        $entityDefinition = new Zend_Entity_Definition_Entity($sampleEntityName);
        $entityDefinition->setTable("sample");
        $entityDefinition->addPrimaryKey("id");
        $entityDefinition->addProperty("test");
        return $entityDefinition;
    }

    /**
     * @return Zend_Db_Adapter_Abstract
     */
    protected function getDatabaseConnection()
    {
        if($this->_db == null)  {
            $this->_db = new Zend_Test_DbAdapter();
        }
        return $this->_db;
    }

    protected function createDatabaseConnectionMock()
    {
        return $this->getMock('Zend_Test_DbAdapter');
    }

    /**
     *
     * @param  Zend_Entity_UnitOfWork $unitOfWork
     * @param  Zend_Entity_MetadataFactory_FactoryAbstract $metadataFactory
     * @param  Zend_Entity_IdentityMap $identityMap
     * @param  Zend_Db_Adapter_Abstract $db
     * @return Zend_Entity_Manager_Interface
     */
    protected function createEntityManager($unitOfWork=null, $metadataFactory=null, $identityMap=null, $db=null)
    {
        if($db == null) {
            $db = $this->getDatabaseConnection();
        }
        
        $options = $this->generateEntityManagerOptions($unitOfWork, $metadataFactory, $identityMap);
        $options['adapter'] = $db;
        return new Zend_Entity_Manager($options);
    }

    /**
     *
     * @param  Zend_Entity_UnitOfWork $unitOfWork
     * @param  Zend_Entity_MetadataFactory_FactoryAbstract $metadataFactory
     * @param  Zend_Entity_IdentityMap $identityMap
     * @param  Zend_Db_Adapter_Abstract $db
     * @return Zend_Entity_TestManagerMock
     */
    protected function createTestingEntityManager($unitOfWork=null, $metadataFactory=null, $identityMap=null, $db=null)
    {
        if($db==null) {
            $db = $this->getDatabaseConnection();
        }
        
        $options = $this->generateEntityManagerOptions($unitOfWork, $metadataFactory, $identityMap);
        $options['adapter'] = $db;
        
        return new Zend_Entity_TestManagerMock($options);
    }

    private function generateEntityManagerOptions($unitOfWork=null, $metadataFactory=null, $identityMap=null)
    {
        $options = array(
            'unitOfWork' => $unitOfWork,
            'identityMap' => $identityMap,
        );
        if($metadataFactory === null) {
            $metadataFactory = $this->getMock('Zend_Entity_MetadataFactory_FactoryAbstract');
            $metadataFactory->expects($this->any())
                            ->method('transform')
                            ->will($this->returnValue(array()));
        }
        $options['metadataFactory'] = $metadataFactory;
        return $options;
    }

    /**
     * @param Zend_Db_Mapper_Loader_LoaderAbstract $loader
     * @return Zend_Db_Mapper_Select
     */
    protected function createDbSelect()
    {
        return new Zend_Db_Mapper_Select($this->getDatabaseConnection());
    }

    const UOW_MOCK_BEGINTRANSACTION = 1;
    const UOW_MOCK_COMMIT = 2;
    const UOW_MOCK_ISMANAGING_TRUE = 4;
    const UOW_MOCK_ISMANAGING_FALSE = 8;
    const UOW_MOCK_CLEAR = 16;
    const UOW_MOCK_SETREADONLY = 32;
    const UOW_MOCK_ROLLBACK = 64;

    const IDENTITY_MOCK_CLEAR = 1;
    const IDENTITY_MOCK_SETREADONLY_NEVER = 2;
    const IDENTITY_MOCK_SETREADONLY_ANY = 4;

    /**
     * @param int $mask
     * @return Zend_Entity_IdentityMap
     */
    protected function createIdentityMapMock($mask=0)
    {
        $identityMap = $this->getMock('Zend_Entity_IdentityMap');
        if( ($mask&self::IDENTITY_MOCK_CLEAR) > 0) {
            $identityMap->expects($this->any())
                        ->method('clear')
                        ->will($this->returnValue(true));
        }
        if( ($mask&self::IDENTITY_MOCK_SETREADONLY_NEVER) > 0) {
            $identityMap->expects($this->never())
                        ->method('setReadOnly')
                        ->will($this->returnValue(true));
        }
        if( ($mask&self::IDENTITY_MOCK_SETREADONLY_ANY) > 0) {
            $identityMap->expects($this->any())
                        ->method('setReadOnly')
                        ->will($this->returnValue(true));
        }
        return $identityMap;
    }

    public function assertLazyLoad($object)
    {
        $lazyLoad = false;
        if($object instanceof Zend_Entity_LazyLoad_Proxy) {
            $lazyLoad = true;
        }
        if($object instanceof Zend_Entity_LazyLoad_Collection) {
            $lazyLoad = true;
        }
        if($object instanceof Zend_Entity_LazyLoad_Field) {
            $lazyLoad = true;
        }
        $this->assertTrue($lazyLoad, "Object of type <".get_class($object)."> is not a lazy load object.");
    }
}