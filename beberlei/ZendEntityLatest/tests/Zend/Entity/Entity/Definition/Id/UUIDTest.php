<?php

class Zend_Entity_Definition_Id_UUIDTest extends Zend_Entity_TestCase
{
    public function testIsPrePersistGenerator()
    {
        $id = new Zend_Entity_Definition_Id_UUID();
        $this->assertTrue($id->isPrePersistGenerator());
    }

    public function testGenerateId()
    {
        $em = $this->createEntityManager();

        $id = new Zend_Entity_Definition_Id_UUID();
        $uuid = $id->generate($em, new stdClass());

        $this->assertEquals(13, strlen($uuid));
        $this->assertRegExp('/([a-zA-Z0-9]{13})/', $uuid);
    }
}