<?php

/**
 * @package    Zend_Json
 * @subpackage UnitTests
 */


/**
 * Zend_Json
 */
require_once 'Zend/Json.php';

/**
 * PHPUnit2 Test Case
 */
require_once 'PHPUnit2/Framework/TestCase.php';


/**
 * @package    Zend_Feed
 * @subpackage UnitTests
 *
 */
class Zend_JsonTest extends PHPUnit2_Framework_TestCase
{
	public function testNull()
	{
		$this->_testEncodeDecode(array(null));
	}

	
    public function testBoolean()
    {
        $this->assertTrue(Zend_Json::decode(Zend_Json::encode(true)));
        $this->assertFalse(Zend_Json::decode(Zend_Json::encode(false)));
    }


	public function testInteger()
	{
		$this->_testEncodeDecode(array(-2));
		$this->_testEncodeDecode(array(-1));

        $zero = Zend_Json::decode(Zend_Json::encode(0));
		$this->assertEquals(0, $zero, 'Failed 0 integer test. Encoded: ' . serialize(Zend_Json::encode(0)));
	}


	public function testFloat()
	{
		$this->_testEncodeDecode(array(-2.1, 1.2));
	}
	
	
	public function testString()
	{
		$this->_testEncodeDecode(array('string'));
		$this->assertEquals('', Zend_Json::decode(Zend_Json::encode('')), 'Empty string encoded: ' . serialize(Zend_Json::encode('')));
	}
	
	
	public function testArray()
	{
        $array = array(1, 'one', 2, 'two');
        $encoded = Zend_Json::encode($array);
        $this->assertSame($array, Zend_Json::decode($encoded), 'Decoded array does not match: ' . serialize($encoded));
	}

    public function testAssocArray() 
    {
        $this->_testEncodeDecode(array(array('one' => 1, 'two' => 2)));
    }
	
	
	public function testObject()
	{
        $value = new StdClass();
        $value->one = 1;
        $value->two = 2;

        $array = array('__className' => 'stdClass', 'one' => 1, 'two' => 2);

        $encoded = Zend_Json::encode($value);
        $this->assertSame($array, Zend_Json::decode($encoded));
	}

    public function testObjectAsObject()
    {
        $value = new stdClass();
        $value->one = 1;
        $value->two = 2;

        $encoded = Zend_Json::encode($value);
        $decoded = Zend_Json::decode($encoded, Zend_Json::TYPE_OBJECT);
        $this->assertTrue(is_object($decoded), 'Not decoded as an object');
        $this->assertTrue($decoded instanceof StdClass, 'Not a StdClass object');
        $this->assertTrue(isset($decoded->one), 'Expected property not set');
        $this->assertEquals($value->one, $decoded->one, 'Unexpected value');
    }
	
	/**
	 * @param array $values   array of values to test against encode/decode 
	 */
	protected function _testEncodeDecode($values) 
	{
		foreach ($values as $value) {
			$encoded = Zend_Json::encode($value);
			$this->assertEquals($value, Zend_Json::decode($encoded));
		}
	}
}
