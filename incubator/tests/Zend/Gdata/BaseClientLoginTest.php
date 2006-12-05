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

        if (!defined('TESTS_ZEND_GDATA_CLIENTLOGIN') || TESTS_ZEND_GDATA_CLIENTLOGIN !== true) {
            $this->markTestSkipped('Skipping Gdata ClientLogin authenticated tests.');
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
