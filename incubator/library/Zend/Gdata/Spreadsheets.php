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

require_once('Zend/Gdata/Spreadsheets/DocumentQuery.php');
require_once('Zend/Gdata/Spreadsheets/ListQuery.php');
require_once('Zend/Gdata/Spreadsheets/CellQuery.php');

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
    
    public static $namespaces = array(
        'gs' => 'http://schemas.google.com/spreadsheets/2006',
        'gsx' => 'http://schemas.google.com/spreadsheets/2006/extended');
    
    /**
     * Create Gdata_Spreadsheets object
     */
    public function __construct($client = null)
    {
        parent::__construct($client);
        $this->_httpClient->setParameterPost('service', self::AUTH_SERVICE_NAME);
        $this->registerPackage('Zend_Gdata_Spreadsheets');
        $this->_server = 'spreadsheets.google.com';
    }
    
    /**
     * Gets a spreadsheet feed.
     * @param string $location A DocumentQuery or a URI specifying the feed location.
     * @return SpreadsheetFeed
     */
    public function getSpreadsheetFeed($location = null)
    {
        //$uri = 'http://'.$this->_server.'/feeds/spreadsheets/'.$visibility.'/'.$projection;
        //if ($query) $uri .= $query->getQueryString();
        
        if ($location == null)
        {
            $uri = self::SPREADSHEETS_FEED_URI;
        } 
        else if ($location instanceof Zend_Gdata_Spreadsheets_DocumentQuery)
        {
            if ($location->getDocumentType() == null)
            {
                $location->setDocumentType('spreadsheets');
            }
            $uri = $location->getQueryUrl();
        }
        else
        {
            $uri = $location;
        }    
            
        return parent::getFeed($uri, 'Zend_Gdata_Spreadsheets_SpreadsheetFeed');
    }
    
    /**
     * Gets a spreadsheet entry.
     * @param string $location A DocumentQuery or a URI specifying the entry location.
     * @return SpreadsheetEntry
     */
    public function getSpreadsheetEntry($location)
    {
        //$uri = 'http://'.$this->_server.'/feeds/spreadsheets/'.$visibility.'/'.$projection.'/'.$key;
        //if ($query) $uri .= $query->getQueryString();
        
        if ($location instanceof Zend_Gdata_Spreadsheets_DocumentQuery)
        {
            if ($location->getDocumentType() == null)
            {
                $location->setDocumentType('spreadsheets');
            }
            $uri = $location->getQueryUrl();
        }
        else
        {
            $uri = $location;
        }  

        return parent::getEntry($uri, 'Zend_Gdata_Spreadsheets_SpreadsheetEntry');
    }
    
    /**
     * Gets a worksheet feed.
     * @param string $location A DocumentQuery or a URI specifying the feed location.
     * @return WorksheetFeed
     */
    public function getWorksheetFeed($location)
    {
        //$uri = 'http://'.$this->_server.'/feeds/worksheets/'.$key.'/'.$visibility.'/'.$projection;
        //if ($query) $uri .= $query->getQueryString();
        
        if ($location instanceof Zend_Gdata_Spreadsheets_DocumentQuery)
        {
            if ($location->getDocumentType() == null)
            {
                $location->setDocumentType('worksheets');
            }
            $uri = $location->getQueryUrl();
        }
        else
        {
            $uri = $location;
        }  
        
        return parent::getFeed($uri, 'Zend_Gdata_Spreadsheets_WorksheetFeed');
    }
    
    /**
     * Gets a worksheet entry.
     * @param string $location A DocumentQuery or a URI specifying the entry location.
     * @return WorksheetEntry
     */
    public function GetWorksheetEntry($location)
    {
        //$uri = 'http://'.$this->_server.'/feeds/worksheets/'.$key.'/'.$visibility.'/'.$projection;
        //if ($query) $uri .= $query->getQueryString();
        
        if ($location instanceof Zend_Gdata_Spreadsheets_DocumentQuery)
        {
            if ($location->getDocumentType() == null)
            {
                $location->setDocumentType('worksheets');
            }
            $uri = $location->getQueryUrl();
        }
        else
        {
            $uri = $location;
        }  
        
        return parent::getEntry($uri, 'Zend_Gdata_Spreadsheets_WorksheetEntry');
    }
    
    /**
     * Gets a cell feed.
     * @param string $location A CellQuery or a URI specifying the feed location.
     * @return CellFeed
     */
    public function getCellFeed($location)
    {
        //$uri = 'http://'.$this->_server.'/feeds/cells/'.$key.'/'.$wkshtId.'/'.$visibility.'/'.$projection;
        //if ($query) $uri .= $query->getQueryString();
        
        if ($location instanceof Zend_Gdata_Spreadsheets_CellQuery)
            $uri = $location->getQueryUrl();
        else
            $uri = $location;
        return parent::getFeed($uri, 'Zend_Gdata_Spreadsheets_CellFeed');
    }
    
    /**
     * Gets a cell entry.
     * @param string $location A CellQuery or a URI specifying the entry location.
     * @return CellEntry
     */
    public function getCellEntry($location)
    {
        //$uri = 'http://'.$this->_server.'/feeds/cells/'.$key.'/'.$wkshtId.'/'.$visibility.'/'.$projection.'/'.$cell;
        //if ($query) $uri .= $query->getQueryString();
        
        if ($location instanceof Zend_Gdata_Spreadsheets_CellQuery)
            $uri = $location->getQueryUrl();
        else
            $uri = $location;
            
        return parent::getEntry($uri, 'Zend_Gdata_Spreadsheets_CellEntry');
    }
    
    /**
     * Gets a list feed.
     * @param string $location A ListQuery or a URI specifying the feed location.
     * @return ListFeed
     */
    public function getListFeed($location)
    {
        if ($location instanceof Zend_Gdata_Spreadsheets_ListQuery)
            $uri = $location->getQueryUrl();
        else
            $uri = $location;
        
        return parent::getFeed($uri, 'Zend_Gdata_Spreadsheets_ListFeed');
    }
    
    /**
     * Gets a list entry.
     * @param string $location A ListQuery or a URI specifying the entry location.
     * @return ListEntry
     */
    public function getListEntry($location)
    {
        if ($location instanceof Zend_Gdata_Spreadsheets_ListQuery)
            $uri = $location->getQueryUrl();
        else
            $uri = $location;
        
        return parent::getEntry($uri, 'Zend_Gdata_Spreadsheets_ListEntry');
    }
    
    /**
     * Updates an existing cell.
     * @param int $row The row containing the cell to update
     * @param int $col The column containing the cell to update
     * @param int $inputValue The new value for the cell
     * @param string $key The key for the spreadsheet to be updated
     * @param string $wkshtId (optional) The worksheet to be updated
     * @return CellEntry The updated cell entry.
     */
    public function updateCell($row, $col, $inputValue, $key, $wkshtId = 'default') 
    {
        $cell = 'R'.$row.'C'.$col;
        
        $query = new Zend_Gdata_Spreadsheets_CellQuery();
        $query->setSpreadsheetKey($key);
        $query->setWorksheetId($wkshtId);
        $query->setCellId($cell);
        
        $entry = $this->getCellEntry($query);
        $entry->setCell(new Zend_Gdata_Spreadsheets_Extension_Cell(null, $row, $col, $inputValue));
        $response = $entry->save();
        return $response;
    }
    
    /**
     * Inserts a new row with provided data.
     * @param array $rowData An array of column header to row data
     * @param string $key The key of the spreadsheet to modify
     * @param string $wkshtId (optional) The worksheet to modify
     * @return ListEntry The inserted row
     */
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
        
        $query = new Zend_Gdata_Spreadsheets_ListQuery();
        $query->setSpreadsheetKey($key);
        $query->setWorksheetId($wkshtId);
        
        $feed = $this->getListFeed($query);
        $editLink = $feed->getLink('http://schemas.google.com/g/2005#post');
        
        /*$response = $this->post($newEntry, $editLink->href);
        
        $returnEntry = new Zend_Gdata_Spreadsheets_ListEntry(null, $response->getBody());
        $returnEntry->setHttpClient($feed->getHttpClient());
        return $returnEntry;*/
        
        return $this->insertEntry($editLink->href, $newEntry->saveXML(), 'Zend_Gdata_Spreadsheets_ListEntry');
    }
    
    /**
     * Updates an existing row with provided data.
     * @param ListEntry $entry The row to update
     * @param array $newRowData An array of column header to row data
     */
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
        
        return $entry->save();
    }
    
    /**
     * Deletes an existing row .
     * @param ListEntry $entry The row to delete
     */
    public function deleteRow($entry)
    {
        $entry->delete();
    }
}
    
    
    
    
