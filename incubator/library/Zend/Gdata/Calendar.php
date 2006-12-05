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
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Gdata Calendar
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_Calendar extends Zend_Gdata
{
    // Calendar-specific query structure:
    // @todo: www.google.com/calendar/feeds/_userID_/_visibility_/_projection_/_eventID_
    // @todo: www.google.com/calendar/feeds/_userID_/_visibility_/_projection_/_eventID_/comments/_subfeedEntryID_
    // @todo: visibility -> public, private, private-magicCookie
    // @todo: projection -> full, full-noattendees, composite, attendees-only, free-busy, basic

    // Calendar operations:
    // @todo: get a feed
    // @todo: get a feed with deleted events
    // @todo: add an event
    // @todo: send a date-range query
    // @todo: get a list of the user's calendars
    // @todo: get a comment subfeed

    // Calendar-specific response content:
    // @todo: <gCal:accesslevel> -> none, read, freebusy, contributor, owner
    // @todo: <gCal:color> -> #RRGGBB
    // @todo: <gCal:hidden> -> boolean
    // @todo: <gCal:selected> -> boolean
    // @todo: <gCal:timezone> -> string
    // @todo: <gCal:webContent> 

    const CAL_FEED_URI = 'http://www.google.com/calendar/feeds';
    const CAL_POST_URI = 'http://www.google.com/calendar/feeds/default/private/full';

    protected static $defaultTokenName = 'cal_token';

    const PROJ_FULL                    = 'full';
    const PROJ_FULL_NOATTENDEES        = 'full-noattendees';
    const PROJ_COMPOSITE               = 'composite';
    const PROJ_ATTENDEES_ONLY          = 'attendees-only';
    const PROJ_FREE_BUSY               = 'free-busy';
    const PROJ_BASIC                   = 'basic';
    protected static $projectionValues = array(
        self::PROJ_FULL,
        self::PROJ_FULL_NOATTENDEES,
        self::PROJ_COMPOSITE,
        self::PROJ_ATTENDEES_ONLY,
        self::PROJ_FREE_BUSY,
        self::PROJ_BASIC
    );

    const VIS_PUBLIC                   = 'public';
    const VIS_PRIVATE                  = 'private';
    const VIS_PRIVATE_MAGIC_COOKIE     = 'private-';
    protected static $visibilityValues = array(
        self::VIS_PUBLIC,
        self::VIS_PRIVATE,
        self::VIS_PRIVATE_MAGIC_COOKIE
    );

    const ORDER_STARTTIME              = 'starttime';
    protected static $orderbyValues    = array(
        self::ORDER_STARTTIME
    );

    /**
     * Create Gdata_Calendar object
     */
    public function __construct(Zend_Http_Client $client)
    {
        parent::__construct($client);
    }

    /**
     * Retreive feed object
     *
     * @return Zend_Feed
     */
    public function getFeed()
    {
        $uri = self::CAL_FEED_URI;
        if (isset($this->params['_user'])) {
            $uri .= '/' . $this->params['_user'];
        } else {
            $uri .= '/default';
        }
        if (isset($this->params['_visibility']) || isset($this->params['_projection']) || isset($this->params['_event'])) {
            if (isset($this->params['_visibility'])) {
                $uri .= '/' . $this->params['_visibility'];
            } else {
                $uri .= '/private';
            }
            if (isset($this->params['_projection'])) {
                $uri .= '/' . $this->params['_projection'];
            } else {
                $uri .= '/full';
            }
            if (isset($this->params['_event'])) {
                $uri .= '/' . $this->params['_event'];
                if (isset($this->params['_subfeed'])) {
                    $uri .= '/comments/' . $this->params['_subfeed'];
                }
            }
        }
        $uri .= $this->getQueryString();
        return parent::getFeed($uri);
    }

    /**
     * POST xml data to Google with authorization headers set
     *
     * @param string $xml
     * @return Zend_Http_Response
     */
    public function post($xml)
    {
        return parent::post($xml, self::CAL_POST_URI);
    }

    protected function __set($var, $value)
    {
        switch ($var) {
            case 'q':
                // @todo: throw exception for invalid param
                break;
            case 'startMin':
                $var = 'start-min';
                $value = $this->formatTimestamp($value);
                break;
            case 'startMax':
                $var = 'start-max';
                $value = $this->formatTimestamp($value);
                break;
            case 'visibility':
                $var = '_visibility';
                if (!(in_array($value, self::$visibilityValues) || strncmp($value, self::VIS_PRIVATE_MAGIC_COOKIE, strlen(self::VIS_PRIVATE_MAGIC_COOKIE)))) {
                    throw Zend::exception('Zend_Gdata_Exception', "Illegal visibility value: '$value', supported values are " .implode(',', self::visibilityValues));
                }
                break;
            case 'projection':
                $var = '_projection';
                if (!in_array($value, self::$projectionValues)) {
                    throw Zend::exception('Zend_Gdata_Exception', "Illegal projection value: '$value', supported values are " . implode(',', self::$projectionValues));
                }
                break;
            case 'orderby':
                if (!in_array($value, self::$orderbyValues)) {
                    throw Zend::exception('Zend_Gdata_Exception', "Illegal orderby value: '$value', supported values are " . implode(',', self::$orderbyValues));
                }
                break;
            case 'user':
                $var = '_user';
                // @todo: validate user value
                break;
            case 'event':
                $var = '_event';
                // @todo: validate event value
                break;
            case 'comments':
            case 'subfeed':
                $var = '_subfeed';
                // @todo: validate comments subfeed value
                break;
            default:
                // other params are handled by parent
                break;
        }
        parent::__set($var, $value);
    }

    protected function __get($var)
    {
        switch ($var) {
            case 'startMin':
                $var = 'start-min';
                break;
            case 'startMax':
                $var = 'start-max';
                break;
            case 'visibility':
                $var = '_visibility';
                break;
            case 'projection':
                $var = '_projection';
                break;
            case 'user':
                $var = '_user';
                break;
            case 'event':
                $var = '_event';
                break;
            case 'comments':
            case 'subfeed':
                $var = '_subfeed';
                break;
            default:
                break;
        }
        return parent::__get($var);
    }

    protected function __isset($var)
    {
        switch ($var) {
            case 'startMin':
                $var = 'start-min';
                break;
            case 'startMax':
                $var = 'start-max';
                break;
            case 'visibility':
                $var = '_visibility';
                break;
            case 'projection':
                $var = '_projection';
                break;
            case 'user':
                $var = '_user';
                break;
            case 'event':
                $var = '_event';
                break;
            case 'comments':
            case 'subfeed':
                $var = '_subfeed';
                break;
            default:
                break;
        }
        return parent::__isset($var);
    }

    protected function __unset($var)
    {
        switch ($var) {
            case 'startMin':
                $var = 'start-min';
                break;
            case 'startMax':
                $var = 'start-max';
                break;
            case 'visibility':
                $var = '_visibility';
                break;
            case 'projection':
                $var = '_projection';
                break;
            case 'user':
                $var = '_user';
                break;
            case 'event':
                $var = '_event';
                break;
            case 'comments':
            case 'subfeed':
                $var = '_subfeed';
                break;
            default:
                break;
        }
        return parent::__unset($var);
    }

}

