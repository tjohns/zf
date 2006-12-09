<?php
/**
 * @package    Zend_Service_Simpy
 * @subpackage UnitTests
 */


/**
 * Zend_Service_Simpy
 */
require_once 'Zend/Service/Simpy.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @package 	Zend_Service_Simpy
 * @subpackage  UnitTests
 */
class Zend_Service_SimpyTest extends PHPUnit_Framework_TestCase
{
    protected $_simpy;
    
	protected function setUp()
	{
	    $this->_simpy = new Zend_Service_Simpy('syapizend', 'mgt37ge');
	}
	
	public function testLinks()
	{
	    $title = 'Zend Framework';
	    $href = 'http://framework.zend.com';

        /* saveLink */
        try {
    	    $this->_simpy->saveLink(
    	    	$title,
    	    	$href,
    	    	Zend_Service_Simpy_Link::ACCESSTYPE_PUBLIC
    	    );
        } catch(Zend_Service_Exception $e) {
            $this->fail('Could not save link: ' . $e->getMessage());
        }
	    
        /* getLinks */
	    $linkSet = $this->_simpy->getLinks($title);
	    $link = $linkSet->getIterator()->current();
        $test = ($link->getTitle() == $title 
                && $link->getUrl() == $href);	    
	    $this->assertTrue($test, 'Saved link not found');
        
        /* deleteLink */
        $this->_simpy->deleteLink($href);        
        $linkSet = $this->_simpy->getLinks($title);
        $test = ($linkSet->length == 0);
        $this->assertTrue($test, 'Link was not deleted');
	}
    
    public function testNotes()
    {
        $title = 'Test Note';
        $tags = array('test');
        $description = 'This is a test note.';
        
        /* saveNote */
        try {
            $this->_simpy->saveNote(
                $title,
                $tags,
                $description
            );
        } catch(Zend_Service_Exception $e) {
            $this->fail('Could not save note: ' . $e->getMessage());
        }
        
        /* getNotes */
        $noteSet = $this->_simpy->getNotes($title);
        $note = $noteSet->getIterator()->current();
        $test = ($note->getTitle() == $title 
                && $note->getTags() == $tags
                && $note->getDescription() == $description);
        $this->assertTrue($test, 'Saved note not found');
    }
    
    public function testTags()
    {
        /* setup */
        $title = 'Zend Framework';
        $href = 'http://framework.zend.com';
        $tags = 'test';

        try {
            $this->_simpy->saveLink(
                $title,
                $href,
                Zend_Service_Simpy_Link::ACCESSTYPE_PUBLIC,
                $tags
            );
        } catch(Zend_Service_Exception $e) {
            $this->fail('saveLink failed: ' . $e->getMessage());
        }
        
        /* getTags */
        $tagSet = $this->_simpy->getTags();
        $tag = $tagSet->getIterator()->current();
        $test = ($tag->getTag() == $tags
                && $tag->getCount() == 1);
        $this->assertTrue($test, 'saveLink failed: Saved tag not found');
        
        /* renameTag */
        $this->_simpy->renameTag($tags, 'renamed');        
        $tagSet = $this->_simpy->getTags();
        $tag = $tagSet->getIterator()->current();
        $test = ($tag->getTag() == 'renamed');
        $this->assertTrue($test, 'renameTag failed');
        $this->_simpy->renameTag('renamed', $tags);
        
        /* splitTags */
        $this->_simpy->splitTag($tags, 'split1', 'split2');
        $tagSet = $this->_simpy->getTags();
        $test = ($tagSet->length == 2);
        $this->assertTrue($test, 'splitTag failed');
        
        /* mergeTags */
        $this->_simpy->mergeTags('split1', 'split2', $tags);
        $tagSet = $this->_simpy->getTags();
        $test = ($tagSet->length == 1);
        $this->assertTrue($test, 'mergeTags failed');
        
        /* removeTag */
        $this->_simpy->removeTag($tags);
        $tagSet = $this->_simpy->getTags();
        $test = ($tagSet->length == 0);
        $this->assertTrue($test, 'removeTag failed');
        
        /* cleanup */
        $this->_simpy->deleteLink($href);
    }
    
    public function testWatchlists()
    {
        /* getWatchlists */
        $watchlistSet = $this->_simpy->getWatchlists();
        $watchlist = $watchlistSet->getIterator()->current();
        $test = ($watchlist != null);
        $this->assertTrue($test, 'Could not get watchlist set');
        
        /* getWatchlist */
        $watchlist = $this->_simpy->getWatchlist($watchlist->getId());
        $test = ($watchlist != null);
        $this->assertTrue($test, 'Could not get watchlist');
    }   
}