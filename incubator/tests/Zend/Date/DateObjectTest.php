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
    	$this->assertTrue($date instanceof Zend_Date_DateObject, "expected date object");
    }

	/**
	 * Test for date object creation null value
	 */
    public function testCreationNull()
    {
    	$date = new Zend_Date_DateObject(0);
    	$this->assertTrue($date instanceof Zend_Date_DateObject, "expected date object");
    }

	/**
	 * Test for date object creation negative timestamp
	 */
    public function testCreationNegative()
    {
    	$date = new Zend_Date_DateObject(-1000);
    	$this->assertTrue($date instanceof Zend_Date_DateObject, "expected date object");
    }

	/**
	 * Test for date object creation text given
	 */
    public function testCreationFailed()
    {
        try {
        	$date = new Zend_Date_DateObject("notimestamp");
        	$this->assertFalse($date instanceof Zend_Date_DateObject, "exception expected");
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
       	$this->assertTrue($date->setTimestamp(0), "true expected");
    }

	/**
	 * Test for setTimestampLong
	 */
    public function testSetTimestampLong()
    {
      	$date = new Zend_Date_DateObject();
       	$this->assertTrue($date->setTimestamp("12345678901234567890"), "true expected");
    }

	/**
	 * Test for setTimestampFailed
	 */
    public function testSetTimestampFailed()
    {
        try {
        	$date = new Zend_Date_DateObject();
        	$date->setTimestamp("notimestamp");
        	$this->assertFalse($date instanceof Zend_Date_DateObject, "exception expected");
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
       	$this->assertSame($result, time(), time()." expected");
    }

	/**
	 * Test for mktime
	 */
    public function testMkTime()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject();
      	$result = $date->mktime(0,0,0);
       	$this->assertTrue($result < time(), " < ".time()." expected");
    }

	/**
	 * Test for mktime
	 */
    public function testMkTime2()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject();
      	$result = $date->mktime(23,59,59);
       	$this->assertTrue($result > time(), " > ".time()." expected");
    }

	/**
	 * Test for mktime
	 */
    public function testMkTime3()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject();
      	$result = $date->mktime(100,100,100);
       	$this->assertTrue($result > time(), " > ".time()." expected");
    }

	/**
	 * Test for mktime
	 */
    public function testMkTime4()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject();
      	$result = $date->mktime(0,0,0,1,1,1970, -1, true);
       	$this->assertSame($result, 0, "0 expected");
    }

	/**
	 * Test for mktime
	 */
    public function testMkTime5()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject();
      	$result = $date->mktime(0,0,0,1,1,1902,-1, true);
       	$this->assertSame($result, -2145916800, "-2145916800 expected");
    }

	/**
	 * Test for mktime
	 */
    public function testMkTime6()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject();
      	$result = $date->mktime(0,0,0,1,1,2037, -1, true);
       	$this->assertSame($result, 2114380800, "2114380800 expected");
    }

	/**
	 * Test for mktime
	 */
    public function testMkTime7()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject();
      	$result = $date->mktime(0,0,0,1,1,1800, -1, true);
       	$this->assertSame($result, -5364662400, "-5364662400 expected");
    }

	/**
	 * Test for mktime
	 */
    public function testMkTime8()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject();
      	$result = $date->mktime(0,0,0,1,1,1600, -1, true);
       	$this->assertSame($result, -11676960000, "-11676960000 expected");
    }

	/**
	 * Test for mktime
	 */
    public function testMkTime9()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject();
      	$result = $date->mktime(0,0,0,1,1,0, -1, true);
       	$this->assertSame($result, -62168256000, "-62168256000 expected");
    }

	/**
	 * Test for mktime
	 */
    public function testMkTime10()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject();
      	$result = $date->mktime(0,0,0,1,1,-999, -1, true);
       	$this->assertSame($result, -93693369600, "-93693369600 expected");
    }

	/**
	 * Test for mktime
	 */
    public function testMkTime11()
    {
        date_default_timezone_set('Europe/Paris');
      	$date = new Zend_Date_DateObject();
      	$result = $date->mktime(0,0,0,1,1,3000, -1, true);
       	$this->assertSame($result, 32503680000, "32503680000 expected");
    }

	/**
	 * Test for mktime
	 */
    public function testMkTime12()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject();
      	$result = $date->mktime(0,0,0,-9,1,1600, -1, true);
       	$this->assertSame($result, -11702534400, "-11702534400 expected");
    }

	/**
	 * Test for mktime
	 */
    public function testMkTime13()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject();
      	$result = $date->mktime(0,0,0,30,1,1600, -1, true);
       	$this->assertSame($result, -11599891200, "-11599891200 expected");
    }

	/**
	 * Test for mktime
	 */
    public function testMkTime14()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject();
      	$result = $date->mktime(0,0,0,10,7,1582, -1, true);
       	$this->assertSame($result, -12219292800, "-12219292800 expected");
    }

	/**
	 * Test for mktime
	 */
    public function testMkTime15()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject();
      	$result = $date->mktime(0,0,0,10,10,1800, -1, true);
       	$this->assertSame($result, -5340297600, "-5340297600 expected");
    }

	/**
	 * Test for mktime
	 */
    public function testMkTime16()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject();
      	$result = $date->mktime(0,0,0,10,10,3000, -1, true);
       	$this->assertSame($result, 32528044800, "32528044800 expected");
    }
}