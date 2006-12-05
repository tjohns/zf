<?php
/**
 * @package    Zend_Gdata
 * @subpackage UnitTests
 */


/**
 * Zend_Gdata
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

    public function testQuery()
    {
        echo "\n----\n";
        $this->gdata->query = 'digital camera';
        $feed = $this->gdata->getFeed();
        foreach ($feed as $feedEntry) {
            $linkList = $feedEntry->link();
            $href = $linkList[0]->getAttribute('href');
            echo "Feed: " . substr($feedEntry->title(), 0, 60) . "\n  <" . substr($href, 0, 60) . ">\n";
        }
    }

    public function testMaxResults()
    {
        echo "\n----\n";
        $this->gdata->query = 'digital camera';
        $this->gdata->maxResults = 3;
        $feed = $this->gdata->getFeed();
        foreach ($feed as $feedEntry) {
            $linkList = $feedEntry->link();
            $href = $linkList[0]->getAttribute('href');
            echo "Feed: " . substr($feedEntry->title(), 0, 60) . "\n  <" . substr($href, 0, 60) . ">\n";
        }
    }

    public function testCategory()
    {
        echo "\n----\n";
        $this->gdata->query = 'nikon';
        $this->gdata->category = 'camera';
        $this->gdata->maxResults = 3;
        $feed = $this->gdata->getFeed();
        foreach ($feed as $feedEntry) {
            $linkList = $feedEntry->link();
            $href = $linkList[0]->getAttribute('href');
            echo "Feed: " . substr($feedEntry->title(), 0, 60) . "\n  <" . substr($href, 0, 60) . ">\n";
        }
    }

}
