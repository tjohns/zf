<?php

class Zend_Entity_Mapper_SelectTest extends PHPUnit_Framework_TestCase
{
    const KNOWN_CLASS = 'foo';

    public function testExecuteSelectQuery()
    {
        $dbAdapter = $this->getMock('Zend_Test_DbAdapterMock');
        $mapperMock = $this->getMock('Zend_Entity_Mapper', array(), array(), '', false);
        $mapperMock->expects($this->once())
                   ->method('find');

        $testingManager = new Zend_Entity_TestManagerMock($dbAdapter);
        $testingManager->addMapper(self::KNOWN_CLASS, $mapperMock);

        $select = new Zend_Entity_Mapper_Select($dbAdapter, $mapperMock);
        $select->setEntityManager($testingManager);

        $select->execute();
    }

    public function testExecuteSelect_NoEntityManagerConnected()
    {
        $this->setExpectedException("Exception");

        $dbAdapter = $this->getMock('Zend_Test_DbAdapterMock');
        $mapperMock = $this->getMock('Zend_Entity_Mapper', array(), array(), '', false);
        $mapperMock->expects($this->never())
                   ->method('find');

        $select = new Zend_Entity_Mapper_Select($dbAdapter, $mapperMock);

        $select->execute();
    }
}