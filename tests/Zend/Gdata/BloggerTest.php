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

require_once 'Zend/Gdata/Blogger.php';
require_once 'Zend/Http/Client.php';
// require_once 'XML/Beautifier.php';

/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_BloggerTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->gdata = new Zend_Gdata_Blogger(new Zend_Http_Client());
        // $this->xml = new XML_Beautifier();
    }

    public function testBlogFeed()
    {
        $this->gdata->resetParameters();
        $blog = 'karwin';
        $this->gdata->setBlogName($blog);
        $this->assertTrue(isset($this->gdata->blogName));
        $this->assertEquals($blog, $this->gdata->getBlogName());
        $feed = $this->gdata->getBloggerFeed();
        foreach ($feed as $feedEntry) {
            // echo $this->xml->formatString($feedEntry->saveXML());
            $author = $feedEntry->author;
            $this->assertTrue(isset($author));
        }
        unset($this->gdata->blogName);
        $this->assertFalse(isset($this->gdata->blogName));
    }

    public function testBlogNameArg()
    {
        $this->gdata->resetParameters();
        $blog = 'karwin';
        $feed = $this->gdata->getBloggerFeed($blog);
        $this->assertTrue(isset($this->gdata->blogName));
        $this->assertEquals($blog, $this->gdata->getBlogName());
        foreach ($feed as $feedEntry) {
            // echo $this->xml->formatString($feedEntry->saveXML());
            $author = $feedEntry->author;
            $this->assertTrue(isset($author));
        }
        unset($this->gdata->blogName);
        $this->assertFalse(isset($this->gdata->blogName));
    }

    public function testMaxResultsParam()
    {
        $this->gdata->resetParameters();
        $blog = 'karwin';
        $this->gdata->setBlogName($blog);
        $max = 3;
        $this->gdata->setMaxResults($max);
        $this->assertTrue(isset($this->gdata->maxResults));
        $this->assertEquals($max, $this->gdata->getMaxResults());
        $feed = $this->gdata->getBloggerFeed();
        $this->assertEquals($max, $feed->count());
        foreach ($feed as $feedEntry) {
            // echo $this->xml->formatString($feedEntry->saveXML());
            $author = $feedEntry->author;
            $this->assertTrue(isset($author));
        }
        unset($this->gdata->maxResults);
        $this->assertFalse(isset($this->gdata->maxResults));
    }

    public function testStartIndexParam()
    {
        $this->gdata->resetParameters();
        $blog = 'karwin';
        $this->gdata->setBlogName($blog);
        $start = 3;
        $this->gdata->setStartIndex($start);
        $this->assertTrue(isset($this->gdata->startIndex));
        $this->assertEquals($start, $this->gdata->getStartIndex());
        $feed = $this->gdata->getBloggerFeed();
        foreach ($feed as $feedEntry) {
            // echo $this->xml->formatString($feedEntry->saveXML());
            $author = $feedEntry->author;
            $this->assertTrue(isset($author));
        }
        unset($this->gdata->startIndex);
        $this->assertFalse(isset($this->gdata->startIndex));
    }

    public function testPublishedMinMaxParam()
    {
        $this->gdata->resetParameters();
        $blog = 'karwin';
        $this->gdata->setBlogName($blog);
        $min = '2006-10-01';
        $this->gdata->setPublishedMin($min);
        $this->assertTrue(isset($this->gdata->publishedMin));
        $this->assertEquals($this->gdata->formatTimestamp($min), $this->gdata->getPublishedMin());
        $max = '2006-10-15';
        $this->gdata->setPublishedMax($max);
        $this->assertTrue(isset($this->gdata->publishedMax));
        $this->assertEquals($this->gdata->formatTimestamp($max), $this->gdata->getPublishedMax());
        $feed = $this->gdata->getBloggerFeed();
        $this->assertEquals(1, $feed->count());
        foreach ($feed as $feedEntry) {
            // echo $this->xml->formatString($feedEntry->saveXML());
            $author = $feedEntry->author;
            $this->assertTrue(isset($author));
            $pub = $feedEntry->published();
            $this->assertThat($pub, $this->greaterThan($min));
            $this->assertThat($pub, $this->lessThan($max));
        }
        unset($this->gdata->publishedMin);
        $this->assertFalse(isset($this->gdata->publishedMin));
        unset($this->gdata->publishedMax);
        $this->assertFalse(isset($this->gdata->publishedMax));
    }

    public function testExceptionNoBlogName()
    {
        $this->gdata->resetParameters();
        try {
            $feed = $this->gdata->getBloggerFeed();
        } catch (Zend_Gdata_Exception $e) {
            $this->assertEquals('You must specify a blog name.', $e->getMessage());
        }
    }

    public function testExceptionQueryParam()
    {
        $this->gdata->resetParameters();
        try {
            $feed = $this->gdata->setQuery('string');
        } catch (Zend_Gdata_Exception $e) {
            $this->assertEquals('Text queries are not currently supported in Blogger.', $e->getMessage());
        }
    }

    public function testExceptionCategoryParam()
    {
        $this->gdata->resetParameters();
        try {
            $feed = $this->gdata->category = 'string';
        } catch (Zend_Gdata_Exception $e) {
            $this->assertEquals('Category queries are not currently supported in Blogger.', $e->getMessage());
        }
    }

    public function testExceptionEntryParam()
    {
        $this->gdata->resetParameters();
        try {
            $feed = $this->gdata->entry = 'string';
        } catch (Zend_Gdata_Exception $e) {
            $this->assertEquals('Entry queries are not currently supported in Blogger.', $e->getMessage());
        }
    }

}
