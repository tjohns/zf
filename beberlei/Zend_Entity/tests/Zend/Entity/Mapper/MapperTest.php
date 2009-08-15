<?php

class Zend_Entity_Mapper_MapperTest extends Zend_Entity_TestCase
{
    public function testCreateFactory_WithoutDbKey_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");
        
        $mapper = Zend_Entity_Mapper_Mapper::create(array());
    }

    public function testCreateFactory_WithoutMetadtaFactoryKey_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $options = array('db' => new Zend_Test_DbAdapter());

        $mapper = Zend_Entity_Mapper_Mapper::create($options);
    }

    public function testSelectType()
    {
        $entityDefinition = new Zend_Entity_Definition_Entity('foo');
        $entityDefinition->addPrimaryKey("id");

        $mapper = $this->createMapper(null, $entityDefinition);

        $select = $mapper->select();

        $this->assertType('Zend_Entity_Mapper_Select', $select);
    }

    public function testLoadEntity()
    {
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

        $queryMock = $this->getMock('Zend_Entity_Mapper_NativeQuery', array('select', 'where', 'getSingleResult'), array(), '', false);
        $queryMock->expects($this->at(0))
                  ->method('where')
                  ->with($this->equalTo('bar.col_id = ?'), $this->equalTo($fixtureId));
        $queryMock->expects($this->at(1))
                  ->method('getSingleResult');

        $emMock = $this->getMock('Zend_Entity_Manager_Interface');
        $emMock->expects($this->once())
               ->method('createNativeQuery')
               ->with($fixtureEntity)
               ->will($this->returnValue($queryMock));

        $mapper->load($fixtureEntity, $emMock, $fixtureId);
    }

    const TEST_KEY_VALUE = 1;

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