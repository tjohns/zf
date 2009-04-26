<?php

abstract class Zend_Entity_Mapper_Loader_ManyToOneFixture extends Zend_Entity_Mapper_Loader_TestCase
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

    public function setUp()
    {
        $this->resourceMap = new Zend_Entity_Resource_Testing();
        $this->resourceMap->addDefinition( $this->createClassBDefinition() );
        $this->resourceMap->addDefinition( $this->createClassADefinition() );
    }

    public function createClassADefinition()
    {
        $def = new Zend_Entity_Mapper_Definition_Entity(self::TEST_A_CLASS);
        $def->setTable(self::TEST_A_TABLE);

        $def->addPrimaryKey(self::TEST_A_ID, array('columnName' => self::TEST_A_ID_COLUMN));
        $def->addProperty(self::TEST_A_PROPERTY, array('columnName' => self::TEST_A_PROPERTY_COLUMN));
        $def->addManyToOneRelation(self::TEST_A_MANYTOONE, array('columnName' => self::TEST_A_MANYTOONE_COLUMN, 'class' => self::TEST_B_CLASS));

        return $def;
    }

    public function createClassBDefinition()
    {
        $def = new Zend_Entity_Mapper_Definition_Entity(self::TEST_B_CLASS);
        $def->setTable(self::TEST_B_TABLE);
        $def->addPrimaryKey(self::TEST_B_ID, array('columnName' => self::TEST_B_ID_COLUMN));
        $def->addProperty(self::TEST_B_PROPERTY, array('columnName' => self::TEST_B_PROPERTY_COLUMN));

        return $def;
    }

    abstract public function createLoader($definition);


    public function getClassALoader()
    {
        return $this->createLoader($this->resourceMap->getDefinitionByEntityName(self::TEST_A_CLASS));
    }

    public function getClassBLoader()
    {
        return $this->createLoader($this->resourceMap->getDefinitionByEntityName(self::TEST_B_CLASS));
    }

    const DUMMY_DATA_ID = 1;
    const DUMMY_DATA_PROPERTY = 'foo';
    const DUMMY_DATA_MANYTOONE = '1';

    public function getDummyDataRowClassA()
    {
        return array(
            self::TEST_A_ID_COLUMN => self::DUMMY_DATA_ID,
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