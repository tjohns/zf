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

    public function setUp()
    {
        date_default_timezone_set('Europe/Paris');
    }

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
    public function testMkTimeforTimeValues()
    {
        $date = new Zend_Date_DateObject();
        
        $this->assertSame($date->mktime(  0,   0,   0), mktime(  0,   0,   0));
        $this->assertSame($date->mktime( 23,  59,  59), mktime( 23,  59,  59));
        $this->assertSame($date->mktime(100, 100, 100), mktime(100, 100, 100));

        $this->assertSame($date->mktime(  0,   0,   0, false, false, false, -1, true), gmmktime(  0,   0,   0));
        $this->assertSame($date->mktime( 23,  59,  59, false, false, false, -1, true), gmmktime( 23,  59,  59));
        $this->assertSame($date->mktime(100, 100, 100, false, false, false, -1, true), gmmktime(100, 100, 100));
    }

	/**
	 * Test for mktime
	 */
    public function testMkTimeforDateValuesInPHPRange()
    {
        $date = new Zend_Date_DateObject();
       	$this->assertSame($date->mktime(0, 0, 0, 12, 30, 2037, -1, false),   mktime(0, 0, 0, 12, 30, 2037, -1));
       	$this->assertSame($date->mktime(0, 0, 0, 12, 30, 2037, -1, true),  gmmktime(0, 0, 0, 12, 30, 2037, -1));

       	$this->assertSame($date->mktime(0, 0, 0,  1,  1, 2000, -1, false),   mktime(0, 0, 0,  1,  1, 2000, -1));
       	$this->assertSame($date->mktime(0, 0, 0,  1,  1, 2000, -1, true),  gmmktime(0, 0, 0,  1,  1, 2000, -1));

       	$this->assertSame($date->mktime(0, 0, 0,  1,  1, 1970, -1, false),   mktime(0, 0, 0,  1,  1, 1970, -1));
       	$this->assertSame($date->mktime(0, 0, 0,  1,  1, 1970, -1, true),  gmmktime(0, 0, 0,  1,  1, 1970, -1));

       	$this->assertSame($date->mktime(0, 0, 0, 12, 30, 1902, -1, false),   mktime(0, 0, 0, 12, 30, 1902, -1));
       	$this->assertSame($date->mktime(0, 0, 0, 12, 30, 1902, -1, true),  gmmktime(0, 0, 0, 12, 30, 1902, -1));
    }

	/**
	 * Test for mktime
	 */
    public function testMkTimeforDateValuesGreaterPHPRange()
    {
        $date = new Zend_Date_DateObject();
       	$this->assertSame($date->mktime(0, 0, 0, 1, 1, 2040, -1, false), 2208985200);
       	$this->assertSame($date->mktime(0, 0, 0, 1, 1, 2040, -1, true),  2208988800);
       	$this->assertSame($date->mktime(0, 0, 0, 1, 1, 2200, -1, false), 7258114800);
       	$this->assertSame($date->mktime(0, 0, 0, 1, 1, 2200, -1, true),  7258118400);
       	$this->assertSame($date->mktime(0, 0, 0, 1, 1, 2500, -1, false), 16725222000);
       	$this->assertSame($date->mktime(0, 0, 0, 1, 1, 2500, -1, true),  16725225600);
       	$this->assertSame($date->mktime(0, 0, 0, 1, 1, 3000, -1, false), 32503676400);
       	$this->assertSame($date->mktime(0, 0, 0, 1, 1, 3000, -1, true),  32503680000);
       	$this->assertSame($date->mktime(0, 0, 0, 1, 1, 5000, -1, false), 95617580400);
       	$this->assertSame($date->mktime(0, 0, 0, 1, 1, 5000, -1, true),  95617584000);
    }

	/**
	 * Test for mktime
	 */
    public function testMkTimeforDateValuesSmallerPHPRange()
    {
        $date = new Zend_Date_DateObject();
       	$this->assertSame($date->mktime(0, 0, 0, 1, 1, 1900, -1, false), -2208985200);
       	$this->assertSame($date->mktime(0, 0, 0, 1, 1, 1900, -1, true),  -2208988800);
       	$this->assertSame($date->mktime(0, 0, 0, 1, 1, 1700, -1, false), -8520332400);
       	$this->assertSame($date->mktime(0, 0, 0, 1, 1, 1700, -1, true),  -8520336000);
       	$this->assertSame($date->mktime(0, 0, 0, 1, 1, 1500, -1, false), -14830988400);
       	$this->assertSame($date->mktime(0, 0, 0, 1, 1, 1500, -1, true),  -14830992000);
       	$this->assertSame($date->mktime(0, 0, 0, 1, 1, 1000, -1, false), -30609788400);
       	$this->assertSame($date->mktime(0, 0, 0, 1, 1, 1000, -1, true),  -30609792000);
       	$this->assertSame($date->mktime(0, 0, 0, 1, 1,    0, -1, false), -62167388400);
       	$this->assertSame($date->mktime(0, 0, 0, 1, 1,    0, -1, true),  -62167392000);
       	$this->assertSame($date->mktime(0, 0, 0, 1, 1,-2000, -1, false), -125282588400);
       	$this->assertSame($date->mktime(0, 0, 0, 1, 1,-2000, -1, true),  -125282592000);

       	$this->assertSame($date->mktime(0, 0, 0, 13, 1, 1899, -1, false), -2208985200);
       	$this->assertSame($date->mktime(0, 0, 0, 13, 1, 1899, -1, true),  -2208988800);
       	$this->assertSame($date->mktime(0, 0, 0,-11, 1, 1901, -1, false), -2208985200);
       	$this->assertSame($date->mktime(0, 0, 0,-11, 1, 1901, -1, true),  -2208988800);
    }

    public function testIsLeapYear()
    {
        $date = new Zend_Date_DateObject();
        $this->assertSame($date->isLeapYear(2000), true);
        $this->assertSame($date->isLeapYear(2002), false);
        $this->assertSame($date->isLeapYear(2004), true);
        $this->assertSame($date->isLeapYear(1899), false);
        $this->assertSame($date->isLeapYear(1500), true);
        $this->assertSame($date->isLeapYear(1455), false);
    }

    public function testWeekNumber()
    {
        $date = new Zend_Date_DateObject();
        $this->assertSame($date->weekNumber(2000, 1, 1), (int) date('W',mktime(0, 0, 0, 1, 1, 2000)));
        $this->assertSame($date->weekNumber(2020, 10, 1), (int) date('W',mktime(0, 0, 0, 10, 1, 2020)));
        $this->assertSame($date->weekNumber(2005, 5, 15), (int) date('W',mktime(0, 0, 0, 5, 15, 2005)));
        $this->assertSame($date->weekNumber(1994, 11, 22), (int) date('W',mktime(0, 0, 0, 11, 22, 1994)));
    }

    public function testDayOfWeek()
    {
        $this->markTestIncomplete('included soon');
    }
    
    public function testCalcSun()
    {
        $this->markTestIncomplete('included soon');
    }
    
    public function testGetDate()
    {
        $this->markTestIncomplete('included soon');
    }
    
    public function testDate()
    {
        $this->markTestIncomplete('included soon');
    }
}