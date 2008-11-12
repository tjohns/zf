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

require_once 'Zend/Gdata/YouTube/PlaylistListFeed.php';
require_once 'Zend/Gdata/YouTube.php';

/**
 * @package Zend_Gdata_App
 * @subpackage UnitTests
 */
class Zend_Gdata_YouTube_PlaylistListFeedTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->feedText = file_get_contents(
                'Zend/Gdata/YouTube/_files/PlaylistListFeedDataSample1.xml',
                true);
        $this->feed = new Zend_Gdata_YouTube_PlaylistListFeed();
    }

    private function verifyAllSamplePropertiesAreCorrect ($playlistListEntry) {
        $this->assertEquals('http://gdata.youtube.com/feeds/users/testuser/playlists',
            $playlistListEntry->id->text);
        $this->assertEquals('2007-09-20T20:59:47.530Z', $playlistListEntry->updated->text);
        $this->assertEquals('http://schemas.google.com/g/2005#kind', $playlistListEntry->category[0]->scheme);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007#playlistLink', $playlistListEntry->category[0]->term);
        $this->assertEquals('http://www.youtube.com/img/pic_youtubelogo_123x63.gif', $playlistListEntry->logo->text);
        $this->assertEquals('text', $playlistListEntry->title->type);
        $this->assertEquals('testuser\'s Playlists', $playlistListEntry->title->text);;
        $this->assertEquals('self', $playlistListEntry->getLink('self')->rel);
        $this->assertEquals('application/atom+xml', $playlistListEntry->getLink('self')->type);
        $this->assertEquals('http://gdata.youtube.com/feeds/users/testuser/playlists?start-index=1&max-results=25', $playlistListEntry->getLink('self')->href);
        $this->assertEquals('testuser', $playlistListEntry->author[0]->name->text);
        $this->assertEquals('http://gdata.youtube.com/feeds/users/testuser', $playlistListEntry->author[0]->uri->text);
        $this->assertEquals(2, $playlistListEntry->totalResults->text);
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

    public function testEmptyPlaylistListFeedToAndFromStringShouldMatch() {
        $entryXml = $this->feed->saveXML();
        $newPlaylistListFeed = new Zend_Gdata_YouTube_PlaylistListFeed();
        $newPlaylistListFeed->transferFromXML($entryXml);
        $newPlaylistListFeedXml = $newPlaylistListFeed->saveXML();
        $this->assertTrue($entryXml == $newPlaylistListFeedXml);
    }

    public function testSamplePropertiesAreCorrect () {
        $this->feed->transferFromXML($this->feedText);
        $this->verifyAllSamplePropertiesAreCorrect($this->feed);
    }

    public function testConvertPlaylistListFeedToAndFromString() {
        $this->feed->transferFromXML($this->feedText);
        $entryXml = $this->feed->saveXML();
        $newPlaylistListFeed = new Zend_Gdata_YouTube_PlaylistListFeed();
        $newPlaylistListFeed->transferFromXML($entryXml);
        $this->verifyAllSamplePropertiesAreCorrect($newPlaylistListFeed);
        $newPlaylistListFeedXml = $newPlaylistListFeed->saveXML();
        $this->assertEquals($entryXml, $newPlaylistListFeedXml);
    }

}
