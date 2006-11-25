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
    }

	/**
	 * Test for date
	 */
/*    public function testDate1()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject();
      	$result = $date->date('d');
       	$this->assertTrue(is_string($result), "string expected");
    }

	/**
	 * Test for date
	 */
/*    public function testDate2()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject();
      	$result = $date->date('d', false, true);
       	$this->assertTrue(is_string($result), "string expected");
    }

	/**
	 * Test for date
	 */
/*    public function testDate3()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject(32503680000, false, true);
        $stamp = $date->mktime(13, 44, 22, 10, 6, 3000, -1, true);
        $this->assertSame($date->date('d', $stamp, true), '01', "01 expected");
    }

	/**
	 * Test for date
	 */
/*    public function testDate4()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject(32503680000, false, true);
       	$this->assertSame($date->date('D', 32503680000, true), '01', "01 expected");
    }

	/**
	 * Test for date
	 */
/*    public function testDate5()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject(32503680000, false, true);
       	$this->assertSame($date->date('j', 32503680000, true), '01', "01 expected");
    }

	/**
	 * Test for date
	 */
/*    public function testDate6()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject(32503680000, false, true);
       	$this->assertSame($date->date('l', 32503680000, true), '01', "01 expected");
    }

	/**
	 * Test for date
	 */
/*    public function testDate7()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject(32503680000, false, true);
       	$this->assertSame($date->date('N', 32503680000, true), '01', "01 expected");
    }

	/**
	 * Test for date
	 */
/*    public function testDate8()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject(32503680000, false, true);
       	$this->assertSame($date->date('S', 32503680000, true), '01', "01 expected");
    }

	/**
	 * Test for date
	 */
/*    public function testDate9()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject(32503680000, false, true);
       	$this->assertSame($date->date('w', 32503680000, true), '01', "01 expected");
    }

	/**
	 * Test for date
	 */
/*    public function testDate10()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject(32503680000, false, true);
       	$this->assertSame($date->date('z', 32503680000, true), '01', "01 expected");
    }

	/**
	 * Test for date
	 */
/*    public function testDate11()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject(32503680000, false, true);
       	$this->assertSame($date->date('W', 32503680000, true), '01', "01 expected");
    }

	/**
	 * Test for date
	 */
/*    public function testDate12()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject(32503680000, false, true);
       	$this->assertSame($date->date('F', 32503680000, true), '01', "01 expected");
    }

	/**
	 * Test for date
	 */
/*    public function testDate13()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject(32503680000, false, true);
       	$this->assertSame($date->date('m', 32503680000, true), '01', "01 expected");
    }

	/**
	 * Test for date
	 */
/*    public function testDate14()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject(32503680000, false, true);
       	$this->assertSame($date->date('M', 32503680000, true), '01', "01 expected");
    }

	/**
	 * Test for date
	 */
/*    public function testDate15()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject(32503680000, false, true);
       	$this->assertSame($date->date('n', 32503680000, true), '01', "01 expected");
    }

	/**
	 * Test for date
	 */
/*    public function testDate16()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject(32503680000, false, true);
       	$this->assertSame($date->date('t', 32503680000, true), '01', "01 expected");
    }

	/**
	 * Test for date
	 */
/*    public function testDate17()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject(32503680000, false, true);
       	$this->assertSame($date->date('L', 32503680000, true), '01', "01 expected");
    }

	/**
	 * Test for date
	 */
/*    public function testDate18()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject(32503680000, false, true);
       	$this->assertSame($date->date('o', 32503680000, true), '01', "01 expected");
    }

	/**
	 * Test for date
	 */
/*    public function testDate19()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject(32503680000, false, true);
       	$this->assertSame($date->date('Y', 32503680000, true), '01', "01 expected");
    }

	/**
	 * Test for date
	 */
/*    public function testDate20()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject(32503680000, false, true);
       	$this->assertSame($date->date('y', 32503680000, true), '01', "01 expected");
    }

	/**
	 * Test for date
	 */
/*    public function testDate21()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject(32503680000, false, true);
       	$this->assertSame($date->date('a', 32503680000, true), '01', "01 expected");
    }

	/**
	 * Test for date
	 */
/*    public function testDate22()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject(32503680000, false, true);
       	$this->assertSame($date->date('A', 32503680000, true), '01', "01 expected");
    }

	/**
	 * Test for date
	 */
/*    public function testDate23()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject(32503680000, false, true);
       	$this->assertSame($date->date('B', 32503680000, true), '01', "01 expected");
    }

	/**
	 * Test for date
	 */
/*    public function testDate24()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject(32503680000, false, true);
       	$this->assertSame($date->date('g', 32503680000, true), '01', "01 expected");
    }

	/**
	 * Test for date
	 */
/*    public function testDate25()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject(32503680000, false, true);
       	$this->assertSame($date->date('G', 32503680000, true), '01', "01 expected");
    }

	/**
	 * Test for date
	 */
/*    public function testDate26()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject(32503680000, false, true);
       	$this->assertSame($date->date('h', 32503680000, true), '01', "01 expected");
    }

	/**
	 * Test for date
	 */
/*    public function testDate27()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject(32503680000, false, true);
       	$this->assertSame($date->date('H', 32503680000, true), '01', "01 expected");
    }

	/**
	 * Test for date
	 */
/*    public function testDate28()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject(32503680000, false, true);
       	$this->assertSame($date->date('i', 32503680000, true), '01', "01 expected");
    }

	/**
	 * Test for date
	 */
/*    public function testDate29()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject(32503680000, false, true);
       	$this->assertSame($date->date('s', 32503680000, true), '01', "01 expected");
    }

	/**
	 * Test for date
	 */
/*    public function testDate30()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject(32503680000, false, true);
       	$this->assertSame($date->date('e', 32503680000, true), '01', "01 expected");
    }

	/**
	 * Test for date
	 */
/*    public function testDate31()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject(32503680000, false, true);
       	$this->assertSame($date->date('I', 32503680000, true), '01', "01 expected");
    }

	/**
	 * Test for date
	 */
/*    public function testDate32()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject(32503680000, false, true);
       	$this->assertSame($date->date('O', 32503680000, true), '01', "01 expected");
    }

	/**
	 * Test for date
	 */
/*    public function testDate33()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject(32503680000, false, true);
       	$this->assertSame($date->date('P', 32503680000, true), '01', "01 expected");
    }

	/**
	 * Test for date
	 */
/*    public function testDate34()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject(32503680000, false, true);
       	$this->assertSame($date->date('T', 32503680000, true), '01', "01 expected");
    }

	/**
	 * Test for date
	 */
/*    public function testDate35()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject(32503680000, false, true);
       	$this->assertSame($date->date('Z', 32503680000, true), '01', "01 expected");
    }

	/**
	 * Test for date
	 */
/*    public function testDate36()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject(32503680000, false, true);
       	$this->assertSame($date->date('c', 32503680000, true), '01', "01 expected");
    }

	/**
	 * Test for date
	 */
/*    public function testDate37()
    {
        date_default_timezone_set('UTC');
        $date = new Zend_Date_DateObject();
        $stamp = $date->mktime(13, 44, 22, 10, 6, 3000, -1, false);
       	$this->assertSame($date->date('r', $stamp, false), 'Mon, 06 Oct 3000 13:44:22 -0000', "Mon, 06 Oct 3000 13:44:22 -0000 expected");
    }

	/**
	 * Test for date
	 */
/*    public function testDate38()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject(32503680000, false, false);
       	$this->assertSame($date->date('U', 32503680000, false), '32503680000', "32503680000 expected");
    }

	/**
	 * Test for date
	 */
/*    public function testDate39()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject();
        $stamp = $date->mktime(13, 44, 22, 10, 6, 3000, -1, false);
       	$this->assertSame($date->date('r', $stamp, false), 'Mon, 06 Oct 3000 13:44:22 +0100', "Mon, 06 Oct 3000 13:44:22 +0100 expected");
    }

	/**
	 * Test for date
	 */
/*    public function testDate40()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject();
        $stamp = $date->mktime(13, 44, 22, 10, 6, 2000, -1, false);
       	$this->assertSame($date->date('r', $stamp, false), 'Mon, 06 Oct 2000 13:44:22 +0100', "Mon, 06 Oct 3000 13:44:22 +0100 expected");
    }

	/**
	 * Test for date
	 */
/*    public function testDate41()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject();
        $stamp = $date->mktime(13, 44, 22, 10, 6, 2000, -1, true);
       	$this->assertSame($date->date('r', $stamp, true), 'Mon, 06 Oct 2000 13:44:22 +0100', "Mon, 06 Oct 3000 13:44:22 +0100 expected");
    }

	/**
	 * Test for date
	 */
/*    public function testDate42()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject();
        $stamp = $date->mktime(13, 44, 22, 10, 6, 2000, -1, true);
       	$this->assertSame($date->date('r', $stamp, false), 'Mon, 06 Oct 2000 13:44:22 +0100', "Mon, 06 Oct 3000 13:44:22 +0100 expected");
    }

	/**
	 * Test for date
	 */
/*    public function testDate43()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject();
        $stamp = $date->mktime(13, 44, 22, 10, 6, 2000, -1, false);
       	$this->assertSame($date->date('r', $stamp, true), 'Mon, 06 Oct 2000 13:44:22 +0100', "Mon, 06 Oct 3000 13:44:22 +0100 expected");
    }
*/
}