<?php

/**
 * @package    Zend_Service_Audioscrobbler
 * @subpackage UnitTests
 */

/**
 * Zend_Service_Audioscrobbler
 */
require_once 'Zend/Service/Audioscrobbler.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @package    Zend_Service_Audioscrobbler
 * @subpackage UnitTests
 */
class Zend_Service_Audioscrobbler_TagDataTest extends PHPUnit_Framework_TestCase
{
    public function testGetTopTags()
    {
        try {
            $as = new Zend_Service_Audioscrobbler();
            $response = $as->tagGetTopTags();
            $this->assertNotNull($response->tag);
        } catch (Exception $e ) {
                $this->fail("Exception: [" . $e->getMessage() . "] thrown by test");
        }
    }
    
    public function testGetTopArtists()
    {
         try {
                $as = new Zend_Service_Audioscrobbler();
                $as->set('tag', 'Rock');
                $response = $as->tagGetTopArtists();
                $this->assertNotNull($response->artist);
                $this->assertEquals($response['tag'], strtolower($as->get('tag')));
            } catch (Exception $e ) {
                    $this->fail("Exception: [" . $e->getMessage() . "] thrown by test");
            }       
    }
    
    public function testGetTopTracks() 
    {
        try {
            $as = new Zend_Service_Audioscrobbler();
            $as->set('tag', 'Rock');
            $response = $as->tagGetTopTracks();
            $this->assertNotNull($response->track);
            $this->assertNotNull($response->artist);
            $this->assertEquals($response['tag'], strtolower($as->get('tag')));
        } catch (Exception $e) {
            $this->fail("Exception: [" . $e->getMessage() . "] thrown by test");
        }
    }
    
}

?>