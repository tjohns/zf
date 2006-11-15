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
class Zend_Service_Audioscrobbler_GroupTest extends PHPUnit_Framework_TestCase
{
	public function testWeeklyChartList()
    {
        try {
            $as = new Zend_Service_Audioscrobbler();
            $as->set('group', urlencode('Jazz Club'));
		    $response = $as->groupGetWeeklyChartList();
            $this->assertNotNull($response);
			$this->assertNotNull($response->chart);
			$this->assertEquals($response['group'], 'Jazz Club');
            return;
        } catch (Exception $e ) {
            $this->fail("Exception: [" . $e->getMessage() . "] thrown by test");
        }

    }
    
    public function testWeeklyArtistChartList()
    {
        try {
            $as = new Zend_Service_Audioscrobbler();
            $as->set('group', urlencode('Jazz Club'));
		    $response = $as->groupGetWeeklyArtistChartList();
            $this->assertNotNull($response);
			$this->assertNotNull($response->chart);
			$this->assertEquals($response['group'], 'Jazz Club');
            return;
        } catch (Exception $e ) {
            $this->fail("Exception: [" . $e->getMessage() . "] thrown by test");
        }        
    }


}

?>