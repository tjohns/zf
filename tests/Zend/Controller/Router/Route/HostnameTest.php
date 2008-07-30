<?php
/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 */

/** Test helper */
require_once dirname(__FILE__) . '/../../../../TestHelper.php';

/** Zend_Controller_Router_Route_Hostname */
require_once 'Zend/Controller/Router/Route/Hostname.php';

/** PHPUnit test case */
require_once 'PHPUnit/Framework/TestCase.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Controller_Router_Route_HostnameTest::main');
}

/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 */
class Zend_Controller_Router_Route_HostnameTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Controller_Router_Route_HostnameTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }
    
    public function testCorrectStaticHostMatch()
    {
        $route = $this->_getStaticHostRoute();

        $values = $route->match('www.zend.com');
        $this->assertEquals('ctrl', $values['controller']);
    }

    public function testWrongStaticHostMatch()
    {
        $route = $this->_getStaticHostRoute();

        $values = $route->match('foo.zend.com');
        $this->assertFalse($values);
    }
    
    public function testCorrectHostMatch()
    {
        $route = $this->_getHostRoute();

        $values = $route->match('foo.zend.com');
        $this->assertEquals('ctrl', $values['controller']);
    }

    public function testWrongHostMatch()
    {
        $route = $this->_getHostRoute();

        $values = $route->match('www.zend.com');
        $this->assertFalse($values);
    }
    
    public function testAssembleStaticHost()
    {
        $route = $this->_getStaticHostRoute();
        
        $this->assertEquals('www.zend.com', $route->assemble());
    }

    public function testAssembleHost()
    {
        $route = $this->_getHostRoute();
        
        $this->assertEquals('foo.zend.com', $route->assemble(array('subdomain' => 'foo')));
    }
    
    public function testAssembleHostWithMissingParam()
    {
        $route = $this->_getHostRoute();
        
        try {
            $route->assemble();
            $this->fail('An expected Zend_Controller_Router_Exception has not been raised');
        } catch (Zend_Controller_Router_Exception $expected) {
            $this->assertContains('subdomain is not specified', $expected->getMessage());
        }
    }
    
    public function testAssembleHostWithDefaultParam()
    {
        $route = $this->_getHostRouteWithDefault();

        $this->assertEquals('bar.zend.com', $route->assemble());
    }
    
    public function testHostGetDefault()
    {
        $route = $this->_getHostRouteWithDefault();

        $this->assertEquals('bar', $route->getDefault('subdomain'));
    }
    
    public function testHostGetNonExistentDefault()
    {
        $route = $this->_getHostRouteWithDefault();

        $this->assertEquals(null, $route->getDefault('blah'));
    }
    
    public function testHostGetDefaults()
    {
        $route    = $this->_getHostRouteWithDefault();
        $defaults = $route->getDefaults();
        
        $this->assertEquals('bar', $defaults['subdomain']);
    }

    protected function _getStaticHostRoute()
    {
        $route = new Zend_Controller_Router_Route_Hostname('www.zend.com',
                                                            array('controller' => 'ctrl',
                                                                  'action' => 'act'));
                                                            
        return $route;
    }

    protected function _getHostRoute()
    {
        $route = new Zend_Controller_Router_Route_Hostname(':subdomain.zend.com',
                                                            array('controller' => 'ctrl',
                                                                  'action' => 'act'),
                                                            array('subdomain' => '(foo|bar)'));
                                                            
        return $route;
    }
    
    protected function _getHostRouteWithDefault()
    {
        $route = new Zend_Controller_Router_Route_Hostname(':subdomain.zend.com',
                                                            array('controller' => 'ctrl',
                                                                  'action' => 'act',
                                                                  'subdomain' => 'bar'),
                                                            array('subdomain' => '(foo|bar)'));
                                                            
        return $route;
    }
}

if (PHPUnit_MAIN_METHOD == "Zend_Controller_Router_Route_HostnameTest::main") {
    Zend_Controller_Router_Route_HostnameTest::main();
}
