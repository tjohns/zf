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
    const GOOGLE_DEVELOPER_CALENDAR = 'developer-calendar@google.com';
    const ZEND_CONFERENCE_EVENT = 'bn2h4o4mc3a03ci4t48j3m56pg';

    public function setUp()
    {
        $this->gdata = new Zend_Gdata_Calendar(new Zend_Http_Client());
        // $this->xml = new XML_Beautifier();
    }

    public function testUpdatedMinMaxParam()
    {
        $updatedMin = '2006-09-20';
        $updatedMax = '2006-11-05';
        $this->gdata->resetParameters();
        $this->gdata->setUser(self::GOOGLE_DEVELOPER_CALENDAR);
        $this->gdata->setUpdatedMin($updatedMin);
        $this->gdata->setUpdatedMax($updatedMax);
        $this->assertTrue(isset($this->gdata->updatedMin));
        $this->assertTrue(isset($this->gdata->updatedMax));
        $this->assertEquals($this->gdata->formatTimestamp($updatedMin), $this->gdata->getUpdatedMin());
        $this->assertEquals($this->gdata->formatTimestamp($updatedMax), $this->gdata->getUpdatedMax());

        $feed = $this->gdata->getCalendarFeed();
        $this->assertEquals(7, $feed->count());

        unset($this->gdata->updatedMin);
        $this->assertFalse(isset($this->gdata->updatedMin));
        unset($this->gdata->updatedMax);
        $this->assertFalse(isset($this->gdata->updatedMax));
    }

    public function testStartMinMaxParam()
    {
        $this->gdata->resetParameters();
        $startMin = '2006-10-30';
        $startMax = '2006-11-01';
        $this->gdata->setUser(self::GOOGLE_DEVELOPER_CALENDAR);
        $this->gdata->setStartMin($startMin);
        $this->gdata->setStartMax($startMax);
        $this->assertTrue(isset($this->gdata->startMin));
        $this->assertTrue(isset($this->gdata->startMax));
        $this->assertEquals($this->gdata->formatTimestamp($startMin), $this->gdata->getStartMin());
        $this->assertEquals($this->gdata->formatTimestamp($startMax), $this->gdata->getStartMax());

        $feed = $this->gdata->getCalendarFeed();
        $this->assertEquals(1, $feed->count());

        unset($this->gdata->startMin);
        $this->assertFalse(isset($this->gdata->startMin));
        unset($this->gdata->startMax);
        $this->assertFalse(isset($this->gdata->startMax));
        unset($this->gdata->user);
        $this->assertFalse(isset($this->gdata->user));
    }

    public function testVisibilityParam()
    {
        $this->gdata->resetParameters();
        $visibility = 'private';
        $this->gdata->setUser(self::GOOGLE_DEVELOPER_CALENDAR);
        $this->gdata->setVisibility($visibility);
        $this->assertTrue(isset($this->gdata->visibility));
        $this->assertEquals($visibility, $this->gdata->getVisibility());
        try {
            $feed = $this->gdata->getCalendarFeed();
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
        $this->gdata->setUser(self::GOOGLE_DEVELOPER_CALENDAR);
        $this->gdata->setProjection($projection);
        $this->assertTrue(isset($this->gdata->projection));
        $this->assertEquals($projection, $this->gdata->getProjection());
        $feed = $this->gdata->getCalendarFeed();
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
        $this->gdata->setUser(self::GOOGLE_DEVELOPER_CALENDAR);
        $this->gdata->setOrderby($orderby);
        $this->assertTrue(isset($this->gdata->orderby));
        $this->assertEquals($orderby, $this->gdata->getOrderby());
        $feed = $this->gdata->getCalendarFeed();
        $prevTs = null;
        foreach ($feed as $feedItem) {
            // echo $this->xml->formatString($feedItem->saveXML());
            $gdw = 'gd:when';
            $elt = $feedItem->$gdw;
            $startTime = $elt->offsetGet('startTime');
            $ts = strtotime($startTime);
            if ($prevTs != null) {
                $this->assertThat($ts, $this->logicalNot($this->greaterThan($prevTs)));
            }
            $prevTs = $ts;
        }

        unset($this->gdata->orderby);
        $this->assertFalse(isset($this->gdata->orderby));
    }

    public function testEventParam()
    {
        $this->gdata->resetParameters();
        $this->gdata->setUser(self::GOOGLE_DEVELOPER_CALENDAR);
        $this->gdata->setEvent(self::ZEND_CONFERENCE_EVENT);
        $this->assertTrue(isset($this->gdata->event));
        $this->assertEquals(self::ZEND_CONFERENCE_EVENT, $this->gdata->getEvent());
        $feed = $this->gdata->getCalendarFeed();
        foreach ($feed as $feedItem) {
            // echo $this->xml->formatString($feedItem->saveXML());
            $this->assertContains(self::ZEND_CONFERENCE_EVENT, $feedItem->id());
        }
        unset($this->gdata->event);
        $this->assertFalse(isset($this->gdata->event));
    }

}
