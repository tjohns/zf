<?php

class Zend_Entity_DbMapper_MapperTest extends Zend_Entity_TestCase
{
    public function testConstructWithoutAdapter_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");
        $mapper = new Zend_Db_Mapper_Mapper(array());
    }

    public function testLoadEntity_InvalidEntity_ThrowsException()
    {
        $metadataFactory = new Zend_Entity_MetadataFactory_Testing();
        $options = array('adapter' => new Zend_Test_DbAdapter(), 'metadataFactory' => $metadataFactory);

        $mapper = new Zend_Db_Mapper_Mapper($options);
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
        $mapper = $this->createMapper(new Zend_Test_DbAdapter(), null, $metadataFactory);
        $emMock = $this->getMock('Zend_Entity_Manager_Interface');

        $this->setExpectedException("Zend_Entity_InvalidEntityException");

        $mapper->save(new Zend_TestEntity2(), $emMock);
    }

    public function testDeleteUnknownEntity_ThrowsInvalidEntityException()
    {
        $metadataFactory = new Zend_Entity_MetadataFactory_Testing();
        $mapper = $this->createMapper(new Zend_Test_DbAdapter(), null, $metadataFactory);
        $emMock = $this->getMock('Zend_Entity_Manager_Interface');

        $this->setExpectedException("Zend_Entity_InvalidEntityException");
        
        $mapper->delete(new Zend_TestEntity2(), $emMock);
    }

    public function testSaveLazyLoadProxy_IsDelegatedToPersister()
    {
        $persister = $this->createPersisterMock();
        $persister->expects($this->once())->method('save');

        $lazyEntity = $this->getMock('Zend_Entity_LazyLoad_Proxy');
        $className = get_class($lazyEntity);

        $sampleDef = $this->createSampleEntityDefinition();
        $sampleDef->setProxyClass($className);

        $mapper = $this->createMapper(null, $sampleDef, null, null, $persister);

        $mapper->save($lazyEntity, $this->createEntityManager());
    }

    public function testCreateNativeQuery_WithResultSetMapping()
    {
        $testAdapter = new Zend_Test_DbAdapter();
        $mapper = $this->createMapper($testAdapter);

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

        $sampleDef = $this->createSampleEntityDefinition();
        $sampleDef->setProxyClass($className);

        $mapper = $this->createMapper(null, $sampleDef, null, null, $persister);
        $mapper->delete($entity, $this->createEntityManager());
    }

    public function testRefresh()
    {
        $rows = array(
            array('a_id' => 1, 'a_property' => 'Foo'),
        );
        
        $stmt = Zend_Test_DbStatement::createSelectStatement($rows);
        $db = new Zend_Test_DbAdapter();
        $db->appendStatementToStack($stmt);

        $identityMap = new Zend_Entity_IdentityMap();
        $em = $this->createTestingEntityManager(null, null, $identityMap);

        $entity = new Zend_TestEntity1();
        $identityMap->addObject("Zend_TestEntity1", 1, $entity);

        $fixture = new Zend_Entity_Fixture_SimpleFixtureDefs();
        $metadata = $fixture->getResourceMap();

        $mapper = $this->createMapper($db, null, $metadata);
        $mapper->refresh($entity, $em);

        $this->assertEquals(1, $entity->id);
        $this->assertEquals("Foo", $entity->property);
    }

    public function dataLoaderModes()
    {
        return array(
            array(Zend_Entity_Manager::FETCH_ARRAY, 'Zend_Db_Mapper_Loader_Array'),
            array(Zend_Entity_Manager::FETCH_ENTITIES, 'Zend_Db_Mapper_Loader_Entity'),
            array(Zend_Entity_Manager::FETCH_SCALAR, 'Zend_Db_Mapper_Loader_Scalar'),
            array(Zend_Entity_Manager::FETCH_SINGLESCALAR, 'Zend_Db_Mapper_Loader_SingleScalar'),
            array(Zend_Entity_Manager::FETCH_REFRESH, 'Zend_Db_Mapper_Loader_Refresh'),
        );
    }

    /**
     * @dataProvider dataLoaderModes
     * @param int $fetchMode
     * @param string $expectedType 
     */
    public function testGetLoader($fetchMode, $expectedType)
    {
        $db = new Zend_Test_DbAdapter();
        $mapper = $this->createMapper($db);

        $em = $this->createTestingEntityManager();

        $loaderA = $mapper->getLoader($fetchMode, $em);
        $loaderB = $mapper->getLoader($fetchMode, $em);

        $this->assertSame($loaderA, $loaderB);
        $this->assertType($expectedType, $loaderA);
    }

    /**
     * @dataProvider dataLoaderModes
     * @param int $fetchMode
     * @param string $expectedType
     */
    public function testGetLoaderPassTypeConverter($fetchMode)
    {
        $mapper = $this->createMapper();

        $em = $this->createTestingEntityManager();

        $tc = $this->getMock('Zend_Entity_StateTransformer_TypeConverter');
        $mapper->setTypeConverter($tc);

        $loader = $mapper->getLoader($fetchMode, $em);
        $this->assertSame($tc, $loader->getTypeConverter());
    }

    public function testGetDefaultTypeConverter()
    {
        $mapper = $this->createMapper();

        $this->assertType('Zend_Entity_StateTransformer_TypeConverter', $mapper->getTypeConverter());
    }

    public function testSetGetTypeConverter()
    {
        $mapper = $this->createMapper();

        $tc = $this->getMock('Zend_Entity_StateTransformer_TypeConverter');

        $mapper->setTypeConverter($tc);
        $this->assertSame($tc, $mapper->getTypeConverter());
    }

    public function testInitializeMappingsTransformsMetadataFactoryWithOptions()
    {
        $mapper = $this->createMapper();

        $mf = $this->getMock('Zend_Entity_MetadataFactory_FactoryAbstract');
        $mf->expects($this->once())
           ->method('transform')
           ->with($this->equalTo("Zend_Db_Mapper_Mapping"), $this->equalTo(array("adapterName" => "DbAdapter")));

        $mapper->initializeMappings($mf);
    }

    public function testInitializeMappingsSetsDefaultIdGeneratorClass()
    {
        $mapper = $this->createMapper();

        $mf = $this->getMock('Zend_Entity_MetadataFactory_FactoryAbstract');
        $mf->expects($this->once())
           ->method('getDefaultIdGeneratorClass')
           ->will($this->returnValue(null));
        $mf->expects($this->once())
           ->method('setDefaultIdGeneratorClass')
           ->with($this->equalTo("Zend_Db_Mapper_Id_AutoIncrement"));

        $mapper->initializeMappings($mf);
    }
}