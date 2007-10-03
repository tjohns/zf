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
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

require_once 'Zend/Gdata/YouTube.php';

/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_YouTubeOnlineTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->ytAccount = constant('TESTS_ZEND_GDATA_YOUTUBE_ACCOUNT');
        $this->gdata = new Zend_Gdata_YouTube();
    }

    public function tearDown()
    {
    }

    public function testRetrieveSubScriptionFeed() 
    {
        $feed = $this->gdata->getSubscriptionFeed($this->ytAccount);
        $this->assertTrue($feed->totalResults->text > 0);
        $this->assertEquals('zfgdata\'s Subscriptions', $feed->title->text);
        $this->assertTrue(count($feed->entry) > 0);
        foreach ($feed->entry as $entry) {
            $this->assertTrue($entry->title->text != '');
        }
    }

    public function testRetrieveContactFeed()
    {
        $feed = $this->gdata->getContactFeed($this->ytAccount);
        $this->assertTrue($feed->totalResults->text > 0);
        $this->assertEquals('zfgdata\'s Contacts', $feed->title->text);
        $this->assertTrue(count($feed->entry) > 0);
        foreach ($feed->entry as $entry) {
            $this->assertTrue($entry->title->text != '');
        }
        $this->assertEquals('ytgdatatest1', $feed->entry[0]->username->text);
    }

    public function testRetrieveUserProfile()
    {
        $entry = $this->gdata->getUserProfile($this->ytAccount);
        $this->assertEquals('zfgdata', $entry->title->text);
        $this->assertEquals('zfgdata', $entry->username->text);
        $this->assertEquals(31, $entry->age->text);
        $this->assertEquals('f', $entry->gender->text);
        $this->assertEquals('US', $entry->location->text);
    }

    public function testRetrievePlaylistList()
    {
        $feed = $this->gdata->getPlaylistListFeed($this->ytAccount);
        $this->assertTrue($feed->totalResults->text > 0);
        $this->assertEquals('zfgdata\'s Playlists', $feed->title->text);
        $this->assertTrue(count($feed->entry) > 0);
        foreach ($feed->entry as $entry) {
            $this->assertTrue($entry->title->text != '');
        }
        $this->assertEquals('test playlist', $feed->entry[0]->description->text);
    }

    public function testRetrievePlaylistVideoFeed()
    {
        $listFeed = $this->gdata->getPlaylistListFeed($this->ytAccount);

        $feed = $this->gdata->getPlaylistVideoFeed($listFeed->entry[0]->feedLink[0]->href);
        $this->assertTrue($feed->totalResults->text > 0);
        $this->assertEquals('test playlist', $feed->title->text);
        $this->assertTrue(count($feed->entry) > 0);
        foreach ($feed->entry as $entry) {
            $this->assertTrue($entry->title->text != '');
        }
    }

    public function testRetrieveTopRatedVideos()
    {
        $feed = $this->gdata->getTopRatedVideoFeed();
        $this->assertTrue($feed->totalResults->text > 10);
        $this->assertEquals('Top Rated', $feed->title->text);
        $this->assertTrue(count($feed->entry) > 0);
        foreach ($feed->entry as $entry) {
            $this->assertTrue($entry->rating->average > 3);
            $this->assertEquals(1, $entry->rating->min);
            $this->assertEquals(5, $entry->rating->max);
            $this->assertTrue($entry->rating->numRaters > 2);
        }
    }

    public function testRetrieveMostViewedVideos()
    {
        $feed = $this->gdata->getMostViewedVideoFeed();
        $this->assertTrue($feed->totalResults->text > 10);
        $this->assertEquals('Most Viewed', $feed->title->text);
        $this->assertTrue(count($feed->entry) > 0);
        foreach ($feed->entry as $entry) {
            if ($entry->rating) {
                $this->assertEquals(1, $entry->rating->min);
                $this->assertEquals(5, $entry->rating->max);
            }
        }
    }

}
