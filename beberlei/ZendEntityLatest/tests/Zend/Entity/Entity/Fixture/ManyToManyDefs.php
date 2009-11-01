<?php

class Zend_Entity_Fixture_ManyToManyDefs extends Zend_Entity_Fixture_Abstract
{
    protected $definitionCreationMethods = array(
        'createClassADefinition', 'createClassBDefinition'
    );

    const TEST_A_CLASS = 'Zend_TestEntity1';
    const TEST_A_TABLE = 'table_a';
    const TEST_A_ID = 'id';
    const TEST_A_ID_COLUMN = 'a_id';
    const TEST_A_MANYTOMANY = 'manytomany';

    const TEST_A_MANYTOMANY_TABLE = 'manytomany_table';
    const TEST_A_JOINTABLE_KEY = 'a_fkey';

    const TEST_B_CLASS = 'Zend_TestEntity2';
    const TEST_B_TABLE = 'table_b';
    const TEST_B_ID = 'id';
    const TEST_B_ID_COLUMN = 'b_id';
    const TEST_B_JOINTABLE_KEY = 'b_fkey';

    public function createClassADefinition()
    {
        $def = new Zend_Entity_Definition_Entity(self::TEST_A_CLASS);
        $def->setTable(self::TEST_A_TABLE);

        $def->addPrimaryKey(self::TEST_A_ID, array(
            'columnName' => self::TEST_A_ID_COLUMN,
            'propertyType' => Zend_Entity_Definition_Property::TYPE_INT
        ));
        $def->addCollection(self::TEST_A_MANYTOMANY, array(
            'relation' => new Zend_Entity_Definition_ManyToManyRelation(self::TEST_A_MANYTOMANY, array(
                'class' => self::TEST_B_CLASS,
                'columnName' => self::TEST_B_JOINTABLE_KEY,
                'inverse' => false,
            )),
            'table' => self::TEST_A_MANYTOMANY_TABLE,
            'key' => self::TEST_A_JOINTABLE_KEY,
        ));

        return $def;
    }

    public function createClassBDefinition()
    {
        $def = new Zend_Entity_Definition_Entity(self::TEST_B_CLASS);
        $def->setTable(self::TEST_B_TABLE);
        $def->addPrimaryKey(self::TEST_B_ID, array('columnName' => self::TEST_B_ID_COLUMN));

        return $def;
    }
}