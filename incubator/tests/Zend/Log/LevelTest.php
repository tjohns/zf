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
class Zend_Log_LevelTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->logger = new Zend_Log();
        $this->mock = new Zend_Log_Writer_Mock();
        $this->logger->addWriter($this->mock);
    }


    public function testOverrideBuiltinLogLevel()
    {
        $e = null;
        try {
            $this->logger->addLevel('BOB', 0);
        } catch (Exception $e) {}

        $this->assertTrue($e instanceof Zend_Log_Exception);
    }


    public function testAddLogLevel()
    {
        $this->logger->addLevel('EIGHT', 8);

        $this->logger->emerg('emergency message');
        $this->logger->eight('eight message');

        $messages = $this->mock->flush();
        $this->assertEquals(2, count($messages));
        $this->assertEquals(Zend_Log::EMERG, $messages[0]['level']);
        $this->assertEquals(8, $messages[1]['level']);
    }

}
