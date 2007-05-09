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
 * Assists in constructing queries for Google Spreadsheets cells 
 *
 * @link http://code.google.com/apis/gdata/spreadsheets/
 *
 * @category   Zend
 * @package    Zend_Gdata_Spreadsheets
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_Spreadsheets_CellQuery extends Zend_Gdata_Query
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
    public function setMinRow($value)
    {
        if ($value != null) {
            $this->_params['min-row'] = $value;
        } else {
            unset($this->_params['min-row']);
        }
        return $this;
    }
    
    /**
     * @return string min-row
     */
    public function getMinRow()
    {
        if (array_key_exists('min-row', $this->_params)) {
            return $this->_params['min-row'];
        } else {
            return null;
        }
    }
    
    /**
     * @return boolean
     * TODO are isset and unset implementations needed for query
     * classes and other data model classes?
     */
    public function issetMinRow()
    {
        return isset($this->_params['min-row']);
    }
    
    /**
     * @param string $value
     * @return Zend_Gdata_Spreadsheets_DocumentQuery Provides a fluent interface
     */
    public function setMaxRow($value)
    {
        if ($value != null) {
            $this->_params['max-row'] = $value;
        } else {
            unset($this->_params['max-row']);
        }
        return $this;
    }
    
    /**
     * @return string max-row
     */
    public function getMaxRow()
    {
        if (array_key_exists('max-row', $this->_params)) {
            return $this->_params['max-row'];
        } else {
            return null;
        }
    }
    
    /**
     * @return boolean
     * TODO are isset and unset implementations needed for query
     * classes and other data model classes?
     */
    public function issetMaxRow()
    {
        return isset($this->_params['max-row']);
    }
    
    /**
     * @param string $value
     * @return Zend_Gdata_Spreadsheets_DocumentQuery Provides a fluent interface
     */
    public function setMinCol($value)
    {
        if ($value != null) {
            $this->_params['min-col'] = $value;
        } else {
            unset($this->_params['min-col']);
        }
        return $this;
    }
    
    /**
     * @return string min-col
     */
    public function getMinCol()
    {
        if (array_key_exists('min-col', $this->_params)) {
            return $this->_params['min-col'];
        } else {
            return null;
        }
    }
    
    /**
     * @return boolean
     * TODO are isset and unset implementations needed for query
     * classes and other data model classes?
     */
    public function issetMinCol()
    {
        return isset($this->_params['min-col']);
    }
    
    /**
     * @param string $value
     * @return Zend_Gdata_Spreadsheets_DocumentQuery Provides a fluent interface
     */
    public function setMaxCol($value)
    {
        if ($value != null) {
            $this->_params['max-col'] = $value;
        } else {
            unset($this->_params['max-col']);
        }
        return $this;
    }
    
    /**
     * @return string max-col
     */
    public function getMaxCol()
    {
        if (array_key_exists('max-col', $this->_params)) {
            return $this->_params['max-col'];
        } else {
            return null;
        }
    }
    
    /**
     * @return boolean
     * TODO are isset and unset implementations needed for query
     * classes and other data model classes?
     */
    public function issetMaxCol()
    {
        return isset($this->_params['max-col']);
    }
    
    /**
     * @param string $value
     * @return Zend_Gdata_Spreadsheets_DocumentQuery Provides a fluent interface
     */
    public function setRange($value)
    {
        if ($value != null) {
            $this->_params['range'] = $value;
        } else {
            unset($this->_params['range']);
        }
        return $this;
    }
    
    /**
     * @return string range
     */
    public function getRange()
    {
        if (array_key_exists('range', $this->_params)) {
            return $this->_params['range'];
        } else {
            return null;
        }
    }
    
    /**
     * @return boolean
     * TODO are isset and unset implementations needed for query
     * classes and other data model classes?
     */
    public function issetRange()
    {
        return isset($this->_params['range']);
    }
    
    /**
     * @param string $value
     * @return Zend_Gdata_Spreadsheets_DocumentQuery Provides a fluent interface
     */
    public function setReturnEmpty($value)
    {
        if ($value != null) {
            $this->_params['return-empty'] = $value;
        } else {
            unset($this->_params['return-empty']);
        }
        return $this;
    }
    
    /**
     * @return string return-empty
     */
    public function getReturnEmpty()
    {
        if (array_key_exists('return-empty', $this->_params)) {
            return $this->_params['return-empty'];
        } else {
            return null;
        }
    }
    
    /**
     * @return boolean
     * TODO are isset and unset implementations needed for query
     * classes and other data model classes?
     */
    public function issetReturnEmpty()
    {
        return isset($this->_params['return-empty']);
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