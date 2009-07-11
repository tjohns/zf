<?php

require_once dirname(__FILE__)."/../../TestHelper.php";
require_once "TestCase.php";

class Zend_Entity_ManagerFindTest extends Zend_Entity_TestCase
{
    const UNKNOWN_ENTITY_CLASS = 'UnknownEntityClass';
    const KNOWN_ENTITY_CLASS = 'KnownEntityClass';

    public function testGetSelectFromUnknownEntityMapper()
    {
        $this->setExpectedException("Exception");
        $e = new Exception();
        
        $resourceMapMock = $this->createResourceMapMock();
        $resourceMapMock->expects($this->once())
                        ->method('getDefinitionByEntityName')
                        ->with($this->equalTo(self::UNKNOWN_ENTITY_CLASS))
                        ->will($this->throwException($e));
        $manager = $this->createEntityManager(null, $resourceMapMock);
        
        $manager->select(self::UNKNOWN_ENTITY_CLASS);
    }

    public function testGetSelectFromKnownMapperIsDelegated()
    {
        $mapper = $this->createMapperMock();
        $mapper->expects($this->once())
               ->method('select');
        $manager = $this->createTestingEntityManager();
        $manager->addMapper(self::KNOWN_ENTITY_CLASS, $mapper);

        $manager->select(self::KNOWN_ENTITY_CLASS);
    }

    public function testFindIsDelegatedToMapper()
    {
        $select = $this->createDbSelectMock();
        $manager = $this->createEntityManagerMockWithMapperMockFindMethodExpectation('find', $select);
        
        $manager->find(self::KNOWN_ENTITY_CLASS, $select);
    }

    public function testFindOneIsDelegatedToMapper()
    {
        $select = $this->createDbSelectMock();
        $manager = $this->createEntityManagerMockWithMapperMockFindMethodExpectation('findOne', $select);

        $manager->findOne(self::KNOWN_ENTITY_CLASS, $select);
    }

    public function testFindAllOfEntityWithoutAdditionalRestriction()
    {
        $select = $this->createDbSelectMock();
        $manager = $this->createEntityManagerMockWithMapperMockFindMethodExpectation('find', $select);

        $select->expects($this->never())->method('where');
        $select->expects($this->never())->method('limit');
        $select->expects($this->never())->method('order');

        $manager->findAll(self::KNOWN_ENTITY_CLASS, null, null, null);
    }

    public function testFindAllOfEntityWithWhereRestriction()
    {
        $select = $this->createDbSelectMock();
        $manager = $this->createEntityManagerMockWithMapperMockFindMethodExpectation('find', $select);

        $select->expects($this->once())->method('where')->with($this->equalTo('foo'));
        $select->expects($this->never())->method('limit');
        $select->expects($this->never())->method('order');

        $manager->findAll(self::KNOWN_ENTITY_CLASS, 'foo', null, null);
    }

    public function testFindAllOfEntityWithLimitRestriction()
    {
        $select = $this->createDbSelectMock();
        $manager = $this->createEntityManagerMockWithMapperMockFindMethodExpectation('find', $select);

        $select->expects($this->never())->method('where');
        $select->expects($this->once())->method('limit')->will($this->returnValue('foo'));
        $select->expects($this->never())->method('order');

        $manager->findAll(self::KNOWN_ENTITY_CLASS, null, 'foo', null);
    }

    public function testFindAllOfEntityWithOrderRestriction()
    {
        $select = $this->createDbSelectMock();
        $manager = $this->createEntityManagerMockWithMapperMockFindMethodExpectation('find', $select);

        $select->expects($this->never())->method('where');
        $select->expects($this->never())->method('limit');
        $select->expects($this->once())->method('order')->will($this->returnValue('foo'));

        $manager->findAll(self::KNOWN_ENTITY_CLASS, null, null, 'foo');
    }

    public function testCreateNativeQuery()
    {
        $manager = $this->createTestingEntityManager();
        $select = $this->createDbSelectMock();

        $mapper = $this->createMapperMock();
        $mapper->expects($this->any())
               ->method('select')
               ->will($this->returnValue($select));
        $manager->addMapper(self::KNOWN_ENTITY_CLASS, $mapper);

        $sqlQuery = $manager->createNativeQuery(self::KNOWN_ENTITY_CLASS);

        $this->assertEquals($select, $sqlQuery);
    }

    public function testCreateQuery()
    {
        
    }

    public function createEntityManagerMockWithMapperMockFindMethodExpectation($methodName, $select)
    {
        $manager = $this->createTestingEntityManager();

        $mapper = $this->createMapperMock();
        $mapper->expects($this->any())->method('select')->will($this->returnValue($select));
        $mapper->expects($this->once())
               ->method($methodName)
               ->with($this->equalTo($select), $this->equalTo($manager));

        $manager->addMapper(self::KNOWN_ENTITY_CLASS, $mapper);

        return $manager;
    }
}