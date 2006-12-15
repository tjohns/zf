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
    	$date = new Zend_Date('13',Zend_Date::HOUR);
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
    	$date = new Zend_Date('13',Zend_Date::HOUR,$locale);
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
        } catch (Zend_Date_Exception $e) {
            // success
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
        } catch (Zend_Date_Exception $e) {
            // success
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
        } catch (Zend_Date_Exception $e) {
            // success
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
    	$this->assertSame($date->toString(false, false, 'en_US'),'Feb 14, 2009 12:31:30 1890');
    	$this->assertSame($date->toString('yyy', false, false),'2009');
    	$this->assertSame($date->toString(false, true, false),'13.02.2009 23:31:30');
    	$this->assertSame($date->toString(false, true, 'en_US'),'Feb 13, 2009 11:31:30 PM');
    	$this->assertSame($date->toString("xx'yy''yy'xx"),"xxyy'yyxx");
    	$this->assertSame($date->toString("GGGGG"),'n');
    	$this->assertSame($date->toString("GGGG"),'n. Chr.');
    	$this->assertSame($date->toString("GGG"),'n. Chr.');
    	$this->assertSame($date->toString("GG"),'n. Chr.');
    	$this->assertSame($date->toString("G"),'n. Chr.');
    	$this->assertSame($date->toString("yyyyy"),'02009');
    	$this->assertSame($date->toString("yyyy"),'2009');
    	$this->assertSame($date->toString("yyy"),'2009');
    	$this->assertSame($date->toString("yy"),'09');
    	$this->assertSame($date->toString("y"),'2009');
    	$this->assertSame($date->toString("YYYYY"),'02009');
    	$this->assertSame($date->toString("YYYY"),'2009');
    	$this->assertSame($date->toString("YYY"),'2009');
    	$this->assertSame($date->toString("YY"),'09');
    	$this->assertSame($date->toString("Y"),'2009');
    	$this->assertSame($date->toString("MMMMM"),'F');
    	$this->assertSame($date->toString("MMMM"),'Februar');
    	$this->assertSame($date->toString("MMM"),'Feb');
    	$this->assertSame($date->toString("MM"),'02');
    	$this->assertSame($date->toString("M"),'2');
    	$this->assertSame($date->toString("ww"),'07');
    	$this->assertSame($date->toString("w"),'07');
    	$this->assertSame($date->toString("dd"),'14');
    	$this->assertSame($date->toString("d"),'14');
    	$this->assertSame($date->toString("DDD"),'044');
    	$this->assertSame($date->toString("DD"),'44');
    	$this->assertSame($date->toString("D"),'44');
    	$this->assertSame($date->toString("EEEEE"),'S');
    	$this->assertSame($date->toString("EEEE"),'Samstag');
    	$this->assertSame($date->toString("EEE"),'Sam');
    	$this->assertSame($date->toString("EE"),'Sa');
    	$this->assertSame($date->toString("E"),'S');
    	$this->assertSame($date->toString("ee"),'06');
    	$this->assertSame($date->toString("e"),'6');
    	$this->assertSame($date->toString("a"),'vorm.');
    	$this->assertSame($date->toString("hh"),'12');
    	$this->assertSame($date->toString("h"),'12');
    	$this->assertSame($date->toString("HH"),'00');
    	$this->assertSame($date->toString("H"),'0');
    	$this->assertSame($date->toString("mm"),'31');
    	$this->assertSame($date->toString("m"),'31');
    	$this->assertSame($date->toString("ss"),'30');
    	$this->assertSame($date->toString("s"),'30');
    	$this->assertSame($date->toString("S"),'0');
    	$this->assertSame($date->toString("zzzz"),'Europe/Paris');
    	$this->assertSame($date->toString("zzz"),'CET');
    	$this->assertSame($date->toString("zz"),'CET');
    	$this->assertSame($date->toString("z"),'CET');
    	$this->assertSame($date->toString("ZZZZ"),'+01:00');
    	$this->assertSame($date->toString("ZZZ"),'+0100');
    	$this->assertSame($date->toString("ZZ"),'+0100');
    	$this->assertSame($date->toString("Z"),'+0100');
    	$this->assertSame($date->toString("AAAAA"),'01890');
    	$this->assertSame($date->toString("AAAA"),'1890');
    	$this->assertSame($date->toString("AAA"),'1890');
    	$this->assertSame($date->toString("AA"),'1890');
    	$this->assertSame($date->toString("A"),'1890');
    }

	/**
	 * Test for toValue
	 */
    public function testToValue()
    {
        $locale = new Zend_Locale('de_AT');
    	$date = new Zend_Date(1234567890,false,$locale);
    	$this->assertSame($date->toValue(),1234567890);
    	$this->assertSame($date->toValue(Zend_Date::DAY),14);
    	$this->assertSame($date->toValue(Zend_Date::DAY, true),13);
    	$this->assertSame($date->toValue(Zend_Date::WEEKDAY_SHORT),false);
    	$this->assertSame($date->toValue(Zend_Date::WEEKDAY_SHORT, true),false);
    	$this->assertSame($date->toValue(Zend_Date::DAY_SHORT),14);
    	$this->assertSame($date->toValue(Zend_Date::DAY_SHORT, true),13);
    	$this->assertSame($date->toValue(Zend_Date::WEEKDAY),false);
    	$this->assertSame($date->toValue(Zend_Date::WEEKDAY, true),false);
    	$this->assertSame($date->toValue(Zend_Date::WEEKDAY_8601),6);
    	$this->assertSame($date->toValue(Zend_Date::WEEKDAY_8601, true),5);
    	$this->assertSame($date->toValue(Zend_Date::DAY_SUFFIX),false);
    	$this->assertSame($date->toValue(Zend_Date::DAY_SUFFIX, true),false);
    	$this->assertSame($date->toValue(Zend_Date::WEEKDAY_DIGIT),6);
    	$this->assertSame($date->toValue(Zend_Date::WEEKDAY_DIGIT, true),5);
    	$this->assertSame($date->toValue(Zend_Date::DAY_OF_YEAR),44);
    	$this->assertSame($date->toValue(Zend_Date::DAY_OF_YEAR, true),43);
    	$this->assertSame($date->toValue(Zend_Date::WEEKDAY_NARROW),false);
    	$this->assertSame($date->toValue(Zend_Date::WEEKDAY_NARROW, true),false);
    	$this->assertSame($date->toValue(Zend_Date::WEEKDAY_NAME),false);
    	$this->assertSame($date->toValue(Zend_Date::WEEKDAY_NAME, true),false);
    	$this->assertSame($date->toValue(Zend_Date::WEEK),7);
    	$this->assertSame($date->toValue(Zend_Date::WEEK, true),7);
    	$this->assertSame($date->toValue(Zend_Date::MONTH),false);
    	$this->assertSame($date->toValue(Zend_Date::MONTH, true),false);
    	$this->assertSame($date->toValue(Zend_Date::MONTH_SHORT),2);
    	$this->assertSame($date->toValue(Zend_Date::MONTH_SHORT, true),2);
    	$this->assertSame($date->toValue(Zend_Date::MONTH_NAME),false);
    	$this->assertSame($date->toValue(Zend_Date::MONTH_NAME, true),false);
    	$this->assertSame($date->toValue(Zend_Date::MONTH_DIGIT),2);
    	$this->assertSame($date->toValue(Zend_Date::MONTH_DIGIT, true),2);
    	$this->assertSame($date->toValue(Zend_Date::MONTH_DAYS),28);
    	$this->assertSame($date->toValue(Zend_Date::MONTH_DAYS, true),28);
    	$this->assertSame($date->toValue(Zend_Date::MONTH_NARROW),false);
    	$this->assertSame($date->toValue(Zend_Date::MONTH_NARROW, true),false);
    	$this->assertSame($date->toValue(Zend_Date::LEAPYEAR),0);
    	$this->assertSame($date->toValue(Zend_Date::LEAPYEAR, true),0);
    	$this->assertSame($date->toValue(Zend_Date::YEAR_8601),2009);
    	$this->assertSame($date->toValue(Zend_Date::YEAR_8601, true),2009);
    	$this->assertSame($date->toValue(Zend_Date::YEAR),2009);
    	$this->assertSame($date->toValue(Zend_Date::YEAR, true),2009);
    	$this->assertSame($date->toValue(Zend_Date::YEAR_SHORT),9);
    	$this->assertSame($date->toValue(Zend_Date::YEAR_SHORT, true),9);
    	$this->assertSame($date->toValue(Zend_Date::YEAR_SHORT_8601),9);
    	$this->assertSame($date->toValue(Zend_Date::YEAR_SHORT_8601, true),9);
    	$this->assertSame($date->toValue(Zend_Date::MERIDIEM),false);
    	$this->assertSame($date->toValue(Zend_Date::MERIDIEM, true),false);
    	$this->assertSame($date->toValue(Zend_Date::SWATCH),21);
    	$this->assertSame($date->toValue(Zend_Date::SWATCH, true),21);
    	$this->assertSame($date->toValue(Zend_Date::HOUR_SHORT_AM),12);
    	$this->assertSame($date->toValue(Zend_Date::HOUR_SHORT_AM, true),11);
    	$this->assertSame($date->toValue(Zend_Date::HOUR_SHORT),0);
    	$this->assertSame($date->toValue(Zend_Date::HOUR_SHORT, true),23);
    	$this->assertSame($date->toValue(Zend_Date::HOUR_AM),12);
    	$this->assertSame($date->toValue(Zend_Date::HOUR_AM, true),11);
    	$this->assertSame($date->toValue(Zend_Date::HOUR),0);
    	$this->assertSame($date->toValue(Zend_Date::HOUR, true),23);
    	$this->assertSame($date->toValue(Zend_Date::MINUTE),31);
    	$this->assertSame($date->toValue(Zend_Date::MINUTE, true),31);
    	$this->assertSame($date->toValue(Zend_Date::SECOND),30);
    	$this->assertSame($date->toValue(Zend_Date::SECOND, true),30);
    	$this->assertSame($date->toValue(Zend_Date::MILLISECOND),0);
    	$this->assertSame($date->toValue(Zend_Date::MILLISECOND, true),0);
    	$this->assertSame($date->toValue(Zend_Date::MINUTE_SHORT),31);
    	$this->assertSame($date->toValue(Zend_Date::MINUTE_SHORT, true),31);
    	$this->assertSame($date->toValue(Zend_Date::SECOND_SHORT),30);
    	$this->assertSame($date->toValue(Zend_Date::SECOND_SHORT, true),30);
    	$this->assertSame($date->toValue(Zend_Date::TIMEZONE_NAME),false);
    	$this->assertSame($date->toValue(Zend_Date::TIMEZONE_NAME, true),false);
    	$this->assertSame($date->toValue(Zend_Date::DAYLIGHT),0);
    	$this->assertSame($date->toValue(Zend_Date::DAYLIGHT, true),0);
    	$this->assertSame($date->toValue(Zend_Date::GMT_DIFF),100);
    	$this->assertSame($date->toValue(Zend_Date::GMT_DIFF, true),0);
    	$this->assertSame($date->toValue(Zend_Date::GMT_DIFF_SEP),false);
    	$this->assertSame($date->toValue(Zend_Date::GMT_DIFF_SEP, true),false);
    	$this->assertSame($date->toValue(Zend_Date::TIMEZONE),false);
    	$this->assertSame($date->toValue(Zend_Date::TIMEZONE, true),false);
    	$this->assertSame($date->toValue(Zend_Date::TIMEZONE_SECS),3600);
    	$this->assertSame($date->toValue(Zend_Date::TIMEZONE_SECS, true),0);
    	$this->assertSame($date->toValue(Zend_Date::ISO_8601),false);
    	$this->assertSame($date->toValue(Zend_Date::ISO_8601, true),false);
    	$this->assertSame($date->toValue(Zend_Date::RFC_2822),false);
    	$this->assertSame($date->toValue(Zend_Date::RFC_2822, true),false);
    	$this->assertSame($date->toValue(Zend_Date::TIMESTAMP),1234567890);
    	$this->assertSame($date->toValue(Zend_Date::TIMESTAMP, true),1234567890);
    	$this->assertSame($date->toValue(Zend_Date::ERA),false);
    	$this->assertSame($date->toValue(Zend_Date::ERA, true),false);
    	$this->assertSame($date->toValue(Zend_Date::ERA_NAME),false);
    	$this->assertSame($date->toValue(Zend_Date::ERA_NAME, true),false);
    	$this->assertSame($date->toValue(Zend_Date::DATES),false);
    	$this->assertSame($date->toValue(Zend_Date::DATES, true),false);
    	$this->assertSame($date->toValue(Zend_Date::DATE_FULL),false);
    	$this->assertSame($date->toValue(Zend_Date::DATE_FULL, true),false);
    	$this->assertSame($date->toValue(Zend_Date::DATE_LONG),false);
    	$this->assertSame($date->toValue(Zend_Date::DATE_LONG, true),false);
    	$this->assertSame($date->toValue(Zend_Date::DATE_MEDIUM),false);
    	$this->assertSame($date->toValue(Zend_Date::DATE_MEDIUM, true),false);
    	$this->assertSame($date->toValue(Zend_Date::DATE_SHORT),false);
    	$this->assertSame($date->toValue(Zend_Date::DATE_SHORT, true),false);
    	$this->assertSame($date->toValue(Zend_Date::TIMES),false);
    	$this->assertSame($date->toValue(Zend_Date::TIMES, true),false);
    	$this->assertSame($date->toValue(Zend_Date::TIME_FULL),false);
    	$this->assertSame($date->toValue(Zend_Date::TIME_FULL, true),false);
    	$this->assertSame($date->toValue(Zend_Date::TIME_LONG),false);
    	$this->assertSame($date->toValue(Zend_Date::TIME_LONG, true),false);
    	$this->assertSame($date->toValue(Zend_Date::TIME_MEDIUM),false);
    	$this->assertSame($date->toValue(Zend_Date::TIME_MEDIUM, true),false);
    	$this->assertSame($date->toValue(Zend_Date::TIME_SHORT),false);
    	$this->assertSame($date->toValue(Zend_Date::TIME_SHORT, true),false);
    	$this->assertSame($date->toValue(Zend_Date::ATOM),false);
    	$this->assertSame($date->toValue(Zend_Date::ATOM, true),false);
    	$this->assertSame($date->toValue(Zend_Date::COOKIE),false);
    	$this->assertSame($date->toValue(Zend_Date::COOKIE, true),false);
    	$this->assertSame($date->toValue(Zend_Date::RFC_822),false);
    	$this->assertSame($date->toValue(Zend_Date::RFC_822, true),false);
    	$this->assertSame($date->toValue(Zend_Date::RFC_850),false);
    	$this->assertSame($date->toValue(Zend_Date::RFC_850, true),false);
    	$this->assertSame($date->toValue(Zend_Date::RFC_1036),false);
    	$this->assertSame($date->toValue(Zend_Date::RFC_1036, true),false);
    	$this->assertSame($date->toValue(Zend_Date::RFC_1123),false);
    	$this->assertSame($date->toValue(Zend_Date::RFC_1123, true),false);
    	$this->assertSame($date->toValue(Zend_Date::RSS),false);
    	$this->assertSame($date->toValue(Zend_Date::RSS, true),false);
    	$this->assertSame($date->toValue(Zend_Date::W3C),false);
    	$this->assertSame($date->toValue(Zend_Date::W3C, true),false);
    }

	/**
	 * Test for toValue
	 */
    public function testGet()
    {
        $locale = new Zend_Locale('de_AT');
    	$date = new Zend_Date(1234567890,false,$locale);
    	$this->assertSame($date->get(),1234567890);

    	$this->assertSame($date->get(Zend_Date::DAY),'14');
    	$this->assertSame($date->get(Zend_Date::DAY, true),'13');
    	$this->assertSame($date->get(Zend_Date::DAY, true, 'es'),'13');

    	$this->assertSame($date->get(Zend_Date::WEEKDAY_SHORT),'Sam');
    	$this->assertSame($date->get(Zend_Date::WEEKDAY_SHORT, true),'Fre');
    	$this->assertSame($date->get(Zend_Date::WEEKDAY_SHORT, true, 'es'),'vie');

    	$this->assertSame($date->get(Zend_Date::DAY_SHORT),'14');
    	$this->assertSame($date->get(Zend_Date::DAY_SHORT, true),'13');
    	$this->assertSame($date->get(Zend_Date::DAY_SHORT, true, 'es'),'13');

    	$this->assertSame($date->get(Zend_Date::WEEKDAY),'Samstag');
    	$this->assertSame($date->get(Zend_Date::WEEKDAY, true),'Freitag');
    	$this->assertSame($date->get(Zend_Date::WEEKDAY, true, 'es'),'viernes');

    	$this->assertSame($date->get(Zend_Date::WEEKDAY_8601),'6');
    	$this->assertSame($date->get(Zend_Date::WEEKDAY_8601, true),'5');
    	$this->assertSame($date->get(Zend_Date::WEEKDAY_8601, true, 'es'),'5');

    	$this->assertSame($date->get(Zend_Date::DAY_SUFFIX),'th');
    	$this->assertSame($date->get(Zend_Date::DAY_SUFFIX, true),'th');
    	$this->assertSame($date->get(Zend_Date::DAY_SUFFIX, true, 'es'),'th');

    	$this->assertSame($date->get(Zend_Date::WEEKDAY_DIGIT),'6');
    	$this->assertSame($date->get(Zend_Date::WEEKDAY_DIGIT, true),'5');
    	$this->assertSame($date->get(Zend_Date::WEEKDAY_DIGIT, true, 'es'),'5');

    	$this->assertSame($date->get(Zend_Date::DAY_OF_YEAR),'44');
    	$this->assertSame($date->get(Zend_Date::DAY_OF_YEAR, true),'43');
    	$this->assertSame($date->get(Zend_Date::DAY_OF_YEAR, true, 'es'),'43');

    	$this->assertSame($date->get(Zend_Date::WEEKDAY_NARROW),'S');
    	$this->assertSame($date->get(Zend_Date::WEEKDAY_NARROW, true),'F');
    	$this->assertSame($date->get(Zend_Date::WEEKDAY_NARROW, true, 'es'),'v');

    	$this->assertSame($date->get(Zend_Date::WEEKDAY_NAME),'Sa');
    	$this->assertSame($date->get(Zend_Date::WEEKDAY_NAME, true),'Fr');
    	$this->assertSame($date->get(Zend_Date::WEEKDAY_NAME, true, 'es'),'vie');

    	$this->assertSame($date->get(Zend_Date::WEEK),'07');
    	$this->assertSame($date->get(Zend_Date::WEEK, true),'07');
    	$this->assertSame($date->get(Zend_Date::WEEK, true, 'es'),'07');

    	$this->assertSame($date->get(Zend_Date::MONTH),'Februar');
    	$this->assertSame($date->get(Zend_Date::MONTH, true),'Februar');
    	$this->assertSame($date->get(Zend_Date::MONTH, true, 'es'),'febrero');

    	$this->assertSame($date->get(Zend_Date::MONTH_SHORT),'02');
    	$this->assertSame($date->get(Zend_Date::MONTH_SHORT, true),'02');
    	$this->assertSame($date->get(Zend_Date::MONTH_SHORT, true, 'es'),'02');

    	$this->assertSame($date->get(Zend_Date::MONTH_NAME),'Feb');
    	$this->assertSame($date->get(Zend_Date::MONTH_NAME, true),'Feb');
    	$this->assertSame($date->get(Zend_Date::MONTH_NAME, true, 'es'),'feb');

    	$this->assertSame($date->get(Zend_Date::MONTH_DIGIT),'2');
    	$this->assertSame($date->get(Zend_Date::MONTH_DIGIT, true),'2');
    	$this->assertSame($date->get(Zend_Date::MONTH_DIGIT, true, 'es'),'2');

    	$this->assertSame($date->get(Zend_Date::MONTH_DAYS),'28');
    	$this->assertSame($date->get(Zend_Date::MONTH_DAYS, true),'28');
    	$this->assertSame($date->get(Zend_Date::MONTH_DAYS, true, 'es'),'28');

    	$this->assertSame($date->get(Zend_Date::MONTH_NARROW),'F');
    	$this->assertSame($date->get(Zend_Date::MONTH_NARROW, true),'F');
    	$this->assertSame($date->get(Zend_Date::MONTH_NARROW, true, 'es'),'f');

    	$this->assertSame($date->get(Zend_Date::LEAPYEAR),'0');
        $this->assertSame($date->get(Zend_Date::LEAPYEAR, true),'0');
        $this->assertSame($date->get(Zend_Date::LEAPYEAR, true, 'es'),'0');

        $this->assertSame($date->get(Zend_Date::YEAR_8601),'2009');
    	$this->assertSame($date->get(Zend_Date::YEAR_8601, true),'2009');
    	$this->assertSame($date->get(Zend_Date::YEAR_8601, true, 'es'),'2009');

    	$this->assertSame($date->get(Zend_Date::YEAR),'2009');
    	$this->assertSame($date->get(Zend_Date::YEAR, true),'2009');
    	$this->assertSame($date->get(Zend_Date::YEAR, true, 'es'),'2009');

    	$this->assertSame($date->get(Zend_Date::YEAR_SHORT),'09');
    	$this->assertSame($date->get(Zend_Date::YEAR_SHORT, true),'09');
    	$this->assertSame($date->get(Zend_Date::YEAR_SHORT, true, 'es'),'09');

    	$this->assertSame($date->get(Zend_Date::YEAR_SHORT_8601),'09');
    	$this->assertSame($date->get(Zend_Date::YEAR_SHORT_8601, true),'09');
    	$this->assertSame($date->get(Zend_Date::YEAR_SHORT_8601, true, 'es'),'09');

    	$this->assertSame($date->get(Zend_Date::MERIDIEM),'vorm.');
    	$this->assertSame($date->get(Zend_Date::MERIDIEM, true),'nachm.');
    	$this->assertSame($date->get(Zend_Date::MERIDIEM, true, 'es'),'PM');

    	$this->assertSame($date->get(Zend_Date::SWATCH),'021');
    	$this->assertSame($date->get(Zend_Date::SWATCH, true),'021');
    	$this->assertSame($date->get(Zend_Date::SWATCH, true, 'es'),'021');

    	$this->assertSame($date->get(Zend_Date::HOUR_SHORT_AM),'12');
    	$this->assertSame($date->get(Zend_Date::HOUR_SHORT_AM, true),'11');
    	$this->assertSame($date->get(Zend_Date::HOUR_SHORT_AM, true, 'es'),'11');

    	$this->assertSame($date->get(Zend_Date::HOUR_SHORT),'0');
    	$this->assertSame($date->get(Zend_Date::HOUR_SHORT, true),'23');
    	$this->assertSame($date->get(Zend_Date::HOUR_SHORT, true, 'es'),'23');

        $this->assertSame($date->get(Zend_Date::HOUR_AM),'12');
    	$this->assertSame($date->get(Zend_Date::HOUR_AM, true),'11');
    	$this->assertSame($date->get(Zend_Date::HOUR_AM, true, 'es'),'11');

    	$this->assertSame($date->get(Zend_Date::HOUR),'00');
    	$this->assertSame($date->get(Zend_Date::HOUR, true),'23');
    	$this->assertSame($date->get(Zend_Date::HOUR, true, 'es'),'23');

    	$this->assertSame($date->get(Zend_Date::MINUTE),'31');
    	$this->assertSame($date->get(Zend_Date::MINUTE, true),'31');
    	$this->assertSame($date->get(Zend_Date::MINUTE, true, 'es'),'31');

    	$this->assertSame($date->get(Zend_Date::SECOND),'30');
    	$this->assertSame($date->get(Zend_Date::SECOND, true),'30');
    	$this->assertSame($date->get(Zend_Date::SECOND, true, 'es'),'30');

    	$this->assertSame($date->get(Zend_Date::MILLISECOND),0);
    	$this->assertSame($date->get(Zend_Date::MILLISECOND, true),0);
    	$this->assertSame($date->get(Zend_Date::MILLISECOND, true, 'es'),0);

    	$this->assertSame($date->get(Zend_Date::MINUTE_SHORT),'31');
    	$this->assertSame($date->get(Zend_Date::MINUTE_SHORT, true),'31');
    	$this->assertSame($date->get(Zend_Date::MINUTE_SHORT, true, 'es'),'31');

    	$this->assertSame($date->get(Zend_Date::SECOND_SHORT),'30');
    	$this->assertSame($date->get(Zend_Date::SECOND_SHORT, true),'30');
    	$this->assertSame($date->get(Zend_Date::SECOND_SHORT, true, 'es'),'30');

    	$this->assertSame($date->get(Zend_Date::TIMEZONE_NAME),'Europe/Paris');
    	$this->assertSame($date->get(Zend_Date::TIMEZONE_NAME, true),'UTC');
    	$this->assertSame($date->get(Zend_Date::TIMEZONE_NAME, true, 'es'),'UTC');

    	$this->assertSame($date->get(Zend_Date::DAYLIGHT),'0');
    	$this->assertSame($date->get(Zend_Date::DAYLIGHT, true),'0');
    	$this->assertSame($date->get(Zend_Date::DAYLIGHT, true, 'es'),'0');

    	$this->assertSame($date->get(Zend_Date::GMT_DIFF),'+0100');
    	$this->assertSame($date->get(Zend_Date::GMT_DIFF, true),'+0000');
    	$this->assertSame($date->get(Zend_Date::GMT_DIFF, true, 'es'),'+0000');

    	$this->assertSame($date->get(Zend_Date::GMT_DIFF_SEP),'+01:00');
    	$this->assertSame($date->get(Zend_Date::GMT_DIFF_SEP, true),'+00:00');
    	$this->assertSame($date->get(Zend_Date::GMT_DIFF_SEP, true, 'es'),'+00:00');

    	$this->assertSame($date->get(Zend_Date::TIMEZONE),'CET');
    	$this->assertSame($date->get(Zend_Date::TIMEZONE, true),'GMT');
    	$this->assertSame($date->get(Zend_Date::TIMEZONE, true, 'es'),'GMT');

    	$this->assertSame($date->get(Zend_Date::TIMEZONE_SECS),'3600');
    	$this->assertSame($date->get(Zend_Date::TIMEZONE_SECS, true),'0');
    	$this->assertSame($date->get(Zend_Date::TIMEZONE_SECS, true, 'es'),'0');

    	$this->assertSame($date->get(Zend_Date::ISO_8601),'2009-02-14T00:31:30+01:00');
    	$this->assertSame($date->get(Zend_Date::ISO_8601, true),'2009-02-13T23:31:30+00:00');
    	$this->assertSame($date->get(Zend_Date::ISO_8601, true, 'es'),'2009-02-13T23:31:30+00:00');

    	$this->assertSame($date->get(Zend_Date::RFC_2822),'Sat, 14 Feb 2009 00:31:30 +0100');
    	$this->assertSame($date->get(Zend_Date::RFC_2822, true),'Fri, 13 Feb 2009 23:31:30 +0000');
    	$this->assertSame($date->get(Zend_Date::RFC_2822, true, 'es'),'Fri, 13 Feb 2009 23:31:30 +0000');

    	$this->assertSame($date->get(Zend_Date::TIMESTAMP),1234567890);
    	$this->assertSame($date->get(Zend_Date::TIMESTAMP, true),1234567890);
    	$this->assertSame($date->get(Zend_Date::TIMESTAMP, true, 'es'),1234567890);

    	$this->assertSame($date->get(Zend_Date::ERA),'n. Chr.');
    	$this->assertSame($date->get(Zend_Date::ERA, true),'n. Chr.');
    	$this->assertSame($date->get(Zend_Date::ERA, true, 'es'),'d.C.');

    	$this->assertSame($date->get(Zend_Date::ERA_NAME),'n. Chr.');
    	$this->assertSame($date->get(Zend_Date::ERA_NAME, true),'n. Chr.');
    	$this->assertSame($date->get(Zend_Date::ERA_NAME, true, 'es'),false);

    	$this->assertSame($date->get(Zend_Date::DATES),'14.02.2009');
    	$this->assertSame($date->get(Zend_Date::DATES, true),'13.02.2009');
    	$this->assertSame($date->get(Zend_Date::DATES, true, 'es'),'13-feb-09');

    	$this->assertSame($date->get(Zend_Date::DATE_FULL),'Samstag, 14. Februar 2009');
    	$this->assertSame($date->get(Zend_Date::DATE_FULL, true),'Freitag, 13. Februar 2009');
    	$this->assertSame($date->get(Zend_Date::DATE_FULL, true, 'es'),'viernes 13 de febrero de 2009');

    	$this->assertSame($date->get(Zend_Date::DATE_LONG),'14. Februar 2009');
    	$this->assertSame($date->get(Zend_Date::DATE_LONG, true),'13. Februar 2009');
    	$this->assertSame($date->get(Zend_Date::DATE_LONG, true, 'es'),'13 de febrero de 2009');

    	$this->assertSame($date->get(Zend_Date::DATE_MEDIUM),'14.02.2009');
    	$this->assertSame($date->get(Zend_Date::DATE_MEDIUM, true),'13.02.2009');
    	$this->assertSame($date->get(Zend_Date::DATE_MEDIUM, true, 'es'),'13-feb-09');

        $this->assertSame($date->get(Zend_Date::DATE_SHORT),'14.02.09');
    	$this->assertSame($date->get(Zend_Date::DATE_SHORT, true),'13.02.09');
    	$this->assertSame($date->get(Zend_Date::DATE_SHORT, true, 'es'),'13/02/09');

    	$this->assertSame($date->get(Zend_Date::TIMES),'00:31:30');
    	$this->assertSame($date->get(Zend_Date::TIMES, true),'23:31:30');
    	$this->assertSame($date->get(Zend_Date::TIMES, true, 'es'),'23:31:30');

    	$this->assertSame($date->get(Zend_Date::TIME_FULL),'00:31 Uhr CET');
    	$this->assertSame($date->get(Zend_Date::TIME_FULL, true),'23:31 Uhr GMT');
    	$this->assertSame($date->get(Zend_Date::TIME_FULL, true, 'es'),'23H3130" GMT');

    	$this->assertSame($date->get(Zend_Date::TIME_LONG),'00:31:30 CET');
    	$this->assertSame($date->get(Zend_Date::TIME_LONG, true),'23:31:30 GMT');
    	$this->assertSame($date->get(Zend_Date::TIME_LONG, true, 'es'),'23:31:30 GMT');

    	$this->assertSame($date->get(Zend_Date::TIME_MEDIUM),'00:31:30');
    	$this->assertSame($date->get(Zend_Date::TIME_MEDIUM, true),'23:31:30');
    	$this->assertSame($date->get(Zend_Date::TIME_MEDIUM, true, 'es'),'23:31:30');

    	$this->assertSame($date->get(Zend_Date::TIME_SHORT),'00:31');
    	$this->assertSame($date->get(Zend_Date::TIME_SHORT, true),'23:31');
    	$this->assertSame($date->get(Zend_Date::TIME_SHORT, true, 'es'),'23:31');

    	$this->assertSame($date->get(Zend_Date::ATOM),'2009-02-14T00:31:30+01:00');
    	$this->assertSame($date->get(Zend_Date::ATOM, true),'2009-02-13T23:31:30+00:00');
    	$this->assertSame($date->get(Zend_Date::ATOM, true, 'es'),'2009-02-13T23:31:30+00:00');

    	$this->assertSame($date->get(Zend_Date::COOKIE),'Saturday, 14-Feb-09 00:31:30 Europe/Paris');
    	$this->assertSame($date->get(Zend_Date::COOKIE, true),'Friday, 13-Feb-09 23:31:30 UTC');
    	$this->assertSame($date->get(Zend_Date::COOKIE, true, 'es'),'Friday, 13-Feb-09 23:31:30 UTC');

    	$this->assertSame($date->get(Zend_Date::RFC_822),'Sat, 14 Feb 09 00:31:30 +0100');
    	$this->assertSame($date->get(Zend_Date::RFC_822, true),'Fri, 13 Feb 09 23:31:30 +0000');
    	$this->assertSame($date->get(Zend_Date::RFC_822, true, 'es'),'Fri, 13 Feb 09 23:31:30 +0000');

    	$this->assertSame($date->get(Zend_Date::RFC_850),'Saturday, 14-Feb-09 00:31:30 Europe/Paris');
    	$this->assertSame($date->get(Zend_Date::RFC_850, true),'Friday, 13-Feb-09 23:31:30 UTC');
    	$this->assertSame($date->get(Zend_Date::RFC_850, true, 'es'),'Friday, 13-Feb-09 23:31:30 UTC');

    	$this->assertSame($date->get(Zend_Date::RFC_1036),'Sat, 14 Feb 09 00:31:30 +0100');
    	$this->assertSame($date->get(Zend_Date::RFC_1036, true),'Fri, 13 Feb 09 23:31:30 +0000');
    	$this->assertSame($date->get(Zend_Date::RFC_1036, true, 'es'),'Fri, 13 Feb 09 23:31:30 +0000');

    	$this->assertSame($date->get(Zend_Date::RFC_1123),'Sat, 14 Feb 2009 00:31:30 +0100');
    	$this->assertSame($date->get(Zend_Date::RFC_1123, true),'Fri, 13 Feb 2009 23:31:30 +0000');
    	$this->assertSame($date->get(Zend_Date::RFC_1123, true, 'es'),'Fri, 13 Feb 2009 23:31:30 +0000');

    	$this->assertSame($date->get(Zend_Date::RSS),'Sat, 14 Feb 2009 00:31:30 +0100');
    	$this->assertSame($date->get(Zend_Date::RSS, true),'Fri, 13 Feb 2009 23:31:30 +0000');
    	$this->assertSame($date->get(Zend_Date::RSS, true, 'es'),'Fri, 13 Feb 2009 23:31:30 +0000');

    	$this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');
    	$this->assertSame($date->get(Zend_Date::W3C, true),'2009-02-13T23:31:30+00:00');
    	$this->assertSame($date->get(Zend_Date::W3C, true, 'es'),'2009-02-13T23:31:30+00:00');

    	$this->assertSame($date->get('x'),'x');
    }

	/**
	 * Test for toValue
	 */
    public function testSet()
    {
        $locale = new Zend_Locale('de_AT');
    	$date = new Zend_Date(0,false,$locale);
    	$d2   = new Zend_Date(1010101010,false,$locale);

    	$retour = $date->set(1234567890);
        $this->assertSame($retour,'1234567890');
        $this->assertSame($date->set($d2),'1010101010');
        $this->assertSame($date->set(1234567891),'1234567891');

        try {
            $date->set('noday', Zend_Date::DAY);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::DAY);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-04T00:31:31+01:00');
        $date->set( 10, Zend_Date::DAY);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-10T00:31:31+01:00');
        $date->set( 40, Zend_Date::DAY);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-03-12T00:31:31+01:00');
        $date->set(-10, Zend_Date::DAY);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-18T00:31:31+01:00');
        $date->set( 10, Zend_Date::DAY, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-10T00:31:31+01:00');
        $date->set($d2, Zend_Date::DAY, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-03T00:31:31+01:00');
        $date->set( 10, Zend_Date::DAY, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-10T00:31:31+01:00');
        $date->set($d2, Zend_Date::DAY, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-04T00:31:31+01:00');
        $date->set(-20, Zend_Date::DAY, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-11T00:31:31+01:00');
        $date->set($d2, Zend_Date::DAY, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-03T00:31:31+01:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::WEEKDAY_SHORT);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::WEEKDAY_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T00:31:30+01:00');
        $date->set('Son', Zend_Date::WEEKDAY_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-08T00:31:30+01:00');
        $date->set('Mon', Zend_Date::WEEKDAY_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-09T00:31:30+01:00');
        $date->set('Fre', Zend_Date::WEEKDAY_SHORT, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');
        $date->set($d2, Zend_Date::WEEKDAY_SHORT, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T00:31:30+01:00');
        $date->set('Thu', Zend_Date::WEEKDAY_SHORT, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-12T00:31:30+01:00');
        $date->set($d2, Zend_Date::WEEKDAY_SHORT, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T00:31:30+01:00');
        $date->set('Fri', Zend_Date::WEEKDAY_SHORT, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');
        $date->set($d2, Zend_Date::WEEKDAY_SHORT, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T00:31:30+01:00');

        $date->set(1234567890);
        try {
            $date->set('xxx', Zend_Date::DAY_SHORT);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::DAY_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-04T00:31:30+01:00');
        $date->set( 10, Zend_Date::DAY_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-10T00:31:30+01:00');
        $date->set( 40, Zend_Date::DAY_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-03-12T00:31:30+01:00');
        $date->set(-10, Zend_Date::DAY_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-18T00:31:30+01:00');
        $date->set( 10, Zend_Date::DAY_SHORT, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-10T00:31:30+01:00');
        $date->set($d2, Zend_Date::DAY_SHORT, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-03T00:31:30+01:00');
        $date->set( 10, Zend_Date::DAY_SHORT, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-10T00:31:30+01:00');
        $date->set($d2, Zend_Date::DAY_SHORT, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-04T00:31:30+01:00');
        $date->set(-20, Zend_Date::DAY_SHORT, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-11T00:31:30+01:00');
        $date->set($d2, Zend_Date::DAY_SHORT, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-03T00:31:30+01:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::WEEKDAY);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::WEEKDAY);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T00:31:30+01:00');
        $date->set('Sonntag', Zend_Date::WEEKDAY);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-08T00:31:30+01:00');
        $date->set('Montag', Zend_Date::WEEKDAY);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-09T00:31:30+01:00');
        $date->set('Freitag', Zend_Date::WEEKDAY, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');
        $date->set($d2, Zend_Date::WEEKDAY, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T00:31:30+01:00');
        $date->set('Wednesday', Zend_Date::WEEKDAY, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-11T00:31:30+01:00');
        $date->set($d2, Zend_Date::WEEKDAY, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T00:31:30+01:00');
        $date->set('Friday', Zend_Date::WEEKDAY, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');
        $date->set($d2, Zend_Date::WEEKDAY, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T00:31:30+01:00');

        $date->set(1234567890);
        try {
            $date->set(0, Zend_Date::WEEKDAY_8601);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }
        try {
            $date->set('noday', Zend_Date::WEEKDAY_8601);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::WEEKDAY_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T00:31:30+01:00');
        $date->set(1, Zend_Date::WEEKDAY_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-09T00:31:30+01:00');
        $date->set(5, Zend_Date::WEEKDAY_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T00:31:30+01:00');
        $date->set(2, Zend_Date::WEEKDAY_8601, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-11T00:31:30+01:00');
        $date->set($d2, Zend_Date::WEEKDAY_8601, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T00:31:30+01:00');
        $date->set(4, Zend_Date::WEEKDAY_8601, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-12T00:31:30+01:00');
        $date->set($d2, Zend_Date::WEEKDAY_8601, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T00:31:30+01:00');
        $date->set(3, Zend_Date::WEEKDAY_8601, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-12T00:31:30+01:00');
        $date->set($d2, Zend_Date::WEEKDAY_8601, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T00:31:30+01:00');

        $date->set(1234567890);
        try {
            $date->set($d2, Zend_Date::DAY_SUFFIX);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set(1234567890);
        try {
            $date->set(0, Zend_Date::WEEKDAY_DIGIT);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        try {
            $date->set('noday', Zend_Date::WEEKDAY_DIGIT);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::WEEKDAY_DIGIT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');
        $date->set(1, Zend_Date::WEEKDAY_DIGIT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-10T00:31:30+01:00');
        $date->set(5, Zend_Date::WEEKDAY_DIGIT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');
        $date->set(2, Zend_Date::WEEKDAY_DIGIT, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-12T00:31:30+01:00');
        $date->set($d2, Zend_Date::WEEKDAY_DIGIT, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');
        $date->set(4, Zend_Date::WEEKDAY_DIGIT, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T00:31:30+01:00');
        $date->set($d2, Zend_Date::WEEKDAY_DIGIT, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');
        $date->set(3, Zend_Date::WEEKDAY_DIGIT, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T00:31:30+01:00');
        $date->set($d2, Zend_Date::WEEKDAY_DIGIT, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::DAY_OF_YEAR);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::DAY_OF_YEAR);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-03T00:31:30+01:00');
        $date->set( 124, Zend_Date::DAY_OF_YEAR);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-05-04T01:31:30+02:00');
        $date->set( 524, Zend_Date::DAY_OF_YEAR);
        $this->assertSame($date->get(Zend_Date::W3C),'2010-06-08T01:31:30+02:00');
        $date->set(-135, Zend_Date::DAY_OF_YEAR);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-08-18T01:31:30+02:00');
        $date->set( 422, Zend_Date::DAY_OF_YEAR, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2010-02-26T00:31:30+01:00');
        $date->set($d2, Zend_Date::DAY_OF_YEAR, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2010-01-02T00:31:30+01:00');
        $date->set( 12, Zend_Date::DAY_OF_YEAR, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2010-01-12T00:31:30+01:00');
        $date->set($d2, Zend_Date::DAY_OF_YEAR, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2010-01-03T00:31:30+01:00');
        $date->set(-253, Zend_Date::DAY_OF_YEAR, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-04-22T01:31:30+02:00');
        $date->set($d2, Zend_Date::DAY_OF_YEAR, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-02T00:31:30+01:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::WEEKDAY_NARROW);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::WEEKDAY_NARROW);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T00:31:30+01:00');
        $date->set('S', Zend_Date::WEEKDAY_NARROW);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-08T00:31:30+01:00');
        $date->set('M', Zend_Date::WEEKDAY_NARROW);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-09T00:31:30+01:00');
        $date->set('F', Zend_Date::WEEKDAY_NARROW, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');
        $date->set($d2, Zend_Date::WEEKDAY_NARROW, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-11T00:31:30+01:00');
        $date->set('W', Zend_Date::WEEKDAY_NARROW, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-11T00:31:30+01:00');
        $date->set($d2, Zend_Date::WEEKDAY_NARROW, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T00:31:30+01:00');
        $date->set('F', Zend_Date::WEEKDAY_NARROW, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');
        $date->set($d2, Zend_Date::WEEKDAY_NARROW, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-11T00:31:30+01:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::WEEKDAY_NAME);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::WEEKDAY_NAME);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T00:31:30+01:00');
        $date->set('So', Zend_Date::WEEKDAY_NAME);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-08T00:31:30+01:00');
        $date->set('Mo', Zend_Date::WEEKDAY_NAME);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-09T00:31:30+01:00');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-09T00:31:30+01:00');
        $date->set('Fr', Zend_Date::WEEKDAY_NAME, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');
        $date->set($d2, Zend_Date::WEEKDAY_NAME, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T00:31:30+01:00');
        $date->set('Thu', Zend_Date::WEEKDAY_NAME, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-12T00:31:30+01:00');
        $date->set($d2, Zend_Date::WEEKDAY_NAME, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T00:31:30+01:00');
        $date->set('Fri', Zend_Date::WEEKDAY_NAME, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');
        $date->set($d2, Zend_Date::WEEKDAY_NAME, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T00:31:30+01:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::WEEK);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::WEEK);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-03T00:31:30+01:00');
        $date->set( 1, Zend_Date::WEEK);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-03T00:31:30+01:00');
        $date->set( 55, Zend_Date::WEEK);
        $this->assertSame($date->get(Zend_Date::W3C),'2010-01-16T00:31:30+01:00');
        $date->set(-57, Zend_Date::WEEK);
        $this->assertSame($date->get(Zend_Date::W3C),'2008-11-29T00:31:30+01:00');
        $date->set( 50, Zend_Date::WEEK, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2008-12-13T00:31:30+01:00');
        $date->set($d2, Zend_Date::WEEK, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2008-01-05T00:31:30+01:00');
        $date->set( 10, Zend_Date::WEEK, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2008-03-08T00:31:30+01:00');
        $date->set($d2, Zend_Date::WEEK, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2008-01-05T00:31:30+01:00');
        $date->set(-25, Zend_Date::WEEK, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2007-07-07T01:31:30+02:00');
        $date->set($d2, Zend_Date::WEEK, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2007-01-06T00:31:30+01:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::MONTH);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::MONTH);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-14T00:31:30+01:00');
        $date->set('März', Zend_Date::MONTH);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-03-14T00:31:30+01:00');
        $date->set('Dezember', Zend_Date::MONTH);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-12-14T00:31:30+01:00');
        $date->set('August', Zend_Date::MONTH, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-08-14T01:31:30+02:00');
        $date->set($d2, Zend_Date::MONTH, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-14T00:31:30+01:00');
        $date->set('April', Zend_Date::MONTH, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-04-14T01:31:30+02:00');
        $date->set($d2, Zend_Date::MONTH, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-14T00:31:30+01:00');
        $date->set('July', Zend_Date::MONTH, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-07-14T01:31:30+02:00');
        $date->set($d2, Zend_Date::MONTH, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-14T00:31:30+01:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::MONTH_SHORT);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::MONTH_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-14T00:31:30+01:00');
        $date->set('03', Zend_Date::MONTH_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-03-14T00:31:30+01:00');
        $date->set( 14, Zend_Date::MONTH_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2010-02-14T00:31:30+01:00');
        $date->set(-6, Zend_Date::MONTH_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-06-14T01:31:30+02:00');
        $date->set( 10, Zend_Date::MONTH_SHORT, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-10-14T01:31:30+02:00');
        $date->set($d2, Zend_Date::MONTH_SHORT, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-14T00:31:30+01:00');
        $date->set( 9, Zend_Date::MONTH_SHORT, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-09-14T01:31:30+02:00');
        $date->set($d2, Zend_Date::MONTH_SHORT, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-14T00:31:30+01:00');
        $date->set(-20, Zend_Date::MONTH_SHORT, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2007-04-15T01:31:30+02:00');
        $date->set($d2, Zend_Date::MONTH_SHORT, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2007-01-15T00:31:30+01:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::MONTH_NAME);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::MONTH_NAME);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-14T00:31:30+01:00');
        $date->set('Mär', Zend_Date::MONTH_NAME);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-03-14T00:31:30+01:00');
        $date->set('Dez', Zend_Date::MONTH_NAME);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-12-14T00:31:30+01:00');
        $date->set('Aug', Zend_Date::MONTH_NAME, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-08-14T01:31:30+02:00');
        $date->set($d2, Zend_Date::MONTH_NAME, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-14T00:31:30+01:00');
        $date->set('Apr', Zend_Date::MONTH_NAME, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-04-14T01:31:30+02:00');
        $date->set($d2, Zend_Date::MONTH_NAME, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-14T00:31:30+01:00');
        $date->set('Jul', Zend_Date::MONTH_NAME, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-07-14T01:31:30+02:00');
        $date->set($d2, Zend_Date::MONTH_NAME, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-14T00:31:30+01:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::MONTH_DIGIT);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::MONTH_DIGIT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-14T00:31:30+01:00');
        $date->set(  3, Zend_Date::MONTH_DIGIT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-03-14T00:31:30+01:00');
        $date->set( 14, Zend_Date::MONTH_DIGIT);
        $this->assertSame($date->get(Zend_Date::W3C),'2010-02-14T00:31:30+01:00');
        $date->set(-6, Zend_Date::MONTH_DIGIT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-06-14T01:31:30+02:00');
        $date->set( 10, Zend_Date::MONTH_DIGIT, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-10-14T01:31:30+02:00');
        $date->set($d2, Zend_Date::MONTH_DIGIT, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-14T00:31:30+01:00');
        $date->set( 9, Zend_Date::MONTH_DIGIT, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-09-14T01:31:30+02:00');
        $date->set($d2, Zend_Date::MONTH_DIGIT, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-14T00:31:30+01:00');
        $date->set(-20, Zend_Date::MONTH_DIGIT, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2007-04-15T01:31:30+02:00');
        $date->set($d2, Zend_Date::MONTH_DIGIT, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2007-01-15T00:31:30+01:00');

        $date->set(1234567890);
        try {
            $date->set($d2, Zend_Date::MONTH_DAYS);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set(1234567890);
        try {
            $date->set('xxday', Zend_Date::MONTH_NARROW);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::MONTH_NARROW);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-14T00:31:30+01:00');
        $date->set('M', Zend_Date::MONTH_NARROW);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-03-14T00:31:30+01:00');
        $date->set('D', Zend_Date::MONTH_NARROW);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-12-14T00:31:30+01:00');
        $date->set('A', Zend_Date::MONTH_NARROW, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-04-14T01:31:30+02:00');
        $date->set($d2, Zend_Date::MONTH_NARROW, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-14T00:31:30+01:00');
        $date->set('A', Zend_Date::MONTH_NARROW, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-04-14T01:31:30+02:00');
        $date->set($d2, Zend_Date::MONTH_NARROW, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-14T00:31:30+01:00');
        $date->set('J', Zend_Date::MONTH_NARROW, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-14T00:31:30+01:00');
        $date->set($d2, Zend_Date::MONTH_NARROW, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-14T00:31:30+01:00');

        $date->set(1234567890);
        try {
            $date->set($d2, Zend_Date::LEAPYEAR);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::YEAR_8601);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::YEAR_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-02-14T00:31:30+01:00');
        $date->set(1970, Zend_Date::YEAR_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'1970-02-14T00:31:30+01:00');
        $date->set(2020, Zend_Date::YEAR_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2020-02-14T00:31:30+01:00');
        $date->set(2040, Zend_Date::YEAR_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2040-02-14T00:31:30+01:00');
        $date->set(1900, Zend_Date::YEAR_8601, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'1900-02-14T00:31:30+01:00');
        $date->set($d2, Zend_Date::YEAR_8601, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-02-14T00:31:30+01:00');
        $date->set(2500, Zend_Date::YEAR_8601, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2500-02-14T00:31:30+01:00');
        $date->set($d2, Zend_Date::YEAR_8601, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2002-02-14T00:31:30+01:00');
        $date->set(-20, Zend_Date::YEAR_8601, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'-20-02-14T00:31:30+01:00');
        $date->set($d2, Zend_Date::YEAR_8601, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2002-02-14T00:31:30+01:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::YEAR);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::YEAR);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-02-14T00:31:30+01:00');
        $date->set(1970, Zend_Date::YEAR);
        $this->assertSame($date->get(Zend_Date::W3C),'1970-02-14T00:31:30+01:00');
        $date->set(2020, Zend_Date::YEAR);
        $this->assertSame($date->get(Zend_Date::W3C),'2020-02-14T00:31:30+01:00');
        $date->set(2040, Zend_Date::YEAR);
        $this->assertSame($date->get(Zend_Date::W3C),'2040-02-14T00:31:30+01:00');
        $date->set(1900, Zend_Date::YEAR, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'1900-02-14T00:31:30+01:00');
        $date->set($d2, Zend_Date::YEAR, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-02-14T00:31:30+01:00');
        $date->set(2500, Zend_Date::YEAR, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2500-02-14T00:31:30+01:00');
        $date->set($d2, Zend_Date::YEAR, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2002-02-14T00:31:30+01:00');
        $date->set(-20, Zend_Date::YEAR, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'-20-02-14T00:31:30+01:00');
        $date->set($d2, Zend_Date::YEAR, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2002-02-14T00:31:30+01:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::YEAR_SHORT);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::YEAR_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-02-14T00:31:30+01:00');
        $date->set(70, Zend_Date::YEAR_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'1970-02-14T00:31:30+01:00');
        $date->set(20, Zend_Date::YEAR_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2020-02-14T00:31:30+01:00');
        $date->set(40, Zend_Date::YEAR_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2040-02-14T00:31:30+01:00');
        $date->set(0, Zend_Date::YEAR_SHORT, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2000-02-14T00:31:30+01:00');
        $date->set($d2, Zend_Date::YEAR_SHORT, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-02-14T00:31:30+01:00');
        $date->set(30, Zend_Date::YEAR_SHORT, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2030-02-14T00:31:30+01:00');
        $date->set($d2, Zend_Date::YEAR_SHORT, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2002-02-14T00:31:30+01:00');
        try {
            $date->set(-20, Zend_Date::YEAR_SHORT, TRUE , 'en_US');
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::YEAR_SHORT, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2002-02-14T00:31:30+01:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::MERIDIEM);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::SWATCH);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::SWATCH);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:36:00+01:00');
        $date->set(0, Zend_Date::SWATCH);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:00:00+01:00');
        $date->set(600, Zend_Date::SWATCH);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T14:23:59+01:00');
        $date->set(1700, Zend_Date::SWATCH);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-15T16:47:59+01:00');
        $date->set(1900, Zend_Date::SWATCH, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-16T21:36:00+01:00');
        $date->set($d2, Zend_Date::SWATCH, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-16T00:36:00+01:00');
        $date->set(3700, Zend_Date::SWATCH, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-19T16:48:00+01:00');
        $date->set($d2, Zend_Date::SWATCH, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-19T00:36:00+01:00');
        $date->set(-200, Zend_Date::SWATCH, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-18T19:12:00+01:00');
        $date->set($d2, Zend_Date::SWATCH, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-18T00:36:00+01:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::HOUR_SHORT_AM);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::HOUR_SHORT_AM);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T12:31:30+01:00');
        $date->set(  3, Zend_Date::HOUR_SHORT_AM);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T03:31:30+01:00');
        $date->set( 14, Zend_Date::HOUR_SHORT_AM);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T14:31:30+01:00');
        $date->set(-6, Zend_Date::HOUR_SHORT_AM);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T18:31:30+01:00');
        $date->set( 30, Zend_Date::HOUR_SHORT_AM, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T06:31:30+01:00');
        $date->set($d2, Zend_Date::HOUR_SHORT_AM, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T11:31:30+01:00');
        $date->set( 9, Zend_Date::HOUR_SHORT_AM, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T09:31:30+01:00');
        $date->set($d2, Zend_Date::HOUR_SHORT_AM, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T12:31:30+01:00');
        $date->set(-26, Zend_Date::HOUR_SHORT_AM, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-12T22:31:30+01:00');
        $date->set($d2, Zend_Date::HOUR_SHORT_AM, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-12T11:31:30+01:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::HOUR_SHORT_AM);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::HOUR_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');
        $date->set(  3, Zend_Date::HOUR_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T03:31:30+01:00');
        $date->set( 14, Zend_Date::HOUR_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T14:31:30+01:00');
        $date->set(-6, Zend_Date::HOUR_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T18:31:30+01:00');
        $date->set( 30, Zend_Date::HOUR_SHORT, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T06:31:30+01:00');
        $date->set($d2, Zend_Date::HOUR_SHORT, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T23:31:30+01:00');
        $date->set( 9, Zend_Date::HOUR_SHORT, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T09:31:30+01:00');
        $date->set($d2, Zend_Date::HOUR_SHORT, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');
        $date->set(-26, Zend_Date::HOUR_SHORT, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-12T22:31:30+01:00');
        $date->set($d2, Zend_Date::HOUR_SHORT, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-12T23:31:30+01:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::HOUR_SHORT_AM);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::HOUR_AM);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T12:31:30+01:00');
        $date->set(  3, Zend_Date::HOUR_AM);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T03:31:30+01:00');
        $date->set( 14, Zend_Date::HOUR_AM);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T14:31:30+01:00');
        $date->set(-6, Zend_Date::HOUR_AM);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T18:31:30+01:00');
        $date->set( 30, Zend_Date::HOUR_AM, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T06:31:30+01:00');
        $date->set($d2, Zend_Date::HOUR_AM, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T11:31:30+01:00');
        $date->set( 9, Zend_Date::HOUR_AM, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T09:31:30+01:00');
        $date->set($d2, Zend_Date::HOUR_AM, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T12:31:30+01:00');
        $date->set(-26, Zend_Date::HOUR_AM, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-12T22:31:30+01:00');
        $date->set($d2, Zend_Date::HOUR_AM, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-12T11:31:30+01:00');
        
        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::HOUR);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::HOUR);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');
        $date->set(  3, Zend_Date::HOUR);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T03:31:30+01:00');
        $date->set( 14, Zend_Date::HOUR);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T14:31:30+01:00');
        $date->set(-6, Zend_Date::HOUR);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T18:31:30+01:00');
        $date->set( 30, Zend_Date::HOUR, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T06:31:30+01:00');
        $date->set($d2, Zend_Date::HOUR, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T23:31:30+01:00');
        $date->set( 9, Zend_Date::HOUR, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T09:31:30+01:00');
        $date->set($d2, Zend_Date::HOUR, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');
        $date->set(-26, Zend_Date::HOUR, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-12T22:31:30+01:00');
        $date->set($d2, Zend_Date::HOUR, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-12T23:31:30+01:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::MINUTE);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::MINUTE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:36:30+01:00');
        $date->set(  3, Zend_Date::MINUTE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:03:30+01:00');
        $date->set( 65, Zend_Date::MINUTE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T01:05:30+01:00');
        $date->set(-6, Zend_Date::MINUTE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:54:30+01:00');
        $date->set( 30, Zend_Date::MINUTE, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:30:30+01:00');
        $date->set($d2, Zend_Date::MINUTE, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:36:30+01:00');
        $date->set( 9, Zend_Date::MINUTE, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:09:30+01:00');
        $date->set($d2, Zend_Date::MINUTE, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:36:30+01:00');
        $date->set(-65, Zend_Date::MINUTE, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T22:55:30+01:00');
        $date->set($d2, Zend_Date::MINUTE, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T22:36:30+01:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::MINUTE_SHORT);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::MINUTE_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:36:30+01:00');
        $date->set(  3, Zend_Date::MINUTE_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:03:30+01:00');
        $date->set( 65, Zend_Date::MINUTE_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T01:05:30+01:00');
        $date->set(-6, Zend_Date::MINUTE_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:54:30+01:00');
        $date->set( 30, Zend_Date::MINUTE_SHORT, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:30:30+01:00');
        $date->set($d2, Zend_Date::MINUTE_SHORT, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:36:30+01:00');
        $date->set( 9, Zend_Date::MINUTE_SHORT, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:09:30+01:00');
        $date->set($d2, Zend_Date::MINUTE_SHORT, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:36:30+01:00');
        $date->set(-65, Zend_Date::MINUTE_SHORT, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T22:55:30+01:00');
        $date->set($d2, Zend_Date::MINUTE_SHORT, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T22:36:30+01:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::SECOND);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
/*        $date->set($d2, Zend_Date::SECOND);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');
        $date->set(  3, Zend_Date::SECOND);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');
        $date->set( 65, Zend_Date::SECOND);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');
        $date->set(-6, Zend_Date::SECOND);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T01:31:30+01:00');
        $date->set( 30, Zend_Date::SECOND, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T01:31:30+01:00');
        $date->set($d2, Zend_Date::SECOND, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');
        $date->set( 9, Zend_Date::SECOND, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T01:31:30+01:00');
        $date->set($d2, Zend_Date::SECOND, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');
        $date->set(-65, Zend_Date::SECOND, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T01:31:30+01:00');
        $date->set($d2, Zend_Date::SECOND, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::SECOND_SHORT);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::SECOND_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');
        $date->set(  3, Zend_Date::SECOND_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');
        $date->set( 65, Zend_Date::SECOND_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');
        $date->set(-6, Zend_Date::SECOND_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T01:31:30+01:00');
        $date->set( 30, Zend_Date::SECOND_SHORT, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T01:31:30+01:00');
        $date->set($d2, Zend_Date::SECOND_SHORT, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');
        $date->set( 9, Zend_Date::SECOND_SHORT, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T01:31:30+01:00');
        $date->set($d2, Zend_Date::SECOND_SHORT, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');
        $date->set(-65, Zend_Date::SECOND_SHORT, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T01:31:30+01:00');
        $date->set($d2, Zend_Date::SECOND_SHORT, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');
        
/**
    	$this->assertSame($date->get(Zend_Date::MILLISECOND),0);
    	$this->assertSame($date->get(Zend_Date::TIMEZONE_NAME),'Europe/Paris');
    	$this->assertSame($date->get(Zend_Date::DAYLIGHT),'0');
    	$this->assertSame($date->get(Zend_Date::GMT_DIFF),'+0100');
    	$this->assertSame($date->get(Zend_Date::GMT_DIFF_SEP),'+01:00');
    	$this->assertSame($date->get(Zend_Date::TIMEZONE),'CET');
    	$this->assertSame($date->get(Zend_Date::TIMEZONE_SECS),'3600');
    	$this->assertSame($date->get(Zend_Date::ISO_8601),'2009-02-14T00:31:30+01:00');
    	$this->assertSame($date->get(Zend_Date::RFC_2822),'Sat, 14 Feb 2009 00:31:30 +0100');
    	$this->assertSame($date->get(Zend_Date::TIMESTAMP),1234567890);
    	$this->assertSame($date->get(Zend_Date::ERA),'n. Chr.');
    	$this->assertSame($date->get(Zend_Date::ERA_NAME),'n. Chr.');
    	$this->assertSame($date->get(Zend_Date::DATES),'14.02.2009');
    	$this->assertSame($date->get(Zend_Date::DATE_FULL),'Samstag, 14. Februar 2009');
    	$this->assertSame($date->get(Zend_Date::DATE_LONG),'14. Februar 2009');
    	$this->assertSame($date->get(Zend_Date::DATE_MEDIUM),'14.02.2009');
    	$this->assertSame($date->get(Zend_Date::DATE_SHORT),'14.02.09');
    	$this->assertSame($date->get(Zend_Date::TIMES),'00:31:30');
    	$this->assertSame($date->get(Zend_Date::TIME_FULL),'00:31 Uhr CET');
    	$this->assertSame($date->get(Zend_Date::TIME_LONG),'00:31:30 CET');
    	$this->assertSame($date->get(Zend_Date::TIME_MEDIUM),'00:31:30');
    	$this->assertSame($date->get(Zend_Date::TIME_SHORT),'00:31');
    	$this->assertSame($date->get(Zend_Date::ATOM),'2009-02-14T00:31:30+01:00');
    	$this->assertSame($date->get(Zend_Date::COOKIE),'Saturday, 14-Feb-09 00:31:30 Europe/Paris');
    	$this->assertSame($date->get(Zend_Date::RFC_822),'Sat, 14 Feb 09 00:31:30 +0100');
    	$this->assertSame($date->get(Zend_Date::RFC_850),'Saturday, 14-Feb-09 00:31:30 Europe/Paris');
    	$this->assertSame($date->get(Zend_Date::RFC_1036),'Sat, 14 Feb 09 00:31:30 +0100');
    	$this->assertSame($date->get(Zend_Date::RFC_1123),'Sat, 14 Feb 2009 00:31:30 +0100');
    	$this->assertSame($date->get(Zend_Date::RSS),'Sat, 14 Feb 2009 00:31:30 +0100');
    	$this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');
    	$this->assertSame($date->get('x'),'x');
*/
    }
}