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
     * @param Zend_Entity_MetadataFactory_Interface $metadataFactory
     * @param Zend_Entity_Mapper_Loader_Interface $loader
     * @param Zend_Entity_Mapper_Persister_Interface $persister
     * @return Zend_Entity_Mapper
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

        $mapper = new Zend_Entity_TestMapper($db, $metadataFactory);
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
        $db = $this->getDatabaseConnection();
        $metadataFactory = $this->createResourceMapMock();
        return $this->getMock('Zend_Entity_Mapper_Mapper', array(), array($db, $metadataFactory));
    }

    /**
     * @return Zend_Entity_Mapper_Loader_Interface
     */
    public function createLoaderMock()
    {
        $loader = $this->getMock('Zend_Entity_Mapper_Loader_Interface');
        return $loader;
    }

    /**
     * @return Zend_Entity_Mapper_Loader_Interface
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
     * @return Zend_Entity_Mapper_Persister_Interface
     */
    public function createPersisterMock()
    {
        return $this->getMock('Zend_Entity_Mapper_Persister_Interface');
    }

    public function createResourceMapMock()
    {
        return $this->getMock('Zend_Entity_MetadataFactory_Interface');
    }

    public function createSampleEntityDefinition($sampleEntityName="Sample")
    {
        $entityDefinition = new Zend_Entity_Definition_Entity($sampleEntityName);
        $entityDefinition->setTable("sample");
        $entityDefinition->addPrimaryKey("id");
        $entityDefinition->addProperty("test");
        $entityDefinition->compile($this->createResourceMapMock());
        return $entityDefinition;
    }

    /**
     * @return Zend_Db_Adapter_Abstract
     */
    protected function getDatabaseConnection()
    {
        if($this->_db == null)  {
            $this->_db = new Zend_Entity_DbAdapterMock();
        }
        return $this->_db;
    }

    protected function createDatabaseConnectionMock()
    {
        return $this->getMock('Zend_Entity_DbAdapterMock');
    }

    /**
     *
     * @param  Zend_Entity_UnitOfWork $unitOfWork
     * @param  Zend_Entity_MetadataFactory_Interface $metadataFactory
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
        return new Zend_Entity_Manager($db, $options);
    }

    /**
     *
     * @param  Zend_Entity_UnitOfWork $unitOfWork
     * @param  Zend_Entity_MetadataFactory_Interface $metadataFactory
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
        return new Zend_Entity_TestManagerMock($db, $options);
    }

    private function generateEntityManagerOptions($unitOfWork=null, $metadataFactory=null, $identityMap=null)
    {
        $options = array(
            'unitOfWork' => $unitOfWork,
            'identityMap' => $identityMap,
        );
        if($metadataFactory === null) {
            $metadataFactory = $this->getMock('Zend_Entity_MetadataFactory_Interface');
        }
        $options['metadataFactory'] = $metadataFactory;
        return $options;
    }

    /**
     * @param Zend_Entity_Mapper_Loader_Interface $loader
     * @return Zend_Entity_Mapper_Select
     */
    protected function createDbSelect()
    {
        return new Zend_Entity_Mapper_Select($this->getDatabaseConnection());
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
        if($object instanceof Zend_Entity_LazyLoad_Entity) {
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