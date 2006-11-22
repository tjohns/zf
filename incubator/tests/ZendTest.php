<?php
/**
 * @package    Zend
 * @subpackage UnitTests
 */


/** Zend */
require_once 'Zend.php';

/** PHPUnit_Framework_TestCase */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @package    Zend
 * @subpackage UnitTests
 */
class ZendTest extends PHPUnit_Framework_TestCase
{
    public function testIsReadable()
    {
        $this->assertTrue(Zend::isReadable(__FILE__));
        $this->assertFalse(Zend::isReadable(__FILE__ . '.foobaar'));
    }

    public function testException()
    {
        $this->assertTrue(Zend::exception('Zend_Exception') instanceof Exception);

        try {
            $e = Zend::exception('Zend_FooBar_Baz', 'should fail');
            $this->fail('invalid exception class should throw exception');
        } catch (Exception $e) {
            // success...
        }
    }
}
