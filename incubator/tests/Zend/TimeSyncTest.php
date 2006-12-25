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

        // valid servers
        'ntp://be.pool.ntp.org',
        'ntp://time.windows.com',
        'sntp://time-C.timefreq.bldrdoc.gov'
    );

    public function testInitTimeserver()
    {
        $server = new Zend_TimeSync();
        
        $this->assertTrue($server instanceof Zend_TimeSync);
    }
    
    public function testInitTimeservers()
    {        
        $server = new Zend_TimeSync($this->timeservers);
        $result = $server->get(count($this->timeservers)-1);
        
        $this->assertTrue($result instanceof Zend_TimeSync_Protocol);
    }

    public function testInitDefaultScheme()
    {
        $server = new Zend_TimeSync('time.windows.com');
        $server->setcurrent(0);
        $result = $server->getCurrent();
        
        $this->assertTrue($result instanceof Zend_TimeSync_Ntp);
    }

    public function testInitNtpScheme()
    {
        $server = new Zend_TimeSync('ntp://time.windows.com');
        $server->setcurrent(0);
        $result = $server->getCurrent();
        
        $this->assertTrue($result instanceof Zend_TimeSync_Ntp);
    }

    public function testInitSntpScheme()
    {
        $server = new Zend_TimeSync('sntp://time.zend.com');
        $server->setcurrent(0);
        $result = $server->getCurrent();
        
        $this->assertTrue($result instanceof Zend_TimeSync_Sntp);
    }
    
    public function testInitUnknownScheme()
    {
        $server = new Zend_TimeSync('http://time.windows.com');
        $server->setcurrent(0);
        $result = $server->getCurrent();
        
        $this->assertTrue($result instanceof Zend_TimeSync_Ntp);
    }

    public function testInitOptions()
    {
        $options = array(
            'timeout' => 5
        );

        $server = new Zend_TimeSync('ntp://time.windows.com', $options);
        
        $this->assertEquals($options['timeout'], $server->getOption('timeout'));
    }

    public function tesSetOption()
    {
        $timeout = 5;

        $server = new Zend_TimeSync();
        $server->setOption('timeout', $timeout);
        
        $this->assertEquals($timeout, $server->getOption('timeout'));
    }

    public function testSetOptions()
    {
        $options = array(
            'timeout' => 5,
            'foo'     => 'bar'
        );

        $server = new Zend_TimeSync('ntp://time.windows.com', $options);
        
        $this->assertEquals($options['timeout'], $server->getOption('timeout'));
        $this->assertEquals($options['foo'], $server->getOption('foo'));
    }

    public function testSetInvalidOptions()
    {
        $server = new Zend_TimeSync();

        try {
            $server->setOptions('*');
            $this->fail('Exception expected because we did not supply an array, array expected');
        } catch (Zend_TimeSync_Exception $e) {
            // success
        }
    }

    public function testSetInvalidOptionKey()
    {
        $server = new Zend_TimeSync();

        try {
            $server->setOption('*', 'foobar');
            $this->fail('Exception expected because we supplied an invalid key');
        } catch (Zend_TimeSync_Exception $e) {
            // success
        }
    }

    public function testSetUnknownCurrent()
    {
        $server = new Zend_TimeSync();

        try {
            $server->setCurrent(1);
            $this->fail('Exception expected because there is no timeserver which we can mark as current');
        } catch (Zend_TimeSync_Exception $e) {
            // success
        }
    }

    public function testGetUnknownCurrent()
    {
        $server = new Zend_TimeSync();

        try {
            $result = $server->getCurrent();
            $this->fail('Exception expected because there is no current timeserver set');
        } catch (Zend_TimeSync_Exception $e) {
            // success
        }
    }

    public function testGetDate()
    {
        $server  = new Zend_TimeSync($this->timeservers);
        $result = $server->getDate();
                
        $this->assertTrue($result instanceof Zend_Date);
    }
    
    public function testGetNtpDate()
    {
        $server  = new Zend_TimeSync('time.windows.com');
        $result = $server->getDate();
                
        $this->assertTrue($result instanceof Zend_Date);
    }
    
    public function testGetSntpDate()
    {
        $server  = new Zend_TimeSync('sntp://time-C.timefreq.bldrdoc.gov');
        $result = $server->getDate();
                
        $this->assertTrue($result instanceof Zend_Date);
    }
    
    public function testGetInvalidDate()
    {
        $servers = array(
            'dummy-ntp-timeserver.com',
            'another-dummy-ntp-timeserver.com'
        );
        
        $server = new Zend_TimeSync($servers);
        
        try {
            $result = $server->getDate();
        } catch (Zend_TimeSync_Exception $e) {
            $exceptions = $e->get();
            
            foreach($exceptions as $key => $exception) {
                $this->assertTrue($exception instanceof Zend_TimeSync_Exception);
            }
        }
    }
    
    public function testWalkServers()
    {
        $servers = new Zend_TimeSync($this->timeservers);
        
        foreach ($servers as $key => $server) {
            $this->assertTrue($server instanceof Zend_TimeSync_Protocol);
        }
    }
}
