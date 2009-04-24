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
     * @param Zend_Entity_Mapper_Definition_Entity $entityDefinition
     * @param Zend_Entity_Resource_Interface $resourceMap
     * @param Zend_Entity_Mapper_Loader_Interface $loader
     * @param Zend_Entity_Mapper_Persister_Interface $persister
     * @return Zend_Entity_Mapper
     */
    public function createMapper($db=null, $entityDefinition=null, $resourceMap=null, $loader=null, $persister=null)
    {
        if($db == null) {
            $db = $this->getDatabaseConnection();
        }
        if($entityDefinition == null) {
            $entityDefinition = $this->createSampleEntityDefinition();
        }
        if($resourceMap == null) {
            $resourceMap = $this->createResourceMapMock();
        }
        $mapper = new Zend_Entity_TestMapper($db, $entityDefinition, $resourceMap);
        if($loader !== null) {
            $mapper->setLoader($loader);
        }
        if($persister !== null) {
            $mapper->setPersister($persister);
        }

        return $mapper;
    }

    /**
     * @return Zend_Entity_Mapper_Loader_Interface
     */
    public function createLoaderMock()
    {
        $loader = $this->getMock('Zend_Entity_Mapper_Loader_Interface');
        return $loader;
    }

    public function createResourceMapMock()
    {
        return $this->getMock('Zend_Entity_Resource_Interface');
    }

    public function createSampleEntityDefinition()
    {
        $entityDefinition = new Zend_Entity_Mapper_Definition_Entity("Sample");
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
     * @param  Zend_Entity_Resource_Interface $resourceMap
     * @param  Zend_Entity_IdentityMap $identityMap
     * @return Zend_Entity_Manager_Interface
     */
    protected function createEntityManager($unitOfWork=null, $resourceMap=null, $identityMap=null)
    {
        $options = array(
            'unitOfWork' => $unitOfWork,
            'identityMap' => $identityMap,
        );
        if($resourceMap !== null) {
            $options['resource'] = $resourceMap;
        }

        return new Zend_Entity_Manager($this->getDatabaseConnection(), $options);
    }

    const UOW_MOCK_BEGINTRANSACTION = 1;
    const UOW_MOCK_COMMIT = 2;
    const UOW_MOCK_ISMANAGING_TRUE = 4;
    const UOW_MOCK_ISMANAGING_FALSE = 8;
    const UOW_MOCK_CLEAR = 16;
    const UOW_MOCK_SETREADONLY = 32;
    const UOW_MOCK_ROLLBACK = 64;

    /**
     * @param int $mockMask
     * @return Zend_Entity_Mapper_UnitOfWork
     */
    protected function createUnitOfWorkMock($mockMask = 0)
    {
        $unitOfWork = $this->getMock('Zend_Entity_Mapper_UnitOfWork');
        if( ($mockMask&self::UOW_MOCK_BEGINTRANSACTION) > 0) {
            $unitOfWork->expects($this->once())
                       ->method('beginTransaction')
                       ->will($this->returnValue(true));
        }
        if( ($mockMask&self::UOW_MOCK_COMMIT) > 0) {
            $unitOfWork->expects($this->once())
                       ->method('commit')
                       ->will($this->returnValue(true));
        }
        if( ($mockMask&self::UOW_MOCK_ISMANAGING_TRUE) > 0) {
                $unitOfWork->expects($this->once())
                           ->method('isManagingCurrentTransaction')
                           ->will($this->returnValue(true));
        } else if( ($mockMask&self::UOW_MOCK_ISMANAGING_FALSE) > 0) {
                $unitOfWork->expects($this->once())
                           ->method('isManagingCurrentTransaction')
                           ->will($this->returnValue(false));
        }
        if( ($mockMask&self::UOW_MOCK_CLEAR) > 0) {
            $unitOfWork->expects($this->once())
                       ->method('clear')
                       ->will($this->returnValue(true));
        }
        if( ($mockMask&self::UOW_MOCK_SETREADONLY) > 0) {
            $unitOfWork->expects($this->any())
                       ->method('setReadOnly')
                       ->will($this->returnValue(true));
        }
        if( ($mockMask&self::UOW_MOCK_ROLLBACK) > 0) {
            $unitOfWork->expects($this->once())
                       ->method('rollBack')
                       ->will($this->returnValue(true));
        }
        return $unitOfWork;
    }

    const IDENTITY_MOCK_CLEAR = 1;
    const IDENTITY_MOCK_SETREADONLY_NEVER = 2;
    const IDENTITY_MOCK_SETREADONLY_ANY = 4;

    /**
     * @param int $mask
     * @return Zend_Entity_Mapper_IdentityMap
     */
    protected function createIdentityMapMock($mask)
    {
        $identityMap = $this->getMock('Zend_Entity_Mapper_IdentityMap');
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
}