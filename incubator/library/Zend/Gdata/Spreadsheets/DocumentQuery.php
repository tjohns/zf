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
 * Zend_Gdata_App_util
 */
require_once('Zend/Gdata/App/Util.php');

/**
 * Zend_Gdata_Query
 */
require_once('Zend/Gdata/Query.php');

/**
 * Assists in constructing queries for Google Spreadsheets documents
 *
 * @link http://code.google.com/apis/gdata/spreadsheets/
 *
 * @category   Zend
 * @package    Zend_Gdata_Spreadsheets
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_Spreadsheets_DocumentQuery extends Zend_Gdata_Query
{

    const SPREADSHEETS_FEED_URI = 'http://spreadsheets.google.com/feeds/spreadsheets';
    
    protected $_defaultFeedUri = self::SPREADSHEETS_FEED_URI;
    
    /**
     * Create Zend_Gdata_Spreadsheets_DocumentQuery object
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * @param string $value
     * @return Zend_Gdata_Spreadsheets_DocumentQuery Provides a fluent interface
     */
    public function setTitle($value)
    {
        if ($value != null) {
            $this->_params['title'] = $value;
        } else {
            unset($this->_params['title']);
        }
        return $this;
    }
    
    /**
     * @param string $value
     * @return Zend_Gdata_Spreadsheets_DocumentQuery Provides a fluent interface
     */
    public function setTitleExact($value)
    {
        if ($value != null) {
            $this->_params['title-exact'] = $value;
        } else {
            unset($this->_params['title-exact']);
        }
        return $this;
    }
    
    /**
     * @return string title
     */
    public function getTitle()
    {
        if (array_key_exists('title', $this->_params)) {
            return $this->_params['title'];
        } else {
            return null;
        }
    }
    
    /**
     * @return string title-exact
     */
    public function getTitleExact()
    {
        if (array_key_exists('title-exact', $this->_params)) {
            return $this->_params['title-exact'];
        } else {
            return null;
        }
    }
    
    /**
     * @return boolean
     * TODO are isset and unset implementations needed for query
     * classes and other data model classes?
     */
    public function issetTitle()
    {
        return isset($this->_params['title']);
    }
    
    /**
     * @return boolean
     * TODO are isset and unset implementations needed for query
     * classes and other data model classes?
     */
    public function issetTitleExact()
    {
        return isset($this->_params['title-exact']);
    }
    
    /**
     * @return string url
     */
    public function getQueryUrl()
    {
        if ($uri == null) {
            $uri = $this->_defaultFeedUri;
        }
        $uri .= $this->getQueryString();
        return $uri;
    }
    
    /**
     * @return string query string
     */
    public function getQueryString()
    {
        return parent::getQueryString();
    }
    
}