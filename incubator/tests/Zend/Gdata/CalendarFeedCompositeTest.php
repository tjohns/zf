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

require_once 'Zend/Gdata/Calendar.php';
require_once 'Zend/Gdata/Calendar/EventFeed.php';
require_once 'Zend/Http/Client.php';
require_once 'Zend/Http/Client/Adapter/Test.php';

/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_CalendarFeedCompositeTest extends PHPUnit_Framework_TestCase
{
    protected $listFeed = null;

    /** 
      * Called before each test to setup any fixtures.
      */
    public function setUp()
    {
        $listFeedText = file_get_contents(
                'Zend/Gdata/Calendar/_files/ListFeedCompositeSample1.xml',
                true);
        $this->listFeed = new Zend_Gdata_Calendar_ListFeed($listFeedText);
    }

    /**
      * Verify that a given property is set to a specific value 
      * and that the getter and magic variable return the same value.
      *
      * @param object $obj The object to be interrogated.
      * @param string $name The name of the property to be verified.
      * @param object $value The expected value of the property.
      */
    protected function verifyProperty($obj, $name, $value)
    {
        $propName = $name;
        $propGetter = "get" . ucfirst($name);

        $this->assertEquals($obj->$propGetter(), $obj->$propName);
        $this->assertEquals($obj->$propGetter(), $value);
    }

    /**
      * Verify that a given property is set to a specific value 
      * and that the getter and magic variable return the same value.
      *
      * @param object $obj The object to be interrogated.
      * @param string $name The name of the property to be verified.
      * @param string $secondName 2nd level accessor function name      
      * @param object $value The expected value of the property.
      */
    protected function verifyProperty2($obj, $name, $secondName, $value)
    {
        $propName = $name;
        $propGetter = "get" . ucfirst($name);
        $secondGetter = "get" . ucfirst($secondName);

        $this->assertEquals($obj->$propGetter(), $obj->$propName);
        $this->assertEquals($obj->$propGetter()->$secondGetter(), $value);
    }
    
    /** 
      * Convert sample feed to XML then back to objects. Ensure that 
      * all objects are instances of EventEntry and object count matches.
      */
    public function testEventFeedToAndFromString()
    {
        $entryCount = 0;
        foreach ($this->listFeed as $entry) {
            $entryCount++;
            $this->assertTrue($entry instanceof Zend_Gdata_Calendar_ListEntry);
        }
        $this->assertTrue($entryCount > 0);

        /* Grab XML from $this->listFeed and convert back to objects */ 
        $newListFeed = new Zend_Gdata_Calendar_ListFeed( 
                $this->listFeed->saveXML());
        $newEntryCount = 0;
        foreach ($newListFeed as $entry) {
            $newEntryCount++;
            $this->assertTrue($entry instanceof Zend_Gdata_Calendar_ListEntry);
        }
        $this->assertEquals($entryCount, $newEntryCount);
    }

    /** 
      * Ensure that there number of lsit feeds equals the number 
      * of calendars defined in the sample file.
      */
    public function testEntryCount()
    {
        //TODO feeds implementing ArrayAccess would be helpful here
        $entryCount = 0;
        foreach ($this->listFeed as $entry) {
            $entryCount++;
        }
        $this->assertEquals($entryCount, 6);
        $this->assertEquals($entryCount, $this->listFeed->totalResults->text);
    }

    /** 
      * Check for the existence of an <atom:author> and verify that they 
      * contain the expected values.
      */
    public function testAuthor()
    {
        $feed = $this->listFeed;

        // Assert that the feed's author is correct
        $feedAuthor = $feed->getAuthor();
        $this->assertEquals($feedAuthor, $feed->author);
        $this->assertEquals(count($feedAuthor), 1);
        $this->assertTrue($feedAuthor[0] instanceof Zend_Gdata_App_Extension_Author);
        $this->verifyProperty2($feedAuthor[0], "name", "text", "GData Ops Demo");
        $this->verifyProperty2($feedAuthor[0], "email", "text", "gdata.ops.demo@gmail.com");

        // Assert that each entry has valid author data
        foreach ($feed as $entry) {
            $entryAuthor = $entry->getAuthor();
            $this->assertEquals(count($entryAuthor), 1);
            $this->verifyProperty2($entryAuthor[0], "name", "text", "GData Ops Demo");
            $this->verifyProperty2($entryAuthor[0], "email", "text", "gdata.ops.demo@gmail.com");
            $this->verifyProperty($entryAuthor[0], "uri", null);            
        }
    }

    /**
      * Check for the existence of an <atom:id> and verify that it contains
      * the expected value.
      */
    public function testId()
    {
        $feed = $this->listFeed;

        // Assert that the feed's ID is correct
        $this->assertTrue($feed->getId() instanceof Zend_Gdata_App_Extension_Id);
        $this->verifyProperty2($feed, "id", "text", 
                "http://www.google.com/calendar/feeds/default/private/composite");

        // Assert that all entry's have an Atom ID object
        foreach ($feed as $entry) {
            $this->assertTrue($entry->getId() instanceof Zend_Gdata_App_Extension_Id);
        }

        // Assert one of the entry's IDs
        $entry = $feed[1];
        $this->verifyProperty2($entry, "id", "text", 
                "http://www.google.com/calendar/feeds/default/private/composite/lq2ai6imsbq209q3aeturho50g");
    }

    /**
      * Check for the existence of an <atom:published> and verify that it contains 
      * the expected value.
      */
    public function testPublished()
    {
        $feed = $this->listFeed;

        // Assert that all entry's have an Atom Published object
        foreach ($feed as $entry) {
            $this->assertTrue($entry->getPublished() instanceof Zend_Gdata_App_Extension_Published);
        }

        // Assert one of the entry's Published dates
        $entry = $feed[1];
        $this->verifyProperty2($entry, "published", "text", "2007-05-09T16:44:38.000Z");
    }

    /**
      * Check for the existence of an <atom:updated> and verify that it contains 
      * the expected value.
      */
    public function testUpdated()
    {
        $feed = $this->listFeed;

        // Assert that the feed's updated date is correct
        $this->assertTrue($feed->getUpdated() instanceof Zend_Gdata_App_Extension_Updated);
        $this->verifyProperty2($feed, "updated", "text", 
                "2007-05-31T01:15:00.000Z");

        // Assert that all entry's have an Atom Published object
        foreach ($feed as $entry) {
            $this->assertTrue($entry->getUpdated() instanceof Zend_Gdata_App_Extension_Updated);
        }

        // Assert one of the entry's Published dates
        $entry = $feed[1];
        $this->verifyProperty2($entry, "updated", "text", "2007-05-17T10:33:49.000Z");
    }

    /**
      * Check for the existence of an <atom:title> and verify that it contains
      * the expected value.
      */
    public function testTitle()
    {
        $feed = $this->listFeed;

        // Assert that the feed's title is correct
        $this->assertTrue($feed->getTitle() instanceof Zend_Gdata_App_Extension_Title);
        $this->verifyProperty2($feed, "title", "text", 
                "GData Ops Demo's Composite View");

        // Assert that all entry's have an Atom ID object
        foreach ($feed as $entry) {
            $this->assertTrue($entry->getTitle() instanceof Zend_Gdata_App_Extension_Title);
        }

        // Assert one of the entry's Titles
        $entry = $feed[1];
        $this->verifyProperty2($entry, "title", "text", "all day event may 24");
    }

    /**
      * Check for the existence of an <atom:subtitle> and verify that it contains
      * the expected value.
      */
    public function testSubtitle()
    {
        $feed = $this->listFeed;
        
        // Assert that the feed's title is correct
        $this->assertTrue($feed->getSubtitle() instanceof Zend_Gdata_App_Extension_Subtitle);
        $this->verifyProperty2($feed, "subtitle", "text", 
                "GData Is Awesome");
    }    

    /**
      * Check for the existence of an <gCal:timezone> and verify that it contains
      * the expected value.
      */
    public function testTimezone()
    {
        $feed = $this->listFeed;

        // Assert that the feed's title is correct
        $this->assertTrue($feed->getTimezone() instanceof Zend_Gdata_App_Extension_Subtitle);
        $this->verifyProperty2($feed, "timezone", "text", 
                "America/Chicago");
    }


    /**
      * Check for the existence of an <gCal:eventStatus> and verify that it contains
      * the expected value.
      */
    public function testEventStatus()
    {
        $feed = $this->listFeed;

        // Assert that all entry's have an eventStatus object
        foreach ($feed as $entry) {
            $this->assertTrue($entry->getEventStatus() instanceof Zend_Gdata_App_Extension_EventStatus);
        }

        // Assert one of the entries values
        $entry = $feed[1];
        $this->verifyProperty2($entry, "eventStatus", "value", "http://schemas.google.com/g/2005#event.confirmed");
    }

    /**
      * Check for the existence of an <gCal:visibility> and verify that it contains
      * the expected value.
      */
    public function testVisibility()
    {
        $feed = $this->listFeed;

        // Assert that all entry's have a visibility object
        foreach ($feed as $entry) {
            $this->assertTrue($entry->getVisibility() instanceof Zend_Gdata_Calendar_Extension_Visibility);
        }

        // Assert one of the entries values
        $entry = $feed[1];
        $this->verifyProperty2($entry, "visibility", "value", "http://schemas.google.com/g/2005#event.default");
    }

    /**
      * Check for the existence of an <gCal:transparency> and verify that it contains
      * the expected value.
      */
    public function testTransparency()
    {
        $feed = $this->listFeed;

        // Assert that all entry's have a transparency object
        foreach ($feed as $entry) {
            $this->assertTrue($entry->getTransparency() instanceof Zend_Gdata_Calendar_Extension_Transparency);
        }

        // Assert one of the entries values
        $entry = $feed[1];
        $this->verifyProperty2($entry, "transparency", "value", "http://schemas.google.com/g/2005#event.transparent");
    }

    /**
      * Check for the existence of an <gCal:sendEventNotifications> and verify that it contains
      * the expected value.
      */
    public function testEventNotifications()
    {
        $feed = $this->listFeed;

        // Assert that all entry's have a sendEventNotifications object
        foreach ($feed as $entry) {
            $this->assertTrue($entry->getEventNotifications() instanceof Zend_Gdata_Calendar_Extension_SendEventNotifications);
        }

        // Assert one of the entry's values
        $entry = $feed[1];
        $this->verifyProperty2($entry, "sendEventNotifications", "value", false);
    }

    /**
      * Check for the existence of an <gd:when> and verify that it contains
      * the expected value.
      */    
    public function testWhen()
    {
        $feed = $this->listFeed;

        // Assert one of the entry's values
        $entry = $feed[1];
        $when = $entry->getWhen();
        $this->assertEquals($entry->getWhen(), $entry->when);
        $this->assertTrue($when instanceof Zend_Gdata_Calendar_Extension_When);
        $this->assertEquals($when.count(), 2);
        $this->verifyProperty($when[0], "minutes", "10");
        $this->verifyProperty($when[0], "method", "alert");
        $this->verifyProperty($when[1], "minutes", "10");
        $this->verifyProperty($when[1], "method", "email");
    }

    /**
      * Check for the existence of an <gd:where> and verify that it contains
      * the expected value.
      */    
    public function testWhere()
    {
        $feed = $this->listFeed;

        // Assert one of the entry's values
        $entry = $feed[1];
        $this->assertTrue($entry->getWhere() instanceof Zend_Gdata_Calendar_Extension_When);
        $this->verifyProperty2($entry, "where", "valueString", "Mountain View, California");
    }

    /**
      * Check for the existence of an <gd:comments> and verify that it contains
      * the expected value.
      */    
    public function testComments()
    {
        $feed = $this->listFeed;

        // Assert that all entry's have a comments object
        foreach ($feed as $entry) {
            $this->assertTrue($entry->getComments() instanceof Zend_Gdata_Extension_Comments);
        }

        // Assert one of the entries has the correct values
        // Make sure the comment element looks right
        $entry = $feed[1];
        $c = $entry->getComments();        
        $this->assertEqual($c, $entry->comments);
        
        // Make sure that the feedLink looks right
        $fl = $c->getFeedLink();
        $this->assertTrue($fl instanceof Zend_Gdata_Extension_FeedLink);
        $this->assertEquals($fl, $c->feedLink);
        $this->verifyProperty($fl, "href", "http://www.google.com/calendar/feeds/default/private/full/g829on5sq4ag12se91d10uumko/comments");
        
        // Make sure the embedded feed looks right
        $cFeed = $fl->getFeed();
        $this->assertTrue($cFeed instanceof Zend_Gdata_App_Feed);
        $this->assertEquals($cFeed, $cl->feed);

        // Verify the remainder of the comment feed metadata
        $this->assertTrue($cFeed->getUpdated() instanceof Zend_Gdata_App_Extension_Updated);
        $this->verifyProperty2($cFeed, "updated", "text", "2007-06-01T21:24:11.020Z");

        $this->assertTrue($cFeed->getTitle() instanceof Zend_Gdata_App_Extension_Title);
        $this->verifyProperty2($cFeed, "title", "text", "Comments for: Test Event");

        // Verify that the comments appear to be good
        $commentCount = 0;
        foreach ($cFeed->getEntries() as $entry)
        {
            $this->assertTrue($entry instanceof Zend_Gdata_App_FeedEntry);
            $commentCount++;
        }
        $this->assertEquals($commentCount, 2);

        // Closely examine one of the comments
        $comment = $cFeed[1];

        $this->assertTrue($comment->getId() instanceof Zend_Gdata_App_Extension_Id);
        $this->verifyProperty2($comment, "id", "text", "dfr2c8pbtb8g6uphrsrlpao7mc");

        $this->assertTrue($comment->getPublished() instanceof Zend_Gdata_App_Extension_Published);
        $this->verifyProperty2($comment, "published", "text", "2007-05-23T20:38:08.000Z");

        $this->assertTrue($comment->getUpdated() instanceof Zend_Gdata_App_Extension_Updated);
        $this->verifyProperty2($comment, "updated", "text", "2007-05-23T20:38:08.000Z");

        $this->assertTrue($comment->getAuthor() instanceof Zend_Gdata_App_Extension_Author);
        $this->assertTrue($comment->getAuthor()->getName() instanceof Zend_Gdata_App_Extension_Name);
        $this->assertTrue($comment->getAuthor()->getEmail() instanceof Zend_Gdata_App_Extension_Email);
        $this->verifyProperty2($comment->getAuthor(), "name", "text", "User 2");
        $this->verifyProperty2($comment->getAuthor(), "email", "text", "user@nowhere.invalid");

        $this->assertTrue($comment->getContent() instanceof Zend_Gdata_App_Extension_Content);
        $this->verifyProperty($comment->getContent(), "type", "html");
        $this->verifyProperty($comment->getContent(), "text", "This is a user supplied comment.");
    }
    
    /**
      * Check for the existence of an <gd:where> and verify that it contains
      * the expected value.
      */    
    public function testRecurrence()
    {
        $feed = $this->listFeed;

        // Assert one of the entry's values
        $entry = $feed[0];
        $this->assertTrue($entry->getRecurrence() instanceof Zend_Gdata_Calendar_Extension_Recurrence);
        $this->verifyProperty2($entry, "recurrence", "text", 
                "DTSTART;VALUE=DATE:20070501 DTEND;VALUE=DATE:20070502 RRULE:FREQ=WEEKLY;BYDAY=Tu;UNTIL=20070904");
    }    

    /**
      * Check for the existence of an <openSearch:startIndex> and verify that it contains
      * the expected value.
      */
    public function testStartIndex()
    {
        $feed = $this->listFeed;

        // Assert that the feed's startIndex is correct
        $this->assertTrue($feed->getStartIndex() instanceof Zend_Gdata_Extension_OpenSearchStartIndex);
        $this->verifyProperty2($feed, "startIndex", "text", "1");
    }

    /**
      * Check for the existence of an <openSearch:itemsPerPage> and verify that it contains
      * the expected value.
      */
    public function testItemsPerPage()
    {
        $feed = $this->listFeed;

        // Assert that the feed's itemsPerPage is correct
        $this->assertTrue($feed->getStartIndex() instanceof Zend_Gdata_Extension_OpenSearchItemsPerPage);
        $this->verifyProperty2($feed, "itemsPerPage", "text", "25");
    }    

    /**
      * Check for the existence of an <atom:generator> and verify that it contains
      * the expected value.
      */
    public function testGenerator()
    {
        $feed = $this->listFeed;

        // Assert that the feed's generator is correct
        $this->assertTrue($feed->getGenerator() instanceof Zend_Gdata_App_Extension_Generator);
        $this->verifyProperty2($feed, "generator", "version", "1.0");
        $this->verifyProperty2($feed, "generator", "uri", "http://www.google.com/calendar");
    }

    
}
