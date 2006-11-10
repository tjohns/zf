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
class Zend_Service_Audioscrobbler_TrackDataTest extends PHPUnit_Framework_TestCase
{
    public function testGetTopFans()
    {
        try {
            $as = new Zend_Service_Audioscrobbler();
            $as->set('artist', 'Metallica');
            $as->set('track', 'Enter Sandman');
            $response = $as->trackGetTopFans();
            $this->assertEquals($response['artist'], 'Metallica');
            $this->assertEquals($response['track'], 'Enter Sandman');
            $this->assertNotNull($response->user);
        } catch (Exception $e ) {
                $this->fail("Exception: [" . $e->getMessage() . "] thrown by test");
        }
    }
    
    public function testGetTopTags()
    {
        try {
            $as = new Zend_Service_Audioscrobbler();
            $as->set('artist', 'Metallica');
            $as->set('track', 'Enter Sandman');
            $response = $as->trackGetTopTags();
            $this->assertNotNull($response->tag);
            $this->assertEquals($response['artist'], 'Metallica');
            $this->assertEquals($response['track'], 'Enter Sandman');
        } catch (Exception $e) {
            $this->fail("Exception: [" . $e->getMessage() . "] thrown by test");
        }
    }
}

?>