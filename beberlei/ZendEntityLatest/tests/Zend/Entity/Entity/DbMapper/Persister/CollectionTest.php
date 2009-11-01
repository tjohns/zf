<?php

class Zend_Entity_DbMapper_Persister_CollectionTest extends Zend_Entity_TestCase
{
    /**
     * @var Zend_Entity_IdentityMap
     */
    public $identityMap;

    /**
     * @var Zend_Entity_Manager_Interface
     */
    public $entityManager;

    /**
     * @var Zend_Test_DbAdapter
     */
    public $testAdapter;

    /**
     *
     * @var Zend_Db_Mapper_Persister_Collection
     */
    public $persister;

    public function setUp()
    {
        $this->testAdapter = new Zend_Test_DbAdapter();
        $this->identityMap = new Zend_Entity_IdentityMap();
        $this->entityManager = $this->createEntityManager(null, null, $this->identityMap, $this->testAdapter);

        $colDef = new Zend_Entity_Definition_Collection("entities", array(
            "key" => "owner_id",
            "table" => "manytomany",
            "relation" => new Zend_Entity_Definition_ManyToManyRelation(
                array(
                    "columnName" => "foreign_id",
                    "class" => "Zend_TestEntity1",
                )
            ),
        ));

        $this->persister = new Zend_Db_Mapper_Persister_Collection($colDef);
    }

    public function testAddedItemsAreSavedInCollectionTable()
    {
        $ownerId = 1;

        $entityA = new Zend_TestEntity1();
        $entityB = new Zend_TestEntity1();
        $this->identityMap->addObject('Zend_TestEntity1', 1, $entityA);
        $this->identityMap->addObject('Zend_TestEntity1', 2, $entityB);

        $col = new Zend_Entity_Collection();
        $col[] = $entityA;
        $col[] = $entityB;

        $this->persister->persist($ownerId, $col, $this->entityManager);

        $queries = $this->testAdapter->getProfiler()->getQueryProfiles();
        $this->assertEquals(2, count($queries));
        $this->assertEquals("INSERT INTO manytomany (owner_id, foreign_id) VALUES (?, ?)", $queries[0]->getQuery());
        $this->assertEquals("INSERT INTO manytomany (owner_id, foreign_id) VALUES (?, ?)", $queries[1]->getQuery());
        $this->assertEquals(array(1 => $ownerId, 2 => 1), $queries[0]->getQueryParams());
        $this->assertEquals(array(1 => $ownerId, 2 => 2), $queries[1]->getQueryParams());
    }

    public function testRemovedItemsAreDeletedFromCollectionTable()
    {
        $ownerId = 1;

        $entityA = new Zend_TestEntity1();
        $entityB = new Zend_TestEntity1();
        $this->identityMap->addObject('Zend_TestEntity1', 1, $entityA);
        $this->identityMap->addObject('Zend_TestEntity1', 2, $entityB);

        $data = array($entityA, $entityB);

        $col = new Zend_Entity_Collection($data);
        unset($col[0]);
        unset($col[1]);

        $this->persister->persist($ownerId, $col, $this->entityManager);

        $queries = $this->testAdapter->getProfiler()->getQueryProfiles();
        $this->assertEquals(2, count($queries));
        $this->assertEquals('DELETE FROM manytomany WHERE (owner_id = 1 AND foreign_id = 1)', $queries[0]->getQuery());
        $this->assertEquals('DELETE FROM manytomany WHERE (owner_id = 1 AND foreign_id = 2)', $queries[1]->getQuery());
    }

    public function testAddedItemWithoutIdentity_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_InvalidEntityException");

        $ownerId = 1;
        $entityA = new Zend_TestEntity1();

        $col = new Zend_Entity_Collection();
        $col[] = $entityA;

        $this->persister->persist($ownerId, $col, $this->entityManager);
    }

    public function testRemovedItemWithoutIdentity_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_InvalidEntityException");

        $ownerId = 1;
        $entityA = new Zend_TestEntity1();

        $col = new Zend_Entity_Collection(array($entityA));
        unset($col[0]);

        $this->persister->persist($ownerId, $col, $this->entityManager);
    }

    public function testAddItemOfWrongType_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_InvalidEntityException");

        $ownerId = 1;

        $entityA = new Zend_TestEntity2();
        $this->identityMap->addObject('Zend_TestEntity2', 1, $entityA);

        $col = new Zend_Entity_Collection();
        $col[] = $entityA;

        $this->persister->persist($ownerId, $col, $this->entityManager);
    }

    public function testRemoveItemOfWrontType_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_InvalidEntityException");

        $ownerId = 1;

        $entityA = new Zend_TestEntity2();
        $this->identityMap->addObject('Zend_TestEntity2', 1, $entityA);

        $col = new Zend_Entity_Collection(array($entityA));
        unset($col[0]);

        $this->persister->persist($ownerId, $col, $this->entityManager);
    }
}