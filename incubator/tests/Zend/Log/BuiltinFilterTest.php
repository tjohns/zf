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
class Zend_Log_BuiltinFilterTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->logger = new Zend_Log();
        $this->mock1 = new Zend_Log_Writer_Mock();
        $this->logger->addWriter($this->mock1);
    }


    public function testWarnFilterAllWriters()
    {
        // filter out anything above a WARNing for all writers
        $this->logger->addFilter(Zend_Log::WARN);

        $this->logger->info('will be ignored');
        $this->logger->warn('will be logged');

        $messages = $this->mock1->flush();
        $this->assertEquals(1, count($messages));
        $message = $messages[0];
        $this->assertEquals(Zend_Log::WARN, $message['level']);
        $this->assertEquals('will be logged', $message['message']);
    }


    public function testErrFilterSingleWriter()
    {
        $mock2 = new Zend_Log_Writer_Mock();
        $mock2->addFilter(Zend_Log::ERR);
        $this->logger->addWriter($mock2);

        $this->logger->warn('will be logged by mock1');
        $this->logger->err('will be logged by both');

        $warnMessages = $this->mock1->flush();
        $errMessages = $mock2->flush();

        $this->assertEquals(2, count($warnMessages));
        $this->assertEquals(1, count($errMessages));
    }

}
