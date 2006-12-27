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

    /**
     * Test for object initialisation without timeservers, options
     *
     * @return void
     */
    public function testInitTimeserver()
    {
        $server = new Zend_TimeSync();

        $this->assertTrue($server instanceof Zend_TimeSync);
    }

    /**
     * Test for object initialisation with multiple timeservers, and
     * without options
     *
     * @return void
     */
    public function testInitTimeservers()
    {        
        $server = new Zend_TimeSync($this->timeservers);
        $result = $server->get(count($this->timeservers)-1);

        $this->assertTrue($result instanceof Zend_TimeSync_Protocol);
    }

    /**
     * Test for object initialisation with a single timeserver that will
     * default to the default scheme (ntp), because no scheme is supplied
     *
     * @return void
     */
    public function testInitDefaultScheme()
    {
        $server = new Zend_TimeSync('time.windows.com');
        $server->setcurrent(0);
        $result = $server->getCurrent();

        $this->assertTrue($result instanceof Zend_TimeSync_Ntp);
    }

    /**
     * Test for object initialisation with a single NTP timeserver
     *
     * @return void
     */
    public function testInitNtpScheme()
    {
        $server = new Zend_TimeSync('ntp://time.windows.com');
        $server->setcurrent(0);
        $result = $server->getCurrent();

        $this->assertTrue($result instanceof Zend_TimeSync_Ntp);
    }


    /**
     * Test for object initialisation with a single SNTP timeserver
     *
     * @return void
     */
    public function testInitSntpScheme()
    {
        $server = new Zend_TimeSync('sntp://time.zend.com');
        $server->setcurrent(0);
        $result = $server->getCurrent();

        $this->assertTrue($result instanceof Zend_TimeSync_Sntp);
    }


    /**
     * Test for object initialisation with an unsupported scheme. This will
     * cause the default scheme to be used (ntp)
     *
     * @return void
     */
    public function testInitUnknownScheme()
    {
        $server = new Zend_TimeSync('http://time.windows.com');
        $server->setcurrent(0);
        $result = $server->getCurrent();

        $this->assertTrue($result instanceof Zend_TimeSync_Ntp);
    }


    /**
     * Test for object initialisation with a single NTP timeserver,
     * and an array of options
     *
     * @return void
     */
    public function testInitOptions()
    {
        $options = array(
            'timeout' => 5
        );

        $server = new Zend_TimeSync('ntp://time.windows.com', $options);

        $this->assertEquals($options['timeout'], $server->getOption('timeout'));
    }

    /**
     * Test adding an invalid timeserver
     *
     * @return void
     */
    public function testAddInvalidServer()
    {
    	$server = new Zend_TimeSync();

    	try {
    		$server->addServer(12345);
            $this->fail('Exception expected because we supplied an invalid timeserverserver');
        } catch (Zend_TimeSync_Exception $e) {
            // success
        }
    }

    /**
     * Test setting a single option
     *
     * @return void
     */
    public function tesSetOption()
    {
        $timeout = 5;

        $server = new Zend_TimeSync();
        $server->setOption('timeout', $timeout);

        $this->assertEquals($timeout, $server->getOption('timeout'));
    }

    /**
     * Test setting an array of options
     *
     * @return void
     */
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

	/**
     * Test setting an invalid option
     *
     * @return void
     */
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

	/**
     * Test setting an invalid option
     *
     * @return void
     */
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

	/**
     * Test getting an option that is not set
     *
     * @return void
     */
    public function testGetInvalidOptionKey()
    {
    	$server = new Zend_TimeSync();

    	try {
    		$result = $server->getOption('foobar');
    		$this->fail('Exception expected because we supplied an invalid option key');
    	} catch (Zend_TimeSync_Exception $e) {
            // success
        }
    }

    /**
     * Test marking a none existing timeserver as current
     *
     * @return void
     */
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

    /**
     * Test getting the current timeserver when none is set
     *
     * @return void
     */
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

    /**
     * Test getting a none existing timeserver
     *
     * @return void
     */
    public function testGetUnknownServer()
    {
        $server = new Zend_TimeSync();

        try {
            $result = $server->get(count($this->timeservers));
            $this->fail('Exception expected, because the requested timeserver does not exist');
        } catch (Zend_TimeSync_Exception $e) {
            // success
        }
    }

    public function testGetDate()
    {
        $server = new Zend_TimeSync($this->timeservers);

        try {
            $result = $server->getDate();
            $this->assertTrue($result instanceof Zend_Date);
        } catch (Zend_TimeSync_Exception $e) {
            $this->assertContains('could not connect', $e->getMessage());
        }
    }

    public function testGetNtpDate()
    {
        $server = new Zend_TimeSync('time.windows.com');

        try {
            $result = $server->getDate();
            $this->assertTrue($result instanceof Zend_Date);
        } catch (Zend_TimeSync_Exception $e) {
            $this->assertContains('could not connect', $e->getMessage());
        }
    }

    public function testGetSntpDate()
    {
        $server = new Zend_TimeSync('sntp://time-C.timefreq.bldrdoc.gov');

        try {
            $result = $server->getDate();
            $this->assertTrue($result instanceof Zend_Date);
        } catch (Zend_TimeSync_Exception $e) {
            $this->assertContains('could not connect', $e->getMessage());
        }
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

    public function testGetInfo()
    {
        $server = new Zend_TimeSync('time.windows.com');
        try {
            $date   = $server->getDate();
            $result = $server->getInfo();
    
            $this->assertTrue(count($result) > 0);
        } catch (Zend_TimeSync_Exception  $e) {
            // nothing
        }
    }
}
