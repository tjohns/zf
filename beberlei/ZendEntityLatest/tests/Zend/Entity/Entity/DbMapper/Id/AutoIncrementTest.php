<?php

class Zend_Db_Mapper_Id_AutoIncrementTest extends Zend_Entity_TestCase
{
    /**
     * @var Zend_Db_Mapper_Id_AutoIncrement
     */
    public $generator = null;

    public function setUp()
    {
        $this->generator = new Zend_Db_Mapper_Id_AutoIncrement();
    }

    public function testLastSequenceIdReturnsMysqLastInsertId()
    {
        $lastInsertId = 1;
        $tableName = "foo";
        $pkName = "bar";

        $this->generator->setTableName($tableName);
        $this->generator->setPrimaryKey($pkName);

        $db = $this->getMock('Zend_Entity_DbAdapterMock');
        $db->expects($this->once())
           ->method('lastInsertId')
           ->with($this->equalTo($tableName), $this->equalTo($pkName))
           ->will($this->returnValue($lastInsertId));

        $em = $this->wrapDbAdapter($db);

        $this->assertEquals($lastInsertId, $this->generator->generate($em, new stdClass()));
    }

    public function testIsNotPrePersistGenerator()
    {
        $this->assertFalse($this->generator->isPrePersistGenerator());
    }

    public function testSetGetTableName()
    {
        $this->assertNull($this->generator->getTableName());
        $this->generator->setTableName("foo");
        $this->assertEquals("foo", $this->generator->getTableName());
    }

    public function testSetGetPrimaryKey()
    {
        $this->assertNull($this->generator->getPrimaryKey());
        $this->generator->setPrimaryKey("foo");
        $this->assertEquals("foo", $this->generator->getPrimaryKey());
    }

    protected function wrapDbAdapter($dbMock)
    {
        $mapper = $this->createMapper($dbMock);
        $em = $this->createEntityManager();
        $em->setMapper($mapper);
        return $em;
    }
}