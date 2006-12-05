<?php
/**
 * @package    Zend_Gdata
 * @subpackage UnitTests
 */

/**
 * Zend_Gdata
 */
require_once 'Zend/Gdata/CodeSearch.php';
require_once 'Zend/Http/Client.php';

/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_CodeSearchTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->gdata = new Zend_Gdata_CodeSearch(new Zend_Http_Client());
    }

    public function testCodeSearch()
    {
        $this->assertTrue(true);
    }

}
