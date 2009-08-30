<?php

class Zend_Entity_DbMapper_MapperTest extends Zend_Entity_TestCase
{
    public function testCreateFactory_WithoutDbKey_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");
        
        $mapper = Zend_Db_Mapper_Mapper::create(array());
    }

    public function testCreateFactory_WithoutMetadtaFactoryKey_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $options = array('db' => new Zend_Test_DbAdapter());

        $mapper = Zend_Db_Mapper_Mapper::create($options);
    }

    public function testLoadEntity_InvalidEntity_ThrowsException()
    {
        $metadataFactory = new Zend_Entity_MetadataFactory_Testing();
        $options = array('db' => new Zend_Test_DbAdapter(), 'metadataFactory' => $metadataFactory);

        $mapper = Zend_Db_Mapper_Mapper::create($options);
        $emMock = $this->getMock('Zend_Entity_Manager_Interface');

        $this->setExpectedException('Zend_Entity_InvalidEntityException');

        $mapper->load($emMock, 'InvalidEntity', 1);
    }

    public function testLoadEntity()
    {
        $this->markTestSkipped('doesnt work anymore :(');

        $fixtureId = 1;
        $fixtureEntity = "foo";

        $entityDefinition = new Zend_Entity_Definition_Entity($fixtureEntity);
        $entityDefinition->setTable("bar");
        $entityDefinition->addPrimaryKey("id", array("columnName" => "col_id"));

        $metadataFactory = new Zend_Entity_MetadataFactory_Testing();
        $metadataFactory->addDefinition($entityDefinition);

        $dbMock = $this->getMock('Zend_Test_DbAdapter');
        $dbMock->expects($this->once())
               ->method('quoteIdentifier')
               ->with($this->equalTo('bar.col_id'))
               ->will($this->returnValue('bar.col_id'));
               
        $mapper = $this->createMapper($dbMock, null, $metadataFactory);

        $queryMock = $this->getMock('Zend_Db_Mapper_SqlQueryBuilder', array('select', 'where', 'getSingleResult'), array(), '', false);
        $queryMock->expects($this->at(0))
                  ->method('where')
                  ->with($this->equalTo('bar.col_id = ?'), $this->equalTo($fixtureId));
        $queryMock->expects($this->at(1))
                  ->method('getSingleResult');

        $emMock = $this->getMock('Zend_Entity_Manager', array('createNativeQueryBuilder', 'getMapper'));
        $emMock->expects($this->once())
               ->method('createNativeQueryBuilder')
               ->with($fixtureEntity)
               ->will($this->returnValue($queryMock));
        $emMock->expects($this->any())
               ->method('getMapper')
               ->will($this->returnValue($mapper));

        $mapper->load($emMock, $fixtureEntity, $fixtureId);
    }

    const TEST_KEY_VALUE = 1;

    public function testSaveUnknownEntity_ThrowsInvalidEntityException()
    {
        $metadataFactory = new Zend_Entity_MetadataFactory_Testing();
        $options = array('db' => new Zend_Test_DbAdapter(), 'metadataFactory' => $metadataFactory);

        $mapper = Zend_Db_Mapper_Mapper::create($options);
        $emMock = $this->getMock('Zend_Entity_Manager_Interface');

        $this->setExpectedException("Zend_Entity_InvalidEntityException");

        $mapper->save(new Zend_TestEntity2(), $emMock);
    }

    public function testDeleteUnknownEntity_ThrowsInvalidEntityException()
    {
        $metadataFactory = new Zend_Entity_MetadataFactory_Testing();
        $options = array('db' => new Zend_Test_DbAdapter(), 'metadataFactory' => $metadataFactory);

        $mapper = Zend_Db_Mapper_Mapper::create($options);
        $emMock = $this->getMock('Zend_Entity_Manager_Interface');

        $this->setExpectedException("Zend_Entity_InvalidEntityException");
        
        $mapper->delete(new Zend_TestEntity2(), $emMock);
    }

    public function testSaveNonLoadedLazyLoadProxy_IsDelegatedToPersister()
    {
        $persister = $this->createPersisterMock();
        $persister->expects($this->once())->method('save');

        $mapper = $this->createMapper(null, null, null, null, $persister);
        $lazyEntity = $this->getMock(
            'Zend_Entity_LazyLoad_Entity',
            array(),
            array(),
            'Zend_Entity_LazyLoad_Entity_Mock'.md5(microtime(True)),
            false
        );
        $lazyEntity->expects($this->never())->method('entityWasLoaded');
        $lazyEntity->expects($this->once())->method('__ze_getClassName')->will($this->returnValue('Sample'));

        $mapper->save($lazyEntity, $this->createEntityManager());
    }

    public function testCreateNativeQuery_WithResultSetMapping()
    {
        $testAdapter = new Zend_Test_DbAdapter();
        $mapper = new Zend_Db_Mapper_Mapper($testAdapter, null, array());

        $resultSetMapping = new Zend_Entity_Query_ResultSetMapping();
        $entityManager = $this->createEntityManager();
        $entityManager->setMapper($mapper);
        
        $q = $mapper->createNativeQuery("select foo", $resultSetMapping, $entityManager);

        $this->assertType('Zend_Db_Mapper_SqlQuery', $q);
    }

    public function testCreateNativeQuery_WithEntityName_CreatesRsmOnTheFly()
    {
        $fixture = new Zend_Entity_Fixture_SimpleFixtureDefs();
        $em = $fixture->createTestEntityManager();
        $mapper = $em->getMapper();

        $q = $mapper->createNativeQuery("select foo", "Zend_TestEntity1", $em);

        $this->assertType('Zend_Db_Mapper_SqlQuery', $q);

        $rsm = $q->getResultSetMapping();
        $this->assertTrue(isset($rsm->entityResult['Zend_TestEntity1']));
        $this->assertEquals(
            array("a_id" => "id", "a_property" => "property"),
            $rsm->entityResult['Zend_TestEntity1']['properties']
        );
    }

    public function testCreateNativeQuery_WithUnknownEntityName_ThrowsException()
    {
        $fixture = new Zend_Entity_Fixture_SimpleFixtureDefs();
        $em = $fixture->createTestEntityManager();
        $mapper = $em->getMapper();

        $this->setExpectedException("Zend_Entity_InvalidEntityException");
        $q = $mapper->createNativeQuery("select foo", "anUnknownEntity", $em);
    }

    public function testCreateNativeQuery_WithInvalidInput_ThrowsException()
    {
        $fixture = new Zend_Entity_Fixture_SimpleFixtureDefs();
        $em = $fixture->createTestEntityManager();
        $mapper = $em->getMapper();

        $this->setExpectedException("Zend_Entity_Exception");
        $q = $mapper->createNativeQuery("select foo", 1234, $em);
    }

    public function testSaveNonLazyNonCleanEntity_IsDelegatedToPersister()
    {
        $persister = $this->createPersisterMock();
        $persister->expects($this->once())->method('save');
        $entity = $this->getMock('Zend_Entity_Interface');
        $className = get_class($entity);

        $mapper = $this->createMapper(null, $this->createSampleEntityDefinition($className), null, null, $persister);
        $mapper->save($entity, $this->createEntityManager());
    }

    public function testDeleteEntity_ThatIsNotCleanOrNew_IsDelegatedToPersister()
    {
        $persister = $this->createPersisterMock();
        $persister->expects($this->once())->method('delete');
        $entity = $this->getMock('Zend_Entity_Interface');
        $className = get_class($entity);

        $mapper = $this->createMapper(null, $this->createSampleEntityDefinition($className), null, null, $persister);
        $mapper->delete($entity, $this->createEntityManager());
    }

    public function testDeleteLazyEntity_ThatIsNotCleanOrNew_IsDelegatedToPersister()
    {
        $persister = $this->createPersisterMock();
        $persister->expects($this->once())->method('delete');
        $entity = $this->getMock('Zend_Entity_LazyLoad_Entity', array(), array(), '', false);
        $className = get_class($entity);
        $entity->expects($this->once())
              ->method('__ze_getClassName')
              ->will($this->returnValue($className));

        $mapper = $this->createMapper(null, $this->createSampleEntityDefinition($className), null, null, $persister);
        $mapper->delete($entity, $this->createEntityManager());
    }
}