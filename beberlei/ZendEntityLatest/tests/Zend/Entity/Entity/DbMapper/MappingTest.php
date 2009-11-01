<?php

class Zend_Entity_DbMapper_MappingTest extends Zend_Entity_Compliance_MappingTestCase
{
    public function createMapping()
    {
        return new Zend_Db_Mapper_Mapping();
    }

    public function testAcceptEntity_UseClassAsTable_IfTableNotDefined()
    {
        $entity = new Zend_Entity_Definition_Entity("foo");
        $entity->addPrimaryKey("id");
        $this->mapping->acceptEntity($entity, $this->metadataFactory);

        $this->assertEquals("foo", $this->mapping->table);
    }

    public function testAcceptEntity_KeepsTable()
    {
        $entity = new Zend_Entity_Definition_Entity("foo", array("table" => "bar"));
        $entity->addPrimaryKey("id");
        $this->mapping->acceptEntity($entity, $this->metadataFactory);

        $this->assertEquals("bar", $this->mapping->table);
    }

    public function testAcceptEntity_KeepsSchema()
    {
        $entity = new Zend_Entity_Definition_Entity("foo", array("table" => "bar", "schema" => "baz"));
        $entity->addPrimaryKey("id");
        $this->mapping->acceptEntity($entity, $this->metadataFactory);

        $this->assertEquals("baz", $this->mapping->schema);
    }

    public function testAcceptPrimaryKey_NoIdGenerator_SetDefaultMetadataFactoryGenerator()
    {
        $this->metadataFactory->expects($this->once())->method('getDefaultIdGeneratorClass')->will($this->returnValue("Zend_Entity_Definition_Id_UUID"));

        $entity = new Zend_Entity_Definition_Entity("foo");
        $entity->addPrimaryKey("id");
        $this->mapping->acceptEntity($entity, $this->metadataFactory);
        $this->mapping->acceptProperty($entity->getPropertyByName("id"), $this->metadataFactory);

        $this->assertType('Zend_Entity_Definition_Id_UUID', $this->mapping->primaryKey->getGenerator());
    }

    public function testAcceptPrimaryKey_NoIdGenerator_AutoIncrementConfigured()
    {
        $this->metadataFactory->expects($this->once())->method('getDefaultIdGeneratorClass')->will($this->returnValue("Zend_Db_Mapper_Id_AutoIncrement"));

        $entity = new Zend_Entity_Definition_Entity("foo", array("table" => "bar"));
        $entity->addPrimaryKey("id");
        $this->mapping->acceptEntity($entity, $this->metadataFactory);
        $this->mapping->acceptProperty($entity->getPropertyByName("id"), $this->metadataFactory);

        $this->assertType('Zend_Db_Mapper_Id_AutoIncrement', $this->mapping->primaryKey->getGenerator());
        $this->assertEquals('bar', $this->mapping->primaryKey->getGenerator()->getTableName());
        $this->assertEquals('id', $this->mapping->primaryKey->getGenerator()->getPrimaryKey());
    }

    public function testAcceptPrimaryKey_NoIdGenerator_SequenceConfigured()
    {
        $this->metadataFactory->expects($this->once())->method('getDefaultIdGeneratorClass')->will($this->returnValue("Zend_Db_Mapper_Id_Sequence"));

        $entity = new Zend_Entity_Definition_Entity("foo", array("table" => "bar"));
        $entity->addPrimaryKey("id");
        $this->mapping->acceptEntity($entity, $this->metadataFactory);
        $this->mapping->acceptProperty($entity->getPropertyByName("id"), $this->metadataFactory);

        $this->assertType('Zend_Db_Mapper_Id_Sequence', $this->mapping->primaryKey->getGenerator());
        $this->assertEquals('bar_id_seq', $this->mapping->primaryKey->getGenerator()->getSequenceName());
    }

    public function testAcceptPrimaryKey_NoIdGenerator_OracleSequenceLongerThan30Chars()
    {
        $this->markTestIncomplete();
    }
}