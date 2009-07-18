<?php

class Zend_Entity_Fixture_SimpleFixtureDefs extends Zend_Entity_Fixture_Abstract
{
    protected $definitionCreationMethods = array(
        'createClassADefinition', 'createClassBDefinition'
    );

    const TEST_A_CLASS = 'Zend_TestEntity1';
    const TEST_A_TABLE = 'table_a';
    const TEST_A_ID = 'id';
    const TEST_A_ID_COLUMN = 'a_id';
    const TEST_A_PROPERTY = 'property';
    const TEST_A_PROPERTY_COLUMN = 'a_property';

    public function setUp()
    {
        $this->resourceMap = new Zend_Entity_MetadataFactory_Testing();
        $this->resourceMap->addDefinition( $this->createClassADefinition() );
    }

    public function createClassADefinition()
    {
        $def = new Zend_Entity_Definition_Entity(self::TEST_A_CLASS);
        $def->setTable(self::TEST_A_TABLE);

        $def->addPrimaryKey(self::TEST_A_ID, array('columnName' => self::TEST_A_ID_COLUMN));
        $def->addProperty(self::TEST_A_PROPERTY, array('columnName' => self::TEST_A_PROPERTY_COLUMN));

        return $def;
    }

    const DUMMY_DATA_ID = 1;
    const DUMMY_DATA_PROPERTY = 'foo';

    public function getDummyDataRow()
    {
        return array(self::TEST_A_ID_COLUMN => self::DUMMY_DATA_ID, self::TEST_A_PROPERTY_COLUMN => self::DUMMY_DATA_PROPERTY);
    }

    public function getDummyDataState()
    {
        return array(self::TEST_A_ID => self::DUMMY_DATA_ID, self::TEST_A_PROPERTY => self::DUMMY_DATA_PROPERTY);
    }
}