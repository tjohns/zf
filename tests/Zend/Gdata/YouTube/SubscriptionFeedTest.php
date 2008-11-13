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
 * @package      Zend_Gdata_YouTube
 * @subpackage   UnitTests
 * @copyright    Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com);
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Test helper
 */
require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

require_once 'Zend/Gdata/YouTube/SubscriptionFeed.php';
require_once 'Zend/Gdata/YouTube.php';

/**
 * @package Zend_Gdata_App
 * @subpackage UnitTests
 */
class Zend_Gdata_YouTube_SubscriptionFeedTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->feedText = file_get_contents(
                'Zend/Gdata/YouTube/_files/SubscriptionFeedDataSample1.xml',
                true);
        $this->feed = new Zend_Gdata_YouTube_SubscriptionFeed();
    }

    private function verifyAllSamplePropertiesAreCorrect ($subscriptionFeed) {
        $this->assertEquals('http://gdata.youtube.com/feeds/users/testuser/subscriptions',
            $subscriptionFeed->id->text);
        $this->assertEquals('2007-09-20T22:12:45.193Z', $subscriptionFeed->updated->text);
        $this->assertEquals('http://schemas.google.com/g/2005#kind', $subscriptionFeed->category[0]->scheme);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007#subscription', $subscriptionFeed->category[0]->term);
        $this->assertEquals('http://www.youtube.com/img/pic_youtubelogo_123x63.gif', $subscriptionFeed->logo->text);
        $this->assertEquals('text', $subscriptionFeed->title->type);
        $this->assertEquals('testuser\'s Subscriptions', $subscriptionFeed->title->text);;
        $this->assertEquals('self', $subscriptionFeed->getLink('self')->rel);
        $this->assertEquals('application/atom+xml', $subscriptionFeed->getLink('self')->type);
        $this->assertEquals('http://gdata.youtube.com/feeds/users/testuser/subscriptions?start-index=1&max-results=25', $subscriptionFeed->getLink('self')->href);
        $this->assertEquals('testuser', $subscriptionFeed->author[0]->name->text);
        $this->assertEquals('http://gdata.youtube.com/feeds/users/testuser', $subscriptionFeed->author[0]->uri->text);
        $this->assertEquals(3, $subscriptionFeed->totalResults->text);
    }

    public function testEmptyEntryShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->feed->extensionElements));
        $this->assertTrue(count($this->feed->extensionElements) == 0);
    }

    public function testEmptyEntryShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->feed->extensionAttributes));
        $this->assertTrue(count($this->feed->extensionAttributes) == 0);
    }

    public function testSampleEntryShouldHaveNoExtensionElements() {
        $this->feed->transferFromXML($this->feedText);
        $this->assertTrue(is_array($this->feed->extensionElements));
        $this->assertTrue(count($this->feed->extensionElements) == 0);
    }

    public function testSampleEntryShouldHaveNoExtensionAttributes() {
        $this->feed->transferFromXML($this->feedText);
        $this->assertTrue(is_array($this->feed->extensionAttributes));
        $this->assertTrue(count($this->feed->extensionAttributes) == 0);
    }

    public function testEmptySubscriptionFeedToAndFromStringShouldMatch() {
        $entryXml = $this->feed->saveXML();
        $newSubscriptionFeed = new Zend_Gdata_YouTube_SubscriptionFeed();
        $newSubscriptionFeed->transferFromXML($entryXml);
        $newSubscriptionFeedXml = $newSubscriptionFeed->saveXML();
        $this->assertTrue($entryXml == $newSubscriptionFeedXml);
    }

    public function testSamplePropertiesAreCorrect () {
        $this->feed->transferFromXML($this->feedText);
        $this->verifyAllSamplePropertiesAreCorrect($this->feed);
    }

    public function testConvertSubscriptionFeedToAndFromString() {
        $this->feed->transferFromXML($this->feedText);
        $entryXml = $this->feed->saveXML();
        $newSubscriptionFeed = new Zend_Gdata_YouTube_SubscriptionFeed();
        $newSubscriptionFeed->transferFromXML($entryXml);
        $this->verifyAllSamplePropertiesAreCorrect($newSubscriptionFeed);
        $newSubscriptionFeedXml = $newSubscriptionFeed->saveXML();
        $this->assertEquals($entryXml, $newSubscriptionFeedXml);
    }

}
