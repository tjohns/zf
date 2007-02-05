<?php
/**
 * @package Zend_Rest
 * @subpackage UnitTests
 */

/**
 * Zend_Rest_Server
 */
require_once 'Zend/Rest/Client.php';

/**
 * PHPUnit Test Case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Test cases for Zend_Rest_Client
 *
 * @package Zend_Rest
 * @subpackage UnitTests
 */
class Zend_Rest_ClientTest extends PHPUnit_Framework_TestCase 
{
    static $path;

    public function __construct()
    {
        self::$path = dirname(__FILE__).'/responses/';
    }
}
