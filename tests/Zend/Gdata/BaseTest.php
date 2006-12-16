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

require_once 'Zend/Gdata/Base.php';
require_once 'Zend/Http/Client.php';

/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_BaseTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->gdata = new Zend_Gdata_Base(new Zend_Http_Client());
    }

    public function testDeveloperKey()
    {
        $key = "foo";
        $this->gdata->setDeveloperKey($key);
        $this->assertEquals($key, $this->gdata->getDeveloperKey());

        $key = "split-header\nattack";
        $this->gdata->setDeveloperKey($key);
        $this->assertEquals("split-header", $this->gdata->getDeveloperKey());
    }

    public function testQueryParam()
    {
        $this->gdata->resetParameters();
        $query = 'digital camera';
        $this->gdata->setQuery($query);
        $this->assertTrue(isset($this->gdata->query));
        $this->assertEquals($query, $this->gdata->getQuery());
        $feed = $this->gdata->getBaseFeed();
        foreach ($feed as $feedEntry) {
            $linkList = $feedEntry->link();
            $href = $linkList[0]->getAttribute('href');
            $this->assertRegExp('|http://.*|', $href);
        }
        unset($this->gdata->query);
        $this->assertFalse(isset($this->gdata->query));
    }

    public function testAltRssParam()
    {
        $this->gdata->resetParameters();
        $query = 'digital camera';
        $alt = 'rss';
        $this->gdata->setQuery($query);
        $this->gdata->setAlt($alt);
        $this->assertTrue(isset($this->gdata->alt));
        $this->assertEquals($alt, $this->gdata->getAlt());
        $channel = $this->gdata->getBaseFeed();
        foreach ($channel as $item) {
            $link = $item->link();
            $this->assertRegExp('|http://.*|', $link);
        }
        unset($this->gdata->alt);
        $this->assertFalse(isset($this->gdata->alt));
    }

    public function testCategoryParam()
    {
        $this->gdata->resetParameters();
        $query = 'nikon';
        $category = 'camera';
        $this->gdata->setQuery($query);
        $this->gdata->setCategory($category);
        $this->assertTrue(isset($this->gdata->category));
        $this->assertEquals($category, $this->gdata->getCategory());
        $feed = $this->gdata->getBaseFeed();
        foreach ($feed as $feedEntry) {
            $linkList = $feedEntry->link();
            $href = $linkList[0]->getAttribute('href');
            $this->assertRegExp('|http://.*|', $href);
        }
        unset($this->gdata->category);
        $this->assertFalse(isset($this->gdata->category));
    }

    public function testMaxResultsParam()
    {
        $this->gdata->resetParameters();
        $query = 'digital camera';
        $max = 3;
        $this->gdata->setQuery($query);
        $this->gdata->setMaxResults($max);
        $this->assertTrue(isset($this->gdata->maxResults));
        $this->assertEquals($max, $this->gdata->getMaxResults());
        $feed = $this->gdata->getBaseFeed();
        $this->assertEquals($max, $feed->count());
        foreach ($feed as $feedEntry) {
            $linkList = $feedEntry->link();
            $href = $linkList[0]->getAttribute('href');
            $this->assertRegExp('|http://.*|', $href);
        }
        unset($this->gdata->maxResults);
        $this->assertFalse(isset($this->gdata->maxResults));
    }

    public function testStartIndexParam()
    {
        $this->gdata->resetParameters();
        $query = 'digital camera';
        $start = 3;
        $this->gdata->setQuery($query);
        $this->gdata->setStartIndex($start);
        $this->assertTrue(isset($this->gdata->startIndex));
        $this->assertEquals($start, $this->gdata->getStartIndex());
        $feed = $this->gdata->getBaseFeed();
        foreach ($feed as $feedEntry) {
            $linkList = $feedEntry->link();
            $href = $linkList[0]->getAttribute('href');
            $this->assertRegExp('|http://.*|', $href);
        }
        unset($this->gdata->startIndex);
        $this->assertFalse(isset($this->gdata->startIndex));
    }

    public function testMetadataItemTypes()
    {
        $locale = 'en_US';
        $itemType = 'jobs';

        // get all item types
        $this->gdata->resetParameters();
        $feed = $this->gdata->getItemTypeFeed($locale);
        $titleArray = array();
        foreach ($feed as $feedEntry) {
            $titleArray[] = $feedEntry->title();
        }
        $this->assertContains($itemType, $titleArray);

        // get info for specific item type
        $this->gdata->resetParameters();
        $feed = $this->gdata->getItemTypeFeed($locale, $itemType);
        foreach ($feed as $feedEntry) {
            $gm = 'gm:item_type';
            $this->assertEquals($itemType, $feedEntry->$gm());
        }
    }

    public function testMetadataStatistics()
    {
        $itemType = 'jobs';
        // get statistics for specific item type
        $this->gdata->resetParameters();
        $feed = $this->gdata->getItemTypeAttributesFeed($itemType);
        foreach ($feed as $feedEntry) {
            $gm = 'gm:attribute';
            $elt = $feedEntry->$gm;
            $count = $elt->offsetGet('count');
            $this->assertTrue(intval($count) >= 0);
        }
    }

    public function testAddAttributeQuery()
    {
        $this->gdata->resetParameters();
        $query = 'digital camera';
        $attrib = 'price';
        $op = '<';
        $attribValue = '50 USD';
        $this->gdata->setQuery('digital camera');
        $this->gdata->addAttributeQuery($attrib, $attribValue, $op);
        $this->gdata->maxResults = 25;
        $feed = $this->gdata->getBaseFeed();
        foreach ($feed as $feedEntry) {
            $g = 'g:price';
            $this->assertThat(intval($feedEntry->$g()), $this->lessThan(intval($attribValue)));
        }

        $this->gdata->unsetAttributeQuery($attrib);
        $op = '>';
        $this->gdata->addAttributeQuery($attrib, $attribValue, $op);
        $feed = $this->gdata->getBaseFeed();
        $prices = array();
        foreach ($feed as $feedEntry) {
            $g = 'g:price';
            $price = intval($feedEntry->$g());
            if ($price >= intval($attribValue)) {
                $prices[] = $price;
            }
        }
        $this->assertThat(count($prices), $this->greaterThan(0));

        $this->gdata->unsetAttributeQuery();
    }

    public function testExceptionInvalidAttributeQueryOperator()
    {
        $this->gdata->resetParameters();
        $attrib = 'price';
        $op = '?';
        $attribValue = '50 USD';
        try {
            $this->gdata->addAttributeQuery($attrib, $attribValue, $op);
        } catch (Zend_Gdata_Exception $e) {
            $this->assertEquals("Unsupported attribute query comparison operator '?'.", $e->getMessage());
        }
    }

}
