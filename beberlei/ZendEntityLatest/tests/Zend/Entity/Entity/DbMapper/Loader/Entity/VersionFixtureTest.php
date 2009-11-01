<?php

class Zend_Entity_DbMapper_Loader_Entity_VersionFixtureTest extends Zend_Entity_DbMapper_Loader_TestCase
{
    public function getLoaderClassName()
    {
        return "Zend_Db_Mapper_Loader_Entity";
    }

    public function getFixtureClassName()
    {
        return "Zend_Entity_Fixture_VersionDef";
    }

    public function testCreateEntityFromState_VersionedField_AddedToIdentityMap()
    {
        $loader = $this->createLoader();

        $entity = new Zend_TestEntity1;
        $versionFixture = 1234;

        $state = array('id' => 1, 'property' => 'foo', 'version' => $versionFixture);

        $entity = $loader->createEntityFromState($state, $this->mappings["Zend_TestEntity1"]);
        $this->assertEquals($versionFixture, $this->identityMap->getVersion($entity));
    }


    public function testCreateEntityFromState_VersionedFieldMissing_ThrowsException()
    {
        $this->setExpectedException(
            "Zend_Entity_Exception",
            "Missing version property 'version' in entity resultset"
        );

        $loader = $this->createLoader();

        $entity = new Zend_TestEntity1;
        $versionFixture = 1234;

        $state = array('id' => 1, 'property' => 'foo');

        $loader->createEntityFromState($state, $this->mappings["Zend_TestEntity1"]);
    }
}