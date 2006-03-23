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
		$this->_testEncodeDecode( array(null) );
	}

	
    public function testBoolean()
    {
		$this->_testEncodeDecode( array(true,
								 	    false) );
    }


	public function testInteger()
	{
		$this->_testEncodeDecode( array(-2,
			                             0,
			                            -1) );
	}


	public function testFloat()
	{
		$this->_testEncodeDecode( array( -2.1,
			                              1.2) );
	}
	
	
	public function testString()
	{
		$this->_testEncodeDecode( array( '',
		                                 'string') );
	}
	
	
	public function testArray()
	{
        $this->_testEncodeDecode(array(array(1, 'one', 2, 'two')));
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

        $array = array('one' => 1, 'two' => 2);

        $encoded = Zend_Json::encode($value);
        $this->assertEquals($array, Zend_Json::decode($array));
	}

    public function testObjectAsObject()
    {
        $value = new StdClass();
        $value->one = 1;
        $value->two = 2;

        $encoded = Zend_Json::encode($value);
        $this->assertEquals($array, Zend_Json::decode($array, Zend_Json::TYPE_OBJECT));
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
