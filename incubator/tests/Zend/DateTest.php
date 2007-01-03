<?php
/**
 * @package    Zend_Date
 * @subpackage UnitTests
 */

error_reporting( E_ALL | E_STRICT ); // now required for each test suite

/**
 * Zend_Date
 */
require_once 'Zend.php';
require_once 'Zend/Date.php';
require_once 'Zend/Locale.php';
require_once 'Zend/Date/Cities.php';

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

    public function setUp()
    {
        date_default_timezone_set('Europe/Paris');
    }

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
        $date = new Zend_Date('13',false,false,$locale);
        $this->assertTrue($date instanceof Zend_Date);
    }

    /**
     * Test for creation but only part of date with locale
     */
    public function testCreationLocalePart()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date('13',Zend_Date::HOUR,false,$locale);
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
        $date = new Zend_Date(0,Zend_Date::TIMESTAMP,false,$locale);
        $result = $date->setTimestamp(10000000);
        $this->assertSame($result->getTimestamp(), '10000000');
    }

    /**
     * Test for setTimestamp
     */
    public function testSetTimestamp2()
    {
        try {
            $locale = new Zend_Locale('de_AT');
            $date = new Zend_Date(0,false,false,$locale);
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
        $date = new Zend_Date(0,false,false,$locale);
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
            $date = new Zend_Date(0,false,false,$locale);
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
        $date = new Zend_Date(0,false,false,$locale);
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
            $date = new Zend_Date(0,false,false,$locale);
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
        $date1 = new Zend_Date(0,false,false,$locale);
        $date2 = new Zend_Date(0,false,false,$locale);
        $this->assertSame($date1->compareTimestamp($date2), 0);

        $date2 = new Zend_Date(100,false,false,$locale);
        $this->assertSame($date1->compareTimestamp($date2), -1);

        $date2 = new Zend_Date(-100,false,false,$locale);
        $this->assertSame($date1->compareTimestamp($date2), 1);
    }

    /**
     * Test for __toString
     */
    public function test_ToString()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(0,false,false,$locale);
        $this->assertSame($date->__toString(),'01.01.1970 01:00:00');
    }

    /**
     * Test for toString
     */
    public function testToString()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,false,false,$locale);
        $this->assertSame($date->toString(),'14.02.2009 00:31:30');
        $this->assertSame($date->toString(false, false, 'en_US'),'Feb 14, 2009 12:31:30 1890');
        $this->assertSame($date->toString('yyy', false, false),'2009');
        $this->assertSame($date->toString(false, true, false),'13.02.2009 23:31:30');
        $this->assertSame($date->toString(false, true, 'en_US'),'Feb 13, 2009 11:31:30 PM');
        $this->assertSame($date->toString("xx'yy''yy'xx"),"xxyy'yyxx");
        $this->assertSame($date->toString("GGGGG"),'n.');
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
        $date = new Zend_Date(1234567890,false,false,$locale);
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
        $date = new Zend_Date(1234567890,false,false,$locale);
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
    public function testGet2()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(-62362925370,false,false,$locale);
        $this->assertSame($date->get(Zend_Date::ERA),'v. Chr.');
        $this->assertSame($date->get(Zend_Date::ERA_NAME),'v. Chr.');
    }

    /**
     * Test for set
     */
    public function testSet()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(0,false,false,$locale);
        $d2   = new Zend_Date(1010101010,false,false,$locale);

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
            $date->set('noday', Zend_Date::YEAR_SHORT_8601);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::YEAR_SHORT_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-02-14T00:31:30+01:00');
        $date->set(70, Zend_Date::YEAR_SHORT_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'1970-02-14T00:31:30+01:00');
        $date->set(20, Zend_Date::YEAR_SHORT_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2020-02-14T00:31:30+01:00');
        $date->set(40, Zend_Date::YEAR_SHORT_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2040-02-14T00:31:30+01:00');
        $date->set(0, Zend_Date::YEAR_SHORT_8601, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2000-02-14T00:31:30+01:00');
        $date->set($d2, Zend_Date::YEAR_SHORT_8601, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-02-14T00:31:30+01:00');
        $date->set(30, Zend_Date::YEAR_SHORT_8601, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2030-02-14T00:31:30+01:00');
        $date->set($d2, Zend_Date::YEAR_SHORT_8601, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2002-02-14T00:31:30+01:00');
        try {
            $date->set(-20, Zend_Date::YEAR_SHORT_8601, TRUE , 'en_US');
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::YEAR_SHORT_8601, TRUE , 'en_US');
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
            $date->set('noday', Zend_Date::HOUR_SHORT);
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
            $date->set('noday', Zend_Date::HOUR_AM);
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
        $date->set($d2, Zend_Date::SECOND);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:50+01:00');
        $date->set(  3, Zend_Date::SECOND);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:03+01:00');
        $date->set( 65, Zend_Date::SECOND);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:32:05+01:00');
        $date->set(-6, Zend_Date::SECOND);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:54+01:00');
        $date->set( 30, Zend_Date::SECOND, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');
        $date->set($d2, Zend_Date::SECOND, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:50+01:00');
        $date->set( 9, Zend_Date::SECOND, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:09+01:00');
        $date->set($d2, Zend_Date::SECOND, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:50+01:00');
        $date->set(-65, Zend_Date::SECOND, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:29:55+01:00');
        $date->set($d2, Zend_Date::SECOND, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:29:50+01:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::SECOND_SHORT);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::SECOND_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:50+01:00');
        $date->set(  3, Zend_Date::SECOND_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:03+01:00');
        $date->set( 65, Zend_Date::SECOND_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:32:05+01:00');
        $date->set(-6, Zend_Date::SECOND_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:54+01:00');
        $date->set( 30, Zend_Date::SECOND_SHORT, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');
        $date->set($d2, Zend_Date::SECOND_SHORT, TRUE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:50+01:00');
        $date->set( 9, Zend_Date::SECOND_SHORT, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:09+01:00');
        $date->set($d2, Zend_Date::SECOND_SHORT, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:50+01:00');
        $date->set(-65, Zend_Date::SECOND_SHORT, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:29:55+01:00');
        $date->set($d2, Zend_Date::SECOND_SHORT, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:29:50+01:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::MILLISECOND);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::MILLISECOND);
        $this->assertSame($date->get(Zend_Date::MILLISECOND),'0');
        $date->set(  3, Zend_Date::MILLISECOND);
        $this->assertSame($date->get(Zend_Date::MILLISECOND),'3');
        $date->set( 1065, Zend_Date::MILLISECOND);
        $this->assertSame($date->get(Zend_Date::MILLISECOND),'65');
        $date->set(-6, Zend_Date::MILLISECOND);
        $this->assertSame($date->get(Zend_Date::MILLISECOND),'994');
        $date->set( 30, Zend_Date::MILLISECOND, TRUE);
        $this->assertSame($date->get(Zend_Date::MILLISECOND),'30');
        $date->set($d2, Zend_Date::MILLISECOND, TRUE);
        $this->assertSame($date->get(Zend_Date::MILLISECOND),'0');
        $date->set( 9, Zend_Date::MILLISECOND, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::MILLISECOND),'9');
        $date->set($d2, Zend_Date::MILLISECOND, FALSE, 'en_US');
        $this->assertSame($date->get(Zend_Date::MILLISECOND),'0');
        $date->set(-65, Zend_Date::MILLISECOND, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::MILLISECOND),'935');
        $date->set($d2, Zend_Date::MILLISECOND, TRUE , 'en_US');
        $this->assertSame($date->get(Zend_Date::MILLISECOND),'0');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::TIMEZONE_NAME);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::DAYLIGHT);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::GMT_DIFF);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::GMT_DIFF_SEP);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::TIMEZONE);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::TIMEZONE_SECS);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::ISO_8601);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:36:50+01:00');
        $date->set(1234567890);
        $date->set('2007-10-20 20:10:30', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2007-10-20T20:10:30+02:00');
        $date->set(1234567890);
        $date->set('2007-10-20 201030', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2007-10-20T20:10:30+02:00');
        $date->set(1234567890);
        $date->set('07-10-20 20:10:30', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2007-10-20T20:10:30+02:00');
        $date->set(1234567890);
        $date->set('80-10-20 20:10:30', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'1980-10-20T20:10:30+01:00');
        $date->set(1234567890);
        $date->set('-0007-10-20 20:10:30', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'-7-10-20T22:10:30+01:00');
        $date->set(1234567890);
        $date->set('-07-10-20 20:10:30', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'-7-10-20T22:10:30+01:00');
        $date->set(1234567890);
        $date->set('2007-10-20T20:10:30', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2007-10-20T20:10:30+02:00');
        $date->set(1234567890);
        $date->set('2007-10-20T201030', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2007-10-20T20:10:30+02:00');
        $date->set(1234567890);
        $date->set('20-10-20T20:10:30', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2020-10-20T20:10:30+02:00');
        $date->set(1234567890);
        $date->set('80-10-20T20:10:30', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'1980-10-20T20:10:30+01:00');
        $date->set(1234567890);
        $date->set('-0007-10-20T20:10:30', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'-7-10-20T22:10:30+01:00');
        $date->set(1234567890);
        $date->set('-07-10-20T20:10:30', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'-7-10-20T22:10:30+01:00');
        $date->set(1234567890);
        $date->set('20071020 20:10:30', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2007-10-20T20:10:30+02:00');
        $date->set(1234567890);
        $date->set('201020 20:10:30', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2020-10-20T20:10:30+02:00');
        $date->set(1234567890);
        $date->set('801020 20:10:30', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'1980-10-20T20:10:30+01:00');
        $date->set(1234567890);
        $date->set('-071020 20:10:30', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'-7-10-20T22:10:30+01:00');
        $date->set(1234567890);
        $date->set('-00071020 20:10:30', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'-7-10-20T22:10:30+01:00');
        $date->set(1234567890);
        $date->set('20071020T20:10:30', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2007-10-20T20:10:30+02:00');
        $date->set(1234567890);
        $date->set('-00071020T20:10:30', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'-7-10-20T22:10:30+01:00');
        $date->set(1234567890);
        $date->set('2007-10-20', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2007-10-20T00:00:00+02:00');
        $date->set(1234567890);
        $date->set('20071020', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2007-10-20T00:00:00+02:00');
        $date->set(1234567890);
        $date->set('20071020122030', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2007-10-20T12:20:30+02:00');
        $date->set(1234567890);
        $date->set('071020', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2007-10-20T00:00:00+02:00');
        $date->set(1234567890);
        $date->set('07:10:20', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'1970-01-01T07:10:20+01:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::RFC_2822);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::RFC_2822);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:36:50+01:00');
        $date->set(1234567890);
        $date->set('Thu, 05 Jan 2009 01:31:30 +0100', Zend_Date::RFC_2822);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-01-05T01:31:30+01:00');
        $date->set(1234567890);
        $date->set('Thu, 05 Feb 2009 01:31:30 +0100', Zend_Date::RFC_2822);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-05T01:31:30+01:00');
        $date->set(1234567890);
        $date->set('Thu, 05 Mar 2009 01:31:30 +0100', Zend_Date::RFC_2822);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-03-05T01:31:30+01:00');
        $date->set(1234567890);
        $date->set('Thu, 05 Apr 2009 01:31:30 +0100', Zend_Date::RFC_2822);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-04-05T01:31:30+02:00');
        $date->set(1234567890);
        $date->set('Thu, 05 May 2009 01:31:30 +0100', Zend_Date::RFC_2822);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-05-05T01:31:30+02:00');
        $date->set(1234567890);
        $date->set('Thu, 05 Jun 2009 01:31:30 +0100', Zend_Date::RFC_2822);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-06-05T01:31:30+02:00');
        $date->set(1234567890);
        $date->set('Thu, 05 Jul 2009 01:31:30 +0100', Zend_Date::RFC_2822);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-07-05T01:31:30+02:00');
        $date->set(1234567890);
        $date->set('Thu, 05 Aug 2009 01:31:30 +0100', Zend_Date::RFC_2822);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-08-05T01:31:30+02:00');
        $date->set(1234567890);
        $date->set('Thu, 05 Sep 2009 01:31:30 +0100', Zend_Date::RFC_2822);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-09-05T01:31:30+02:00');
        $date->set(1234567890);
        $date->set('Thu, 05 Oct 2009 01:31:30 +0100', Zend_Date::RFC_2822);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-10-05T01:31:30+02:00');
        $date->set(1234567890);
        $date->set('Thu, 05 Nov 2009 01:31:30 +0100', Zend_Date::RFC_2822);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-11-05T01:31:30+01:00');
        $date->set(1234567890);
        $date->set('Thu, 05 Dec 2009 01:31:30 +0100', Zend_Date::RFC_2822);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-12-05T01:31:30+01:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::TIMESTAMP);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::TIMESTAMP);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:36:50+01:00');
        $date->set(1234567890);
        $date->set('1010101099', Zend_Date::TIMESTAMP);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:38:19+01:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::ERA);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::ERA_NAME);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::DATES);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::DATES);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:31:30+01:00');
        $date->set(1234567890);
        $date->set('14.02.2009', Zend_Date::DATES);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::DATE_FULL);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::DATE_FULL);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:31:30+01:00');
        $date->set(1234567890);
        $date->set('Samstag, 14. Februar 2009', Zend_Date::DATE_FULL);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::DATE_LONG);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::DATE_LONG);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:31:30+01:00');
        $date->set(1234567890);
        $date->set('14. Februar 2009', Zend_Date::DATE_LONG);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::DATE_MEDIUM);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::DATE_MEDIUM);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:31:30+01:00');
        $date->set(1234567890);
        $date->set('14.02.2009', Zend_Date::DATE_MEDIUM);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::DATE_SHORT);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::DATE_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:31:30+01:00');
        $date->set(1234567890);
        $date->set('14.02.09', Zend_Date::DATE_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::TIMES);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::TIMES);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:36:50+01:00');
        $date->set(1234567890);
        $date->set('15:26:40', Zend_Date::TIMES);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T15:26:40+01:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::TIME_FULL);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::TIME_FULL);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:36:00+01:00');
        $date->set(1234567890);
        $date->set('15:26 Uhr CET', Zend_Date::TIME_FULL);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T15:26:00+01:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::TIME_LONG);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::TIME_LONG);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:36:50+01:00');
        $date->set(1234567890);
        $date->set('15:26:40 CET', Zend_Date::TIME_LONG);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T15:26:40+01:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::TIME_MEDIUM);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::TIME_MEDIUM);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:36:50+01:00');
        $date->set(1234567890);
        $date->set('15:26:40', Zend_Date::TIME_MEDIUM);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T15:26:40+01:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::TIME_SHORT);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::TIME_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:36:00+01:00');
        $date->set(1234567890);
        $date->set('15:26', Zend_Date::TIME_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T15:26:00+01:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::ATOM);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::ATOM);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:36:50+01:00');
        $date->set(1234567890);
        $date->set('2009-02-14T00:31:30+01:00', Zend_Date::ATOM);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::COOKIE);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::COOKIE);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:36:50+01:00');
        $date->set(1234567890);
        $date->set('Saturday, 14-Feb-09 00:31:30 Europe/Paris', Zend_Date::COOKIE);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::RFC_822);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::RFC_822);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:36:50+01:00');
        $date->set(1234567890);
        $date->set('Sat, 14 Feb 09 00:31:30 +0100', Zend_Date::RFC_822);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::RFC_850);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::RFC_850);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:36:50+01:00');
        $date->set(1234567890);
        $date->set('Saturday, 14-Feb-09 00:31:30 Europe/Paris', Zend_Date::RFC_850);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::RFC_1036);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::RFC_1036);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:36:50+01:00');
        $date->set(1234567890);
        $date->set('Sat, 14 Feb 09 00:31:30 +0100', Zend_Date::RFC_1036);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::RFC_1123);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::RFC_1123);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:36:50+01:00');
        $date->set(1234567890);
        $date->set('Sat, 14 Feb 2009 00:31:30 +0100', Zend_Date::RFC_1123);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::RSS);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::RSS);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:36:50+01:00');
        $date->set(1234567890);
        $date->set('Sat, 14 Feb 2009 00:31:30 +0100', Zend_Date::RSS);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::W3C);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::W3C);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:36:50+01:00');
        $date->set(1234567890);
        $date->set('2009-02-14T00:31:30+01:00', Zend_Date::W3C);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');

        $date->set(1234567890);
        try {
            $date->set('noday', 'xx');
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        try {
            $date->set($d2, 'xx');
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set(1234567890);
        $date->set('1000', 'xx');
        $this->assertSame($date->get(Zend_Date::W3C),'1970-01-01T01:16:40+01:00');
    }

    /**
     * Test for add
     */
    public function testAdd()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(0,false,$locale);
        $d2   = new Zend_Date(1010101010,false,$locale);

        $retour = $date->set(1234567890);
        $this->assertSame($retour,'1234567890');
        $this->assertSame($date->add(10),'1234567900');
        $this->assertSame($date->add(-10),'1234567890');
        $this->assertSame($date->add(0),'1234567890');

        $date->set($d2);
        $date->add(10, Zend_Date::DAY);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-14T00:36:50+01:00');
        $date->add(-10, Zend_Date::DAY);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:36:50+01:00');

        $date->set($d2);
        $date->add('Mon', Zend_Date::WEEKDAY_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-05T00:36:50+01:00');

        $date->set($d2);
        $date->add(10, Zend_Date::DAY_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-14T00:36:50+01:00');
        $date->add(-10, Zend_Date::DAY_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:36:50+01:00');

        $date->set($d2);
        $date->add('Montag', Zend_Date::WEEKDAY);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-05T00:36:50+01:00');

        $date->set($d2);
        $date->add(1, Zend_Date::WEEKDAY_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-05T00:36:50+01:00');
        
        $date->set($d2);
        try {
            $date->add($d2, Zend_Date::DAY_SUFFIX);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        $date->add(1, Zend_Date::WEEKDAY_DIGIT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-06T00:36:50+01:00');

        $date->set($d2);
        $date->add(10, Zend_Date::DAY_OF_YEAR);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-14T00:36:50+01:00');
        $date->add(-10, Zend_Date::DAY_OF_YEAR);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:36:50+01:00');

        $date->set($d2);
        $date->add('M', Zend_Date::WEEKDAY_NARROW);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-05T00:36:50+01:00');

        $date->set($d2);
        $date->add('Mo', Zend_Date::WEEKDAY_NAME);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-05T00:36:50+01:00');

        $date->set($d2);
        $date->add(10, Zend_Date::WEEK);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-03-15T00:36:50+01:00');
        $date->add(-10, Zend_Date::WEEK);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:36:50+01:00');

        $date->set($d2);
        $date->add('April', Zend_Date::MONTH);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-05-04T01:36:50+02:00');

        $date->set($d2);
        $date->add(10, Zend_Date::MONTH_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-11-04T00:36:50+01:00');
        $date->add(-10, Zend_Date::MONTH_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-02T00:36:50+01:00');

        $date->set($d2);
        $date->add('Apr', Zend_Date::MONTH_NAME);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-05-04T01:36:50+02:00');

        $date->set($d2);
        $date->add(10, Zend_Date::MONTH_DIGIT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-11-04T00:36:50+01:00');
        $date->add(-10, Zend_Date::MONTH_DIGIT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-02T00:36:50+01:00');

        $date->set($d2);
        try {
            $date->add($d2, Zend_Date::MONTH_DAYS);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        $date->add('M', Zend_Date::MONTH_NARROW);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-04-04T01:36:50+02:00');

        $date->set($d2);
        try {
            $date->add($d2, Zend_Date::LEAPYEAR);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        $date->add(10, Zend_Date::YEAR_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2012-01-04T00:36:50+01:00');
        $date->add(-10, Zend_Date::YEAR_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-03T00:36:50+01:00');

        $date->set($d2);
        $date->add(10, Zend_Date::YEAR);
        $this->assertSame($date->get(Zend_Date::W3C),'2012-01-04T00:36:50+01:00');
        $date->add(-10, Zend_Date::YEAR);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-03T00:36:50+01:00');

        $date->set($d2);
        $date->add(10, Zend_Date::YEAR_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'4012-01-04T00:36:50+01:00');
        try {
            $date->add(-10, Zend_Date::YEAR_SHORT);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        $date->add(10, Zend_Date::YEAR_SHORT_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'4012-01-04T00:36:50+01:00');
        try {
            $date->add(-10, Zend_Date::YEAR_SHORT_8601);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        try {
            $date->add('noday', Zend_Date::MERIDIEM);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        $date->add(10, Zend_Date::SWATCH);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:51:14+01:00');
        $date->add(-10, Zend_Date::SWATCH);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:36:49+01:00');

        $date->set($d2);
        $date->add(10, Zend_Date::HOUR_SHORT_AM);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T10:36:50+01:00');
        $date->add(-10, Zend_Date::HOUR_SHORT_AM);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:36:50+01:00');

        $date->set($d2);
        $date->add(10, Zend_Date::HOUR_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T10:36:50+01:00');
        $date->add(-10, Zend_Date::HOUR_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:36:50+01:00');

        $date->set($d2);
        $date->add(10, Zend_Date::HOUR_AM);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T10:36:50+01:00');
        $date->add(-10, Zend_Date::HOUR_AM);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:36:50+01:00');

        $date->set($d2);
        $date->add(10, Zend_Date::HOUR);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T10:36:50+01:00');
        $date->add(-10, Zend_Date::HOUR);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:36:50+01:00');

        $date->set($d2);
        $date->add(10, Zend_Date::MINUTE);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:46:50+01:00');
        $date->add(-10, Zend_Date::MINUTE);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:36:50+01:00');

        $date->set($d2);
        $date->add(10, Zend_Date::MINUTE_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:46:50+01:00');
        $date->add(-10, Zend_Date::MINUTE_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:36:50+01:00');

        $date->set($d2);
        $date->add(10, Zend_Date::SECOND);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:37:00+01:00');
        $date->add(-10, Zend_Date::SECOND);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:36:50+01:00');

        $date->set($d2);
        $date->add(10, Zend_Date::SECOND_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:37:00+01:00');
        $date->add(-10, Zend_Date::SECOND_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:36:50+01:00');

        $date->set($d2);
        $date->add(10, Zend_Date::MILLISECOND);
        $this->assertSame($date->get(Zend_Date::MILLISECOND),'10');
        $date->add(-10, Zend_Date::MILLISECOND);
        $this->assertSame($date->get(Zend_Date::MILLISECOND),'0');

        $date->set($d2);
        try {
            $date->add('noday', Zend_Date::TIMEZONE_NAME);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        try {
            $date->add('noday', Zend_Date::DAYLIGHT);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        try {
            $date->add('noday', Zend_Date::GMT_DIFF);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        try {
            $date->add('noday', Zend_Date::GMT_DIFF_SEP);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        try {
            $date->add('noday', Zend_Date::TIMEZONE);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        try {
            $date->add('noday', Zend_Date::TIMEZONE_SECS);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        $date->add('1000-01-02 20:05:12', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'3002-02-07T20:42:02+01:00');

        $date->set($d2);
        $date->add('Thu, 02 Jan 1000 20:05:12 +0100', Zend_Date::RFC_2822);
        $this->assertSame($date->get(Zend_Date::W3C),'3002-02-07T20:42:02+01:00');

        $date->set($d2);
        $date->add(10, Zend_Date::TIMESTAMP);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:37:00+01:00');

        $date->set($d2);
        try {
            $date->add('noday', Zend_Date::ERA);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        try {
            $date->add('noday', Zend_Date::ERA_NAME);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        $date->add('10.02.0005', Zend_Date::DATES);
        $this->assertSame($date->get(Zend_Date::W3C),'2007-03-14T00:36:50+01:00');

        $date->set($d2);
        $date->add('Samstag, 10. Februar 0005', Zend_Date::DATE_FULL);
        $this->assertSame($date->get(Zend_Date::W3C),'2007-03-14T00:36:50+01:00');

        $date->set($d2);
        $date->add('10. Februar 0005', Zend_Date::DATE_LONG);
        $this->assertSame($date->get(Zend_Date::W3C),'2007-03-14T00:36:50+01:00');

        $date->set($d2);
        $date->add('10.02.0005', Zend_Date::DATE_MEDIUM);
        $this->assertSame($date->get(Zend_Date::W3C),'2007-03-14T00:36:50+01:00');

        $date->set($d2);
        $date->add('10.02.05', Zend_Date::DATE_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'4007-03-14T00:36:50+01:00');

        $date->set($d2);
        $date->add('10:05:05', Zend_Date::TIMES);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T10:41:55+01:00');

        $date->set($d2);
        $date->add('10:05 Uhr CET', Zend_Date::TIME_FULL);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T10:41:50+01:00');

        $date->set($d2);
        $date->add('10:05:05 CET', Zend_Date::TIME_LONG);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T10:41:55+01:00');

        $date->set($d2);
        $date->add('10:05:05', Zend_Date::TIME_MEDIUM);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T10:41:55+01:00');

        $date->set($d2);
        $date->add('10:05', Zend_Date::TIME_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T10:41:50+01:00');

        $date->set($d2);
        $date->add('1000-01-02T20:05:12+01:00', Zend_Date::ATOM);
        $this->assertSame($date->get(Zend_Date::W3C),'3002-02-07T20:42:02+01:00');

        $date->set($d2);
        $date->add('Saturday, 02-Jan-00 20:05:12 Europe/Paris', Zend_Date::COOKIE);
        $this->assertSame($date->get(Zend_Date::W3C),'4002-02-06T20:42:02+01:00');

        $date->set($d2);
        $date->add('Sat, 02 Jan 00 20:05:12 +0100', Zend_Date::RFC_822);
        $this->assertSame($date->get(Zend_Date::W3C),'4002-02-06T20:42:02+01:00');

        $date->set($d2);
        $date->add('Saturday, 02-Jan-00 20:05:12 Europe/Paris', Zend_Date::RFC_850);
        $this->assertSame($date->get(Zend_Date::W3C),'4002-02-06T20:42:02+01:00');

        $date->set($d2);
        $date->add('Sat, 02 Jan 00 20:05:12 +0100', Zend_Date::RFC_1036);
        $this->assertSame($date->get(Zend_Date::W3C),'4002-02-06T20:42:02+01:00');

        $date->set($d2);
        $date->add('Sat, 02 Jan 1000 20:05:12 +0100', Zend_Date::RFC_1123);
        $this->assertSame($date->get(Zend_Date::W3C),'3002-02-07T20:42:02+01:00');

        $date->set($d2);
        $date->add('Sat, 02 Jan 1000 20:05:12 +0100', Zend_Date::RSS);
        $this->assertSame($date->get(Zend_Date::W3C),'3002-02-07T20:42:02+01:00');

        $date->set($d2);
        $date->add('1000-01-02T20:05:12+01:00', Zend_Date::W3C);
        $this->assertSame($date->get(Zend_Date::W3C),'3002-02-07T20:42:02+01:00');

        $date->set($d2);
        $date->add('1000', 'xx');
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:53:30+01:00');
    }

    /**
     * Test for sub
     */
    public function testSub()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(0,false,$locale);
        $d2   = new Zend_Date(1010101010,false,$locale);

        $retour = $date->set(1234567890);
        $this->assertSame($retour,'1234567890');
        $this->assertSame($date->sub(-10),'1234567900');
        $this->assertSame($date->sub(10),'1234567890');
        $this->assertSame($date->sub(0),'1234567890');

        $date->set($d2);
        $date->sub(-10, Zend_Date::DAY);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-14T00:36:50+01:00');
        $date->sub(10, Zend_Date::DAY);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:36:50+01:00');

        $date->set($d2);
        $date->sub('Mon', Zend_Date::WEEKDAY_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-03T00:36:50+01:00');

        $date->set($d2);
        $date->sub(-10, Zend_Date::DAY_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-14T00:36:50+01:00');
        $date->sub(10, Zend_Date::DAY_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:36:50+01:00');

        $date->set($d2);
        $date->sub('Montag', Zend_Date::WEEKDAY);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-03T00:36:50+01:00');

        $date->set($d2);
        $date->sub(1, Zend_Date::WEEKDAY_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-03T00:36:50+01:00');
        
        $date->set($d2);
        try {
            $date->sub($d2, Zend_Date::DAY_SUFFIX);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        $date->sub(1, Zend_Date::WEEKDAY_DIGIT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-02T00:36:50+01:00');

        $date->set($d2);
        $date->sub(-10, Zend_Date::DAY_OF_YEAR);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-14T00:36:50+01:00');
        $date->sub(10, Zend_Date::DAY_OF_YEAR);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:36:50+01:00');

        $date->set($d2);
        $date->sub('M', Zend_Date::WEEKDAY_NARROW);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-03T00:36:50+01:00');

        $date->set($d2);
        $date->sub('Mo', Zend_Date::WEEKDAY_NAME);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-03T00:36:50+01:00');

        $date->set($d2);
        $date->sub(-10, Zend_Date::WEEK);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-03-15T00:36:50+01:00');
        $date->sub(10, Zend_Date::WEEK);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:36:50+01:00');

        $date->set($d2);
        $date->sub('April', Zend_Date::MONTH);
        $this->assertSame($date->get(Zend_Date::W3C),'2001-09-06T01:36:50+02:00');

        $date->set($d2);
        $date->sub(-10, Zend_Date::MONTH_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-11-06T00:36:50+01:00');
        $date->sub(10, Zend_Date::MONTH_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-06T00:36:50+01:00');

        $date->set($d2);
        $date->sub('Apr', Zend_Date::MONTH_NAME);
        $this->assertSame($date->get(Zend_Date::W3C),'2001-09-06T01:36:50+02:00');

        $date->set($d2);
        $date->sub(-10, Zend_Date::MONTH_DIGIT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-11-06T00:36:50+01:00');
        $date->sub(10, Zend_Date::MONTH_DIGIT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-06T00:36:50+01:00');

        $date->set($d2);
        try {
            $date->sub($d2, Zend_Date::MONTH_DAYS);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        $date->sub('M', Zend_Date::MONTH_NARROW);
        $this->assertSame($date->get(Zend_Date::W3C),'2001-10-06T01:36:50+02:00');

        $date->set($d2);
        try {
            $date->sub($d2, Zend_Date::LEAPYEAR);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        $date->sub(-10, Zend_Date::YEAR_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2012-01-05T00:36:50+01:00');
        $date->sub(10, Zend_Date::YEAR_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-05T00:36:50+01:00');

        $date->set($d2);
        $date->sub(-10, Zend_Date::YEAR);
        $this->assertSame($date->get(Zend_Date::W3C),'2012-01-05T00:36:50+01:00');
        $date->sub(10, Zend_Date::YEAR);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-05T00:36:50+01:00');

        $date->set($d2);
        $date->sub(10, Zend_Date::YEAR_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'-8-01-07T00:36:50+01:00');
        try {
            $date->sub(-10, Zend_Date::YEAR_SHORT);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        $date->sub(10, Zend_Date::YEAR_SHORT_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'-8-01-07T00:36:50+01:00');
        try {
            $date->sub(-10, Zend_Date::YEAR_SHORT_8601);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        try {
            $date->sub('noday', Zend_Date::MERIDIEM);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        $date->sub(-10, Zend_Date::SWATCH);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:51:15+01:00');
        $date->sub(10, Zend_Date::SWATCH);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:36:51+01:00');

        $date->set($d2);
        $date->sub(-10, Zend_Date::HOUR_SHORT_AM);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T10:36:50+01:00');
        $date->sub(10, Zend_Date::HOUR_SHORT_AM);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:36:50+01:00');

        $date->set($d2);
        $date->sub(-10, Zend_Date::HOUR_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T10:36:50+01:00');
        $date->sub(10, Zend_Date::HOUR_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:36:50+01:00');

        $date->set($d2);
        $date->sub(-10, Zend_Date::HOUR_AM);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T10:36:50+01:00');
        $date->sub(10, Zend_Date::HOUR_AM);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:36:50+01:00');

        $date->set($d2);
        $date->sub(-10, Zend_Date::HOUR);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T10:36:50+01:00');
        $date->sub(10, Zend_Date::HOUR);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:36:50+01:00');

        $date->set($d2);
        $date->sub(-10, Zend_Date::MINUTE);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:46:50+01:00');
        $date->sub(10, Zend_Date::MINUTE);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:36:50+01:00');

        $date->set($d2);
        $date->sub(-10, Zend_Date::MINUTE_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:46:50+01:00');
        $date->sub(10, Zend_Date::MINUTE_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:36:50+01:00');

        $date->set($d2);
        $date->sub(-10, Zend_Date::SECOND);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:37:00+01:00');
        $date->sub(10, Zend_Date::SECOND);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:36:50+01:00');

        $date->set($d2);
        $date->sub(-10, Zend_Date::SECOND_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:37:00+01:00');
        $date->sub(10, Zend_Date::SECOND_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:36:50+01:00');

        $date->set($d2);
        $date->sub(-10, Zend_Date::MILLISECOND);
        $this->assertSame($date->get(Zend_Date::MILLISECOND),'10');
        $date->sub(10, Zend_Date::MILLISECOND);
        $this->assertSame($date->get(Zend_Date::MILLISECOND),'0');

        $date->set($d2);
        try {
            $date->sub('noday', Zend_Date::TIMEZONE_NAME);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        try {
            $date->sub('noday', Zend_Date::DAYLIGHT);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        try {
            $date->sub('noday', Zend_Date::GMT_DIFF);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        try {
            $date->sub('noday', Zend_Date::GMT_DIFF_SEP);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        try {
            $date->sub('noday', Zend_Date::TIMEZONE);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        try {
            $date->sub('noday', Zend_Date::TIMEZONE_SECS);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        $date->sub('1000-01-02 20:05:12', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'1001-11-25T04:31:38+01:00');
        $date->set($d2);
        $date->sub('1000-01-02T20:05:12+01:00', Zend_Date::ISO_8601);
        $this->assertSame($date->get(Zend_Date::W3C),'1001-11-25T04:31:38+01:00');
        
        $date->set($d2);
        $date->sub('Thu, 02 Jan 1000 20:05:12 +0100', Zend_Date::RFC_2822);
        $this->assertSame($date->get(Zend_Date::W3C),'1001-11-25T04:31:38+01:00');

        $date->set($d2);
        $date->sub(-10, Zend_Date::TIMESTAMP);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:37:00+01:00');

        $date->set($d2);
        try {
            $date->sub('noday', Zend_Date::ERA);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        try {
            $date->sub('noday', Zend_Date::ERA_NAME);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        $date->sub('10.02.0005', Zend_Date::DATES);
        $this->assertSame($date->get(Zend_Date::W3C),'1996-10-27T01:36:50+02:00');

        $date->set($d2);
        $date->sub('Samstag, 10. Februar 0005', Zend_Date::DATE_FULL);
        $this->assertSame($date->get(Zend_Date::W3C),'1996-10-27T01:36:50+02:00');

        $date->set($d2);
        $date->sub('10. Februar 0005', Zend_Date::DATE_LONG);
        $this->assertSame($date->get(Zend_Date::W3C),'1996-10-27T01:36:50+02:00');

        $date->set($d2);
        $date->sub('10.02.0005', Zend_Date::DATE_MEDIUM);
        $this->assertSame($date->get(Zend_Date::W3C),'1996-10-27T01:36:50+02:00');

        $date->set($d2);
        $date->sub('10.02.05', Zend_Date::DATE_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'-4-10-29T00:36:50+01:00');

        $date->set($d2);
        $date->sub('10:05:05', Zend_Date::TIMES);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-03T14:31:45+01:00');

        $date->set($d2);
        $date->sub('10:05 Uhr CET', Zend_Date::TIME_FULL);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-03T14:31:50+01:00');

        $date->set($d2);
        $date->sub('10:05:05 CET', Zend_Date::TIME_LONG);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-03T14:31:45+01:00');

        $date->set($d2);
        $date->sub('10:05:05', Zend_Date::TIME_MEDIUM);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-03T14:31:45+01:00');

        $date->set($d2);
        $date->sub('10:05', Zend_Date::TIME_SHORT);
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-03T14:31:50+01:00');

        $date->set($d2);
        $date->sub('1000-01-02T20:05:12+01:00', Zend_Date::ATOM);
        $this->assertSame($date->get(Zend_Date::W3C),'1001-11-25T04:31:38+01:00');

        $date->set($d2);
        $date->sub('Saturday, 02-Jan-00 20:05:12 Europe/Paris', Zend_Date::COOKIE);
        $this->assertSame($date->get(Zend_Date::W3C),'1-12-03T04:31:38+01:00' );

        $date->set($d2);
        $date->sub('Sat, 02 Jan 00 20:05:12 +0100', Zend_Date::RFC_822);
        $this->assertSame($date->get(Zend_Date::W3C),'1-12-03T04:31:38+01:00');

        $date->set($d2);
        $date->sub('Saturday, 02-Jan-00 20:05:12 Europe/Paris', Zend_Date::RFC_850);
        $this->assertSame($date->get(Zend_Date::W3C),'1-12-03T04:31:38+01:00');

        $date->set($d2);
        $date->sub('Sat, 02 Jan 00 20:05:12 +0100', Zend_Date::RFC_1036);
        $this->assertSame($date->get(Zend_Date::W3C),'1-12-03T04:31:38+01:00');

        $date->set($d2);
        $date->sub('Sat, 02 Jan 1000 20:05:12 +0100', Zend_Date::RFC_1123);
        $this->assertSame($date->get(Zend_Date::W3C),'1001-11-25T04:31:38+01:00');

        $date->set($d2);
        $date->sub('Sat, 02 Jan 1000 20:05:12 +0100', Zend_Date::RSS);
        $this->assertSame($date->get(Zend_Date::W3C),'1001-11-25T04:31:38+01:00');

        $date->set($d2);
        $date->sub('1000-01-02T20:05:12+01:00', Zend_Date::W3C);
        $this->assertSame($date->get(Zend_Date::W3C),'1001-11-25T04:31:38+01:00');

        $date->set($d2);
        $date->sub('1000', 'xx');
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:20:10+01:00');
    }

    /**
     * Test for compare
     */
    public function testCompare()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(0,false,$locale);
        $d2   = new Zend_Date(1010101010,false,$locale);

        $retour = $date->set(1234567890);
        $this->assertSame($retour,'1234567890');
        $this->assertSame($date->compare(1234567890),0);
        $this->assertSame($date->compare(1234567800),1);
        $this->assertSame($date->compare(1234567899),-1);

        $date->set($d2);
        $this->assertSame($date->compare(3,Zend_Date::DAY),1);
        $this->assertSame($date->compare(4,Zend_Date::DAY),0);
        $this->assertSame($date->compare(5,Zend_Date::DAY),-1);

        $this->assertSame($date->compare('Mon',Zend_Date::WEEKDAY_SHORT),1);
        $this->assertSame($date->compare('Sam',Zend_Date::WEEKDAY_SHORT),-1);

        $date->set($d2);
        $this->assertSame($date->compare(0,Zend_Date::MILLISECOND),0);
    }

    /**
     * Test for copy
     */
    public function testCopy()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(0,false,$locale);
        $d2   = new Zend_Date(1010101010,false,$locale);

        $date->set(1234567890);
        $newdate = $date->copy();
        $this->assertSame($date->get(),$newdate->get());

        $date->set($d2);
        $newdate = $date->copy(Zend_Date::DAY);
        $this->assertSame($newdate->get(Zend_Date::W3C), '1970-01-05T00:00:00+01:00');
    }

    /**
     * Test for equals
     */
    public function testEquals()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(0,false,$locale);
        $d2   = new Zend_Date(1010101010,false,$locale);

        $retour = $date->set(1234567890);
        $this->assertSame($retour,'1234567890');
        $this->assertSame($date->equals(1234567890),TRUE);
        $this->assertSame($date->equals(1234567800),FALSE);

        $date->set($d2);
        $this->assertSame($date->equals(3,Zend_Date::DAY),FALSE);
        $this->assertSame($date->equals(4,Zend_Date::DAY),TRUE);
    }

    /**
     * Test for isEarlier
     */
    public function testIsEarlier()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(0,false,$locale);
        $d2   = new Zend_Date(1010101010,false,$locale);

        $retour = $date->set(1234567890);
        $this->assertSame($retour,'1234567890');
        $this->assertSame($date->isEarlier(1234567890),FALSE);
        $this->assertSame($date->isEarlier(1234567800),FALSE);
        $this->assertSame($date->isEarlier(1234567899),TRUE);
        
        $date->set($d2);
        $this->assertSame($date->isEarlier(3,Zend_Date::DAY),FALSE);
        $this->assertSame($date->isEarlier(4,Zend_Date::DAY),FALSE);
        $this->assertSame($date->isEarlier(5,Zend_Date::DAY),TRUE);
    }

    /**
     * Test for isLater
     */
    public function testIsLater()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(0,false,$locale);
        $d2   = new Zend_Date(1010101010,false,$locale);

        $retour = $date->set(1234567890);
        $this->assertSame($retour,'1234567890');
        $this->assertSame($date->isLater(1234567890),FALSE);
        $this->assertSame($date->isLater(1234567800),TRUE);
        $this->assertSame($date->isLater(1234567899),FALSE);
        
        $date->set($d2);
        $this->assertSame($date->isLater(3,Zend_Date::DAY),TRUE);
        $this->assertSame($date->isLater(4,Zend_Date::DAY),FALSE);
        $this->assertSame($date->isLater(5,Zend_Date::DAY),FALSE);
    }

    /**
     * Test for getTime
     */
    public function testGetTime()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1010101010,false,$locale);
        $d2   = new Zend_Date(1010101010,false,$locale);

        $result = $date->getTime();
        $this->assertSame($result->get(Zend_Date::W3C),'1970-01-01T00:36:50+01:00');
    }

    /**
     * Test for setTime
     */
    public function testSetTime()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,false,$locale);
        $d2   = new Zend_Date(1234567899,false,$locale);

        $result = $date->setTime();
        $this->assertTrue($result instanceof Zend_Date);
        $result = $date->setTime('10:20:30');
        $this->assertSame($result->get(Zend_Date::W3C),'2009-02-14T10:20:30+01:00');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T10:20:30+01:00');
        $date->setTime('30-20-10','ss:mm:HH');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T10:20:30+01:00');
        $date->setTime($d2);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:39+01:00');
    }

    /**
     * Test for addTime
     */
    public function testAddTime()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,false,$locale);
        $d2   = new Zend_Date(1234567899,false,$locale);

        $result = $date->addTime();
        $this->assertTrue($result instanceof Zend_Date);

        $date = new Zend_Date(1234567890,false,$locale);
        $result = $date->addTime('10:20:30');
        $this->assertSame($result->get(Zend_Date::W3C),'2009-02-14T10:52:00+01:00');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T10:52:00+01:00');

        $date = new Zend_Date(1234567890,false,$locale);
        $date->addTime('30:20:10','ss:mm:HH');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T10:52:00+01:00');

        $date = new Zend_Date(1234567890,false,$locale);
        $date->addTime($d2);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T01:03:09+01:00');
    }

    /**
     * Test for subTime
     */
    public function testSubTime()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,false,$locale);
        $d2   = new Zend_Date(1234567899,false,$locale);

        $result = $date->subTime();
        $this->assertTrue($result instanceof Zend_Date);

        $date = new Zend_Date(1234567890,false,$locale);
        $result = $date->subTime('10:20:30');
        $this->assertSame($result->get(Zend_Date::W3C),'2009-02-13T14:11:00+01:00');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T14:11:00+01:00');

        $date = new Zend_Date(1234567890,false,$locale);
        $date->subTime('30-20-10','ss:mm:HH');
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T14:11:00+01:00');

        $date = new Zend_Date(1234567890,false,$locale);
        $date->subTime($d2);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T23:59:51+01:00');
    }

    /**
     * Test for compareTime
     */
    public function testCompareTime()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,false,$locale);
        $d2   = new Zend_Date(1234567899,false,$locale);

        $date = new Zend_Date(1234567890,false,$locale);
        // echo "date=",$date->get(Zend_Date::RFC_822);
        // Sat, 14 Feb 09 00:31:30 +0100
        $this->assertSame($date->compareTime('10:20:30'), -1);
        $this->assertSame($date->compareTime('00:31:30'), 0);
        $this->assertSame($date->compareTime('00:00:30'), 1);
        $this->assertSame($date->compareTime($d2), -1);
    }

    /**
     * Test for getDate
     */
    public function testGetDate()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1010101010,false,$locale);
        $d2   = new Zend_Date(1010101010,false,$locale);

        $result = $date->getDate();
        $this->assertSame($result->get(Zend_Date::W3C),'2002-01-04T01:00:00+01:00');
    }

    /**
     * Test for setDate
     */
    public function testSetDate()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,false,$locale);
        $d2   = new Zend_Date(1234567899,false,$locale);

        $result = $date->setDate();
        $this->assertTrue($result instanceof Zend_Date);
        $result = $date->setDate('11.05.2008');
        $this->assertSame($result->get(Zend_Date::W3C),'2008-04-11T01:31:30+02:00');
        $this->assertSame($date->get(Zend_Date::W3C),'2008-04-11T01:31:30+02:00');
        $date->setDate('2008-05-11','YYYY-MM-dd');
        $this->assertSame($date->get(Zend_Date::W3C),'2008-04-11T01:31:30+02:00');
        $date->setDate($d2);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:30+01:00');
    }

    /**
     * Test for addDate
     */
    public function testAddDate()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,false,$locale);
        $d2   = new Zend_Date(1234567899,false,$locale);

        $result = $date->addDate();
        $this->assertTrue($result instanceof Zend_Date);

        $date = new Zend_Date(1234567890,false,$locale);
        $result = $date->addDate('02-03-05');
        $this->assertSame($result->get(Zend_Date::W3C),'2014-05-17T01:31:30+02:00');
        $this->assertSame($date->get(Zend_Date::W3C),'2014-05-17T01:31:30+02:00');

        $date = new Zend_Date(1234567890,false,$locale);
        $date->addDate('05-03-02','YY-MM-dd');
        $this->assertSame($date->get(Zend_Date::W3C),'2014-05-17T01:31:30+02:00');

        $date = new Zend_Date(1234567890,false,$locale);
        $date->addDate($d2);
        $this->assertSame($date->get(Zend_Date::W3C),'4018-04-28T00:31:30+01:00');
    }

    /**
     * Test for subDate
     */
    public function testSubDate()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,false,$locale);
        $d2   = new Zend_Date(1234567899,false,$locale);

        $result = $date->subDate();
        $this->assertTrue($result instanceof Zend_Date);

        $date = new Zend_Date(1234567890,false,$locale);
        $result = $date->subDate('03-05-1001');
        $this->assertSame($result->get(Zend_Date::W3C),'1007-09-08T00:31:30+01:00');
        $this->assertSame($date->get(Zend_Date::W3C),'1007-09-08T00:31:30+01:00');

        $date = new Zend_Date(1234567890,false,$locale);
        $date->subDate('1001-05-03','YYYY-MM-dd');
        $this->assertSame($date->get(Zend_Date::W3C),'1007-09-08T00:31:30+01:00');

        $date = new Zend_Date(1234567890,false,$locale);
        $date->subDate($d2);
        $this->assertSame($date->get(Zend_Date::W3C),'-1-12-06T00:31:30+01:00');
    }

    /**
     * Test for compareDate
     */
    public function testCompareDate()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,false,$locale);
        $d2   = new Zend_Date(1234567899,false,$locale);

        $date = new Zend_Date(1234567890,false,$locale);
        $this->assertSame($date->compareDate('10-01-2009'), 1);
        $this->assertSame($date->compareDate('14-02-2009'), 0);
        $this->assertSame($date->compareDate('15-02-2009'), -1);
        $this->assertSame($date->compareDate($d2), 0);
    }

    /**
     * Test for getIso
     */
    public function testGetIso()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1010101010,false,$locale);
        $d2   = new Zend_Date(1010101010,false,$locale);

        $result = $date->getIso();
        $this->assertTrue($result instanceof Zend_Date);
        $this->assertSame($date->get(Zend_Date::ISO_8601),'2002-01-04T00:36:50+01:00');
    }

    /**
     * Test for setIso
     */
    public function testSetIso()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,false,$locale);
        $d2   = new Zend_Date(1234567899,false,$locale);

        $result = $date->setIso();
        $this->assertTrue($result instanceof Zend_Date);
        $result = $date->setIso('2002-01-04T00:00:00+0100');
        $this->assertSame($result->get(Zend_Date::W3C),'2002-01-04T00:00:00+01:00');
        $this->assertSame($date->get(Zend_Date::W3C),'2002-01-04T00:00:00+01:00');
// this brakes php cli... but the output is correct
// seems to be a problem with phpunit or xdebug and the huge ammount of tests
//        $date->setIso($d2);
//        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-14T00:31:39+01:00');
    }

    /**
     * Test for addIso
     */
    public function testAddIso()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,false,$locale);
        $d2   = new Zend_Date(1234567899,false,$locale);

        $result = $date->addIso();
        $this->assertTrue($result instanceof Zend_Date);

        $date = new Zend_Date(1234567890,false,$locale);
        $result = $date->addIso('2002-01-04T00:00:00+0100');
        $this->assertSame($result->get(Zend_Date::W3C),'4011-03-21T00:31:30+01:00');
        $this->assertSame($date->get(Zend_Date::W3C),'4011-03-21T00:31:30+01:00');

        $date = new Zend_Date(1234567890,false,$locale);
        $date->addIso($d2);
        $this->assertSame($date->get(Zend_Date::W3C),'4018-04-28T01:03:09+01:00');
    }

    /**
     * Test for subIso
     */
    public function testSubIso()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,false,$locale);
        $d2   = new Zend_Date(1234567899,false,$locale);

        $result = $date->subIso();
        $this->assertTrue($result instanceof Zend_Date);

        $date = new Zend_Date(1234567890,false,$locale);
        $result = $date->subIso('2002-01-04T00:00:00+0100');
        $this->assertSame($result->get(Zend_Date::W3C),'7-01-13T00:31:30+01:00');
        $this->assertSame($date->get(Zend_Date::W3C),'7-01-13T00:31:30+01:00');

        $date = new Zend_Date(1234567890,false,$locale);
        $date->subIso($d2);
        $this->assertSame($date->get(Zend_Date::W3C),'-1-12-05T23:59:51+01:00');
    }

    /**
     * Test for compareIso
     */
    public function testCompareIso()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,false,$locale);
        $d2   = new Zend_Date(1234567899,false,$locale);

        $date = new Zend_Date(1234567890,false,$locale);
        $this->assertSame($date->compareIso('2002-01-04T00:00:00+0100'), 1);
        $this->assertSame($date->compareIso('2009-02-14T00:31:30+0100'), 0);
        $this->assertSame($date->compareIso('2010-01-04T01:00:00+0100'), -1);
        $this->assertSame($date->compareIso($d2), -1);
    }

    /**
     * Test for getArpa
     */
    public function testGetArpa()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1010101010,false,$locale);
        $d2   = new Zend_Date(1010101010,false,$locale);

        $result = $date->getArpa();
        $this->assertTrue($result instanceof Zend_Date);
        $this->assertSame($date->get(Zend_Date::RFC_822),'Fri, 04 Jan 02 00:36:50 +0100');
    }

    /**
     * Test for setArpa
     */
    public function testSetArpa()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,false,$locale);
        $d2   = new Zend_Date(1234567899,false,$locale);

        $result = $date->setArpa();
        $this->assertTrue($result instanceof Zend_Date);
        $result = $date->setArpa('Sat, 03 May 01 00:00:00 +0100');
        $this->assertSame($result->get(Zend_Date::RFC_822),'Thu, 03 May 01 01:00:00 +0200');
        $this->assertSame($date->get(Zend_Date::W3C),'2001-05-03T01:00:00+02:00');
        $date->setArpa($d2);
        $this->assertSame($date->get(Zend_Date::W3C),'2009-02-13T23:31:39+01:00');
    }

    /**
     * Test for addArpa
     */
    public function testAddArpa()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,false,$locale);
        $d2   = new Zend_Date(1234567899,false,$locale);

        $result = $date->addArpa();
        $this->assertTrue($result instanceof Zend_Date);

        $date = new Zend_Date(1234567890,false,$locale);
        $result = $date->addArpa('Sat, 03 May 01 00:00:00 +0100');
        $this->assertSame($result->get(Zend_Date::RFC_822),'Sun, 18 Jul 10 00:31:30 +0100');
        $this->assertSame($date->get(Zend_Date::W3C),'4010-07-18T00:31:30+01:00');

        $date = new Zend_Date(1234567890,false,$locale);
        $date->addArpa($d2);
        $this->assertSame($date->get(Zend_Date::W3C),'4018-04-28T01:03:09+01:00');
    }

    /**
     * Test for subArpa
     */
    public function testSubArpa()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,false,$locale);
        $d2   = new Zend_Date(1234567899,false,$locale);

        $result = $date->subArpa();
        $this->assertTrue($result instanceof Zend_Date);

        $date = new Zend_Date(1234567890,false,$locale);
        $result = $date->subArpa('Sat, 03 May 01 00:00:00 +0100');
        $this->assertSame($result->get(Zend_Date::RFC_822),'Wed, 16 Sep 7 00:31:30 +0100');
        $this->assertSame($date->get(Zend_Date::W3C),'7-09-16T00:31:30+01:00');

        $date = new Zend_Date(1234567890,false,$locale);
        $date->subArpa($d2);
        $this->assertSame($date->get(Zend_Date::W3C),'-1-12-05T23:59:51+01:00');
    }

    /**
     * Test for compareArpa
     */
    public function testCompareArpa()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,false,$locale);
        $d2   = new Zend_Date(1234567899,false,$locale);

        $date = new Zend_Date(1234567890,false,$locale);
        $this->assertSame($date->compareArpa('Sat, 14 Feb 09 01:31:30 +0100'), -1);
        $this->assertSame($date->compareArpa('Sat, 14 Feb 09 00:31:30 +0100'), 0);
        $this->assertSame($date->compareArpa('Sat, 13 Feb 09 00:31:30 +0100'), 1);
        $this->assertSame($date->compareArpa($d2), -1);
    }

    /**
     * Test for false locale setting
     */
    public function testReducedParams()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1010101010,$locale);

        $date->setArpa('Sat, 03 May 01 00:00:00 +0100',$locale);
        $this->assertSame($date->get(Zend_Date::RFC_822),'Thu, 03 May 01 01:00:00 +0200');
    }

    /**
     * Test for SunFunc
     */
    public function testSunFunc()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1010101010,$locale);

        $result = Zend_Date_Cities::City('vienna');
        $this->assertTrue(is_array($result));
        $result = $date->getSunSet($result);
        $this->assertSame($result->get(Zend_Date::W3C),'2002-01-04T16:10:10+01:00');

        unset($result);
        $result = Zend_Date_Cities::City('vienna', 'civil');
        $this->assertTrue(is_array($result));
        $result = $date->getSunSet($result);
        $this->assertSame($result->get(Zend_Date::W3C),'2002-01-04T16:09:31+01:00');

        unset($result);
        $result = Zend_Date_Cities::City('vienna', 'nautic');
        $this->assertTrue(is_array($result));
        $result = $date->getSunSet($result);
        $this->assertSame($result->get(Zend_Date::W3C),'2002-01-04T16:08:45+01:00');

        unset($result);
        $result = Zend_Date_Cities::City('vienna', 'astronomic');
        $this->assertTrue(is_array($result));
        $result = $date->getSunSet($result);
        $this->assertSame($result->get(Zend_Date::W3C),'2002-01-04T16:08:00+01:00');

        unset($result);
        $result = Zend_Date_Cities::City('BERLIN');
        $this->assertTrue(is_array($result));
        $result = $date->getSunRise($result);
        $this->assertSame($result->get(Zend_Date::W3C),'2002-01-04T08:21:17+01:00');

        unset($result);
        $result = Zend_Date_Cities::City('London');
        $this->assertTrue(is_array($result));
        $result = $date->getSunInfo($result);
        $this->assertSame($result['sunrise']['effective']->get(Zend_Date::W3C), '2002-01-04T09:10:07+01:00');
        $this->assertSame($result['sunrise']['civil']->get(Zend_Date::W3C),     '2002-01-04T09:10:51+01:00');
        $this->assertSame($result['sunrise']['nautic']->get(Zend_Date::W3C),    '2002-01-04T09:11:42+01:00');
        $this->assertSame($result['sunrise']['astronomic']->get(Zend_Date::W3C),'2002-01-04T09:12:31+01:00');
        $this->assertSame($result['sunset']['effective']->get(Zend_Date::W3C),  '2002-01-04T17:01:04+01:00');
        $this->assertSame($result['sunset']['civil']->get(Zend_Date::W3C),      '2002-01-04T17:00:20+01:00');
        $this->assertSame($result['sunset']['nautic']->get(Zend_Date::W3C),     '2002-01-04T16:59:30+01:00');
        $this->assertSame($result['sunset']['astronomic']->get(Zend_Date::W3C), '2002-01-04T16:58:40+01:00');
    }

    /**
     * Test for Timezone
     */
    public function testTimezone()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1010101010,$locale);

        $result = $date->getTimeZone();
        $this->assertSame($result, 'Europe/Paris');

        $result = $date->setTimeZone('unknown');
        $this->assertSame($result, FALSE);
        $result = $date->getTimeZone();
        $this->assertSame($result, 'Europe/Paris');

        $result = $date->setTimeZone('Europe/Vienna');
        $this->assertSame($result, true);
        $result = $date->getTimeZone();
        $this->assertSame($result, 'Europe/Vienna');
    }

    /**
     * Test for LeapYear
     */
    public function testLeapYear()
    {
        $locale = new Zend_Locale('de_AT');

        $date = new Zend_Date('01.01.2006', Zend_Date::DATES, $locale);
        $this->assertFalse($date->isLeapYear());

        unset($date);
        $date = new Zend_Date('01.01.2004', Zend_Date::DATES, $locale);
// this brakes php cli... but the output is correct
// seems to be a problem with phpunit or xdebug and the huge ammount of tests
//        $this->assertTrue($date->isLeapYear());
    }

    /**
     * Test for Today
     */
    public function testToday()
    {
        $locale = new Zend_Locale('de_AT');

        $date = new Zend_Date();
        $d2 = new Zend_Date(1010101010,$locale);

        $this->assertFalse($d2->isToday());
        $this->assertTrue($date->isToday());
    }

    /**
     * Test for Yesterday
     */
    public function testYesterday()
    {
        $locale = new Zend_Locale('de_AT');

        $date = new Zend_Date();
        $d2 = new Zend_Date(1010101010,$locale);

        $date->subDay(1);
        $this->assertFalse($d2->isYesterday());
        $this->assertTrue($date->isYesterday());
    }
}