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
 * @package    Zend_Feed
 * @subpackage UnitTests
 */
class Zend_Feed_AtomEntryOnlyTest extends PHPUnit_Framework_TestCase {

    public function testEntryOnly()
    {
        $feed = new Zend_Feed_Atom(null, file_get_contents(dirname(__FILE__) . '/_files/TestAtomFeedEntryOnly.xml'));

        $this->assertEquals(1, $feed->count(), 'The entry-only feed should report one entry.');

        foreach ($feed as $entry);
        $this->assertEquals('Zend_Feed_EntryAtom', get_class($entry), 'The single entry should be an instance of Zend_Feed_EntryAtom');

        $this->assertEquals('1', $entry->id(), 'The single entry should have id 1');
        $this->assertEquals('Bug', $entry->title(), 'The entry\'s title should be "Bug"');
    }

}
