<?php

class Zend_Entity_Definition_PrimaryKeyTest extends Zend_Entity_Definition_TestCase
{
    public function testSetGetGenerator()
    {
        $idMock = $this->getMock('Zend_Entity_Definition_Id_Interface');

        $primaryKeyDef = new Zend_Entity_Definition_PrimaryKey(self::TEST_PROPERTY);
        $primaryKeyDef->setGenerator($idMock);
        $this->assertEquals($idMock, $primaryKeyDef->getGenerator());
    }

    public function testColumnIsKeyName()
    {
        $primaryKeyDef = new Zend_Entity_Definition_PrimaryKey(self::TEST_PROPERTY);
        $primaryKeyDef->setColumnName(self::TEST_PROPERTY2);
        $this->assertEquals(self::TEST_PROPERTY2, $primaryKeyDef->getKey());
    }

    public function testSetGetColumnName()
    {
        $primaryKeyDef = new Zend_Entity_Definition_PrimaryKey(self::TEST_PROPERTY);
        $primaryKeyDef->setColumnName(self::TEST_PROPERTY2);

        $this->assertEquals(self::TEST_PROPERTY2, $primaryKeyDef->getColumnName());
    }
    
    public function testBuildPrimaryKeyWhereConditionWithValidValues()
    {
        $primaryKeyDef = $this->createPrimaryKeyWithColumn();
        $values = array(
            self::TEST_PROPERTY => 1
        );

        $expectedWhereCondition = self::TEST_TABLE.'.'.self::TEST_PROPERTY.' = 1';
        $actualWhereCondition = $primaryKeyDef->buildWhereCondition($this->getDatabaseAdapterMock(), self::TEST_TABLE, $values);
        $this->assertEquals($expectedWhereCondition, $actualWhereCondition);
    }

    public function testRemoveSequenceFromDatabaseState()
    {
        $primaryKeyDef = $this->createPrimaryKeyWithColumn();
        $entityDatabaseState = array(
            self::TEST_PROPERTY => 1,
            self::TEST_PROPERTY2 => "foo",
        );

        $entityDatabaseState = $primaryKeyDef->removeSequenceFromState($entityDatabaseState);

        $this->assertEquals("foo", $entityDatabaseState[self::TEST_PROPERTY2]);
        $this->assertFalse(isset($entityDatabaseState[self::TEST_PROPERTY]));
    }

    public function testGetEmptySequence()
    {
        $primaryKeyDef = $this->createPrimaryKeyWithColumn();
        $emptyKey = $primaryKeyDef->getEmptyKeyProperties();
        $expectedEmptyKey = array(self::TEST_PROPERTY => null);

        $this->assertTrue(is_array($emptyKey));
        $this->assertEquals($expectedEmptyKey, $emptyKey);
    }

    public function testRetrieveKeyValuesFromState()
    {
        $primaryKeyDef = $this->createPrimaryKeyWithColumn();
        $state = array(
            self::TEST_PROPERTY => 1,
            self::TEST_PROPERTY2 => "foo",
        );
        $pkValues = $primaryKeyDef->retrieveKeyValuesFromProperties($state);

        $this->assertEquals(1, $pkValues);
    }

    /**
     * @return Zend_Db_Adapter_Pdo_Mysql
     */
    public function getDatabaseAdapterMock()
    {
        $args = array("dbname" => "dbname", "password" => "password", "username" => "user");
        $db = new Zend_Entity_DbAdapterMock($args);
        return $db;
    }

    const MOCK_IDGEN_NEXTSEQUENCEID = 1;
    const MOCK_IDGEN_LASTSEQUENCEID = 2;

    /**
     * @param  int $mask
     * @param  int $sequenceId
     * @return Zend_Entity_Definition_PrimaryKey
     */
    public function createPrimaryKeyWithColumnAndIdGeneratorMock($mask, $sequenceId=1)
    {
        $primaryKeyDef = $this->createPrimaryKeyWithColumn();

        $idGeneratorMock = $this->getMock('Zend_Entity_Definition_Id_Interface');
        if( ($mask&self::MOCK_IDGEN_NEXTSEQUENCEID) > 0) {
            $idGeneratorMock->expects($this->once())->method('nextSequenceId')->will($this->returnValue($sequenceId));
        }
        if( ($mask&self::MOCK_IDGEN_LASTSEQUENCEID) > 0) {
            $idGeneratorMock->expects($this->once())->method('lastSequenceId')->will($this->returnValue($sequenceId));
        }
        $primaryKeyDef->setGenerator($idGeneratorMock);

        return $primaryKeyDef;
    }

    /**
     * @return Zend_Entity_Definition_PrimaryKey
     */
    public function createPrimaryKeyWithColumn()
    {
        $primaryKeyDef = new Zend_Entity_Definition_PrimaryKey(self::TEST_PROPERTY);
        $primaryKeyDef->setColumnName(self::TEST_PROPERTY);
        return $primaryKeyDef;
    }
}