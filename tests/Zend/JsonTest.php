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
    /**
     * test null encoding/decoding
     * 
     * @access public
     * @return void
     */
	public function testNull()
	{
		$this->_testEncodeDecode(array(null));
	}

	
    /**
     * test boolean encoding/decoding
     * 
     * @access public
     * @return void
     */
    public function testBoolean()
    {
        $this->assertTrue(Zend_Json::decode(Zend_Json::encode(true)));
        $this->assertFalse(Zend_Json::decode(Zend_Json::encode(false)));
    }


    /**
     * test integer encoding/decoding
     * 
     * @access public
     * @return void
     */
	public function testInteger()
	{
		$this->_testEncodeDecode(array(-2));
		$this->_testEncodeDecode(array(-1));

        $zero = Zend_Json::decode(Zend_Json::encode(0));
		$this->assertEquals(0, $zero, 'Failed 0 integer test. Encoded: ' . serialize(Zend_Json::encode(0)));
	}


    /**
     * test float encoding/decoding
     * 
     * @access public
     * @return void
     */
	public function testFloat()
	{
		$this->_testEncodeDecode(array(-2.1, 1.2));
	}
	
	
    /**
     * test string encoding/decoding
     * 
     * @access public
     * @return void
     */
	public function testString()
	{
		$this->_testEncodeDecode(array('string'));
		$this->assertEquals('', Zend_Json::decode(Zend_Json::encode('')), 'Empty string encoded: ' . serialize(Zend_Json::encode('')));
	}

    /**
     * Test backslash escaping of string
     * 
     * @access public
     * @return void
     */
	public function testString2()
    {
        $string   = 'INFO: Path \\\\test\\123\\abc';
        $expected = '"INFO: Path \\\\\\\\test\\\\123\\\\abc"';
        $encoded = Zend_Json::encode($string);
		$this->assertEquals($expected, $encoded, 'Backslash encoding incorrect: expected: ' . serialize($expected) . '; received: ' . serialize($encoded) . "\n");
        $this->assertEquals($string, Zend_Json::decode($encoded));
    }
	
    /**
     * Test newline escaping of string
     * 
     * @access public
     * @return void
     */
	public function testString3()
    {
        $expected = '"INFO: Path\nSome more"';
        $string   = "INFO: Path\nSome more";
        $encoded  = Zend_Json::encode($string);
		$this->assertEquals($expected, $encoded, 'Newline encoding incorrect: expected ' . serialize($expected) . '; received: ' . serialize($encoded) . "\n");
        $this->assertEquals($string, Zend_Json::decode($encoded));
    }

    /**
     * Test tab/non-tab escaping of string
     * 
     * @access public
     * @return void
     */
	public function testString4()
    {
        $expected = '"INFO: Path\\t\\\\tSome more"';
        $string   = "INFO: Path\t\\tSome more";
        $encoded  = Zend_Json::encode($string);
		$this->assertEquals($expected, $encoded, 'Tab encoding incorrect: expected ' . serialize($expected) . '; received: ' . serialize($encoded) . "\n");
        $this->assertEquals($string, Zend_Json::decode($encoded));
    }

    /**
     * Test double-quote escaping of string
     * 
     * @access public
     * @return void
     */
	public function testString5()
    {
        $expected = '"INFO: Path \"Some more\""';
        $string   = 'INFO: Path "Some more"';
        $encoded  = Zend_Json::encode($string);
		$this->assertEquals($expected, $encoded, 'Quote encoding incorrect: expected ' . serialize($expected) . '; received: ' . serialize($encoded) . "\n");
        $this->assertEquals($string, Zend_Json::decode($encoded));
    }

    /**
     * test indexed array encoding/decoding
     * 
     * @access public
     * @return void
     */
	public function testArray()
	{
        $array = array(1, 'one', 2, 'two');
        $encoded = Zend_Json::encode($array);
        $this->assertSame($array, Zend_Json::decode($encoded), 'Decoded array does not match: ' . serialize($encoded));
	}

    /**
     * test associative array encoding/decoding
     * 
     * @access public
     * @return void
     */
    public function testAssocArray() 
    {
        $this->_testEncodeDecode(array(array('one' => 1, 'two' => 2)));
    }

    /**
     * test associative array encoding/decoding, with mixed key types
     * 
     * @access public
     * @return void
     */
    public function testAssocArray2() 
    {
        $this->_testEncodeDecode(array(array('one' => 1, 2 => 2)));
    }
	
    /**
     * test object encoding/decoding (decoding to array)
     * 
     * @access public
     * @return void
     */
	public function testObject()
	{
        $value = new stdClass();
        $value->one = 1;
        $value->two = 2;

        $array = array('__className' => 'stdClass', 'one' => 1, 'two' => 2);

        $encoded = Zend_Json::encode($value);
        $this->assertSame($array, Zend_Json::decode($encoded));
	}

    /**
     * test object encoding/decoding (decoding to stdClass)
     * 
     * @access public
     * @return void
     */
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
     * Test encoding and decoding in a single step
     *
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
