<?php
/**
 * @package    Zend_Gdata
 * @subpackage UnitTests
 */


/**
 * Zend_Gdata
 */
require_once 'Zend/Gdata/Blogger.php';
require_once 'Zend/Http/Client.php';


/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_BloggerTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->gdata = new Zend_Gdata_Blogger(new Zend_Http_Client());
    }

    public function testBlogger()
    {
        $this->assertTrue(true);
    }

}
