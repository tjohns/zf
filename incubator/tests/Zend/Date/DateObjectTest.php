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
/*    public function testMkTimeforTimeValues()
    {
        $date = new Zend_Date_DateObject();
        
        $this->assertSame($date->mktime(0, 0, 0),       mktime(0, 0, 0));
        $this->assertSame($date->mktime(23, 59, 59),    mktime(23, 59, 59));
        $this->assertSame($date->mktime(100, 100, 100), mktime(100, 100, 100));
        $this->assertSame($date->mktime(0, 0, 0, false, false, false, -1, true),       gmmktime(0, 0, 0));
        $this->assertSame($date->mktime(23, 59, 59, false, false, false, -1, true),    gmmktime(23, 59, 59));
        $this->assertSame($date->mktime(100, 100, 100, false, false, false, -1, true), gmmktime(100, 100, 100));
    }

	/**
	 * Test for mktime
	 */
    public function testMkTimeforDateValuesInPositivePHPRange()
    {
        $date = new Zend_Date_DateObject();
       	$this->assertSame($date->mktime(0, 0, 0, 1, 1, 1970, -1, false),   mktime(0, 0, 0, 1, 1, 1970, -1));
       	$this->assertSame($date->mktime(0, 0, 0, 1, 1, 1970, -1, true),  gmmktime(0, 0, 0, 1, 1, 1970, -1));
       	$this->assertSame($date->mktime(0, 0, 0, 1, 1, 2000, -1, false),   mktime(0, 0, 0, 1, 1, 2000, -1));
       	$this->assertSame($date->mktime(0, 0, 0, 1, 1, 2000, -1, true),  gmmktime(0, 0, 0, 1, 1, 2000, -1));
       	$this->assertSame($date->mktime(0, 0, 0, 12, 30, 2037, -1, false),   mktime(0, 0, 0, 12, 30, 2037, -1));
       	$this->assertSame($date->mktime(0, 0, 0, 12, 30, 2037, -1, true),  gmmktime(0, 0, 0, 12, 30, 2037, -1));
print "DIFF:".($date->mktime(0, 0, 0, 12, 30, 1902, -1, false) - mktime(0, 0, 0, 12, 30, 1902, -1));
//       	$this->assertSame($date->mktime(0, 0, 0, 12, 30, 1902, -1, false),   mktime(0, 0, 0, 12, 30, 1902, -1));
//       	$this->assertSame($date->mktime(0, 0, 0, 12, 30, 1902, -1, true),  gmmktime(0, 0, 0, 12, 30, 1902, -1));
    }

	/**
	 * Test for mktime
	 */
/*    public function testMkTime5()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject();
      	$result = $date->mktime(0,0,0,1,1,1902,-1, true);
       	$this->assertSame($result, -2145916800, "-2145916800 expected");
    }

	/**
	 * Test for mktime
	 */
/*    public function testMkTime6()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject();
      	$result = $date->mktime(0,0,0,1,1,2037, -1, true);
       	$this->assertSame($result, 2114380800, "2114380800 expected");
    }

	/**
	 * Test for mktime
	 */
/*    public function testMkTime7()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject();
      	$result = $date->mktime(0,0,0,1,1,1800, -1, true);
       	$this->assertSame($result, -5364662400, "-5364662400 expected");
    }

	/**
	 * Test for mktime
	 */
/*    public function testMkTime8()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject();
      	$result = $date->mktime(0,0,0,1,1,1600, -1, true);
       	$this->assertSame($result, -11676096000, "-11676096000 expected");
    }

	/**
	 * Test for mktime
	 */
/*    public function testMkTime9()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject();
      	$result = $date->mktime(0,0,0,1,1,0, -1, true);
       	$this->assertSame($result, -62167392000, "-62167392000 expected");
    }

	/**
	 * Test for mktime
	 */
/*    public function testMkTime10()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject();
      	$result = $date->mktime(0,0,0,1,1,-999, -1, true);
       	$this->assertSame($result, -93693369600, "-93693369600 expected");
    }

	/**
	 * Test for mktime
	 */
/*    public function testMkTime11()
    {
        date_default_timezone_set('Europe/Paris');
      	$date = new Zend_Date_DateObject();
      	$result = $date->mktime(0,0,0,1,1,3000, -1, true);
       	$this->assertSame($result, 32503680000, "32503680000 expected");
    }

	/**
	 * Test for mktime
	 */
/*    public function testMkTime12()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject();
      	$result = $date->mktime(0,0,0,-9,1,1600, -1, true);
       	$this->assertSame($result, -11702534400, "-11702534400 expected");
    }

	/**
	 * Test for mktime
	 */
/*    public function testMkTime13()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject();
      	$result = $date->mktime(0,0,0,30,1,1600, -1, true);
       	$this->assertSame($result, -11599891200, "-11599891200 expected");
    }

	/**
	 * Test for mktime
	 */
/*    public function testMkTime14()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject();
      	$result = $date->mktime(0,0,0,10,7,1582, -1, true);
       	$this->assertSame($result, -12219292800, "-12219292800 expected");
    }

	/**
	 * Test for mktime
	 */
/*    public function testMkTime15()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject();
      	$result = $date->mktime(0,0,0,10,10,1800, -1, true);
       	$this->assertSame($result, -5340297600, "-5340297600 expected");
    }

	/**
	 * Test for mktime
	 */
/*    public function testMkTime16()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject();
      	$result = $date->mktime(0,0,0,10,10,3004, -1, true);
       	$this->assertSame($result, 32654275200, "32654275200 expected");
    }

	/**
	 * Test for mktime
	 */
/*    public function testMkTime17()
    {
        date_default_timezone_set('Europe/Paris');
        $date = new Zend_Date_DateObject();
      	$result = $date->mktime(0,0,0,10,10,3004, -1, false);
       	$this->assertSame($result, 32654271600, "32654271600 expected");
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