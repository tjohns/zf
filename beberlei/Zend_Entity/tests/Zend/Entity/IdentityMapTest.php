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

    public function testAddObject_LetsSaveVersion()
    {
        $identityMap = new Zend_Entity_IdentityMap();

        $entity = new Zend_TestEntity1();
        $versionFixture = 1234;
        $identityMap->addObject("Zend_TestEntity1", "1", $entity, $versionFixture);

        $this->assertEquals($versionFixture, $identityMap->getVersion($entity));
    }

    public function testGetVersion_UnregisteredEntity()
    {
        $identityMap = new Zend_Entity_IdentityMap();
        $entity = new Zend_TestEntity1();
        $this->assertFalse($identityMap->getVersion($entity));
    }

    public function testSetVersion()
    {
        $identityMap = new Zend_Entity_IdentityMap();

        $entity = new Zend_TestEntity1();
        $versionFixture = 1234;
        $identityMap->addObject("Zend_TestEntity1", "1", $entity, $versionFixture);

        $identityMap->setVersion($entity, $versionFixture+1);

        $this->assertEquals($versionFixture+1, $identityMap->getVersion($entity));
    }

    public function testAddingGettingObjectReturnsReferenceToSameObject()
    {
        $identityMap = new Zend_Entity_IdentityMap();

        $entity = new Zend_TestEntity1();

        $identityMap->addObject("Zend_TestEntity1", "1", $entity);
        $this->assertEquals($entity, $identityMap->getObject("Zend_TestEntity1", "1"));
    }

    public function testContains_KnownObject()
    {
        $identityMap = new Zend_Entity_IdentityMap();
        $entity = new Zend_TestEntity1();
        
        $identityMap->addObject("Zend_TestEntity1", "1", $entity);
        
        $this->assertTrue($identityMap->contains($entity));
    }

    public function testContains_NonObject_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_InvalidEntityException");

        $identityMap = new Zend_Entity_IdentityMap();

        $identityMap->contains("foo");
    }

    public function testContains_UnknownObject_ReturnFalse()
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

    public function testGetPrimaryKey_FromUncontainedEntity_ThrowsException()
    {
        $identityMap = new Zend_Entity_IdentityMap();
        $entity = new Zend_TestEntity1();

        $this->setExpectedException(
            "Zend_Entity_Exception",
            "Entity of class 'Zend_TestEntity1' is not contained in persistence context and has no primary key."
        );
        $identityMap->getPrimaryKey($entity);
    }

    public function testRemoveEntity()
    {
        $identityMap = new Zend_Entity_IdentityMap();
        $entity = new Zend_TestEntity1();

        $identityMap->addObject("Zend_TestEntity1", "1", $entity);

        $this->assertTrue($identityMap->hasObject("Zend_TestEntity1", "1"));
        $this->assertTrue($identityMap->contains($entity));

        $identityMap->remove("Zend_TestEntity1", $entity);

        $this->assertFalse($identityMap->hasObject("Zend_TestEntity1", "1"));
        $this->assertFalse($identityMap->contains($entity));
    }
}