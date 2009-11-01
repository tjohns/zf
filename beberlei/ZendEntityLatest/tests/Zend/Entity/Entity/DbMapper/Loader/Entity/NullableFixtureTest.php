<?php

class Zend_Entity_DbMapper_Loader_Entity_NullableFixtureTest extends Zend_Entity_DbMapper_Loader_TestCase
{
    
    public function getLoaderClassName() {
        return "Zend_Db_Mapper_Loader_Entity";
    }
    public function getFixtureClassName() {
        return "Zend_Entity_Fixture_NullableDefs";
    }

    public function testLoadNullProperty()
    {
        $loader = $this->createLoader();

        $this->identityMap->addObject("Zend_TestEntity2", 1, new stdClass());
        $this->identityMap->addObject("Zend_TestEntity2", 2, new stdClass());

        $state = array(
            'id' => 1,
            'property' => null,
            'manytoone1' => 1,
            'manytoone2' => 2,
        );

        $entity = $loader->createEntityFromState($state, $this->mappings['Zend_TestEntity1']);

        $this->assertNull($entity->property);
    }
    
    public function testLoadRelatedEntityWithNull()
    {
        $loader = $this->createLoader();

        $this->identityMap->addObject("Zend_TestEntity2", 2, new stdClass());

        $state = array(
            'id' => 1,
            'property' => 'Foo',
            'manytoone1' => null,
            'manytoone2' => 2,
        );

        $entity = $loader->createEntityFromState($state, $this->mappings['Zend_TestEntity1']);

        $this->assertNull($entity->manytoone1);
    }

    public function testLoadRelatedEntityWithUnallowedNull_ThrowsException()
    {
        $loader = $this->createLoader();

        $this->identityMap->addObject("Zend_TestEntity2", 2, new stdClass());

        $state = array(
            'id' => 1,
            'property' => 'Foo',
            'manytoone1' => 2,
            'manytoone2' => null,
        );

        $this->setExpectedException("Zend_Entity_InvalidEntityException");
        $entity = $loader->createEntityFromState($state, $this->mappings['Zend_TestEntity1']);
    }
}