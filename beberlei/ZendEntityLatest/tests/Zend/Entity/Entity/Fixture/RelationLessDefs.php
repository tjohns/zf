<?php

class Zend_Entity_Fixture_RelationLessDefs extends Zend_Entity_Fixture_Abstract
{
    protected $definitionCreationMethods = array('createEntityDefinition');

    public function createEntityDefinition()
    {
        $def = new Zend_Entity_Definition_Entity("Zend_TestEntity1", array("table" => "entities"));
        $def->addPrimaryKey('id', array('columnName' => 'entities_id'));
        $def->addProperty('foo');
        $def->addProperty('bar', array('columnName' => 'baz'));
        return $def;
    }
}