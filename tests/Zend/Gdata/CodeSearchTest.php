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

require_once 'Zend/Gdata/CodeSearch.php';
require_once 'Zend/Http/Client.php';
// require_once 'XML/Beautifier.php';

/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_CodeSearchTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->gdata = new Zend_Gdata_CodeSearch(new Zend_Http_Client());
        // $this->xml = new XML_Beautifier();
   }

    public function testMaxResultsParam()
    {
        $this->gdata->resetParameters();
        $query = 'malloc';
        $this->gdata->setQuery($query);
        $max = 3;
        $this->gdata->setMaxResults($max);
        $this->assertTrue(isset($this->gdata->maxResults));
        $this->assertEquals($max, $this->gdata->getMaxResults());
        $feed = $this->gdata->getCodeSearchFeed();
        $this->assertEquals($max, $feed->count());
        foreach ($feed as $feedEntry) {
            // echo $this->xml->formatString($feedEntry->saveXML());
            $gcs = 'gcs:package';
            $gcsPackage = $feedEntry->$gcs;
            $gcs = 'gcs:file';
            $gcsFile = $feedEntry->$gcs;
            $gcs = 'gcs:match';
            $gcsMatch = $feedEntry->$gcs;
            $this->assertTrue(isset($gcsPackage) && isset($gcsFile) && isset($gcsMatch));
        }
        unset($this->gdata->maxResults);
        $this->assertFalse(isset($this->gdata->maxResults));
    }

    public function testStartIndexParam()
    {
        $this->gdata->resetParameters();
        $query = 'malloc';
        $this->gdata->setQuery($query);
        $start = 3;
        $this->gdata->setStartIndex($start);
        $this->assertTrue(isset($this->gdata->startIndex));
        $this->assertEquals($start, $this->gdata->getStartIndex());
        $feed = $this->gdata->getCodeSearchFeed();
        foreach ($feed as $feedEntry) {
            // echo $this->xml->formatString($feedEntry->saveXML());
            $gcs = 'gcs:package';
            $gcsPackage = $feedEntry->$gcs;
            $gcs = 'gcs:file';
            $gcsFile = $feedEntry->$gcs;
            $gcs = 'gcs:match';
            $gcsMatch = $feedEntry->$gcs;
            $this->assertTrue(isset($gcsPackage) && isset($gcsFile) && isset($gcsMatch));
        }
        unset($this->gdata->startIndex);
        $this->assertFalse(isset($this->gdata->startIndex));
    }

    public function testExceptionUpdatedMinMaxParam()
    {
        $this->gdata->resetParameters();
        try {
            $feed = $this->gdata->updatedMin = 'string';
        } catch (Zend_Gdata_Exception $e) {
            $this->assertEquals("Parameter 'updatedMin' is not currently supported in CodeSearch.", $e->getMessage());
        }
        $this->gdata->resetParameters();
        try {
            $feed = $this->gdata->updatedMax = 'string';
        } catch (Zend_Gdata_Exception $e) {
            $this->assertEquals("Parameter 'updatedMax' is not currently supported in CodeSearch.", $e->getMessage());
        }
    }

}
