<?php

class Zend_Entity_Fixture_SelfReferenceDefs extends Zend_Entity_Fixture_Abstract
{
    protected $definitionCreationMethods = array(
        'createTestEntity'
    );

    static public function createTestEntity()
    {
        $def = new Zend_Entity_Definition_Entity('Zend_TestEntity1');
        $def->addPrimaryKey('id', array('columnName' => 'a_id'));
        $def->addProperty('name', array('columnName' => 'a_name'));
        $def->addManyToOneRelation('mother', array(
            'class' => 'Zend_TestEntity1',
            'columnName' => 'mother_id',
        ));
        $def->addManyToOneRelation('father', array(
            'class' => 'Zend_TestEntity1',
            'columnName' => 'father_id',
        ));

        return $def;
    }
}