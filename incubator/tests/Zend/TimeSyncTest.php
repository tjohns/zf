<?php

/**
 * @package    Zend_TimeSync
 * @subpackage UnitTests
 */

/**
 * Zend_timeSync
 */
require_once 'Zend/TimeSync.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @package    Zend_TimeSync
 * @subpackage UnitTests
 */
class Zend_TimeSyncTest extends PHPUnit_Framework_TestCase
{
    public $timeservers = array(
        // invalid servers
        'ntp://be.foo.bar.org',
        'sntp://be.foo.bar.org',
        'sntp://foo:bar@be.foo.bar.org:123',
        'sntp://be.foo.bar.org',
        
        // valid servers
        'ntp://be.pool.ntp.org',
        'sntp://time-C.timefreq.bldrdoc.gov'
    );
    
	/**
	 * Test for timesync object creation with an array of timeservers
	 */
    public function testServerCreation()
    {
    	$server = new Zend_TimeSync($this->timeservers);
    	$server->addServer($this->timeservers);
    	
    	$this->assertTrue($server instanceof Zend_TimeSync);
    	
        try {
            $server->addServer(123);
            $this->fail('Exception expected because of invalid timeserver must be either a string or array');
        } catch (Zend_TimeSync_Exception $e) {
            // success
        }
    }
    
    public function testOptions()
    {
    	$server  = new Zend_TimeSync($this->timeservers);
    	$options = array('timeout' => 1);
    	
    	$server->setOptions($options);
    	
    	$this->assertEquals(Zend_TimeSync::$options['timeout'], $server->getOption('timeout'));
    	
        try {
            $server->setOptions(array('$' => 10));
            $this->fail('Exception expected because of invalid option key');
        } catch (Zend_TimeSync_Exception $e) {
            // success
        }
        
        try {
            $server->setOptions('foobar');
            $this->fail('Exception expected because we did not supply an array');
        } catch (Zend_TimeSync_Exception $e) {
            // success
        }
        
        $this->assertEquals(count($server->getOptions()), 1);
        
        try {
            $server->getOption('noneexistingkey');
            $this->fail('Exception expected because we supplied an unknown key');
        } catch (Zend_TimeSync_Exception $e) {
            // success
        }
    }
    
    public function testGetDate()
    {
        $server  = new Zend_TimeSync($this->timeservers);
        $result = $server->getDate();
                
        $this->assertTrue($result instanceof Zend_Date);
        
        $sntpServer = $server->get(5);
        $result = $sntpServer->getDate();
        
        $this->assertTrue($result instanceof Zend_Date);
        
        $timeservers = array(
            'foo-bar.be',
            'foo-bar.be'
        );
        
        $server = new Zend_TimeSync($timeservers);
        
        try {
            $result = $server->getDate();
            $this->fail('Exception expected because of invalid timeservers');
        } catch (Zend_TimeSync_Exception $e) {
            // success
        }
    }
}