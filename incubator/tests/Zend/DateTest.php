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
        	$date = new Zend_Date(0,Zend_Date::TIMESTAMP,$locale);
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
    	$date = new Zend_Date(0,Zend_Date::TIMESTAMP,$locale);
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
        	$date = new Zend_Date(0,Zend_Date::TIMESTAMP,$locale);
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
    	$date = new Zend_Date(0,Zend_Date::TIMESTAMP,$locale);
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
        	$date = new Zend_Date(0,Zend_Date::TIMESTAMP,$locale);
    	    $result = $date->subTimestamp('notimestamp');
        	$this->Fail();
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }
}