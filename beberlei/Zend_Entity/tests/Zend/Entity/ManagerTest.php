<?php

require_once dirname(__FILE__)."/../TestHelper.php";
require_once "TestCase.php";

class Zend_Entity_ManagerTest extends Zend_Entity_TestCase
{
    public function testGetDefaultFlushMode_IsImmediate()
    {
        $manager = $this->createEntityManager();

        $this->assertEquals(Zend_Entity_Manager::FLUSHMODE_IMMEDIATE, $manager->getFlushMode());
    }

    public function testSetUnknownFlushMode_ThrowsException()
    {
        $manager = $this->createEntityManager();

        $this->setExpectedException("Zend_Entity_Exception");

        $manager->setFlushMode("foo");
    }

    public function testSetFlushMode()
    {
        $manager = $this->createEntityManager();
        $manager->setFlushMode(Zend_Entity_Manager::FLUSHMODE_IMMEDIATE);

        $this->assertEquals(Zend_Entity_Manager::FLUSHMODE_IMMEDIATE, $manager->getFlushMode());
    }

    public function testGetDefaultIdentityMap()
    {
        $manager = $this->createEntityManager();
        
        $this->assertTrue( $manager->getIdentityMap() instanceof Zend_Entity_IdentityMap );
    }

    public function testSetGetIdentityMap()
    {
        $manager = $this->createEntityManager();
        $map = new Zend_Entity_IdentityMap();

        $manager->setIdentityMap($map);
        $this->assertEquals($map, $manager->getIdentityMap());
    }

    public function testSettingNoResourceMapThrowsExceptionOnGet()
    {
        $this->setExpectedException("Zend_Entity_Exception");
        $manager = new Zend_Entity_Manager(array());
        
        $manager->getMetadataFactory();
    }

    public function testSetGetMetadataFactoryEqualsReference()
    {
        $manager = $this->createEntityManager();
        $metadataFactory = new Zend_Entity_MetadataFactory_Code("path");
        
        $manager->setMetadataFactory($metadataFactory);
        $this->assertEquals($metadataFactory, $manager->getMetadataFactory());
    }

    public function testSetMetadataFactoryThroughConstructorOptionsArray()
    {
        $metadataFactory = new Zend_Entity_MetadataFactory_Code("path");
        $manager = $this->createEntityManager(null, $metadataFactory, null);
        
        $this->assertEquals($metadataFactory, $manager->getMetadataFactory());
    }

    public function testGetEventListener_Default()
    {
        $manager = $this->createEntityManager();
        $this->assertType('Zend_Entity_Event_Listener', $manager->getEventListener());
    }

    public function testSetEventListener()
    {
        $manager = $this->createEntityManager();
        $listener = $this->getMock('Zend_Entity_Event_EventAbstract');
        $manager->setEventListener($listener);
        $this->assertSame($listener, $manager->getEventListener());
    }

    public function testSetEventListener_ThroughConstructorOptions()
    {
        $listener = $this->getMock('Zend_Entity_Event_EventAbstract');
        $options = array('eventListener' => $listener, 'adapter' => $this->getDatabaseConnection());
        $manager = new Zend_Entity_Manager($options);

        $manager->setEventListener($listener);
        $this->assertSame($listener, $manager->getEventListener());
    }

    public function testGetTransaction()
    {
        $db = $this->getMock('Zend_Entity_DbAdapterMock');
        $manager = $this->createEntityManager(null, null, null, $db);
        $manager->getMapper(); // initialize transaction

        $transaction = $manager->beginTransaction();
        $transaction2 = $manager->getTransaction();

        $this->assertSame($transaction, $transaction2);
    }

    public function testManagerShouldDelegateBeginTransactionToAdapter()
    {
        $db = $this->getMock('Zend_Entity_DbAdapterMock');
        $db->expects($this->once())
           ->method('beginTransaction');
        $manager = $this->createEntityManager(null, null, null, $db);
        $manager->getMapper(); // initialize transaction

        $transaction = $manager->beginTransaction();
        $transaction2 = $manager->getTransaction();

        $this->assertSame($transaction, $transaction2);
    }

    public function testContains_ProxyToIdentityMap()
    {
        $identityMap = $this->getMock('Zend_Entity_IdentityMap');
        $identityMap->expects($this->once())
                    ->method('contains')
                    ->will($this->returnValue(true));

        $entity = new Zend_TestEntity1;

        $manager = $this->createEntityManager(null, null, $identityMap);

        $this->assertTrue($manager->contains($entity));
    }

    public function testDetach_ProxyToIdentityMap()
    {
        $entity = new Zend_TestEntity1;

        $identityMap = $this->getMock('Zend_Entity_IdentityMap');
        $identityMap->expects($this->once())
                    ->method('remove')
                    ->with($this->equalTo('Zend_TestEntity1'), $this->equalTo($entity));

        $manager = $this->createEntityManager(null, null, $identityMap);

        $manager->detach($entity);
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
        $manager = $this->createEntityManager(null, null, null, $db);
        $manager->getMapper();
        
        $manager->close();
    }

    public function testSaveIsDelegatedToMapper()
    {
        $entity = new Zend_TestEntity1();
        $className = get_class($entity);

        $mapperMock = $this->createMapperMock();
        $mapperMock->expects($this->once())
                   ->method('save')
                   ->with($this->equalTo($entity));

        $manager = $this->createTestingEntityManager();
        $manager->addMapper($className, $mapperMock);

        $manager->save($entity);
    }

    public function testDelete_KnownEntity_IsDelegatedToMapper()
    {
        $this->doDeleteOfEntity_IsContained(true);
    }

    public function testDelete_UnknownEntity_ThrowsIllegalStateException()
    {
        $this->setExpectedException("Zend_Entity_IllegalStateException");

        $this->doDeleteOfEntity_IsContained(false);
    }

    public function doDeleteOfEntity_IsContained($contained)
    {
        $entity = new Zend_TestEntity1();
        $className = get_class($entity);

        $identityMap = $this->createIdentityMapMock();
        $identityMap->expects($this->once())
                    ->method('contains')
                    ->with($this->equalTo($entity))
                    ->will($this->returnValue($contained));

        $mapperMock = $this->createMapperMock();
        $mapperMock->expects( ($contained==true)?$this->once():$this->never() )
                   ->method('delete');

        $manager = $this->createTestingEntityManager(null, null, $identityMap);
        $manager->addMapper($className, $mapperMock);

        $manager->delete($entity);
    }

    public function testGetReference_FetchesLoadedObjects_FromIdentityMap()
    {
        $fixtureClass = "foo";
        $fixtureId = "1";
        $fixtureInstance = new stdClass();

        $identityMapMock = $this->createIdentityMapMock();
        $identityMapMock->expects($this->at(0))
                        ->method('hasObject')
                        ->with($fixtureClass, $fixtureId)
                        ->will($this->returnValue(true));
        $identityMapMock->expects($this->at(1))
                        ->method('getObject')
                        ->with($fixtureClass, $fixtureId)
                        ->will($this->returnValue($fixtureInstance));

        $manager = $this->createTestingEntityManager(null, null, $identityMapMock);

        $actual = $manager->getReference($fixtureClass, $fixtureId);

        $this->assertSame($fixtureInstance, $actual);
    }

    public function testGetReference_FetchesLazyLoadObject_FromIdentityMap()
    {
        $fixtureClass = "foo";
        $fixtureId = "1";
        $fixtureInstance = new stdClass();

        $identityMapMock = $this->createIdentityMapMock();
        $identityMapMock->expects($this->at(0))
                        ->method('hasObject')
                        ->with($fixtureClass, $fixtureId)
                        ->will($this->returnValue(false));
        $identityMapMock->expects($this->at(1))
                        ->method('hasLazyObject')
                        ->with($fixtureClass, $fixtureId)
                        ->will($this->returnValue(true));
        $identityMapMock->expects($this->at(2))
                        ->method('getObject')
                        ->with($fixtureClass, $fixtureId)
                        ->will($this->returnValue($fixtureInstance));

        $manager = $this->createTestingEntityManager(null, null, $identityMapMock);

        $actual = $manager->getReference($fixtureClass, $fixtureId);

        $this->assertSame($fixtureInstance, $actual);
    }

    public function testGetReference_CreatesLazyLoad_IfObjectNotLoadedBefore()
    {
        $fixtureClass = "foo";
        $fixtureId = "1";

        $identityMapMock = $this->createIdentityMapMock();
        $identityMapMock->expects($this->at(0))
                        ->method('hasObject')
                        ->with($fixtureClass, $fixtureId)
                        ->will($this->returnValue(false));

        $manager = $this->createTestingEntityManager(null, null, $identityMapMock);

        $actual = $manager->getReference($fixtureClass, $fixtureId);

        $this->assertType('Zend_Entity_LazyLoad_Entity', $actual);
        $this->assertEquals($fixtureClass, $actual->__ze_getClassName());
    }

    public function testGetReference_CreatedLazyLoad_IsAddedToIdentityMap()
    {
        $fixtureClass = "foo";
        $fixtureId = "1";

        $identityMapMock = $this->createIdentityMapMock();
        $identityMapMock->expects($this->at(0))
                        ->method('hasObject')
                        ->with($fixtureClass, $fixtureId)
                        ->will($this->returnValue(false));
        $identityMapMock->expects($this->at(1))
                        ->method('addObject');

        $manager = $this->createTestingEntityManager(null, null, $identityMapMock);

        $manager->getReference($fixtureClass, $fixtureId);
    }

    const UNKNOWN_ENTITY_CLASS = 'UnknownEntityClass';
    const KNOWN_ENTITY_CLASS = 'KnownEntityClass';

    public function testCreateNativeQuery()
    {
        $fixtureSql = "select foo";
        $rsm = new Zend_Entity_Query_ResultSetMapping();

        $manager = $this->createTestingEntityManager();
        $mapper = $this->createMapperMock();
        $mapper->expects($this->once())
               ->method('createNativeQuery')
               ->with($this->equalTo($fixtureSql), $this->equalTo($rsm), $this->equalTo($manager));
        $manager->addMapper(self::KNOWN_ENTITY_CLASS, $mapper);

        $query = $manager->createNativeQuery($fixtureSql, $rsm);
    }

    public function testCreateNamedQuery_UsesNamedQueryName_ForLoadingPlugin()
    {
        $loaderMock = $this->getMock('Zend_Loader_PluginLoader');
        $loaderMock->expects($this->any())
                   ->method('getClassName')
                   ->with($this->equalTo('ANamedQuery'))
                   ->will($this->returnValue('Zend_Entity_TestNamedQuery'));

        $em = $this->createEntityManager();
        $em->setNamedQueryLoader($loaderMock);

        $em->createNamedQuery("ANamedQuery");
    }

    public function testCreateNamedQuery_ThrowsException_IfNameDoesContainNonCharOrNumbers()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $loaderMock = $this->getMock('Zend_Loader_PluginLoader');

        $em = $this->createEntityManager();
        $em->setNamedQueryLoader($loaderMock);

        $em->createNamedQuery("$!&$");
    }

    public function testCreateNamedQuery_ThrowsException_IfNoNamedQueryLoader_IsInitialized()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $em = $this->createEntityManager();

        $em->createNamedQuery("ANamedQuery");
    }

    public function testCreateNamedQuery_ThrowsException_IfLoaderClassIsInvalid()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $loaderMock = $this->getMock('Zend_Loader_PluginLoader');
        $loaderMock->expects($this->any())
                   ->method('getClassName')
                   ->with($this->equalTo('ANamedQuery'))
                   ->will($this->returnValue('InvalidClassName'));

        $em = $this->createEntityManager();
        $em->setNamedQueryLoader($loaderMock);

        $em->createNamedQuery("ANamedQuery");
    }

    public function testCreateNamedQuery_PluginIsStateless_OnlyInitializedOnce()
    {
        $loaderMock = $this->getMock('Zend_Loader_PluginLoader');
        $loaderMock->expects($this->once())
                   ->method('getClassName')
                   ->with($this->equalTo('ANamedQuery'))
                   ->will($this->returnValue('Zend_Entity_TestNamedQuery'));

        $em = $this->createEntityManager();
        $em->setNamedQueryLoader($loaderMock);

        $q1 = $em->createNamedQuery("ANamedQuery");
        $q2 = $em->createNamedQuery("ANamedQuery");

        $this->assertNotSame($q1, $q2);
    }

    public function testCreateNamedQuery_NotANamedQueryAbstractImplementation_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $loaderMock = $this->getMock('Zend_Loader_PluginLoader');
        $loaderMock->expects($this->once())
                   ->method('getClassName')
                   ->with($this->equalTo('ANamedQuery'))
                   ->will($this->returnValue('stdClass'));

        $em = $this->createEntityManager();
        $em->setNamedQueryLoader($loaderMock);

        $em->createNamedQuery("ANamedQuery");
    }

    public function testSaveNonObject_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_InvalidEntityException");

        $em = $this->createEntityManager();
        $em->save("string");
    }

    public function testDeleteNonObject_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_InvalidEntityException");

        $em = $this->createEntityManager();
        $em->delete("string");
    }
}

class Zend_Entity_TestNamedQuery extends Zend_Entity_Query_NamedQueryAbstract
{    
    public function create()
    {
        return new stdClass();
    }
}