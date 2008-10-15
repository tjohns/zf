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

require_once 'Zend/Gdata/YouTube/PlaylistListEntry.php';
require_once 'Zend/Gdata/YouTube.php';

/**
 * @package Zend_Gdata_App
 * @subpackage UnitTests
 */
class Zend_Gdata_YouTube_PlaylistListEntryTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->entryText = file_get_contents(
                'Zend/Gdata/YouTube/_files/PlaylistListEntryDataSample1.xml',
                true);
        $this->entry = new Zend_Gdata_YouTube_PlaylistListEntry();
    }

    private function verifyAllSamplePropertiesAreCorrect ($playlistListEntry) {
        $this->assertEquals('http://gdata.youtube.com/feeds/users/testuser/playlists/46A2F8C9B36B6FE7',
            $playlistListEntry->id->text);
        $this->assertEquals('2007-09-20T13:42:19.000-07:00', $playlistListEntry->updated->text);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007/tags.cat', $playlistListEntry->category[0]->scheme);
        $this->assertEquals('music', $playlistListEntry->category[0]->term);
        $this->assertEquals('http://schemas.google.com/g/2005#kind', $playlistListEntry->category[1]->scheme);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007#playlistLink', $playlistListEntry->category[1]->term);
        $this->assertEquals('text', $playlistListEntry->title->type);
        $this->assertEquals('YouTube Musicians', $playlistListEntry->title->text);;
        $this->assertEquals('text', $playlistListEntry->content->type);
        $this->assertEquals('Music from talented people on YouTube.', $playlistListEntry->content->text);;
        $this->assertEquals('self', $playlistListEntry->getLink('self')->rel);
        $this->assertEquals('application/atom+xml', $playlistListEntry->getLink('self')->type);
        $this->assertEquals('http://gdata.youtube.com/feeds/users/testuser/playlists/46A2F8C9B36B6FE7', $playlistListEntry->getLink('self')->href);
        $this->assertEquals('testuser', $playlistListEntry->author[0]->name->text);
        $this->assertEquals('http://gdata.youtube.com/feeds/users/testuser', $playlistListEntry->author[0]->uri->text);
        $this->assertEquals('Music from talented people on YouTube.', $playlistListEntry->description->text);
        $this->assertEquals('http://gdata.youtube.com/feeds/playlists/46A2F8C9B36B6FE7', $playlistListEntry->getPlaylistVideoFeedUrl());
        $this->assertEquals('http://gdata.youtube.com/feeds/playlists/46A2F8C9B36B6FE7', $playlistListEntry->feedLink[0]->href);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007#playlist', $playlistListEntry->feedLink[0]->rel);
    }

    public function testEmptyEntryShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->entry->extensionElements));
        $this->assertTrue(count($this->entry->extensionElements) == 0);
    }

    public function testEmptyEntryShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->entry->extensionAttributes));
        $this->assertTrue(count($this->entry->extensionAttributes) == 0);
    }

    public function testSampleEntryShouldHaveNoExtensionElements() {
        $this->entry->transferFromXML($this->entryText);
        $this->assertTrue(is_array($this->entry->extensionElements));
        $this->assertTrue(count($this->entry->extensionElements) == 0);
    }

    public function testSampleEntryShouldHaveNoExtensionAttributes() {
        $this->entry->transferFromXML($this->entryText);
        $this->assertTrue(is_array($this->entry->extensionAttributes));
        $this->assertTrue(count($this->entry->extensionAttributes) == 0);
    }

    public function testEmptyPlaylistListEntryToAndFromStringShouldMatch() {
        $entryXml = $this->entry->saveXML();
        $newPlaylistListEntry = new Zend_Gdata_YouTube_PlaylistListEntry();
        $newPlaylistListEntry->transferFromXML($entryXml);
        $newPlaylistListEntryXml = $newPlaylistListEntry->saveXML();
        $this->assertTrue($entryXml == $newPlaylistListEntryXml);
    }

    public function testGetFeedLinkReturnsAllStoredEntriesWhenUsedWithNoParameters() {
        // Prepare test data
        $entry1 = new Zend_Gdata_Extension_FeedLink();
        $entry1->rel = "first";
        $entry1->href= "foo";
        $entry2 = new Zend_Gdata_Extension_FeedLink();
        $entry2->rel = "second";
        $entry2->href= "bar";
        $data = array($entry1, $entry2);

        // Load test data and run test
        $this->entry->feedLink = $data;
        $this->assertEquals(2, count($this->entry->feedLink));
    }

    public function testGetFeedLinkCanReturnEntriesByRelValue() {
        // Prepare test data
        $entry1 = new Zend_Gdata_Extension_FeedLink();
        $entry1->rel = "first";
        $entry1->href= "foo";
        $entry2 = new Zend_Gdata_Extension_FeedLink();
        $entry2->rel = "second";
        $entry2->href= "bar";
        $data = array($entry1, $entry2);

        // Load test data and run test
        $this->entry->feedLink = $data;
        $this->assertEquals($entry1, $this->entry->getFeedLink('first'));
        $this->assertEquals($entry2, $this->entry->getFeedLink('second'));
    }

    public function testSamplePropertiesAreCorrect () {
        $this->entry->transferFromXML($this->entryText);
        $this->verifyAllSamplePropertiesAreCorrect($this->entry);
    }

    public function testConvertPlaylistListEntryToAndFromString() {
        $this->entry->transferFromXML($this->entryText);
        $entryXml = $this->entry->saveXML();
        $newPlaylistListEntry = new Zend_Gdata_YouTube_PlaylistListEntry();
        $newPlaylistListEntry->transferFromXML($entryXml);
        $this->verifyAllSamplePropertiesAreCorrect($newPlaylistListEntry);
        $newPlaylistListEntryXml = $newPlaylistListEntry->saveXML();
        $this->assertEquals($entryXml, $newPlaylistListEntryXml);
    }

}
