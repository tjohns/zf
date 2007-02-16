<?php
/**
 * @package    Zend_Log
 * @subpackage UnitTests
 */


/** PHPUnit_Framework_TestCase */
require_once 'PHPUnit/Framework/TestCase.php';

/** Zend_Log */
require_once 'Zend/Log.php';

/** Zend_Log_Writer_Mock */
require_once 'Zend/Log/Writer/Mock.php';


/**
 * @package    Zend_Log
 * @subpackage UnitTests
 */
class Zend_Log_LogTest extends PHPUnit_Framework_TestCase
{
    public function testWriterInConstructor()
    {
        $mock = new Zend_Log_Writer_Mock();
        $logger = new Zend_Log($mock);
        $logger->log('message', Zend_Log::INFO);

        $messages = $mock->flush();
        $this->assertEquals(1, count($messages));
        $this->assertEquals('message', $messages[0]['message']);
    }

    public function testAddWriter()
    {
        $logger = new Zend_Log();
        $mock = new Zend_Log_Writer_Mock();
        $logger->addWriter($mock);
        $logger->log('message', Zend_Log::INFO);

        $messages = $mock->flush();
        $this->assertEquals(1, count($messages));
        $this->assertEquals('message', $messages[0]['message']);
    }

}
