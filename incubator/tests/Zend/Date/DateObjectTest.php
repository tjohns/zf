<?php
/**
 * @package    Zend_Date
 * @subpackage UnitTests
 */


/**
 * Zend_Date
 */
require_once 'Zend/Date/DateObject.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @package    Zend_Date
 * @subpackage UnitTests
 */
class Zend_Date_DateObjectTest extends PHPUnit_Framework_TestCase
{

	/**
	 * Test for date object creation empty value
	 */
    public function testCreation()
    {
    	$date = new Zend_Date_DateObject();
    	$this->assertTrue($date instanceof Zend_Date_DateObject);
    }

	/**
	 * Test for date object creation null value
	 */
    public function testCreationNull()
    {
    	$date = new Zend_Date_DateObject(0);
    	$this->assertTrue($date instanceof Zend_Date_DateObject);
    }

	/**
	 * Test for date object creation negative timestamp
	 */
    public function testCreationNegative()
    {
    	$date = new Zend_Date_DateObject(-1000);
    	$this->assertTrue($date instanceof Zend_Date_DateObject);
    }

	/**
	 * Test for date object creation text given
	 */
    public function testCreationFailed()
    {
        try {
        	$date = new Zend_Date_DateObject("notimestamp");
        	$this->assertFalse($date instanceof Zend_Date_DateObject);
        } catch (Exception $e) {
            return true;
        }
    }

	/**
	 * Test for setTimestamp
	 */
    public function testSetTimestamp()
    {
      	$date = new Zend_Date_DateObject();
       	$this->assertTrue($date->setTimestamp(0));
    }

	/**
	 * Test for setTimestampLong
	 */
    public function testSetTimestampLong()
    {
      	$date = new Zend_Date_DateObject();
       	$this->assertTrue($date->setTimestamp("12345678901234567890"));
    }

	/**
	 * Test for setTimestampFailed
	 */
    public function testSetTimestampFailed()
    {
        try {
        	$date = new Zend_Date_DateObject();
        	$date->setTimestamp("notimestamp");
        	$this->assertFalse($date instanceof Zend_Date_DateObject);
        } catch (Exception $e) {
            return true;
        }
    }

	/**
	 * Test for getTimestamp
	 */
    public function testGetTimestamp()
    {
      	$date = new Zend_Date_DateObject();
      	$result = $date->getTimestamp();
       	$this->assertSame($result, time());
    }

	/**
	 * Test for mktime
	 */
    public function testMkTime()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject();
      	$result = $date->mktime(0,0,0);
       	$this->assertTrue($result < time());
    }

	/**
	 * Test for mktime
	 */
    public function testMkTime2()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject();
      	$result = $date->mktime(23,50,50);
       	$this->assertTrue($result > time());
    }

	/**
	 * Test for mktime
	 */
    public function testMkTime3()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject();
      	$result = $date->mktime(100,100,100);
       	$this->assertTrue($result > time());
    }

	/**
	 * Test for mktime
	 */
    public function testMkTime4()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject();
      	$result = $date->mktime(0,0,0,1,1,1970);
       	$this->assertTrue($result < time());
    }

	/**
	 * Test for mktime
	 */
    public function testMkTime5()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject();
      	$result = $date->mktime(0,0,0,1,1,1902);
       	$this->assertTrue($result < time());
    }

	/**
	 * Test for mktime
	 */
    public function testMkTime6()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject();
      	$result = $date->mktime(0,0,0,1,1,2037);
       	$this->assertTrue($result > time());
    }

	/**
	 * Test for mktime
	 */
    public function testMkTime7()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject();
      	$result = $date->mktime(0,0,0,1,1,1800);
       	$this->assertTrue($result < time());
    }

	/**
	 * Test for mktime
	 */
    public function testMkTime8()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject();
      	$result = $date->mktime(0,0,0,1,1,1600);
       	$this->assertTrue($result < time());
    }

	/**
	 * Test for mktime
	 */
    public function testMkTime9()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject();
      	$result = $date->mktime(0,0,0,1,1,0);
       	$this->assertTrue($result < time());
    }

	/**
	 * Test for mktime
	 */
    public function testMkTime10()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject();
      	$result = $date->mktime(0,0,0,1,1,-2000);
       	$this->assertTrue($result < time());
    }

	/**
	 * Test for mktime
	 */
    public function testMkTime11()
    {
        date_default_timezone_set('Europe/Paris');
      	$date = new Zend_Date_DateObject();
      	$result = $date->mktime(0,0,0,1,1,3000);
       	$this->assertTrue($result > time());
    }
}