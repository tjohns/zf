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
 * @package    Zend_Service_Simpy
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License


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
        
        /* clear any previous test data */
        $linkSet = $this->_simpy->getLinks();
        foreach ($linkSet as $link) {
            $this->_simpy->deleteLink($link->getUrl());
        }
        
        $noteSet = $this->_simpy->getNotes();
        foreach ($noteSet as $note) {
        	$this->_simpy->deleteNote($note->getId());
        }
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
        $linkQuery = new Zend_Service_Simpy_LinkQuery();
        $linkQuery->setQueryString($title);
	    $linkSet = $this->_simpy->getLinks($linkQuery);
	    $link = $linkSet->getIterator()->current();
        $test = ($link->getTitle() == $title 
                && $link->getUrl() == $href);	    
	    $this->assertTrue($test, 'Saved link not found');
        
        /* deleteLink */
        $this->_simpy->deleteLink($href);        
        $linkSet = $this->_simpy->getLinks($linkQuery);
        $test = ($linkSet->getLength() == 0);
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
        
        /* deleteNote */
        $this->_simpy->deleteNote($note->getId());
        $noteSet = $this->_simpy->getNotes($note->getUri());
        $test = ($noteSet->getLength() == 0);
        $this->assertTrue($test, 'Note was not deleted');
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
        $test = ($tagSet->getLength() == 2);
        $this->assertTrue($test, 'splitTag failed');
        
        /* mergeTags */
        $this->_simpy->mergeTags('split1', 'split2', $tags);
        $tagSet = $this->_simpy->getTags();
        $test = ($tagSet->getLength() == 1);
        $this->assertTrue($test, 'mergeTags failed');
        
        /* removeTag */
        $this->_simpy->removeTag($tags);
        $tagSet = $this->_simpy->getTags();
        $test = ($tagSet->getLength() == 0);
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