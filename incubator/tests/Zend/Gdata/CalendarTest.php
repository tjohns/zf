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

require_once 'Zend/Gdata/Calendar.php';
require_once 'Zend/Http/Client.php';
// require_once 'XML/Beautifier.php';

/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_CalendarTest extends PHPUnit_Framework_TestCase
{
    const ZEND_CONFERENCE_GROUP = 'ogr93sav88fmf2ssnv851osqm4@group.calendar.google.com';
    const ZEND_CONFERENCE_EVENT_PIRATE_RECEPTION = 'turhun9osc2ajfoie57gb7tcs4';

    public function setUp()
    {
        $this->gdata = new Zend_Gdata_Calendar(new Zend_Http_Client());
        // $this->xml = new XML_Beautifier();
    }

    public function testUpdatedMinParam()
    {
        $updatedMin = '2006-10-27T11:03';
        $this->gdata->resetParameters();
        $this->gdata->setUser(self::ZEND_CONFERENCE_GROUP);
        $this->gdata->setUpdatedMin($updatedMin);
        $this->assertTrue(isset($this->gdata->updatedMin));
        $this->assertEquals($this->gdata->formatTimestamp($updatedMin), $this->gdata->getUpdatedMin());
        $feed = $this->gdata->getFeed();
        $this->assertEquals(3, $feed->count());
        unset($this->gdata->updatedMin);
        $this->assertFalse(isset($this->gdata->updatedMin));
    }

    public function testUpdatedMaxParam()
    {
        $updatedMax = '2006-10-27T11:02';
        $this->gdata->resetParameters();
        $this->gdata->setUser(self::ZEND_CONFERENCE_GROUP);
        $this->gdata->setUpdatedMax($updatedMax);
        $this->assertTrue(isset($this->gdata->updatedMax));
        $this->assertEquals($this->gdata->formatTimestamp($updatedMax), $this->gdata->getUpdatedMax());
        $feed = $this->gdata->getFeed();
        $this->assertEquals(1, $feed->count());
        unset($this->gdata->updatedMax);
        $this->assertFalse(isset($this->gdata->updatedMax));
    }

    /*
     * @todo: Calendar does not seem to recognize the published-min parameter
     *
    public function testPublishedMinParam()
    {
        $publishedMin = '2006-10-27T11:03';
        $this->gdata->resetParameters();
        $this->gdata->setUser(self::ZEND_CONFERENCE_GROUP);
        $this->gdata->setPublishedMin($publishedMin);
        $this->assertTrue(isset($this->gdata->publishedMin));
        $this->assertEquals($this->gdata->formatTimestamp($publishedMin), $this->gdata->getPublishedMin());
        $feed = $this->gdata->getFeed();
        $this->assertEquals(3, $feed->count());
        foreach ($feed as $feedItem) {
            // echo $this->xml->formatString($feedItem->saveXML());
        }
        unset($this->gdata->publishedMin);
        $this->assertFalse(isset($this->gdata->publishedMin));
    }

    public function testPublishedMaxParam()
    {
        $publishedMax = '2006-10-27T11:02';
        $this->gdata->resetParameters();
        $this->gdata->setUser(self::ZEND_CONFERENCE_GROUP);
        // $this->gdata->setPublishedMax($publishedMax);
        // $this->assertTrue(isset($this->gdata->publishedMax));
        // $this->assertEquals($this->gdata->formatTimestamp($publishedMax), $this->gdata->getPublishedMax());
        $feed = $this->gdata->getFeed();
        // $this->assertEquals(1, $feed->count());
        foreach ($feed as $feedItem) {
            // echo $this->xml->formatString($feedItem->saveXML());
        }
        unset($this->gdata->publishedMax);
        $this->assertFalse(isset($this->gdata->publishedMax));
    }
     */

    public function testUserParam()
    {
        $this->gdata->resetParameters();
        $this->gdata->setUser(self::ZEND_CONFERENCE_GROUP);
        $this->assertTrue(isset($this->gdata->user));
        $this->assertEquals(self::ZEND_CONFERENCE_GROUP, $this->gdata->getUser());
        $feed = $this->gdata->getFeed();
        $this->assertEquals(6, $feed->count());
        unset($this->gdata->user);
        $this->assertFalse(isset($this->gdata->user));
    }

    public function testStartMinParam()
    {
        $this->gdata->resetParameters();
        $startMin = '2006-10-31';
        $this->gdata->setUser(self::ZEND_CONFERENCE_GROUP);
        $this->gdata->setStartMin($startMin);
        $this->assertTrue(isset($this->gdata->startMin));
        $this->assertEquals($this->gdata->formatTimestamp($startMin), $this->gdata->getStartMin());
        $feed = $this->gdata->getFeed();
        $this->assertEquals(6, $feed->count());
        unset($this->gdata->startMin);
        $this->assertFalse(isset($this->gdata->startMin));
        unset($this->gdata->user);
        $this->assertFalse(isset($this->gdata->user));
    }

    public function testStartMaxParam()
    {
        $this->gdata->resetParameters();
        $startMax = '2006-10-31';
        $this->gdata->setUser(self::ZEND_CONFERENCE_GROUP);
        $this->gdata->setStartMax($startMax);
        $this->assertTrue(isset($this->gdata->startMax));
        $this->assertEquals($this->gdata->formatTimestamp($startMax), $this->gdata->getStartMax());
        $feed = $this->gdata->getFeed();
        $this->assertEquals(1, $feed->count());
        unset($this->gdata->startMax);
        $this->assertFalse(isset($this->gdata->startMax));
    }

    public function testVisibilityParam()
    {
        $this->gdata->resetParameters();
        $visibility = 'private';
        $this->gdata->setUser(self::ZEND_CONFERENCE_GROUP);
        $this->gdata->setVisibility($visibility);
        $this->assertTrue(isset($this->gdata->visibility));
        $this->assertEquals($visibility, $this->gdata->getVisibility());
        try {
            $feed = $this->gdata->getFeed();
        } catch (Zend_Feed_Exception $e) {
            $this->assertContains('response code 401', $e->getMessage());
        }
        unset($this->gdata->visibility);
        $this->assertFalse(isset($this->gdata->visibility));
    }

    public function testProjectionParam()
    {
        $this->gdata->resetParameters();
        $projection = 'composite';
        $this->gdata->setUser(self::ZEND_CONFERENCE_GROUP);
        $this->gdata->setProjection($projection);
        $this->assertTrue(isset($this->gdata->projection));
        $this->assertEquals($projection, $this->gdata->getProjection());
        $feed = $this->gdata->getFeed();
        foreach ($feed as $feedItem) {
            // echo $this->xml->formatString($feedItem->saveXML());
            $gdc = 'gd:comments';
            $comments = $feedItem->$gdc;
            $gdf = 'gd:feedLink';
            $feedLink = $comments->$gdf;
            $feedElt = $feedLink->feed;
            $this->assertTrue(isset($feedElt));
        }

        unset($this->gdata->projection);
        $this->assertFalse(isset($this->gdata->projection));
    }

    public function testOrderbyParam()
    {
        $this->gdata->resetParameters();
        $orderby = 'starttime';
        $this->gdata->setUser(self::ZEND_CONFERENCE_GROUP);
        $this->gdata->setOrderby($orderby);
        $this->assertTrue(isset($this->gdata->orderby));
        $this->assertEquals($orderby, $this->gdata->getOrderby());
        $feed = $this->gdata->getFeed();
        $prevTs = time();
        foreach ($feed as $feedItem) {
            // echo $this->xml->formatString($feedItem->saveXML());
            $gdw = 'gd:when';
            $elt = $feedItem->$gdw;
            $startTime = $elt->offsetGet('startTime');
            $ts = strtotime($startTime);
            $this->assertThat($ts, $this->logicalNot($this->greaterThan($prevTs)));
            $prevTs = $ts;
        }

        unset($this->gdata->orderby);
        $this->assertFalse(isset($this->gdata->orderby));
    }

    public function testEventParam()
    {
        $this->gdata->resetParameters();
        $this->gdata->setUser(self::ZEND_CONFERENCE_GROUP);
        $this->gdata->setEvent(self::ZEND_CONFERENCE_EVENT_PIRATE_RECEPTION);
        $this->assertTrue(isset($this->gdata->event));
        $this->assertEquals(self::ZEND_CONFERENCE_EVENT_PIRATE_RECEPTION, $this->gdata->getEvent());
        $feed = $this->gdata->getFeed();
        foreach ($feed as $feedItem) {
            // echo $this->xml->formatString($feedItem->saveXML());
            $this->assertContains(self::ZEND_CONFERENCE_EVENT_PIRATE_RECEPTION, $feedItem->id());
        }
        unset($this->gdata->event);
        $this->assertFalse(isset($this->gdata->event));
    }

    /*
     * @todo: Need a calendar entry with comments for test data.
     *
    public function testCommentSubfeed()
    {
        $this->gdata->resetParameters();
        $this->gdata->setUser(self::ZEND_CONFERENCE_GROUP);
        $this->gdata->setEvent(self::ZEND_CONFERENCE_EVENT_PIRATE_RECEPTION);
        $commentId = '???';
        $this->gdata->setComments($commentId);
        $this->assertTrue(isset($this->gdata->comments));
        $this->assertEquals($commentId, $this->gdata->getComments());
        $feed = $this->gdata->getFeed();
        foreach ($feed as $feedItem) {
            // echo $this->xml->formatString($feedItem->saveXML());
        }
        unset($this->gdata->comments);
        $this->assertFalse(isset($this->gdata->comments));
    }
     */

}
