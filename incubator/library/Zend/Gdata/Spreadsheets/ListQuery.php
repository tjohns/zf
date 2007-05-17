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

    const SPREADSHEETS_LIST_FEED_URI = 'http://spreadsheets.google.com/feeds/list';
    
    protected $_defaultFeedUri = self::SPREADSHEETS_LIST_FEED_URI;
    protected $_visibility = 'private';
    protected $_projection = 'full';
    protected $_spreadsheetKey = null;
    protected $_worksheetId = 'default';
    protected $_rowId = null;
    
    /**
     * Create Zend_Gdata_Spreadsheets_DocumentQuery object
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * @param string $value
     * @return Zend_Gdata_Spreadsheets_CellQuery Provides a fluent interface
     */
    public function setSpreadsheetKey($value)
    {
        $this->_spreadsheetKey = $value;
        return $this;
    }
    
    /**
     * @return string spreadsheet key
     */
    public function getSpreadsheetKey()
    {
        return $this->_spreadsheetKey;
    }
    
    /**
     * @param string $value
     * @return Zend_Gdata_Spreadsheets_CellQuery Provides a fluent interface
     */
    public function setWorksheetId($value)
    {
        $this->_worksheetId = $value;
        return $this;
    }
    
    /**
     * @return string worksheet id
     */
    public function getWorksheetId()
    {
        return $this->_worksheetId;
    }
    
    /**
     * @param string $value
     * @return Zend_Gdata_Spreadsheets_CellQuery Provides a fluent interface
     */
    public function setRowId($value)
    {
        $this->_rowId = $value;
        return $this;
    }
    
    /**
     * @return string row id
     */
    public function getRowId()
    {
        return $this->_rowId;
    }
    
    /**
     * @param string $value
     * @return Zend_Gdata_Spreadsheets_ListQuery Provides a fluent interface
     */
    public function setProjection($value)
    {
        $this->_projection = $value;
        return $this;
    }

    /**
     * @return string visibility
     * @return Zend_Gdata_Spreadsheets_ListQuery Provides a fluent interface
     */
    public function setVisibility($value)
    {
        $this->_visibility = $value;
        return $this;
    }
    
    /**
     * @return string projection
     */
    public function getProjection()
    {
        return $this->_projection;
    }
    
    /**
     * @return string visibility
     */
    public function getVisibility()
    {
        return $this->_visibility;
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
     * @return string url
     */
    public function getQueryUrl()
    {

        $uri = $this->_defaultFeedUri;
        
        if ($this->_spreadsheetKey != null) {
            $uri .= '/'.$this->_spreadsheetKey;
        } else {
            throw new Zend_Gdata_App_Exception('A spreadsheet key must be provided for list queries.');
        }
        
        if ($this->_worksheetId != null) {
            $uri .= '/'.$this->_worksheetId;
        } else {
            throw new Zend_Gdata_App_Exception('A worksheet id must be provided for list queries.');
        }
        
        if ($this->_visibility != null) {
            $uri .= '/'.$this->_visibility;
        } else {
            throw new Zend_Gdata_App_Exception('A visibility must be provided for list queries.');
        }
        
        if ($this->_projection != null) {
            $uri .= '/'.$this->_projection;
        } else {
            throw new Zend_Gdata_App_Exception('A projection must be provided for list queries.');
        }
        
        if ($this->_rowId != null) {
            $uri .= '/'.$this->_rowId;
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