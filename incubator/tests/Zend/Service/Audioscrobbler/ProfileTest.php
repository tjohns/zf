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
class Zend_Service_Audioscrobbler_ProfileTest extends PHPUnit_Framework_TestCase
{
    public function testConstructValid()
    {
        try {
            $response = new Zend_Service_Audioscrobbler( );
            $this->assertNotNull($response);
            return;
        } catch (Exception $e) {
            $this->fail("Exception $e->message thrown by test");
        }

    }
    
    public function testGetProfileInfo()
    {
        try {
            $as = new Zend_Service_Audioscrobbler();
            $as->setUser('RJ');
		    $response = $as->userGetProfileInformation();
            $this->assertNotNull($response);
            return;
        } catch (Exception $e ) {
            $this->fail("Exception: [" . $e->getMessage() . "] thrown by test");
        }

    }
    
	public function testGetBadProfileInfo()
	{
		$as = new Zend_Service_Audioscrobbler();
		$as->setUser('kljadsfjllkj');
		
		try {
			$response = $as->userGetProfileInformation();
		} catch (Exception $e) {
            return;
        }

        $this->fail('Exception was not thrown when submitting bad user info');    
    }

    public function testUserGetTopArtists( ) 
    {
        try {
            $as = new Zend_Service_Audioscrobbler();
            $as->setUser('RJ');
            $response = $as->userGetTopArtists();
            $this->assertEquals($response['user'], 'RJ');
            $this->assertNotNull($response->artist);
        } catch (Exception $e) {
            $this->fail("Exception: [" . $e->getMessage() . "] thrown by test" );
        }
    }
    
    public function testUserGetTopAlbums( )
    {
        try {
            $as = new Zend_Service_Audioscrobbler();
            $as->setUser('RJ');
            $response = $as->userGetTopAlbums();
            $this->assertEquals($response['user'], 'RJ');
            $this->assertNotNull($response->album);
        } catch (Exception $e) {
            $this->fail("Exception: [" . $e->getMessage() . "] thrown by test");
        }
    }
    
    public function testUserGetTopTracks( )
    {
        try {
            $as = new Zend_Service_Audioscrobbler();
            $as->setUser('RJ');
            $response = $as->userGetTopTracks();
            $this->assertEquals($response['user'], 'RJ');
            $this->assertNotNull($response->track );
        } catch (Exception $e ) {
            $this->fail("Exception: [$e->getMessage()] thrown by test");
        }
    }

    public function testUserGetTopTags( ) 
    {
        try {
            $as = new Zend_Service_Audioscrobbler();
            $as->setUser('RJ');
            $response = $as->userGetTopTags();
            $this->assertEquals($response['user'], 'RJ');
            $this->assertNotNull($response->tag);
        } catch (Exception $e) {
            $this->fail("Exception: [$e->getMessage()] thrown by test");
        }
    }
    
    public function testUserGetTopTagsForArtist()
    {
        try {
            $as = new Zend_Service_Audioscrobbler();
            $as->setUser('RJ');
            $as->setArtist('Metallica');
            $response = $as->userGetTopTagsForArtist();
            $this->assertEquals($response['user'], 'RJ');
            $this->assertEquals($response['artist'], 'Metallica');
            $this->assertNotNull($response->tag);
        } catch (Exception $e) {
            $this->fail("Exception: [" . $e->getMessage() . "] thrown by test");
        }
    }
    
    public function testBadUserGetTopTagsForArtist()
    {
        try {
            $as = new Zend_Service_Audioscrobbler();
            $response = $as->userGetTopTagsForArtist();
        } catch (Exception $e) {
            return;
        }
        
        $this->fail("User did not set proper parameters");
    }
    
    public function testUserGetTopTagsForAlbum()
    {
        try {
            $as = new Zend_Service_Audioscrobbler();
            $as->setUser("RJ");
            $as->setArtist("Metallica");
            $as->setAlbum("Ride The Lightning");
            $response = $as->userGetTopTagsForAlbum();
            $this->assertEquals($response['user'], 'RJ');
            $this->assertEquals(strtolower($response['artist']), strtolower('Metallica'));
            $this->assertEquals(strtolower($response['album']), strtolower('Ride The Lightning'));
            $this->assertNotNull($response->albumtags);
        } catch (Exception $e) {
            $this->fail("Exception: [" . $e->getMessage() . "] thrown by test");          }
    }
    
}
