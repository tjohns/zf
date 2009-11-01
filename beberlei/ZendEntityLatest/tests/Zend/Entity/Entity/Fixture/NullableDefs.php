<?php

class Zend_Entity_Fixture_NullableDefs extends Zend_Entity_Fixture_Abstract
{
    protected $definitionCreationMethods = array(
        'createClassBDefinition', 'createClassADefinition',
    );

    static public function createClassADefinition()
    {
        $def = new Zend_Entity_Definition_Entity("Zend_TestEntity1");
        $def->setTable("entities1");

        $def->addPrimaryKey("id", array(
            'columnName' => "a_id",
            'propertyType' => Zend_Entity_Definition_Property::TYPE_INT
        ));
        $def->addProperty("property", array(
                'columnName' => "a_property"
            ));
        $def->addManyToOneRelation("manytoone1", array(
            'columnName' => "a_manytoone1",
            'class' => "Zend_TestEntity2",
            'nullable' => true,
        ));
        $def->addManyToOneRelation("manytoone2", array(
            'columnName' => "a_manytoone2",
            'class' => "Zend_TestEntity2",
            'nullable' => false,
        ));

        return $def;
    }

    static public function createClassBDefinition()
    {
        $def = new Zend_Entity_Definition_Entity("Zend_TestEntity2");
        $def->setTable("entities2");
        $def->addPrimaryKey("id", array(
            'columnName' => "b_id",
            'propertyType' => Zend_Entity_Definition_Property::TYPE_INT
        ));
        $def->addProperty("property", array(
                'columnName' => "b_property"
        ));

        return $def;
    }

    public function createLoader($def)
    {
        $mi = $this->resourceMap->transform('Zend_Db_Mapper_Mapping');
        return new Zend_Db_Mapper_Loader_Entity($this->entityManager, $mi[$def->getClass()]);
    }
}