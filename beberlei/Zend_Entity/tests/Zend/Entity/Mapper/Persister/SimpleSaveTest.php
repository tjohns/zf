<?php

require_once dirname(__FILE__)."/../../../../TestHelper.php";

class Zend_Entity_Mapper_Persister_SimpleSaveTest extends Zend_Entity_TestCase
{
    /**
     * @var Zend_Entity_Mapper_Definition_Entity
     */
    private $entityDefinition = null;

    /**
     * @var Zend_Entity_Mapper_Persister_Simple
     */
    private $persister = null;

    /**
     * @var Zend_Entity_Interface
     */
    private $entityUnderTest = null;

    /**
     * @var <type>
     */
    private $fixture = null;

    /**
     * @return Zend_Entity_Mapper_Definition_Entity
     */
    public function getEntityDefinition()
    {
        if($this->entityDefinition == null) {
            $def = new Zend_Entity_Mapper_Definition_Entity("Zend_TestEntity1", array("table" => "entities"));
            $def->addPrimaryKey('id', array('columnName' => 'entities_id'));
            $def->addProperty('foo');
            $def->addProperty('bar', array('columnName' => 'baz'));

            $def->compile(new Zend_Entity_Resource_Testing());

            $this->entityDefinition = $def;
        }
        return $this->entityDefinition;
    }

    /**
     *
     * @return Zend_Entity_Mapper_Persister_Simple
     */
    public function createPersister($entityDef=null, $defMap=null)
    {
        if($entityDef == null) {
            $entityDef = $this->getEntityDefinition();
        }
        if($defMap == null) {
            $defMap = new Zend_Entity_Resource_Testing();
            $defMap->addDefinition($entityDef);
        }

        $this->persister = new Zend_Entity_Mapper_Persister_Simple();
        $this->persister->initialize($entityDef, $defMap);

        return $this->persister;
    }

    public function createEntity($initialState)
    {
        $entity = new Zend_TestEntity1();
        $entity->setState( $initialState );
        return $entity;
    }

    public function testSaveEntityWithoutPrimaryKeyInsertsDbStateIntoDbAdapter()
    {
        $newId = 1;

        $propertyFixtureState = array('id' => null, 'foo' => 'foo', 'bar' => 'bar');
        $columnExpectedState = array('entities_id' => null, 'foo' => 'foo', 'baz' => 'bar');

        $this->entityUnderTest = $this->createEntity($propertyFixtureState);

        $db = $this->createDatabaseConnectionMock();
        $db->expects($this->once())
           ->method('insert')
           ->with('entities', $columnExpectedState);
        $db->expects($this->once())
            ->method('lastInsertId')
            ->will($this->returnValue($newId));

        $em = $this->createEntityManagerMock(null, null, null, $db);

        $persister = $this->createPersister();
        $persister->save($this->entityUnderTest, $em);

        $entityState = $this->entityUnderTest->getState();
        $this->assertEquals($newId, $entityState['id']);
    }

    public function testSaveEntityWithPrimaryKeyUpdatesDbStateWithWhereCondition()
    {
        $propertyFixtureState = array('id' => 1, 'foo' => 'foo', 'bar' => 'bar');
        $columnExpectedState = array('foo' => 'foo', 'baz' => 'bar');
        $columnExpectedWhere = 'entities.entities_id = 1';

        $this->entityUnderTest = $this->createEntity($propertyFixtureState);

        $db = $this->createDatabaseConnectionMock();
        $db->expects($this->once())
           ->method('update')
           ->with('entities', $columnExpectedState, $columnExpectedWhere);
        $db->expects($this->once())
           ->method('quoteInto')
           ->with('entities.entities_id = ?', 1)
           ->will($this->returnValue('entities.entities_id = 1'));

        $em = $this->createEntityManagerMock(null, null, null, $db);

        $persister = $this->createPersister();
        $persister->save($this->entityUnderTest, $em);
    }

    public function testInsertNewEntityUpdatesIdentityMap()
    {
        $newId = 1;

        $propertyFixtureState = array('id' => null, 'foo' => 'foo', 'bar' => 'bar');
        $columnExpectedState = array('entities_id' => null, 'foo' => 'foo', 'baz' => 'bar');

        $this->entityUnderTest = $this->createEntity($propertyFixtureState);

        $db = $this->createDatabaseConnectionMock();
        $db->expects($this->once())->method('insert');
        $db->expects($this->once())->method('lastInsertId')->will($this->returnValue($newId));

        $newIdHash = Zend_Entity_Mapper_Definition_Utility::hashKeyIdentifier($newId);
        $identityMap = $this->createIdentityMapMock(0);
        $identityMap->expects($this->once())
                    ->method('addObject')
                    ->with('Zend_TestEntity1', $newIdHash, $this->entityUnderTest);

        $em = $this->createEntityManagerMock(null, null, $identityMap, $db);

        $persister = $this->createPersister();
        $persister->save($this->entityUnderTest, $em);
    }

    public function testSaveEntityWithRelatedEntitySavesForeignKeyIntoDb()
    {
        $this->fixture = new Zend_Entity_Fixture_ManyToOneDefs();
        $this->entityUnderTest = new Zend_TestEntity1();
        $this->entityUnderTest->setState($this->fixture->getDummyDataStateClassA());
        $entityB = new Zend_TestEntity2();
        $entityB->setState($this->fixture->getDummyDataStateClassB());

        $relatedId = $entityB->getid();

        $this->entityUnderTest->setmanytoone($entityB);

        $this->assertDatabaseAdapterUpdateIsCalledCorrectlyOnSave($relatedId);
    }

    public function testSaveEntityWithRelatedLazyEntitySavesForeignKeyIntoDb()
    {
        $relatedId = 1;

        $this->fixture = new Zend_Entity_Fixture_ManyToOneDefs();
        $this->entityUnderTest = new Zend_TestEntity1();
        $this->entityUnderTest->setState($this->fixture->getDummyDataStateClassA());
        $entityB = new Zend_Entity_Mapper_LazyLoad_Entity('trim', array('findByKey', $relatedId));
        $this->entityUnderTest->setmanytoone($entityB);

        $this->assertDatabaseAdapterUpdateIsCalledCorrectlyOnSave($relatedId);
    }

    public function testSaveEntityWithRelatedNullSavesNullForeignKeyIntoDb()
    {
        $relatedId = null;

        $this->fixture = new Zend_Entity_Fixture_ManyToOneDefs();
        $this->entityUnderTest = new Zend_TestEntity1();
        $this->entityUnderTest->setState($this->fixture->getDummyDataStateClassA());
        $this->entityUnderTest->setmanytoone($relatedId);

        $this->assertDatabaseAdapterUpdateIsCalledCorrectlyOnSave($relatedId);
    }

    public function assertDatabaseAdapterUpdateIsCalledCorrectlyOnSave($relatedId)
    {
        $expectedColumnState = $this->fixture->getDummyDataRowClassA();
        $expectedColumnState[Zend_Entity_Fixture_ManyToOneDefs::TEST_A_MANYTOONE_COLUMN] = $relatedId;
        $columnExpectedWhere = 'table_a.a_id = '.$this->entityUnderTest->getid();
        unset($expectedColumnState[Zend_Entity_Fixture_ManyToOneDefs::TEST_A_ID_COLUMN]);

        $db = $this->createDatabaseConnectionMock();
        $db->expects($this->once())
           ->method('update')
           ->with('table_a', $expectedColumnState, $columnExpectedWhere);
        $db->expects($this->once())
           ->method('quoteInto')
           ->with('table_a.a_id = ?', $this->entityUnderTest->getid())
           ->will($this->returnValue($columnExpectedWhere));

        $em = $this->createEntityManagerMock(null, null, null, $db);

        $persister = $this->createPersister(
            $this->fixture->getResourceMap()->getDefinitionByEntityName('Zend_TestEntity1'),
            $this->fixture->getResourceMap()
        );
        $persister->save($this->entityUnderTest, $em);
    }

    public function testSaveEntityWithEmptyRelatedEntitySavesNullIfNullable()
    {

    }

    public function testSaveEntityWithEmptyRelatedEntityThrowsExceptionIfNotNullable()
    {
        
    }

    public function testSaveEntityWithPersistCascadeDelegatesToEntityManager()
    {
        
    }
}