<?php

require_once dirname(__FILE__)."/../TestHelper.php";

class Zend_Entity_IdentityMapTest extends PHPUnit_Framework_TestCase
{
    public function testDefaultHasObjectEvaluatesToFalse()
    {
        $identityMap = new Zend_Entity_Mapper_IdentityMap();
        $this->assertFalse($identityMap->hasObject("Anything", 1));
    }

    public function testAddingObjectLetsHasObjectEvaluateTrue()
    {
        $identityMap = new Zend_Entity_Mapper_IdentityMap();

        $entity = new Zend_TestEntity1();

        $identityMap->addObject("Zend_TestEntity1", "1", $entity);
        $this->assertTrue($identityMap->hasObject("Zend_TestEntity1", "1"));
    }

    public function testAddingGettingObjectReturnsReferenceToSameObject()
    {
        $identityMap = new Zend_Entity_Mapper_IdentityMap();

        $entity = new Zend_TestEntity1();

        $identityMap->addObject("Zend_TestEntity1", "1", $entity);
        $this->assertEquals($entity, $identityMap->getObject("Zend_TestEntity1", "1"));
    }
}