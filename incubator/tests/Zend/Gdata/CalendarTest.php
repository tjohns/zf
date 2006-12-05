<?php
/**
 * @package    Zend_Gdata
 * @subpackage UnitTests
 */


/**
 * Zend_Gdata
 */
require_once 'Zend/Gdata/Calendar.php';
require_once 'Zend/Http/Client.php';


/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_CalendarTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->gdata = new Zend_Gdata_Calendar(new Zend_Http_Client());
    }

    public function testCalendar()
    {
        $this->assertTrue(true);
    }

}
