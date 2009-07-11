<?php

class Zend_Entity_Mapper_Definition_TestCase extends PHPUnit_Framework_TestCase
{
    const TEST_CLASS = "TestEntity";
    const TEST_CLASS2 = "TestEntity2";
    const TEST_TABLE = "TestTable";
    const TEST_PROPERTY = "prop1";
    const TEST_PROPERTY2 = "prop2";
    const TEST_COLUMN = 'TestTable_prop1';
    const TEST_COLUMN2 = 'TestTable_prop2';

    /**
     * @return Zend_Entity_Mapper_Definition_Entity
     */
    public function createEntityDefinitionMock()
    {
        return $this->getMock('Zend_Entity_Mapper_Definition_Entity');
    }

    /**
     *
     * @return Zend_Entity_MetadataFactory_Interface
     */
    public function createEntityResourceMock()
    {
        return $this->getMock('Zend_Entity_MetadataFactory_Interface');
    }

    /**
     * @return Zend_Entity_Mapper_Definition_Property
     */
    public function createPropertyMockWithCompileExpectation()
    {
        $propertyMock = $this->getMock('Zend_Entity_Mapper_Definition_Property');
        $propertyMock->expects($this->once())->method('compile');
        return $propertyMock;
    }

    /**
     * @param  string $name
     * @return Zend_Entity_Mapper_Definition_Join
     */
    public function createJoinDefinition($name)
    {
        return new Zend_Entity_Mapper_Definition_Join($name);
    }
}