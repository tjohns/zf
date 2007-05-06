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
 * @package    Zend_Gdata_Calendar
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Zend_Gdata
 */
require_once('Zend/Gdata.php');

/**
 * Zend_Gdata_Query
 */
require_once('Zend/Gdata/Query.php');

/**
 * Zend_Gdata_Calendar
 */
require_once('Zend/Gdata/Calendar.php');

/**
 * Zend_Gdata_Data
 */
require_once('Zend/Gdata/Data.php');

/**
 * Zend_Gdata_App_InvalidArgumentException
 */
require_once('Zend/Gdata/App/InvalidArgumentException.php');

/**
 * Assists in constructing queries for Google Calendar events 
 *
 * @link http://code.google.com/apis/gdata/calendar/
 *
 * @category   Zend
 * @package    Zend_Gdata_Calendar
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_Calendar_EventQuery extends Zend_Gdata_Query
{

    const CALENDAR_FEED_URI = 'http://www.google.com/calendar/feeds';

    protected $_defaultFeedUri = self::CALENDAR_FEED_URI;
    protected $_comments = null;
    protected $_user = null;
    protected $_visibility = null;
    protected $_projection = null;
    protected $_event = null;

    /**
     * Create Gdata_Calendar_EventQuery object
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param string $value
     * @return Zend_Gdata_Calendar_EventQuery Provides a fluent interface
     */
    public function setComments($value)
    {
        $this->_comments = $value;
        return $this;
    }

    /**
     * @param string $value
     * @return Zend_Gdata_Calendar_EventQuery Provides a fluent interface
     */
    public function setEvent($value)
    {
        $this->_event = $value;
        return $this;
    }

    /**
     * @param string $value
     * @return Zend_Gdata_Calendar_EventQuery Provides a fluent interface
     */
    public function setProjection($value)
    {
        $this->_projection = $value;
        return $this;
    }

    /**
     * @param string $value
     * @return Zend_Gdata_Calendar_EventQuery Provides a fluent interface
     */
    public function setUser($value)
    {
        $this->_user = $value;
        return $this;
    }

    /**
     * @return string visibility
     * @return Zend_Gdata_Calendar_EventQuery Provides a fluent interface
     */
    public function setVisibility($value)
    {
        $this->_visibility = $value;
        return $this;
    }

    /**
     * @return string comments
     */
    public function getComments()
    {
        return $this->_comments;
    }

    /**
     * @return string event
     */
    public function getEvent()
    {
        return $this->_event;
    }

    /**
     * @return string projection
     */
    public function getProjection()
    {
        return $this->_projection;
    }

    /**
     * @return string user
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * @return string visibility
     */
    public function getVisibility()
    {
        return $this->_visibility;
    }

    /**
     * @returns boolean
     */
    public function issetComments()
    {
        return isset($this->_comments);
    }

    /**
     * @returns boolean
     */
    public function issetEvent()
    {
        return isset($this->_event);
    }

    /**
     * @returns boolean
     */
    public function issetProjection()
    {
        return isset($this->_projection);
    }

    /**
     * @returns boolean
     */
    public function issetUser()
    {
        return isset($this->_user);
    }

    /**
     * @returns boolean
     */
    public function issetVisibility()
    {
        return isset($this->_visibility);
    }

    /**
     * @param int $value
     * @return Zend_Gdata_Calendar_EventQuery Provides a fluent interface
     */
    public function setStartMax($value)
    {
        if ($value != null) {
            $this->_params['start-max'] = self::formatTimestamp($value);
        } else {
            unset($_params['start-max']);
        }
        return $this;
    }

    /**
     * @param int $value
     * @return Zend_Gdata_Calendar_EventQuery Provides a fluent interface
     */
    public function setStartMin($value)
    {
        if ($value != null) {
            $this->_params['start-min'] = self::formatTimestamp($value);
        } else {
            unset($_params['start-min']);
        }
        return $this;
    }

    /**
     * @param string $value
     * @return Zend_Gdata_Calendar_EventQuery Provides a fluent interface
     */
    public function setOrderby($value)
    {
        if ($value != null) {
            $this->_params['orderby'] = $value;
        } else {
            unset($_params['orderby']);
        }
        return $this;
    }

    /**
     * @returns int startMax
     */
    public function getStartMax()
    {
        if (array_key_exists('start-max', $this->_params)) {
            return $this->_params['start-max'];
        } else {
            return null;
        }
    }

    /**
     * @returns int startMin
     */
    public function getStartMin()
    {
        if (array_key_exists('start-min', $this->_params)) {
            return $this->_params['start-min'];
        } else {
            return null;
        }
    }

    /**
     * @return string orderBy
     */
    public function getOrderby()
    {
        if (array_key_exists('orderby', $this->_params)) {
            return $this->_params['orderby'];
        } else {
            return null;
        }
    }

    /**
     * @return string futureevents
     */
    public function getFutureevents()
    {
        if (array_key_exists('futureevents', $this->_params)) {
            return $this->_params['futureevents'];
        } else {
            return null;
        }
    }

    /**
     * @param string $value
     * @return Zend_Gdata_Calendar_EventQuery Provides a fluent interface
     */
    public function setFutureevents($value)
    {
        if ($value != null) {
            $this->_params['futureevents'] = $value;
        } else {
            unset($_params['futureevents']);
        }
        return $this;
    }

    /**
     * @returns boolean
     * TODO are isset and unset implementations needed for query
     * classes and other data model classes?
     */
    public function issetFutureevents()
    {
        return isset($this->_params['futureevents']);
    }

    public function __get($name)
    {
        $method = 'get'.ucfirst($name);
        if (method_exists($this, $method)) {
            return call_user_func(array(&$this, $method));
        } else {
            throw new Exception('Property ' . $name . '  does not exist');
        }
    }

    public function __set($name, $val)
    {
        $method = 'set'.ucfirst($name);
        if (method_exists($this, $method)) {
            return call_user_func(array(&$this, $method), $val);
        } else {
            throw new Exception('Property ' . $name . '  does not exist');
        }
    }

    public function __isset($name)
    {
        $method = 'isset'.ucfirst($name);
        if (method_exists($this, $method)) {
            return call_user_func(array(&$this, $method), $val);
        } else {
            throw new Exception('Property ' . $name . '  does not exist');
        }
    }

    public function __unset($name)
    {
        $method = 'set'.ucfirst($name);
        if (method_exists($this, $method)) {
            return call_user_func(array(&$this, $method), null);
        } else {
            throw new Exception('Property ' . $name . '  does not exist');
        }
    }

    /**
     * @return string url
     */
    public function getQueryUrl()
    {
        if ($uri == null) {
            $uri = $this->_defaultFeedUri;
        }
        if ($this->issetUser()) {
            $uri .= '/' . $this->getUser();
        } else { 
            $uri .= '/default';
        }
        if ($this->issetVisibility()) {
            $uri .= '/' . $this->getVisibility();
        } else {
            $uri .= '/public';
        }
        if ($this->issetProjection()) {
            $uri .= '/' . $this->getProjection();
        } else {
            $uri .= '/full';
        }
        if ($this->issetEvent()) {
            $uri .= '/' . $this->getEvent();
            if ($this->issetComments()) {
                $uri .= '/comments/' . $this->getComments();
            }
        }
        $uri .= $this->getQueryString();
        return $uri;
    }

}
