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

require_once 'Zend/Gdata/Spreadsheets.php';
require_once 'Zend/Http/Client.php';

/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_SpreadsheetsOnlineTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $user = constant('TESTS_ZEND_GDATA_CLIENTLOGIN_EMAIL');
        $pass = constant('TESTS_ZEND_GDATA_CLIENTLOGIN_PASSWORD');
        $this->sprKey = constant('TESTS_ZEND_GDATA_SPREADSHEETS_SPREADSHEETKEY');
        $this->wksId = constant('TESTS_ZEND_GDATA_SPREADSHEETS_WORKSHEETID');
        $service = Zend_Gdata_Spreadsheets::AUTH_SERVICE_NAME;
        $client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $service);
        $this->gdata = new Zend_Gdata_Spreadsheets($client);
    }

    public function testGetSpreadsheetFeed()
    {
        $feed = $this->gdata->getSpreadsheetFeed();
        $this->assertTrue($feed instanceof Zend_Gdata_Spreadsheets_SpreadsheetFeed);
        foreach ($feed->entries as $entry) {
            $this->assertTrue($entry instanceof Zend_Gdata_Spreadsheets_SpreadsheetEntry);
            $this->assertTrue($entry->getHttpClient() == $feed->getHttpClient());
        }
    }
    
    public function testGetWorksheetFeed()
    {
        $feed = $this->gdata->getWorksheetFeed($this->sprKey);
        $this->assertTrue($feed instanceof Zend_Gdata_Spreadsheets_WorksheetFeed);
        foreach ($feed->entries as $entry) {
            $this->assertTrue($entry instanceof Zend_Gdata_Spreadsheets_WorksheetEntry);
            $this->assertTrue($entry->getHttpClient() == $feed->getHttpClient());
        }
    }
    
    public function testGetCellFeed()
    {
        $feed = $this->gdata->getCellFeed($this->sprKey, $this->wksId);
        $this->assertTrue($feed instanceof Zend_Gdata_Spreadsheets_CellFeed);
        foreach ($feed->entries as $entry) {
            $this->assertTrue($entry instanceof Zend_Gdata_Spreadsheets_CellEntry);
            $this->assertTrue($entry->getHttpClient() == $feed->getHttpClient());
        }
    }
    
    public function testGetListFeed()
    {
        $feed = $this->gdata->getListFeed($this->sprKey, $this->wksId);
        $this->assertTrue($feed instanceof Zend_Gdata_Spreadsheets_ListFeed);
        foreach ($feed->entries as $entry) {
            $this->assertTrue($entry instanceof Zend_Gdata_Spreadsheets_ListEntry);
            $this->assertTrue($entry->getHttpClient() == $feed->getHttpClient());
        }
    }
    
    public function testGetSpreadsheetEntry()
    {
        $entry = $this->gdata->getSpreadsheetEntry($this->sprKey);
        $this->assertTrue($entry instanceof Zend_Gdata_Spreadsheets_SpreadsheetEntry);
    }
    
    public function testGetWorksheetEntry()
    {
        $entry = $this->gdata->getWorksheetEntry($this->sprKey, $this->wksId);
        $this->assertTrue($entry instanceof Zend_Gdata_Spreadsheets_WorksheetEntry);
    }
    
    public function testGetCellEntry()
    {
        $entry = $this->gdata->getCellEntry($this->sprKey, 'R1C1');
        $this->assertTrue($entry instanceof Zend_Gdata_Spreadsheets_CellEntry);
    }
    
    public function testGetListEntry()
    {
        $entry = $this->gdata->getListEntry($this->sprKey, '1');
        $this->assertTrue($entry instanceof Zend_Gdata_Spreadsheets_ListEntry);
    }
    
    public function testUpdateCell()
    {
        $this->gdata->updateCell(5, 1, 'updated data', $this->sprKey, $this->wksId);
        $this->gdata->updateCell(5, 1, null, $this->sprKey);
    }
    
    public function testInsertUpdateRow()
    {
        $rowData = array();
        $rowData['a1'] = 'new';
        $rowData['b1'] = 'row';
        $rowData['c1'] = 'data';
        $rowData['d1'] = 'here';
        $entry = $this->gdata->insertRow($rowData, $this->sprKey);
        $rowData['a1'] = 'newer';
        $entry = $this->gdata->updateRow($entry, $rowData);
        $entry->delete();
    }

}
