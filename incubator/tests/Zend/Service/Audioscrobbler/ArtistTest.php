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
class Zend_Service_Audioscrobbler_ArtistTest extends PHPUnit_Framework_TestCase
{
	public function testGetRelatedArtists()
    {
        try {
            $as = new Zend_Service_Audioscrobbler();
            $as->set('artist', 'Metallica');
		    $response = $as->artistGetRelatedArtists();
            $this->assertNotNull($response);
			$this->assertNotNull($response->similarartists);
			$this->assertNotNull($response->artist);
            return;
        } catch (Exception $e ) {
            $this->fail("Exception: [" . $e->getMessage() . "] thrown by test");
        }

    }

	public function testGetFans()
	{
		try {
			$as = new Zend_Service_Audioscrobbler();
			$as->set('artist', 'Metallica');
			$response = $as->artistGetTopFans();
			$this->assertNotNull($response->fans);
			$this->assertEquals($response['artist'], 'Metallica');
			$this->assertNotNull($response->user);
			return;
		} catch (Exception $e) {
            $this->fail("Exception: [" . $e->getMessage() . "] thrown by test");
		}
	}
	
	public function testTopTracks()
	{
		try {
			$as = new Zend_Service_Audioscrobbler();
			$as->set('artist', 'Metallica');
			$response = $as->artistGetTopTracks();
			$this->assertNotNull($response->mostknowntracks);
			$this->assertEquals($response['artist'], 'Metallica');
			$this->assertNotNull($response->track);
			return;
		} catch (Exception $e) {
			$this->fail("Exception: [" . $e->getMessage() . "] thrown by test");
		}
	}
	
	public function testTopAlbums()
	{
		try {
			$as = new Zend_Service_Audioscrobbler();
			$as->set('artist', 'Metallica');
			$response = $as->artistGetTopAlbums();
			$this->assertNotNull($response->topalbums);
			$this->assertEquals($response['artist'], 'Metallica');
			$this->assertNotNull($response->album);
			return;
		} catch (Exception $e) {
			$this->fail("Exception: [" . $e->getMessage() . "] thrown by test");
		}
	}
	
	public function testTopTags()
	{
		try {
			$as = new Zend_Service_Audioscrobbler();
			$as->set('artist', 'Metallica');
			$response = $as->artistGetTopTags();
			$this->assertNotNull($response->toptags);
			$this->assertEquals($response['artist'], 'Metallica');
			$this->assertNotNull($response->tag);
		} catch (Exception $e) {
			$this->fail("Exception: [" . $e->getMessage() . "] thrown by test");
		}
	}
}

?>