<?php

class Zend_Entity_Definition_Id_AutoIncrementTest extends Zend_Entity_TestCase
{
    public function testNextSequenceIdReturnsNull()
    {
        $db = new Zend_Entity_DbAdapterMock();
        $idGenerator = new Zend_Entity_Definition_Id_AutoIncrement();

        $this->assertNull($idGenerator->nextSequenceId($db));
    }

    public function testLastSequenceIdReturnsMysqLastInsertId()
    {
        $lastInsertId = 1;

        $db = $this->getMock('Zend_Entity_DbAdapterMock');
        $db->expects($this->once())->method('lastInsertId')->will($this->returnValue($lastInsertId));
        $idGenerator = new Zend_Entity_Definition_Id_AutoIncrement();

        $this->assertEquals($lastInsertId, $idGenerator->lastSequenceId($db));
    }

    public function testIsNotPrePersistGenerator()
    {
        $idGenerator = new Zend_Entity_Definition_Id_AutoIncrement();
        $this->assertFalse($idGenerator->isPrePersistGenerator());
    }
}