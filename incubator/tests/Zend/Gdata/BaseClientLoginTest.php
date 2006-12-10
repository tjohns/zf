<?php
/**
 * @package    Zend_Gdata
 * @subpackage UnitTests
 */


/**
 * Zend_Gdata
 */
require_once 'Zend/Gdata/Base.php';
require_once 'Zend/Gdata/ClientLogin.php';

if (!defined('TESTS_ZEND_GDATA_CLIENTLOGIN')) {
    if (is_readable('TestConfiguration.php')) {
        require_once 'TestConfiguration.php';
    } else {
        require_once 'TestConfiguration.php.dist';
    }
}

/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_BaseClientLoginTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $email = '';
        $password = '';

        if (!(defined('TESTS_ZEND_GDATA_CLIENTLOGIN_ENABLED') && TESTS_ZEND_GDATA_CLIENTLOGIN === true)) {
            $this->markTestSkipped("Skipping Gdata ClientLogin authenticated tests.  Edit TestConfiguration.php\nand set TESTS_ZEND_GDATA_CLIENTLOGIN_ENABLED to true to enable tests.");
            return;
        }

        $email = TESTS_ZEND_GDATA_CLIENTLOGIN_EMAIL;
        $password = TESTS_ZEND_GDATA_CLIENTLOGIN_PASSWORD;
        $client = Zend_Gdata_ClientLogin::getHttpClient($email, $password);
        $this->gdata = new Zend_Gdata_Base($client);
    }

    public function testQuery()
    {
        $this->gdata->query = 'digital camera';
        $feed = $this->gdata->getFeed();
        // print_r($feed);
    }

}
