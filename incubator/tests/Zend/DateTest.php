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
}