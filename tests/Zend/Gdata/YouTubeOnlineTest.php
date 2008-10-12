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
require_once 'Zend/Gdata/YouTube/VideoQuery.php';

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
        $this->assertEquals('Subscriptions of zfgdata', $feed->title->text);
        $this->assertTrue(count($feed->entry) > 0);
        foreach ($feed->entry as $entry) {
            $this->assertTrue($entry->title->text != '');
        }
    }

    public function testRetrieveContactFeed()
    {
        $feed = $this->gdata->getContactFeed($this->ytAccount);
        $this->assertTrue($feed->totalResults->text > 0);
        $this->assertEquals('Contacts of zfgdata', $feed->title->text);
        $this->assertTrue(count($feed->entry) > 0);
        foreach ($feed->entry as $entry) {
            $this->assertTrue($entry->title->text != '');
        }
        $this->assertEquals('ytgdatatest1', $feed->entry[0]->username->text);
    }

    public function testRetrieveUserVideos()
    {
        $feed = $this->gdata->getUserUploads($this->ytAccount);
        $this->assertEquals('Videos of zfgdata', $feed->title->text);
        $this->assertTrue(count($feed->entry) === 1);
    }

    public function testRetrieveVideoFeed()
    {
        $feed = $this->gdata->getVideoFeed();

        $query = new Zend_Gdata_YouTube_VideoQuery();
        $query->setVideoQuery('puppy');
        $feed = $this->gdata->getVideoFeed($query);
        foreach ($feed as $videoEntry) {
            $videoResponsesLink = $videoEntry->getVideoResponsesLink();
            $videoRatingsLink = $videoEntry->getVideoRatingsLink();
            $videoComplaintsLink = $videoEntry->getVideoComplaintsLink();
        }

        $feed = $this->gdata->getVideoFeed($query->getQueryUrl());
    }

    public function testRetrieveVideoEntry()
    {
        $entry = $this->gdata->getVideoEntry('66wj2g5yz0M');
        $this->assertEquals('TestMovie', $entry->title->text);

        $entry = $this->gdata->getVideoEntry(null, 'http://gdata.youtube.com/feeds/videos/66wj2g5yz0M');
        $this->assertEquals('TestMovie', $entry->title->text);
    }

    public function testRetrieveOtherFeeds()
    {
        $feed = $this->gdata->getRelatedVideoFeed('66wj2g5yz0M');
        $feed = $this->gdata->getVideoResponseFeed('66wj2g5yz0M');
        $feed = $this->gdata->getVideoCommentFeed('66wj2g5yz0M');
        $feed = $this->gdata->getWatchOnMobileVideoFeed();
        $feed = $this->gdata->getUserFavorites('zfgdata');
    }

    public function testRetrieveUserProfile()
    {
        $entry = $this->gdata->getUserProfile($this->ytAccount);
        $this->assertEquals('zfgdata Channel', $entry->title->text);
        $this->assertEquals('zfgdata', $entry->username->text);
        $this->assertEquals('I\'m a lonely test account, with little to do but sit around and wait for people to use me.  I get bored in between releases and often sleep to pass the time.  Please use me more often, as I love to show off my talent in breaking your code.',
                $entry->description->text);
        $this->assertEquals(32, $entry->age->text);
        $this->assertEquals('crime and punishment, ps i love you, the stand', $entry->books->text);
        $this->assertEquals('Google', $entry->company->text);
        $this->assertEquals('software engineering, information architecture, photography, travel', $entry->hobbies->text);
        $this->assertEquals('Mountain View, CA', $entry->hometown->text);
        $this->assertEquals('San Francisco, CA, US', $entry->location->text);
        $this->assertEquals('monk, heroes, law and order, top gun', $entry->movies->text);
        $this->assertEquals('imogen heap, frou frou, thievory corp, morcheeba, barenaked ladies', $entry->music->text);
        $this->assertEquals('Developer Programs', $entry->occupation->text);
        $this->assertEquals('University of the World', $entry->school->text);
        $this->assertEquals('f', $entry->gender->text);
        $this->assertEquals('taken', $entry->relationship->text);
    }

    public function testRetrievePlaylistList()
    {
        $feed = $this->gdata->getPlaylistListFeed($this->ytAccount);
        $this->assertTrue($feed->totalResults->text > 0);
        $this->assertEquals('Playlists of zfgdata', $feed->title->text);
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
