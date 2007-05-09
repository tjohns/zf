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
 * @package    Zend_Gdata_Spreadsheets
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
 * Assists in constructing queries for Google Spreadsheets lists 
 *
 * @link http://code.google.com/apis/gdata/calendar/
 *
 * @category   Zend
 * @package    Zend_Gdata_Spreadsheets
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_Spreadsheets_ListQuery extends Zend_Gdata_Query
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
    public function setSpreadsheetQuery($value)
    {
        if ($value != null) {
            $this->_params['sq'] = $value;
        } else {
            unset($this->_params['sq']);
        }
        return $this;
    }
    
    /**
     * @return string spreadsheet query
     */
    public function getSpreadsheetQuery()
    {
        if (array_key_exists('sq', $this->_params)) {
            return $this->_params['sq'];
        } else {
            return null;
        }
    }
    
    /**
     * @return boolean
     * TODO are isset and unset implementations needed for query
     * classes and other data model classes?
     */
    public function issetSpreadsheetQuery()
    {
        return isset($this->_params['sq']);
    }
    
    /**
     * @param string $value
     * @return Zend_Gdata_Spreadsheets_DocumentQuery Provides a fluent interface
     */
    public function setOrderBy($value)
    {
        if ($value != null) {
            $this->_params['orderby'] = $value;
        } else {
            unset($this->_params['orderby']);
        }
        return $this;
    }
    
    /**
     * @return string orderby
     */
    public function getOrderBy()
    {
        if (array_key_exists('orderby', $this->_params)) {
            return $this->_params['orderby'];
        } else {
            return null;
        }
    }
    
    /**
     * @return boolean
     * TODO are isset and unset implementations needed for query
     * classes and other data model classes?
     */
    public function issetOrderBy()
    {
        return isset($this->_params['orderby']);
    }
    
    /**
     * @param string $value
     * @return Zend_Gdata_Spreadsheets_DocumentQuery Provides a fluent interface
     */
    public function setReverse($value)
    {
        if ($value != null) {
            $this->_params['reverse'] = $value;
        } else {
            unset($this->_params['reverse']);
        }
        return $this;
    }
    
    /**
     * @return string reverse
     */
    public function getReverse()
    {
        if (array_key_exists('reverse', $this->_params)) {
            return $this->_params['reverse'];
        } else {
            return null;
        }
    }
    
    /**
     * @return boolean
     * TODO are isset and unset implementations needed for query
     * classes and other data model classes?
     */
    public function issetReverse()
    {
        return isset($this->_params['reverse']);
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