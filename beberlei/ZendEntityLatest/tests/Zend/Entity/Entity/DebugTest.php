<?php

class Zend_Entity_DebugTest extends PHPUnit_Framework_TestCase
{
    public function testDumpEntityManager()
    {
        $em = $this->getMock('Zend_Entity_Manager_Interface');

        $expected = 'string(15) "*ENTITYMANAGER*"
';

        $this->assertDebugOutputEquals($expected, $em);
    }

    public function testDumpArrayWithEntityManager()
    {
        $em = $this->getMock('Zend_Entity_Manager_Interface');

        $data = array($em, "foo");

        $expected = 'array(2) {
  [0]=>
  string(15) "*ENTITYMANAGER*"
  [1]=>
  string(3) "foo"
}
';

        $this->assertDebugOutputEquals($expected, $data);
    }

    public function testDumpObjectWithEntityManager()
    {
        $em = $this->getMock('Zend_Entity_Manager_Interface');

        $data = new stdClass();
        $data->em = $em;
        $data->foo = "bar";

        $expected = 'array(3) {
  ["__CLASSNAME__"]=>
  string(8) "stdClass"
  ["em"]=>
  string(15) "*ENTITYMANAGER*"
  ["foo"]=>
  string(3) "bar"
}
';

        $this->assertDebugOutputEquals($expected, $data);
    }

    public function testDumpObjectWithPrivateAttributes()
    {
        $em = $this->getMock('Zend_Entity_Manager_Interface');
        $data = new Zend_Entity_TestDebug($em, "val");

        $expected = 'array(3) {
  ["__CLASSNAME__"]=>
  string(21) "Zend_Entity_TestDebug"
  ["em"]=>
  string(15) "*ENTITYMANAGER*"
  ["val"]=>
  string(3) "val"
}
';
        $this->assertDebugOutputEquals($expected, $data);
    }

    public function testDumpObjectChildWithPrivateAttributes()
    {
        $em = $this->getMock('Zend_Entity_Manager_Interface');
        $data = new Zend_Entity_TestDebugChild($em, "val");

        $expected = 'array(2) {
  ["__CLASSNAME__"]=>
  string(26) "Zend_Entity_TestDebugChild"
  ["val"]=>
  string(3) "val"
}
';
        $this->assertDebugOutputEquals($expected, $data);
    }

    public function assertDebugOutputEquals($expected, $object)
    {
        ob_start();
        Zend_Entity_Debug::dump($object);
        $output = ob_get_clean();
        $this->assertEquals($expected, $output);
    }

    public function tearDown()
    {
        while(ob_get_level() > 0) {
            ob_end_flush();
        }
    }
}

class Zend_Entity_TestDebug
{
    private $em = null;
    protected $val;

    public function __construct($em, $val)
    {
        $this->em = $em;
        $this->val = $val;
    }
}

class Zend_Entity_TestDebugChild extends Zend_Entity_TestDebug
{

}