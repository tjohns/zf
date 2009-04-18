<?php

require_once dirname(__FILE__)."/../TestHelper.php";
require_once "TestCase.php";

class Zend_Entity_ManagerTest extends Zend_Entity_TestCase
{
    public function testConstructingManagerRequiresDatabaseConnection()
    {
        $manager = $this->createEntityManager();

        $this->assertEquals($this->getDatabaseConnection(), $manager->getAdapter());
    }

    public function testGetDefaultUnitOfWork()
    {
        $manager = $this->createEntityManager();

        $this->assertTrue( $manager->getUnitOfWork() instanceof Zend_Entity_Mapper_UnitOfWork );
    }

    public function testSetGetUnitOfWork()
    {
        $manager = $this->createEntityManager();
        $uow = new Zend_Entity_Mapper_UnitOfWork();

        $manager->setUnitOfWork($uow);
        $this->assertEquals($uow, $manager->getUnitOfWork());
    }

    public function testGetDefaultIdentityMap()
    {
        $manager = $this->createEntityManager();
        
        $this->assertTrue( $manager->getIdentityMap() instanceof Zend_Entity_Mapper_IdentityMap );
    }

    public function testSetGetIdentityMap()
    {
        $manager = $this->createEntityManager();
        $map = new Zend_Entity_Mapper_IdentityMap();

        $manager->setIdentityMap($map);
        $this->assertEquals($map, $manager->getIdentityMap());
    }

    public function testSettingNoResourceMapThrowsExceptionOnGet()
    {
        $this->setExpectedException("Zend_Entity_Exception");
        $manager = $this->createEntityManager();
        
        $manager->getResource();
    }

    public function testSetGetResourceMapEqualsReference()
    {
        $manager = $this->createEntityManager();
        $resourceMap = new Zend_Entity_Resource_Code("path");
        
        $manager->setResource($resourceMap);
        $this->assertEquals($resourceMap, $manager->getResource());
    }

    public function testSetResourceThroughConstructorOptionsArray()
    {
        $resourceMap = new Zend_Entity_Resource_Code("path");
        $manager = $this->createEntityManager(null, $resourceMap, null);
        
        $this->assertEquals($resourceMap, $manager->getResource());
    }

    public function testGetMapperByEntityStringNameReturnsMapperOnValidCall()
    {
        $entityName = "Zend_TestEntity1";
        $definitionMock = $this->getMock('Zend_Entity_Mapper_Definition_Entity');
        $resourceMap = $this->getMock('Zend_Entity_Resource_Interface');
        $resourceMap->expects($this->once())
                    ->method('getDefinitionByEntityName')
                    ->with($this->equalTo($entityName))
                    ->will($this->returnValue($definitionMock));
        $manager = $this->createEntityManager(null, $resourceMap);
        $mapper  = $manager->getMapperByEntity($entityName);
        
        $this->assertTrue($mapper instanceof Zend_Entity_Mapper);
    }

    public function testGetMapperByEntityClassNameReturnsMapperOnValidCall()
    {
        $entity = new Zend_TestEntity1();
        $entityName = "Zend_TestEntity1";
        $definitionMock = $this->getMock('Zend_Entity_Mapper_Definition_Entity');
        $resourceMap = $this->getMock('Zend_Entity_Resource_Interface');
        $resourceMap->expects($this->once())
                    ->method('getDefinitionByEntityName')
                    ->with($this->equalTo($entityName))
                    ->will($this->returnValue($definitionMock));
        $manager = $this->createEntityManager(null, $resourceMap);
        $mapper  = $manager->getMapperByEntity($entity);
        
        $this->assertTrue($mapper instanceof Zend_Entity_Mapper);
    }

    public function testManagerShouldDelegateBeginTransactionToUnitOfWork()
    {
        $unitOfWork = $this->createUnitOfWorkMock(self::UOW_MOCK_BEGINTRANSACTION);
        $manager = $this->createEntityManager($unitOfWork);

        $manager->beginTransaction();
    }

    public function testManagerShouldDelegateCommitToUnitOfWork()
    {
        $unitOfWork = $this->createUnitOfWorkMock(self::UOW_MOCK_COMMIT);
        $manager = $this->createEntityManager($unitOfWork);
        
        $manager->commit();
    }
    public function testManagerShouldDelegateRollbackToUnitOfWork()
    {
        $unitOfWork = $this->createUnitOfWorkMock(self::UOW_MOCK_ROLLBACK);
        $manager = $this->createEntityManager($unitOfWork);
        
        $manager->rollBack();
    }

    public function testManagerFlushThrowsExceptionIfUnitOfWorkDoesNotManageTransaction()
    {
        $this->setExpectedException("Zend_Entity_Exception");
        $manager = $this->createEntityManager();
        
        $manager->flush();
    }

    public function testManagerFlushCommitsAndRestartsTransaction()
    {
        $unitOfWork = $this->createUnitOfWorkMock(self::UOW_MOCK_BEGINTRANSACTION|self::UOW_MOCK_COMMIT|self::UOW_MOCK_ISMANAGING_TRUE);
        $manager = $this->createEntityManager($unitOfWork);
        
        $manager->flush();
    }

    public function testManagerClearCallsUnitOfWorkClear()
    {
        $unitOfWork = $this->createUnitOfWorkMock(self::UOW_MOCK_CLEAR);
        $identityMap = $this->getMock('Zend_Entity_Mapper_IdentityMap');
        $identityMap->expects($this->any())
                    ->method('clear')
                    ->will($this->returnValue(true));
        $manager = $this->createEntityManager($unitOfWork, null, $identityMap);

        $manager->clear();
    }

    public function testManagerClearCallsIdentityMapClear()
    {
        $unitOfWork = $this->createUnitOfWorkMock(self::UOW_MOCK_CLEAR);
        $identityMap = $this->createIdentityMapMock(self::IDENTITY_MOCK_CLEAR);
        $manager = $this->createEntityManager($unitOfWork, null, $identityMap);
        
        $manager->clear();
    }

    public function testManagerDelegatesSetReadOnlyToUnitOfWork()
    {
        $unitOfWork = $this->createUnitOfWorkMock(self::UOW_MOCK_SETREADONLY);
        $identityMap = $this->createIdentityMapMock(self::IDENTITY_MOCK_SETREADONLY_ANY);
        $manager = $this->createEntityManager($unitOfWork, null, $identityMap);
        
        $manager->setReadOnly();
    }

    public function testManagerDelegatesSetReadOnlyNotToIdentityMap()
    {
        $unitOfWork = $this->createUnitOfWorkMock(self::UOW_MOCK_SETREADONLY);
        $identityMap = $this->createIdentityMapMock(self::IDENTITY_MOCK_SETREADONLY_NEVER);
        $manager = $this->createEntityManager($unitOfWork, null, $identityMap);
        
        $manager->setReadOnly();
    }

    public function testCloseConnectionDelegatesToAdapter()
    {
        $db = $this->getDatabaseConnection();
        $db->expects($this->once())
           ->method('closeConnection')
           ->will($this->returnValue(true));
        $manager = new Zend_Entity_Manager($db);
        
        $manager->closeConnection();
    }

    public function testCloseConnectionChecksUnitOfworkManagingTransaction()
    {
        $unitOfWork = $this->createUnitOfWorkMock(self::UOW_MOCK_ISMANAGING_FALSE);
        $manager = $this->createEntityManager($unitOfWork);
        
        $manager->closeConnection();
    }

    public function testCloseConnectionCommitsUnitOfWorkIfManagingTransaction()
    {
        $unitOfWork = $this->createUnitOfWorkMock(self::UOW_MOCK_ISMANAGING_TRUE|self::UOW_MOCK_COMMIT);
        $manager = $this->createEntityManager($unitOfWork);
        
        $manager->closeConnection();
    }
}