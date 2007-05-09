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
 * @category     Zend
 * @package        Zend_Gdata
 * @copyright    Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license        http://framework.zend.com/license/new-bsd         New BSD License
 */

/**
 * Zend_Gdata
 */
require_once('Zend/Gdata.php');

/**
 * Zend_Gdata_Data
 */
require_once('Zend/Gdata/Data.php');

/**
 * Zend_Gdata_Spreadsheets_SpreadsheetFeed
 */
require_once('Zend/Gdata/Spreadsheets/SpreadsheetFeed.php');

/**
 * Zend_Gdata_Spreadsheets_WorksheetFeed
 */
require_once('Zend/Gdata/Spreadsheets/WorksheetFeed.php');

/**
 * Zend_Gdata_Spreadsheets_CellFeed
 */
require_once('Zend/Gdata/Spreadsheets/CellFeed.php');

/**
 * Zend_Gdata_Spreadsheets_ListFeed
 */
require_once('Zend/Gdata/Spreadsheets/ListFeed.php');

/**
 * Zend_Gdata_Spreadsheets_SpreadsheetEntry
 */
require_once('Zend/Gdata/Spreadsheets/SpreadsheetEntry.php');

/**
 * Zend_Gdata_Spreadsheets_WorksheetEntry
 */
require_once('Zend/Gdata/Spreadsheets/WorksheetEntry.php');

/**
 * Zend_Gdata_Spreadsheets_CellEntry
 */
require_once('Zend/Gdata/Spreadsheets/CellEntry.php');

/**
 * Zend_Gdata_Spreadsheets_ListEntry
 */
require_once('Zend/Gdata/Spreadsheets/ListEntry.php');

/**
 * Gdata Spreadsheets
 *
 * @link http://code.google.com/apis/gdata/spreadsheets.html
 *
 * @category     Zend
 * @package        Zend_Gdata
 * @copyright    Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license        http://framework.zend.com/license/new-bsd         New BSD License
 */
class Zend_Gdata_Spreadsheets extends Zend_Gdata
{
    const SPREADSHEETS_FEED_URI = 'http://spreadsheets.google.com/feeds/spreadsheets';
    const SPREADSHEETS_POST_URI = 'http://spreadsheets.google.com/feeds/spreadsheets/private/full';
    const AUTH_SERVICE_NAME = 'wise';
    
    const NAMESPACE_URI = 'http://schemas.google.com/spreadsheets/2006';
    const EXT_NAMESPACE_URI = 'http://schemas.google.com/spreadsheets/2006/extended';
    
    /**
     * Create Gdata_Spreadsheets object
     */
    public function __construct($client = null)
    {
        parent::__construct($client);
        $this->_httpClient->setParameterPost('service', self::AUTH_SERVICE_NAME);
        $this->registerPackage('Zend_Gdata_Spreadsheets');
        Zend_Gdata_Data::registerNamespace('gs', Zend_Gdata_Spreadsheets::NAMESPACE_URI);
        Zend_Gdata_Data::registerNamespace('gsx', Zend_Gdata_Spreadsheets::EXT_NAMESPACE_URI);
        $this->_server = 'spreadsheets.google.com';
    }
    
    public function getSpreadsheetFeed($query = null, $visibility = 'private', $projection = 'full')
    {
        $uri = 'http://'.$this->_server.'/feeds/spreadsheets/'.$visibility.'/'.$projection;
        if ($query) $uri .= $query->getQueryString();
        return parent::getFeed($uri, 'Zend_Gdata_Spreadsheets_SpreadsheetFeed');
    }
    
    public function getSpreadsheetEntry($key, $query = null, $visibility = 'private', $projection = 'full')
    {
        $uri = 'http://'.$this->_server.'/feeds/spreadsheets/'.$visibility.'/'.$projection.'/'.$key;
        if ($query) $uri .= $query->getQueryString();
        return parent::getEntry($uri, 'Zend_Gdata_Spreadsheets_SpreadsheetEntry');
    }
    
    public function getWorksheetFeed($key, $query = null, $visibility = 'private', $projection = 'full')
    {
        $uri = 'http://'.$this->_server.'/feeds/worksheets/'.$key.'/'.$visibility.'/'.$projection;
        if ($query) $uri .= $query->getQueryString();
        return parent::getFeed($uri, 'Zend_Gdata_Spreadsheets_WorksheetFeed');
    }
    
    public function GetWorksheetEntry($key, $wkshtId, $query = null, $visibility = 'private', $projection = 'full')
    {
        $uri = 'http://'.$this->_server.'/feeds/worksheets/'.$key.'/'.$visibility.'/'.$projection;
        if ($query) $uri .= $query->getQueryString();
        return parent::getEntry($uri, 'Zend_Gdata_Spreadsheets_WorksheetEntry');
    }
    
    public function getCellFeed($key, $wkshtId = 'default', $query = null, $visibility = 'private', $projection = 'full')
    {
        $uri = 'http://'.$this->_server.'/feeds/cells/'.$key.'/'.$wkshtId.'/'.$visibility.'/'.$projection;
        if ($query) $uri .= $query->getQueryString();
        return parent::getFeed($uri, 'Zend_Gdata_Spreadsheets_CellFeed');
    }
    
    public function getCellEntry($key, $cell, $wkshtId = 'default', $query = null, $visibility = 'private', $projection = 'full')
    {
        $uri = 'http://'.$this->_server.'/feeds/cells/'.$key.'/'.$wkshtId.'/'.$visibility.'/'.$projection.'/'.$cell;
        if ($query) $uri .= $query->getQueryString();
        return parent::getEntry($uri, 'Zend_Gdata_Spreadsheets_CellEntry');
    }
    
    public function getListFeed($key, $wkshtId = 'default', $query = null, $visibility = 'private', $projection = 'full')
    {
        $uri = 'http://'.$this->_server.'/feeds/list/'.$key.'/'.$wkshtId.'/'.$visibility.'/'.$projection;
        if ($query) $uri .= $query->getQueryString();
        
        return parent::getFeed($uri, 'Zend_Gdata_Spreadsheets_ListFeed');
    }
    
    public function getListEntry($key, $rowId, $wkshtId = 'default', $query = null, $visibility = 'private', $projection = 'full')
    {
        $uri = 'http://'.$this->_server.'/feeds/list/'.$key.'/'.$wkshtId.'/'.$visibility.'/'.$projection.'/'.$rowId;
        if ($query) $uri .= $query->getQueryString();
        return parent::getFeed($uri, 'Zend_Gdata_Spreadsheets_ListEntry');
    }
    
    public function updateCell($row, $col, $inputValue, $key, $wkshtId = 'default') 
    {
        $newCell = new Zend_Gdata_Spreadsheets_Extension_Cell($row, $col, $inputValue);
        $cell = 'R'.$row.'C'.$col;
        $entry = $this->getCellEntry($key, $cell, $wkshtId);
        $entry->cell->setInputValue($inputValue);
        $editLink = $entry->getLink('edit');
        $response = $this->put($entry, $editLink->href);
        return $response;
    }
    
    public function insertRow($rowData, $key, $wkshtId = 'default')
    {
        $newEntry = new Zend_Gdata_Spreadsheets_ListEntry();
        $newCustomArr = array();
        foreach ($rowData as $k => $v)
        {
            $newCustom = new Zend_Gdata_Spreadsheets_Extension_Custom();
            $newCustom->setText($v)->setColumnName($k);
            $newCustomArr[] = $newCustom;
        }
        $newEntry->setCustom($newCustomArr);
        $feed = $this->getListFeed($key, $wkshtId);
        $editLink = $feed->getLink('http://schemas.google.com/g/2005#post');
        $response = $this->post($newEntry, $editLink->href);
        return $response;
    }
    
    public function updateRow($entry, $newRowData)
    {
        $newCustomArr = array();
        foreach ($newRowData as $k => $v)
        {
            $newCustom = new Zend_Gdata_Spreadsheets_Extension_Custom();
            $newCustom->setText($v)->setColumnName($k);
            $newCustomArr[] = $newCustom;
        }
        $entry->setCustom($newCustomArr);
        $editLink = $entry->getLink('edit');
        return $this->put($entry, $editLink);
    }
    
    public function deleteRow($entry)
    {
        $entry->delete();
    }
}
    
    
    
    
