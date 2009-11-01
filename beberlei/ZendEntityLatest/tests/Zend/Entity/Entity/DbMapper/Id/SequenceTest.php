<?php

class Zend_Db_Mapper_Id_SequenceTest extends Zend_Entity_TestCase
{
    public $generator;

    public function setUp()
    {
        $this->generator = new Zend_Db_Mapper_Id_Sequence();
    }

    public function testDefaultSequenceNameIsNull()
    {
        $this->assertNull($this->generator->getSequenceName());
    }

    public function testSetGetSequenceName()
    {
        $this->generator->setSequenceName("Foo");
        $this->assertEquals("Foo", $this->generator->getSequenceName());
    }

    public function testIsPrePersistGenerator()
    {
        $this->assertTrue($this->generator->isPrePersistGenerator());
    }

    public function testConstructWithSequenceName()
    {
        $this->generator = new Zend_Db_Mapper_Id_Sequence("Foo");
        $this->assertEquals("Foo", $this->generator->getSequenceName());
    }

    public function testGenerateId()
    {
        $sequenceName = "Foo";
        $sequenceValue = 100;

        $this->generator->setSequenceName($sequenceName);

        $dbMock = $this->getMock('Zend_Test_DbAdapter');
        $dbMock->expects($this->at(0))
               ->method('nextSequenceId')
               ->with($this->equalTo($sequenceName))
               ->will($this->returnValue($sequenceValue));

        $em = $this->wrapDbAdapter($dbMock);

        $this->assertEquals($sequenceValue, $this->generator->generate($em, new stdClass()));
    }

    protected function wrapDbAdapter($dbMock)
    {
        $mapper = $this->createMapper($dbMock);
        $em = $this->createEntityManager();
        $em->setMapper($mapper);
        return $em;
    }
}