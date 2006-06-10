<?php
/**
 * @package    Zend_Feed
 * @subpackage UnitTests
 */


/**
 * Zend_Feed
 */
require_once 'Zend/Feed.php';


/**
 * @package Zend_Feed
 * @subpackage UnitTests
 */
class Zend_Feed_ArrayAccessTest extends PHPUnit2_Framework_TestCase {

    private $feed;
    private $nsfeed;

    public function setUp()
    {
        $this->feed = Zend_Feed::importFile(dirname(__FILE__) . '/_files/TestAtomFeed.xml');
        $this->nsfeed = Zend_Feed::importFile(dirname(__FILE__) . '/_files/TestAtomFeedNamespaced.xml');
    }

    public function testExists()
    {
        $this->assertFalse(isset($this->feed[-1]), 'Negative array access should fail');
        $this->assertTrue(isset($this->feed['version']), 'Feed version should be set');

        $this->assertFalse(isset($this->nsfeed[-1]), 'Negative array access should fail');
        $this->assertTrue(isset($this->nsfeed['version']), 'Feed version should be set');
    }

    public function testGet()
    {
        $this->assertEquals($this->feed['version'], '1.0', 'Feed version should be 1.0');
        $this->assertEquals($this->nsfeed['version'], '1.0', 'Feed version should be 1.0');
    }

    public function testSet()
    {
        $this->feed['category'] = 'tests';
        $this->assertTrue(isset($this->feed['category']), 'Feed category should be set');
        $this->assertEquals($this->feed['category'], 'tests', 'Feed category should be tests');

        $this->nsfeed['atom:category'] = 'tests';
        $this->assertTrue(isset($this->nsfeed['atom:category']), 'Feed category should be set');
        $this->assertEquals($this->nsfeed['atom:category'], 'tests', 'Feed category should be tests');

        // Changing an existing index.
        $oldEntry = $this->feed['version'];
        $this->feed['version'] = '1.1';
        $this->assertTrue($oldEntry != $this->feed['version'], 'Version should have changed');
    }

    public function testUnset()
    {
        $feed = Zend_Feed::importFile(dirname(__FILE__) . '/_files/TestAtomFeed.xml');
        unset($feed['version']);
        $this->assertFalse(isset($feed['version']), 'Version should be unset');
        $this->assertEquals('', $feed['version'], 'Version should be equal to the empty string');

        $nsfeed = Zend_Feed::importFile(dirname(__FILE__) . '/_files/TestAtomFeedNamespaced.xml');
        unset($nsfeed['version']);
        $this->assertFalse(isset($nsfeed['version']), 'Version should be unset');
        $this->assertEquals('', $nsfeed['version'], 'Version should be equal to the empty string');
    }

}
