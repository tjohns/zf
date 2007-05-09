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
require_once 'Zend/Gdata/Calendar/EventQuery.php';
require_once 'Zend/Http/Client.php';

/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_Calendar_EventQueryTest extends PHPUnit_Framework_TestCase
{
    
    const GOOGLE_DEVELOPER_CALENDAR = 'developer-calendar@google.com';
    const ZEND_CONFERENCE_EVENT = 'bn2h4o4mc3a03ci4t48j3m56pg';

    public function setUp()
    {
        $this->query = new Zend_Gdata_Calendar_EventQuery();
    }

    public function testUpdatedMinMaxParam()
    {
        $updatedMin = '2006-09-20';
        $updatedMax = '2006-11-05';
        $this->query->resetParameters();
        $this->query->setUser(self::GOOGLE_DEVELOPER_CALENDAR);
        $this->query->setUpdatedMin($updatedMin);
        $this->query->setUpdatedMax($updatedMax);
        $this->assertTrue(isset($this->query->updatedMin));
        $this->assertTrue(isset($this->query->updatedMax));
        $this->assertTrue(isset($this->query->user));
        $this->assertEquals(Zend_Gdata_App_Util::formatTimestamp($updatedMin), $this->query->getUpdatedMin());
        $this->assertEquals(Zend_Gdata_App_Util::formatTimestamp($updatedMax), $this->query->getUpdatedMax());
        $this->assertEquals(self::GOOGLE_DEVELOPER_CALENDAR, $this->query->getUser());

        unset($this->query->updatedMin);
        $this->assertFalse(isset($this->query->updatedMin));
        unset($this->query->updatedMax);
        $this->assertFalse(isset($this->query->updatedMax));
        unset($this->query->user);
        $this->assertFalse(isset($this->query->user));
    }

    public function testStartMinMaxParam()
    {
        $this->query->resetParameters();
        $startMin = '2006-10-30';
        $startMax = '2006-11-01';
        $this->query->setUser(self::GOOGLE_DEVELOPER_CALENDAR);
        $this->query->setStartMin($startMin);
        $this->query->setStartMax($startMax);
        $this->assertTrue(isset($this->query->startMin));
        $this->assertTrue(isset($this->query->startMax));
        $this->assertEquals(Zend_Gdata_App_Util::formatTimestamp($startMin), $this->query->getStartMin());
        $this->assertEquals(Zend_Gdata_App_Util::formatTimestamp($startMax), $this->query->getStartMax());

        unset($this->query->startMin);
        $this->assertFalse(isset($this->query->startMin));
        unset($this->query->startMax);
        $this->assertFalse(isset($this->query->startMax));
        unset($this->query->user);
        $this->assertFalse(isset($this->query->user));
    }

    public function testVisibilityParam()
    {
        $this->query->resetParameters();
        $visibility = 'private';
        $this->query->setUser(self::GOOGLE_DEVELOPER_CALENDAR);
        $this->query->setVisibility($visibility);
        $this->assertTrue(isset($this->query->visibility));
        $this->assertEquals($visibility, $this->query->getVisibility());
        unset($this->query->visibility);
        $this->assertFalse(isset($this->query->visibility));
    }

    public function testProjectionParam()
    {
        $this->query->resetParameters();
        $projection = 'composite';
        $this->query->setUser(self::GOOGLE_DEVELOPER_CALENDAR);
        $this->query->setProjection($projection);
        $this->assertTrue(isset($this->query->projection));
        $this->assertEquals($projection, $this->query->getProjection());
        unset($this->query->projection);
        $this->assertFalse(isset($this->query->projection));
    }

    public function testOrderbyParam()
    {
        $this->query->resetParameters();
        $orderby = 'starttime';
        $this->query->setUser(self::GOOGLE_DEVELOPER_CALENDAR);
        $this->query->setOrderby($orderby);
        $this->assertTrue(isset($this->query->orderby));
        $this->assertEquals($orderby, $this->query->getOrderby());
        unset($this->query->orderby);
        $this->assertFalse(isset($this->query->orderby));
    }

    public function testEventParam()
    {
        $this->query->resetParameters();
        $this->query->setUser(self::GOOGLE_DEVELOPER_CALENDAR);
        $this->query->setEvent(self::ZEND_CONFERENCE_EVENT);
        $this->assertTrue(isset($this->query->event));
        $this->assertEquals(self::ZEND_CONFERENCE_EVENT, $this->query->getEvent());
        unset($this->query->event);
        $this->assertFalse(isset($this->query->event));
    }

    public function testCommentsParam()
    {
        $this->query->resetParameters();
        $comment = 'we need to reschedule';
        $this->query->setComments($comment);
        $this->assertTrue(isset($this->query->comments));
        $this->assertEquals($comment, $this->query->getComments());
        unset($this->query->comments);
        $this->assertFalse(isset($this->query->comments));
    }

}
