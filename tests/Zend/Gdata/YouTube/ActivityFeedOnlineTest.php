<?php

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

require_once 'Zend/Gdata/YouTube.php';
require_once 'Zend/Gdata/YouTube/ActivityFeed.php';
require_once 'Zend/Gdata/YouTube/ActivityEntry.php';
require_once 'Zend/Gdata/ClientLogin.php';

/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_YouTube_ActivityFeedOnlineTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->user = constant('TESTS_ZEND_GDATA_CLIENTLOGIN_EMAIL');
        $this->pass = constant('TESTS_ZEND_GDATA_CLIENTLOGIN_PASSWORD');
        $this->developerKey = constant(
            'TESTS_ZEND_GDATA_YOUTUBE_DEVELOPER_KEY');
        $this->clientId = constant(
            'TESTS_ZEND_GDATA_YOUTUBE_CLIENT_ID');
        $this->ytAccount = constant(
            'TESTS_ZEND_GDATA_YOUTUBE_ACCOUNT');
        $this->youtube = new Zend_Gdata_YouTube();
        $this->youtube->setMajorProtocolVersion(2);
    }

    public function testRetrieveActivityFeed()
    {
        $client = Zend_Gdata_ClientLogin::getHttpClient(
            $this->user, $this->pass, 'youtube' , null, 'ZF_UnitTest', null, null,
            'https://www.google.com/youtube/accounts/ClientLogin');

        $this->youtube = new Zend_Gdata_YouTube($client, 'ZF_UnitTest',
            $this->clientId, $this->developerKey);
        $this->youtube->setMajorProtocolVersion(2);

        $feed = $this->youtube->getActivityForUser($this->ytAccount);
        $this->assertTrue($feed instanceof Zend_Gdata_YouTube_ActivityFeed);
        $this->assertTrue((count($feed->entries) > 0));
        $this->assertEquals('Activity of ' . $this->ytAccount,
            $feed->title->text);
    }

    public function testExceptionIfNotUsingDeveloperKey()
    {
        $exceptionThrown = false;
        try {
            $this->youtube->getActivityForUser($this->ytAccount);
        } catch (Zend_Gdata_App_HttpException $e) {
            $exceptionThrown = true;
        }
        $this->assertTrue($exceptionThrown, 'Was expecting an exception when ' .
            'making a request to the YouTube Activity feed without a ' . 
            'developer key.');
    }

    public function testRetrieveActivityFeedForMultipleUsers()
    {
        $client = Zend_Gdata_ClientLogin::getHttpClient(
            $this->user, $this->pass, 'youtube' , null, 'ZF_UnitTest', null, null,
            'https://www.google.com/youtube/accounts/ClientLogin');

        $this->youtube = new Zend_Gdata_YouTube($client, 'ZF_UnitTest',
            $this->clientId, $this->developerKey);
        $this->youtube->setMajorProtocolVersion(2);

        $feed = $this->youtube->getActivityForUser(
            $this->ytAccount . ',zfgdata');
        $this->assertTrue($feed instanceof Zend_Gdata_YouTube_ActivityFeed);
        $this->assertTrue((count($feed->entries) > 0));
        $this->assertEquals('Activity of ' . $this->ytAccount . ',zfgdata',
            $feed->title->text);
    }

    public function testRetrieveFriendFeed()
    {
        $client = Zend_Gdata_ClientLogin::getHttpClient(
            $this->user, $this->pass, 'youtube' , null, 'ZF_UnitTest', null, null,
            'https://www.google.com/youtube/accounts/ClientLogin');

        $this->youtube = new Zend_Gdata_YouTube($client, 'ZF_UnitTest',
            $this->clientId, $this->developerKey);
        $this->youtube->setMajorProtocolVersion(2);

        $feed = $this->youtube->getFriendActivityForCurrentUser();
        $this->assertTrue($feed instanceof Zend_Gdata_YouTube_ActivityFeed);
        $this->assertTrue((count($feed->entries) > 0));
        $this->assertEquals('Activity of ' . $this->ytAccount . "'s friends",
            $feed->title->text);
    }

    public function testThrowExceptionOnRequestForMoreThan20Users()
    {
        $exceptionThrown = false;
        $listOfMoreThan20Users = null;
        for ($x = 0;  $x < 30; $x++) {
            $listOfMoreThan20Users .= "user$x";
            if ($x != 29) {
                $listOfMoreThan20Users .= ",";
            }
        }

        try {
            $this->youtube->getActivityForUser($listOfMoreThan20Users);
        } catch (Zend_Gdata_App_InvalidArgumentException $e) {
            $exceptionThrown = true;            
        }
        $this->assertTrue($exceptionThrown, 'Was expecting an exception on ' .
            'a request to ->getActivityForUser when more than 20 users were ' .
            'specified in YouTube.php');
    }

}
