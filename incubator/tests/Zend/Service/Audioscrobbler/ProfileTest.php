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
            $as->set('user', 'RJ');
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
		$as->set('user', 'kljadsfjllkj');
		
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
            $as->set('user', 'RJ');
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
            $as->set('user', 'RJ');
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
            $as->set('user', 'RJ');
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
            $as->set('user', 'RJ');
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
            $as->set('user', 'RJ');
            $as->set('artist', 'Metallica');
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
        
        $this->fail("Function did not throw exception based on bad parameters");
    }
    
    public function testUserGetTopTagsForAlbum()
    {
        try {
            $as = new Zend_Service_Audioscrobbler();
            $as->set('user', 'RJ');
            $as->set('artist', 'Metallica');
            $as->set('album', 'Ride The Lightning');
            $response = $as->userGetTopTagsForAlbum();
            $this->assertEquals($response['user'], 'RJ');
            $this->assertEquals(strtolower($response['artist']), strtolower('Metallica'));
            $this->assertEquals(strtolower($response['album']), strtolower('Ride The Lightning'));
            $this->assertNotNull($response->albumtags);
        } catch (Exception $e) {
            $this->fail("Exception: [" . $e->getMessage() . "] thrown by test");          }
    }

    public function testUserGetTopTagsForTrack()
    {
        try {
            $as = new Zend_Service_Audioscrobbler();
            $as->set('user', 'RJ');
			$as->set('artist', 'Metallica');
			$as->set('track', 'Nothing Else Matters');
            $response = $as->userGetTopTagsForTrack();
            $this->assertEquals($response['user'], 'RJ');
            $this->assertEquals($response['artist'], 'Metallica');
            $this->assertEquals($response['track'], 'Nothing Else Matters');
            $this->assertNotNull($response->tracktags);
        } catch ( Exception $e) {
            $this->fail("Exception: ]" . $e->getMessage() . "] thrown by test");
        }
    }
    
    public function testUserGetFriends()
    {
        try {
            $as = new Zend_Service_Audioscrobbler();
            $as->set('user', 'RJ');
            $response = $as->userGetFriends();
            $this->assertEquals($response['user'], 'RJ');
            $this->assertNotNull($response->friends);
            $this->assertNotNull($response->user);
        } catch (Exception $e) {
            $this->fail("Exception: [" . $e->getMessage() . "] thrown by test");
        }
    }
    
    public function testUserGetNeighbours()
    {
        try {
            $as = new Zend_Service_Audioscrobbler();
            $as->set('user', 'RJ');
            $response = $as->userGetNeighbours();
            $this->assertEquals($response['user'], 'RJ');
            $this->assertNotNull($response->user);
            $this->assertNotNull($response->neighbours);
        } catch (Exception $e) {
            $this->fail("Exception: [" . $e->getMessage() . "] thrown by test");
        }
    }
    
    public function testUserRecentTracks()
    {
        try {
            $as = new Zend_Service_Audioscrobbler();
            $as->set('user', 'RJ');
            $response = $as->userGetRecentTracks();
            $this->assertEquals($response['user'], 'RJ');
            $this->assertNotNull($response->recenttracks);
            $this->assertNotNull($response->track);
        } catch (Exception $e) {
            $this->fail("Exception: [" . $e->getMessage() . "] thrown by test");
        }
    }
    
    public function testUserRecentBannedTracks()
    {
        try {
            $as = new Zend_Service_Audioscrobbler();
            $as->set('user', 'RJ');
            $response = $as->userGetRecentBannedTracks();
            $this->assertEquals($response['user'], 'RJ');
            $this->assertNotNull($response->recentbannedtracks);
            $this->assertNotNull($response->track);
        } catch (Exception $e) {
            $this->fail("Exception: [" . $e->getMessage() . "] thrown by test");
        }
    }

    public function testUserRecentLovedTracks()
    {
        try {
            $as = new Zend_Service_Audioscrobbler();
            $as->set('user', 'RJ');
            $response = $as->userGetRecentLovedTracks( );
            $this->assertEquals($response['user'], 'RJ');
            $this->assertNotNull($response->recentlovedtracks);
            $this->assertNotNull($response->track);
        } catch (Exception $e) {
            $this->fail("Exception: [" . $e->getMessage() . "] thrown by test");
        }
    }    

    public function testUserGetWeeklyChartList()
    {
        try {
            $as = new Zend_Service_Audioscrobbler();
            $as->set('user', 'RJ');
            $response = $as->userGetWeeklyChartList();
            $this->assertEquals($response['user'], 'RJ');
            $this->assertNotNull($response->weeklychartlist);
            $this->assertNotNull($response->chart['from']);
            $this->assertNotNull($response->chart['to']);
        } catch (Exception $e) {
            $this->fail("Exception: [" . $e->getMessage(). "] thrown by test");
       }
    }

    public function testUserGetRecentWeeklyArtistChart()
    {
        try {
            $as = new Zend_Service_Audioscrobbler();
            $as->set('user', 'RJ');
            $response = $as->userGetWeeklyArtistChart();
            $this->assertEquals($response['user'], 'RJ');
            $this->assertNotNull($response->weeklyartistchart);
            $this->assertNotNull($response->artist);
        } catch (Exception $e) {
            $this->fail("Exception: [" . $e->getMessage(). "] thrown by test");
        }
    }
    
    public function testUserGetWeeklyAlbumChart()
    {
        try {
            $as = new Zend_Service_Audioscrobbler();
            $as->set('user', 'RJ');
            $response = $as->userGetWeeklyAlbumChart();
            $this->assertEquals($response['user'], 'RJ');
            $this->assertNotNull($response->weeklyalbumchart);
            $this->assertNotNull($response['from']);
            $this->assertNotNull($response['to']);
        } catch (Exception $e) {
            $this->fail("Exception: [" . $e->getMessage(). "] thrown by test");
        }
    }

    public function testUserGetPreviousWeeklyArtistChart()
    {
        try {
            $as = new Zend_Service_Audioscrobbler();
            $as->set('user', 'RJ');
            $from = 1114965332;
            $to = 1115570132;
            $response = $as->userGetWeeklyArtistChart($from, $to);
            $this->assertEquals($response['user'], 'RJ');
            $this->assertEquals($response['from'], 1114965332);
            $this->assertEquals($response['to'], 1115570132);
            $this->assertNotNull($response->weeklyartistchart);
            $this->assertNotNull($response->artist);
        } catch ( Exception $e) {
            $this->fail("Exception: [" . $e->getMessage(). "] thrown by test");
        }
    }

    public function testUserGetPreviousWeeklyAlbumChart()
    {
        try {
            $as = new Zend_Service_Audioscrobbler();
            $as->set('user', 'RJ');
            $from = 1114965332;
            $to = 1115570132;
            $response = $as->userGetWeeklyAlbumChart($from, $to);
            $this->assertEquals($response['user'], 'RJ');
            $this->assertEquals($response['from'], 1114965332);
            $this->assertEquals($response['to'], 1115570132);
            $this->assertNotNull($response->weeklyartistchart);
            $this->assertNotNull($response->album);
        } catch ( Exception $e) {
            $this->fail("Exception: [" . $e->getMessage(). "] thrown by test");
        }
 
    }

    public function testUserGetPreviousWeeklyTrackChart()
    {
        try {
            $as = new Zend_Service_Audioscrobbler();
            $as->set('user', 'RJ');
            $from = 1114965332;
            $to = 1115570132;
            $response = $as->userGetWeeklyTrackChart($from, $to);
            $this->assertEquals($response['user'], 'RJ');
            $this->assertEquals($response['from'], 1114965332);
            $this->assertEquals($response['to'], 1115570132);
            $this->assertNotNull($response->weeklytrackchart);
            $this->assertNotNull($response->track);
        } catch ( Exception $e) {
            $this->fail("Exception: [" . $e->getMessage(). "] thrown by test");
        }
 
    }

}
