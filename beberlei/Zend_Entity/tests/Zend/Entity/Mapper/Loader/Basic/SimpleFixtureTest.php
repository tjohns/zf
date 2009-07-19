<?php

class Zend_Entity_Mapper_Loader_Basic_SimpleFixtureTest extends Zend_Entity_Mapper_Loader_TestCase
{
    /**
     * @var Zend_Entity_Fixture_SimpleFixtureDefs
     */
    protected $fixture;

    public function setUp()
    {
        $this->fixture = new Zend_Entity_Fixture_SimpleFixtureDefs();
    }

    /**
     * @param Zend_Entity_Definition_Entity $def
     * @return Zend_Entity_Mapper_Loader_Basic
     */
    public function createLoader(Zend_Entity_Definition_Entity $def)
    {
        return new Zend_Entity_Mapper_Loader_Basic($def);
    }

    public function getLoader()
    {
        return $this->createLoader($this->fixture->getEntityDefinition(Zend_Entity_Fixture_SimpleFixtureDefs::TEST_A_CLASS));
    }

    public function testCreateSimpleEntityFromRow()
    {
        $loader = $this->getLoader();

        $row = $this->fixture->getDummyDataRow();
        $state = $this->fixture->getDummyDataState();

        $entity = $loader->createEntityFromRow($row, $this->createEntityManager());

        $this->assertEquals($state, $entity->getState());
    }

    public function testLoadRowForSimpleEntity()
    {
        $entity = new Zend_TestEntity1;
        $loader = $this->getLoader();
        $row = $this->fixture->getDummyDataRow();
        $state = $this->fixture->getDummyDataState();

        $loader->loadRow($entity, $row, $this->createEntityManager());

        $this->assertEquals($state, $entity->getState());
    }

    public function testCheckOnIdentityMapIsPerformedBeforeCreatingNewEntityFromRow()
    {
        $loader = $this->getLoader();
        $row = $this->fixture->getDummyDataRow();

        $this->identityMap = $this->createIdentityMapMock(0);
        $this->identityMap->expects($this->once())->method('hasObject')->will($this->returnValue(true));
        $this->identityMap->expects($this->once())->method('getObject')->will($this->returnValue('foo'));
        
        $entityManager = $this->createEntityManager();

        $entity = $loader->createEntityFromRow($row, $entityManager);

        $this->assertEquals('foo', $entity);
    }

    public function testProcessResultsetInEntityMode()
    {
        $loader = $this->getLoader();
        $row = $this->fixture->getDummyDataRow();
        $state = $this->fixture->getDummyDataState();

        $resultSet = array($row);

        $collection = $loader->processResultset($resultSet, $this->createEntityManager(), Zend_Entity_Manager::FETCH_ENTITIES);

        $this->assertTrue($collection instanceof Zend_Entity_Collection);
        $this->assertEquals(1, count($collection));

        $entity = $collection[0];
        $this->assertEquals($state, $entity->getState());
    }

    public function testProcessResultsetInArrayMode()
    {
        $loader = $this->getLoader();
        $row = $this->fixture->getDummyDataRow();
        $state = $this->fixture->getDummyDataState();

        $resultSet = array($row);

        $array = $loader->processResultset($resultSet, $this->createEntityManager(), Zend_Entity_Manager::FETCH_ARRAY);

        $this->assertTrue(is_array($array));
        $this->assertEquals(1, count($array));

        $this->assertEquals($state, $array[0]);
    }

    public function testLoadRowWithMissingColumnsThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $entity = new Zend_TestEntity1;
        $loader = $this->getLoader();
        $rowMissingColumn = array(Zend_Entity_Fixture_SimpleFixtureDefs::TEST_A_ID_COLUMN => 1);

        $loader->loadRow($entity, $rowMissingColumn, $this->createEntityManager());
    }

    public function testLoadRow_VersionedField_AddedToIdentityMap()
    {
        $def = $this->fixture->getEntityDefinition(Zend_Entity_Fixture_SimpleFixtureDefs::TEST_A_CLASS);
        $def->addVersion("version", array("columnName" => 'a_version'));

        $entity = new Zend_TestEntity1;
        $loader = $this->getLoader();
        $versionFixture = 1234;

        $row = array('a_id' => 1, 'a_property' => 'foo', 'a_version' => $versionFixture);

        $em = $this->createEntityManager();

        $entity = $loader->createEntityFromRow($row, $em);
        $this->assertEquals($versionFixture, $em->getIdentityMap()->getVersion($entity));
    }


    public function testLoadRow_VersionedFieldMissing_ThrowsException()
    {
        $this->setExpectedException(
            "Zend_Entity_Exception",
            "Missing version column 'a_version' in entity resultset"
        );

        $def = $this->fixture->getEntityDefinition(Zend_Entity_Fixture_SimpleFixtureDefs::TEST_A_CLASS);
        $def->addVersion("version", array("columnName" => 'a_version'));

        $entity = new Zend_TestEntity1;
        $loader = $this->getLoader();
        $versionFixture = 1234;

        $row = array('a_id' => 1, 'a_property' => 'foo');

        $em = $this->createEntityManager();

        $loader->createEntityFromRow($row, $em);
    }
}