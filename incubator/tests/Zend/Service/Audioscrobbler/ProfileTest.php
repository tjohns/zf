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
        // throws exception on failure

        $response = NULL;

        try {
            new Zend_Service_Audioscrobbler();
        } catch (Zend_Service_Exception $e) {
            $response = $e;
        }

        $this->assertNull($response);
    }
    
    public function testGetProfileInfo()
    {
        $as = new Zend_Service_Audioscrobbler();
        $as->setUser('RJ');
		$response = $as->userGetProfileInformation();
        $this->assertNotNull($response);
    }

	public function testGetBadProfileInfo()
	{
		$as = new Zend_Service_Audioscrobbler();
		$as->setUser('kljadsfjllkj');
		
		try {
			$response = $as->userGetProfileInformation();
		} catch (Zend_Service_Exception $e) {
			$response = $e;
		}
		
		$this->assertFalse($response);
	}

}
