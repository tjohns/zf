<?php

abstract class Zend_Test_PHPUnit_Database_DataSet_DataSetTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    protected $connectionMock = null;

    public function setUp()
    {
        $this->connectionMock = $this->getMock('Zend_Test_PHPUnit_Database_Connection', array(), array(), '', false);
    }

    public function decorateConnectionMockWithZendAdapter()
    {
        $this->decorateConnectionGetConnectionWith(new Zend_Test_DbAdapterMock());
    }

    public function decorateConnectionGetConnectionWith($returnValue)
    {
        $this->connectionMock->expects($this->any())
                             ->method('getConnection')
                             ->will($this->returnValue($returnValue));
    }
}