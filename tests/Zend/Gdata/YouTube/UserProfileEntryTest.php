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

require_once 'Zend/Gdata/YouTube/UserProfileEntry.php';
require_once 'Zend/Gdata/YouTube.php';

/**
 * @package Zend_Gdata_App
 * @subpackage UnitTests
 */
class Zend_Gdata_YouTube_UserProfileEntryTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->entryText = file_get_contents(
                'Zend/Gdata/YouTube/_files/UserProfileEntryDataSample1.xml',
                true);
        $this->entry = new Zend_Gdata_YouTube_UserProfileEntry();
    }

    private function verifyAllSamplePropertiesAreCorrect ($userProfileEntry) {
        $this->assertEquals('http://gdata.youtube.com/feeds/users/darcy',
            $userProfileEntry->id->text);
        $this->assertEquals('2007-08-13T12:37:03.000-07:00', $userProfileEntry->updated->text);
        $this->assertEquals('http://schemas.google.com/g/2005#kind', $userProfileEntry->category[0]->scheme);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007#userProfile', $userProfileEntry->category[0]->term);
        $this->assertEquals('text', $userProfileEntry->title->type);
        $this->assertEquals('Darcy', $userProfileEntry->title->text);;
        $this->assertEquals('self', $userProfileEntry->getLink('self')->rel);
        $this->assertEquals('application/atom+xml', $userProfileEntry->getLink('self')->type);
        $this->assertEquals('http://gdata.youtube.com/feeds/users/darcy', $userProfileEntry->getLink('self')->href);
        $this->assertEquals('Fitzwilliam Darcy', $userProfileEntry->author[0]->name->text);
        $this->assertEquals(32, $userProfileEntry->age->text);
        $this->assertEquals('darcy', $userProfileEntry->username->text);
        $this->assertEquals('A person of great interest', $userProfileEntry->description->text);
        $this->assertEquals('Pride and Prejudice', $userProfileEntry->books->text);
        $this->assertEquals('Self employed', $userProfileEntry->company->text);
        $this->assertEquals('Reading, arguing with Liz', $userProfileEntry->hobbies->text);
        $this->assertEquals('Steventon', $userProfileEntry->hometown->text);
        $this->assertEquals('Longbourn in Hertfordshire, Pemberley in Derbyshire', $userProfileEntry->location->text);
        $this->assertEquals('Pride and Prejudice, 2005', $userProfileEntry->movies->text);
        $this->assertEquals('Air Con Varizzioni, The Pleasure of the Town', $userProfileEntry->music->text);
        $this->assertEquals('Gentleman', $userProfileEntry->occupation->text);
        $this->assertEquals('Home schooling', $userProfileEntry->school->text);
        $this->assertEquals('m', $userProfileEntry->gender->text);
        $this->assertEquals('taken', $userProfileEntry->relationship->text);
        $this->assertEquals('http://gdata.youtube.com/feeds/users/darcy', $userProfileEntry->author[0]->uri->text);
        $this->assertEquals('http://gdata.youtube.com/feeds/users/darcy/favorites', $userProfileEntry->feedLink[0]->href);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007#user.favorites', $userProfileEntry->feedLink[0]->rel);
        $this->assertEquals('http://gdata.youtube.com/feeds/users/darcy/contacts', $userProfileEntry->feedLink[1]->href);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007#user.contacts', $userProfileEntry->feedLink[1]->rel);
        $this->assertEquals('http://gdata.youtube.com/feeds/users/darcy/inbox', $userProfileEntry->feedLink[2]->href);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007#user.inbox', $userProfileEntry->feedLink[2]->rel);
        $this->assertEquals('http://gdata.youtube.com/feeds/users/darcy/playlists', $userProfileEntry->feedLink[3]->href);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007#user.playlists', $userProfileEntry->feedLink[3]->rel);
        $this->assertEquals('http://gdata.youtube.com/feeds/users/darcy/subscriptions', $userProfileEntry->feedLink[4]->href);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007#user.subscriptions', $userProfileEntry->feedLink[4]->rel);
        $this->assertEquals('http://gdata.youtube.com/feeds/users/darcy/uploads', $userProfileEntry->feedLink[5]->href);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007#user.uploads', $userProfileEntry->feedLink[5]->rel);
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

    public function testEmptyUserProfileEntryToAndFromStringShouldMatch() {
        $entryXml = $this->entry->saveXML();
        $newUserProfileEntry = new Zend_Gdata_YouTube_UserProfileEntry();
        $newUserProfileEntry->transferFromXML($entryXml);
        $newUserProfileEntryXml = $newUserProfileEntry->saveXML();
        $this->assertTrue($entryXml == $newUserProfileEntryXml);
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

    public function testConvertUserProfileEntryToAndFromString() {
        $this->entry->transferFromXML($this->entryText);
        $entryXml = $this->entry->saveXML();
        $newUserProfileEntry = new Zend_Gdata_YouTube_UserProfileEntry();
        $newUserProfileEntry->transferFromXML($entryXml);
        $this->verifyAllSamplePropertiesAreCorrect($newUserProfileEntry);
        $newUserProfileEntryXml = $newUserProfileEntry->saveXML();
        $this->assertEquals($entryXml, $newUserProfileEntryXml);
    }

}
