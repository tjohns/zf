<?php

require_once dirname(__FILE__)."/../TestHelper.php";

class Zend_Entity_IdentityMapTest extends PHPUnit_Framework_TestCase
{
    public function testDefaultHasObjectEvaluatesToFalse()
    {
        $identityMap = new Zend_Entity_IdentityMap();
        $this->assertFalse($identityMap->hasObject("Anything", 1));
    }

    public function testAddingObjectLetsHasObjectEvaluateTrue()
    {
        $identityMap = new Zend_Entity_IdentityMap();

        $entity = new Zend_TestEntity1();

        $identityMap->addObject("Zend_TestEntity1", "1", $entity);
        $this->assertTrue($identityMap->hasObject("Zend_TestEntity1", "1"));
    }

    public function testAddingGettingObjectReturnsReferenceToSameObject()
    {
        $identityMap = new Zend_Entity_IdentityMap();

        $entity = new Zend_TestEntity1();

        $identityMap->addObject("Zend_TestEntity1", "1", $entity);
        $this->assertEquals($entity, $identityMap->getObject("Zend_TestEntity1", "1"));
    }

    public function testAddedObjectIsContained()
    {
        $identityMap = new Zend_Entity_IdentityMap();
        $entity = new Zend_TestEntity1();
        
        $identityMap->addObject("Zend_TestEntity1", "1", $entity);
        
        $this->assertTrue($identityMap->contains($entity));
    }

    public function testUnknownObjectIsNotContained()
    {
        $identityMap = new Zend_Entity_IdentityMap();
        $entity = new Zend_TestEntity1();

        $this->assertFalse($identityMap->contains($entity));
    }

    public function testGetPrimaryKeyFromIdentity()
    {
        $identityMap = new Zend_Entity_IdentityMap();
        $entity = new Zend_TestEntity1();

        $identityMap->addObject("Zend_TestEntity1", "1", $entity);

        $this->assertEquals("1", $identityMap->getPrimaryKey($entity));
    }

    public function testClearEmptiesPrimaryKeys()
    {
        $identityMap = new Zend_Entity_IdentityMap();
        $entity = new Zend_TestEntity1();

        $identityMap->addObject("Zend_TestEntity1", "1", $entity);

        $identityMap->clear();
        $this->assertFalse($identityMap->contains($entity));
    }

    public function testClearEmptiesIdentities()
    {
        $identityMap = new Zend_Entity_IdentityMap();
        $entity = new Zend_TestEntity1();

        $identityMap->addObject("Zend_TestEntity1", "1", $entity);

        $identityMap->clear();
        $this->assertFalse($identityMap->hasObject("Zend_TestEntity1", "1"));
    }

    public function testGetPrimaryKeyFromUncontainedEntityThrowsException()
    {
        $identityMap = new Zend_Entity_IdentityMap();
        $entity = new Zend_TestEntity1();

        $this->setExpectedException("Exception");
        $identityMap->getPrimaryKey($entity);
    }
}