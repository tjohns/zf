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
class Zend_Service_Audioscrobbler_AlbumDataTest extends PHPUnit_Framework_TestCase
{
    public function testGetAlbumInfo()
    {
        try {
            $as = new Zend_Service_Audioscrobbler();
            $as->set('album', 'Metallica');
            $as->set('artist', 'Metallica');
            $response = $as->albumGetInfo();
            $this->assertEquals($response['artist'], 'Metallica');
            $this->assertEquals($response['title'], 'Metallica');
            $this->assertNotNull($response->url);
            $this->assertNotNull($response->tracks);
        } catch (Exception $e ) {
                $this->fail("Exception: [" . $e->getMessage() . "] thrown by test");
        }
    }
}

?>