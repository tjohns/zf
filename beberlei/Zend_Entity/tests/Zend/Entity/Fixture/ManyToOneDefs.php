<?php

class Zend_Entity_Fixture_ManyToOneDefs extends Zend_Entity_Fixture_Abstract
{
    const TEST_A_CLASS = 'Zend_TestEntity1';
    const TEST_A_TABLE = 'table_a';
    const TEST_A_ID = 'id';
    const TEST_A_ID_COLUMN = 'a_id';
    const TEST_A_PROPERTY = 'property';
    const TEST_A_PROPERTY_COLUMN = 'a_property';
    const TEST_A_MANYTOONE = 'manytoone';
    const TEST_A_MANYTOONE_COLUMN = 'a_manytoone';

    const TEST_B_CLASS = 'Zend_TestEntity2';
    const TEST_B_TABLE = 'table_b';
    const TEST_B_ID = 'id';
    const TEST_B_ID_COLUMN = 'b_id';
    const TEST_B_PROPERTY = 'property';
    const TEST_B_PROPERTY_COLUMN = 'b_property';

    const DUMMY_DATA_ID = 1;
    const DUMMY_DATA_PROPERTY = 'foo';
    const DUMMY_DATA_MANYTOONE = '1';

    protected $definitionCreationMethods = array(
        'createClassBDefinition', 'createClassADefinition',
    );

    public function createClassADefinition()
    {
        $def = new Zend_Entity_Definition_Entity(self::TEST_A_CLASS);
        $def->setTable(self::TEST_A_TABLE);

        $def->addPrimaryKey(self::TEST_A_ID, array(
            'columnName' => self::TEST_A_ID_COLUMN,
            'propertyType' => Zend_Entity_Definition_Property::TYPE_INT
        ));
        $def->addProperty(self::TEST_A_PROPERTY, array('columnName' => self::TEST_A_PROPERTY_COLUMN));
        $def->addManyToOneRelation(self::TEST_A_MANYTOONE, array('columnName' => self::TEST_A_MANYTOONE_COLUMN, 'class' => self::TEST_B_CLASS));

        return $def;
    }

    public function createClassBDefinition()
    {
        $def = new Zend_Entity_Definition_Entity(self::TEST_B_CLASS);
        $def->setTable(self::TEST_B_TABLE);
        $def->addPrimaryKey(self::TEST_B_ID, array(
            'columnName' => self::TEST_B_ID_COLUMN,
            'propertyType' => Zend_Entity_Definition_Property::TYPE_INT
        ));
        $def->addProperty(self::TEST_B_PROPERTY, array('columnName' => self::TEST_B_PROPERTY_COLUMN));

        return $def;
    }
    
    public function createLoader($def)
    {
        return new Zend_Entity_Mapper_Loader_Basic($def);
    }


    public function getClassALoader()
    {
        return $this->createLoader($this->resourceMap->getDefinitionByEntityName(self::TEST_A_CLASS));
    }

    public function getClassBLoader()
    {
        return $this->createLoader($this->resourceMap->getDefinitionByEntityName(self::TEST_B_CLASS));
    }

    public function getDummyDataRowClassA($id=self::DUMMY_DATA_ID)
    {
        return array(
            self::TEST_A_ID_COLUMN => $id,
            self::TEST_A_PROPERTY_COLUMN => self::DUMMY_DATA_PROPERTY,
            self::TEST_A_MANYTOONE_COLUMN => self::DUMMY_DATA_MANYTOONE,
        );
    }

    public function getDummyDataStateClassA()
    {
        return array(
            self::TEST_A_ID => self::DUMMY_DATA_ID,
            self::TEST_A_PROPERTY => self::DUMMY_DATA_PROPERTY,
            self::TEST_A_MANYTOONE => self::DUMMY_DATA_MANYTOONE
        );
    }

    public function getDummyDataRowClassB()
    {
        return array(self::TEST_B_ID_COLUMN => self::DUMMY_DATA_ID, self::TEST_B_PROPERTY_COLUMN => self::DUMMY_DATA_PROPERTY);
    }

    public function getDummyDataStateClassB()
    {
        return array(self::TEST_B_ID => self::DUMMY_DATA_ID, self::TEST_B_PROPERTY => self::DUMMY_DATA_PROPERTY);
    }
}