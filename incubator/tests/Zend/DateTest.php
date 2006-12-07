<?php
/**
 * @package    Zend_Date
 * @subpackage UnitTests
 */


/**
 * Zend_Date
 */
require_once 'Zend.php';
Zend::loadClass('Zend_Date');
Zend::loadClass('Zend_Locale');

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @package    Zend_Date
 * @subpackage UnitTests
 */
class Zend_DateTest extends PHPUnit_Framework_TestCase
{

	/**
	 * Test for date object creation
	 */
    public function testCreation()
    {
    	$date = new Zend_Date();
    	$this->assertTrue($date instanceof Zend_Date);
    }

	/**
	 * Test for creation with timestamp
	 */
    public function testCreationTimestamp()
    {
    	$date = new Zend_Date('12345678');
    	$this->assertTrue($date instanceof Zend_Date);
    }

	/**
	 * Test for creation but only part of date
	 */
    public function testCreationDatePart()
    {
    	$date = new Zend_Date('13:22:50',Zend_Date::HOUR);
    	$this->assertTrue($date instanceof Zend_Date);
    }

	/**
	 * Test for creation but only a defined locale
	 */
    public function testCreationLocale()
    {
        $locale = new Zend_Locale('de_AT');
    	$date = new Zend_Date('13:22:50',false,$locale);
    	$this->assertTrue($date instanceof Zend_Date);
    }

	/**
	 * Test for creation but only part of date with locale
	 */
    public function testCreationLocalePart()
    {
        $locale = new Zend_Locale('de_AT');
    	$date = new Zend_Date('13:22:50',Zend_Date::HOUR,$locale);
    	$this->assertTrue($date instanceof Zend_Date);
    }

	/**
	 * Test for getTimestamp
	 */
    public function testGetTimestamp()
    {
        $locale = new Zend_Locale('de_AT');
    	$date = new Zend_Date(10000000);
    	$this->assertSame($date->getTimestamp(), 10000000);
    }

	/**
	 * Test for getTimestamp
	 */
    public function testGetTimestamp2()
    {
        $locale = new Zend_Locale('de_AT');
    	$date = new Zend_Date(-100000000);
    	$this->assertSame($date->getTimestamp(), -100000000);
    }

	/**
	 * Test for setTimestamp
	 */
    public function testSetTimestamp()
    {
        $locale = new Zend_Locale('de_AT');
    	$date = new Zend_Date(0,Zend_Date::TIMESTAMP,$locale);
    	$result = $date->setTimestamp(10000000);
    	$this->assertSame($result->getTimestamp(), 10000000);
    }

	/**
	 * Test for setTimestamp
	 */
    public function testSetTimestamp2()
    {
        try {
            $locale = new Zend_Locale('de_AT');
        	$date = new Zend_Date(0,false,$locale);
    	    $result = $date->setTimestamp('notimestamp');
        	$this->Fail();
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

	/**
	 * Test for addTimestamp
	 */
    public function testAddTimestamp()
    {
        $locale = new Zend_Locale('de_AT');
    	$date = new Zend_Date(0,false,$locale);
    	$result = $date->addTimestamp(10000000);
    	$this->assertSame($result->getTimestamp(), '10000000');
    }

	/**
	 * Test for addTimestamp
	 */
    public function testAddTimestamp2()
    {
        try {
            $locale = new Zend_Locale('de_AT');
        	$date = new Zend_Date(0,false,$locale);
    	    $result = $date->addTimestamp('notimestamp');
        	$this->Fail();
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

	/**
	 * Test for subTimestamp
	 */
    public function testSubTimestamp()
    {
        $locale = new Zend_Locale('de_AT');
    	$date = new Zend_Date(0,false,$locale);
    	$result = $date->subTimestamp(10000000);
    	$this->assertSame($result->getTimestamp(), '-10000000');
    }

	/**
	 * Test for subTimestamp
	 */
    public function testSubTimestamp2()
    {
        try {
            $locale = new Zend_Locale('de_AT');
        	$date = new Zend_Date(0,false,$locale);
    	    $result = $date->subTimestamp('notimestamp');
        	$this->Fail();
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

	/**
	 * Test for compareTimestamp
	 */
    public function testCompareTimestamp()
    {
        $locale = new Zend_Locale('de_AT');
    	$date1 = new Zend_Date(0,false,$locale);
    	$date2 = new Zend_Date(0,false,$locale);
    	$this->assertSame($date1->compareTimestamp($date2), '0');
    }

	/**
	 * Test for compareTimestamp
	 */
    public function testCompareTimestamp2()
    {
        $locale = new Zend_Locale('de_AT');
    	$date1 = new Zend_Date(0,false,$locale);
    	$date2 = new Zend_Date(100,false,$locale);
    	$this->assertSame($date1->compareTimestamp($date2), '-100');
    }

	/**
	 * Test for isTimestamp
	 */
    public function testIsTimestamp()
    {
        $locale = new Zend_Locale('de_AT');
    	$date1 = new Zend_Date(0,false,$locale);
    	$date2 = new Zend_Date(0,false,$locale);
    	$this->assertTrue($date1->isTimestamp($date2));
    }

	/**
	 * Test for isTimestamp
	 */
    public function testIsTimestamp2()
    {
        $locale = new Zend_Locale('de_AT');
    	$date1 = new Zend_Date(0,false,$locale);
    	$date2 = new Zend_Date(100,false,$locale);
    	$this->assertFalse($date1->isTimestamp($date2));
    }

	/**
	 * Test for __toString
	 */
    public function test_ToString()
    {
        $locale = new Zend_Locale('de_AT');
    	$date = new Zend_Date(0,false,$locale);
    	$this->assertSame($date->__toString(),'01.01.1970 01:00:00');
    }

	/**
	 * Test for toString
	 */
    public function testToString()
    {
        $locale = new Zend_Locale('de_AT');
    	$date = new Zend_Date(1234567890,false,$locale);
    	$this->assertSame($date->toString(),'14.02.2009 00:31:30');
    	$this->assertSame($date->toString('en_US'),'Feb 14, 2009 12:31:30 1890');
    	$this->assertSame($date->toString(false, 'yyyy'),'2009');
    	$this->assertSame($date->toString(false, false, true),'13.02.2009 23:31:30');
    	$this->assertSame($date->toString('en_US', false, true),'Feb 13, 2009 11:31:30 PM');
    	$this->assertSame($date->toString(false, "'yyyy'"),'yyyy');
    	$this->assertSame($date->toString(false, "GGGGG"),'n');
    	$this->assertSame($date->toString(false, "GGGG"),'n. Chr.');
    	$this->assertSame($date->toString(false, "GGG"),'n. Chr.');
    	$this->assertSame($date->toString(false, "GG"),'n. Chr.');
    	$this->assertSame($date->toString(false, "G"),'n. Chr.');
    	$this->assertSame($date->toString(false, "yyyyy"),'02009');
    	$this->assertSame($date->toString(false, "yyyy"),'2009');
    	$this->assertSame($date->toString(false, "yyy"),'2009');
    	$this->assertSame($date->toString(false, "yy"),'09');
    	$this->assertSame($date->toString(false, "y"),'2009');
    	$this->assertSame($date->toString(false, "YYYYY"),'02009');
    	$this->assertSame($date->toString(false, "YYYY"),'2009');
    	$this->assertSame($date->toString(false, "YYY"),'2009');
    	$this->assertSame($date->toString(false, "YY"),'09');
    	$this->assertSame($date->toString(false, "Y"),'2009');
    	$this->assertSame($date->toString(false, "MMMMM"),'F');
    	$this->assertSame($date->toString(false, "MMMM"),'Februar');
    	$this->assertSame($date->toString(false, "MMM"),'Feb');
    	$this->assertSame($date->toString(false, "MM"),'02');
    	$this->assertSame($date->toString(false, "M"),'2');
    	$this->assertSame($date->toString(false, "ww"),'07');
    	$this->assertSame($date->toString(false, "w"),'07');
    	$this->assertSame($date->toString(false, "dd"),'14');
    	$this->assertSame($date->toString(false, "d"),'14');
    	$this->assertSame($date->toString(false, "DDD"),'044');
    	$this->assertSame($date->toString(false, "DD"),'44');
    	$this->assertSame($date->toString(false, "D"),'44');
    	$this->assertSame($date->toString(false, "EEEEE"),'S');
    	$this->assertSame($date->toString(false, "EEEE"),'Samstag');
    	$this->assertSame($date->toString(false, "EEE"),'Sa');
    	$this->assertSame($date->toString(false, "EE"),'Sa');
    	$this->assertSame($date->toString(false, "E"),'S');
    	$this->assertSame($date->toString(false, "ee"),'06');
    	$this->assertSame($date->toString(false, "e"),'6');
    	$this->assertSame($date->toString(false, "a"),'vorm.');
    	$this->assertSame($date->toString(false, "hh"),'12');
    	$this->assertSame($date->toString(false, "h"),'12');
    	$this->assertSame($date->toString(false, "HH"),'00');
    	$this->assertSame($date->toString(false, "H"),'0');
    	$this->assertSame($date->toString(false, "mm"),'31');
    	$this->assertSame($date->toString(false, "m"),'31');
    	$this->assertSame($date->toString(false, "ss"),'30');
    	$this->assertSame($date->toString(false, "s"),'30');
    	$this->assertSame($date->toString(false, "S"),'0');
    	$this->assertSame($date->toString(false, "zzzz"),'Europe/Paris');
    	$this->assertSame($date->toString(false, "zzz"),'CET');
    	$this->assertSame($date->toString(false, "zz"),'CET');
    	$this->assertSame($date->toString(false, "z"),'CET');
    	$this->assertSame($date->toString(false, "ZZZZ"),'+01:00');
    	$this->assertSame($date->toString(false, "ZZZ"),'+0100');
    	$this->assertSame($date->toString(false, "ZZ"),'+0100');
    	$this->assertSame($date->toString(false, "Z"),'+0100');
    	$this->assertSame($date->toString(false, "AAAAA"),'01890');
    	$this->assertSame($date->toString(false, "AAAA"),'1890');
    	$this->assertSame($date->toString(false, "AAA"),'1890');
    	$this->assertSame($date->toString(false, "AA"),'1890');
    	$this->assertSame($date->toString(false, "A"),'1890');
    }
}