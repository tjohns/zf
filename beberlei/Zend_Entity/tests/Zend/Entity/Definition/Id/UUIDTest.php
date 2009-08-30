<?php

class Zend_Entity_Definition_Id_UUIDTest extends PHPUnit_Framework_TestCase
{
    public function testIsPrePersistGenerator()
    {
        $id = new Zend_Entity_Definition_Id_UUID();
        $this->assertTrue($id->isPrePersistGenerator());
    }

    public function testNextSequenceId()
    {
        $db = new Zend_Test_DbAdapter();

        $id = new Zend_Entity_Definition_Id_UUID();
        $uuid = $id->nextSequenceId($db);

        $this->assertEquals(13, strlen($uuid));
        $this->assertRegExp('/([a-zA-Z0-9]{13})/', $uuid);
    }
}