<?php

class Zend_Entity_Fixture_CollectionElementDefs extends Zend_Entity_Fixture_Abstract
{
    protected $definitionCreationMethods = array('createCollectionElementDefinition');

    public function createCollectionElementDefinition()
    {
        $def = new Zend_Entity_Definition_Entity("Zend_TestEntity1");
        $def->setTable("entities");
        $def->addPrimaryKey("id");
        $def->addCollection("elements", array(
            'mapKey' => 'col_key',
            'element' => 'col_name',
            'table' => 'entities_elements',
            'key' => 'fk_id',
            "fetch" => "select",
        ));

        return $def;
    }
}
