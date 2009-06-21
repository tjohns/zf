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

    public function testManagerShouldDelegateBeginTransactionToAdapter()
    {
        $db = $this->getMock('Zend_Entity_DbAdapterMock');
        $db->expects($this->once())
           ->method('beginTransaction');
        $manager = $this->createEntityManager(null, null, null, $db);

        $manager->beginTransaction();
    }

    public function testManagerShouldDelegateCommitToAdapter()
    {
        $db = $this->getMock('Zend_Entity_DbAdapterMock');
        $db->expects($this->once())
           ->method('commit');
        $manager = $this->createEntityManager(null, null, null, $db);
        
        $manager->commit();
    }
    public function testManagerShouldDelegateRollbackToAdapter()
    {
        $db = $this->getMock('Zend_Entity_DbAdapterMock');
        $db->expects($this->once())
           ->method('rollBack');
        $manager = $this->createEntityManager(null, null, null, $db);
        
        $manager->rollBack();
    }

    public function testManagerContainsProxyToIdentityMap()
    {
        $identityMap = $this->getMock('Zend_Entity_Mapper_IdentityMap');
        $identityMap->expects($this->once())
                    ->method('contains')
                    ->will($this->returnValue(true));

        $entity = new Zend_TestEntity1;

        $manager = $this->createEntityManager(null, null, $identityMap);

        $this->assertTrue($manager->contains($entity));
    }

    public function testManagerClearCallsIdentityMapClear()
    {
        $identityMap = $this->createIdentityMapMock(self::IDENTITY_MOCK_CLEAR);
        $manager = $this->createEntityManager(null, null, $identityMap);
        
        $manager->clear();
    }

    public function testCloseConnectionDelegatesToAdapter()
    {
        $db = $this->createDatabaseConnectionMock();
        $db->expects($this->once())
           ->method('closeConnection')
           ->will($this->returnValue(true));
        $manager = new Zend_Entity_Manager($db);
        
        $manager->closeConnection();
    }
}