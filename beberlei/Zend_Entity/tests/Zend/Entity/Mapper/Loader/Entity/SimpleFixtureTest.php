<?php

class Zend_Entity_Mapper_Loader_Entity_SimpleFixtureTest extends Zend_Entity_Mapper_Loader_TestCase
{
    public function getLoaderClassName()
    {
        return "Zend_Entity_Mapper_Loader_Entity";
    }

    public function getFixtureClassName()
    {
        return "Zend_Entity_Fixture_SimpleFixtureDefs";
    }

    public function getLoader()
    {
        return $this->createLoader($this->fixture->getEntityDefinition(Zend_Entity_Fixture_SimpleFixtureDefs::TEST_A_CLASS));
    }

    public function testCreateSimpleEntityFromRow()
    {
        $this->assertType('Zend_Entity_Fixture_SimpleFixtureDefs', $this->fixture);
        $loader = $this->getLoader();

        $row = $this->fixture->getDummyDataRow();
        $state = $this->fixture->getDummyDataState();

        $entity = $loader->createEntityFromRow($row, $this->mappings["Zend_TestEntity1"]);

        $this->assertEquals($state, $entity->getState());
    }

    public function testLoadRowForSimpleEntity()
    {
        $entity = new Zend_TestEntity1;
        $loader = $this->getLoader();
        $row = $this->fixture->getDummyDataRow();
        $state = $this->fixture->getDummyDataState();

        $loader->loadRow($entity, $row, $this->mappings["Zend_TestEntity1"]);

        $this->assertEquals($state, $entity->getState());
    }

    public function testCheckOnIdentityMapIsPerformedBeforeCreatingNewEntityFromRow()
    {
        $loader = $this->getLoader();
        $row = $this->fixture->getDummyDataRow();

        $entity = new Zend_TestEntity1();

        $this->identityMap->addObject("Zend_TestEntity1", 1, $entity);

        $createdEntity = $loader->createEntityFromRow($row, $this->mappings["Zend_TestEntity1"]);

        $this->assertSame($entity, $createdEntity);
    }

    public function testProcessResultsetInEntityMode()
    {
        $loader = $this->getLoader();
        $row = $this->fixture->getDummyDataRow();
        $state = $this->fixture->getDummyDataState();

        $resultSet = array($row);

        $rsm = new Zend_Entity_Query_ResultSetMapping();
        $rsm->addEntity(Zend_Entity_Fixture_SimpleFixtureDefs::TEST_A_CLASS, "a");
        $rsm->addProperty("a", "a_id", "id");
        $rsm->addProperty("a", "a_property", "property");

        $collection = $loader->processResultset($resultSet, $rsm);

        $this->assertType('array', $collection);
        $this->assertEquals(1, count($collection));

        $entity = $collection[0];
        $this->assertEquals($state, $entity->getState());
    }

    public function testLoadRowWithMissingColumnsThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $entity = new Zend_TestEntity1;
        $loader = $this->getLoader();
        $rowMissingColumn = array(Zend_Entity_Fixture_SimpleFixtureDefs::TEST_A_ID_COLUMN => 1);

        $loader->loadRow($entity, $rowMissingColumn, $this->mappings["Zend_TestEntity1"]);
    }

    public function testLoadRow_VersionedField_AddedToIdentityMap()
    {
        $def = $this->fixture->getEntityDefinition(Zend_Entity_Fixture_SimpleFixtureDefs::TEST_A_CLASS);
        $def->addVersion("version", array("columnName" => 'a_version'));

        $entity = new Zend_TestEntity1;
        $loader = $this->getLoader();
        $versionFixture = 1234;

        $row = array('a_id' => 1, 'a_property' => 'foo', 'a_version' => $versionFixture);

        $entity = $loader->createEntityFromRow($row, $this->mappings["Zend_TestEntity1"]);
        $this->assertEquals($versionFixture, $this->identityMap->getVersion($entity));
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

        $loader->createEntityFromRow($row, $this->mappings["Zend_TestEntity1"]);
    }
}