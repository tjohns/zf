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
require_once 'Zend/Gdata/ClientLogin.php';
require_once 'Zend/Http/Client.php';

/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_CalendarOnlineTest extends PHPUnit_Framework_TestCase
{

    const GOOGLE_DEVELOPER_CALENDAR = 'developer-calendar@google.com';
    const ZEND_CONFERENCE_EVENT = 'bn2h4o4mc3a03ci4t48j3m56pg';

    public function setUp()
    {
        $user = constant('TESTS_ZEND_GDATA_CLIENTLOGIN_EMAIL');
        $pass = constant('TESTS_ZEND_GDATA_CLIENTLOGIN_PASSWORD');
        $service = Zend_Gdata_Calendar::AUTH_SERVICE_NAME;
        $client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $service);
        $this->gdata = new Zend_Gdata_Calendar($client);
    }

    public function testCalendarListFeed() 
    {
        $calFeed = $this->gdata->getCalendarListFeed();
        $this->assertTrue(strpos($calFeed->title->text, 'Calendar List') 
                !== false);
        $calCount = 0;
        foreach ($calFeed as $calendar) {
            $calCount++;
        }
        $this->assertTrue($calCount > 0);
    } 

    public function testCalendarOnlineFeed()
    {
        $eventFeed = $this->gdata->getCalendarEventFeed();
        foreach ($eventFeed as $event) {
            $title = $event->title;
            $times = $event->when;
            $location = $event->where;
            $recurrence = $event->recurrence;
        }
    }
}
