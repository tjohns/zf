<?php
/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 */

/** Zend_Controller_Router_Route */
require_once 'Zend/Controller/Router/StaticRoute.php';

/** PHPUnit test case */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 */
class Zend_Controller_StaticRouteTest extends PHPUnit_Framework_TestCase
{

    public function testStaticMatch()
    {
        $route = new Zend_Controller_Router_StaticRoute('users/all');
        $values = $route->match('users/all');

        $this->assertType('array', $values);
    }

    public function testStaticMatchFailure()
    {
        $route = new Zend_Controller_Router_StaticRoute('archive/2006');
        $values = $route->match('users/all');

        $this->assertSame(false, $values);
    }

    public function testStaticMatchWithDefaults()
    {
        $route = new Zend_Controller_Router_StaticRoute('users/all', 
                    array('controller' => 'ctrl', 'action' => 'act'));
        $values = $route->match('users/all');

        $this->assertType('array', $values);
        $this->assertSame('ctrl', $values['controller']);
        $this->assertSame('act', $values['action']);
    }

    public function testStaticUTFMatch()
    {
        $route = new Zend_Controller_Router_StaticRoute('żółć');
        $values = $route->match('żółć');

        $this->assertType('array', $values);
    }

    public function testRootRoute()
    {
        $route = new Zend_Controller_Router_StaticRoute('/');
        $values = $route->match('');

        $this->assertSame(array(), $values);
    }

    public function testAssemble()
    {
        $route = new Zend_Controller_Router_StaticRoute('/about');
        $url = $route->assemble();

        $this->assertSame('about', $url);
    }

    public function testGetDefaults()
    {
        $route = new Zend_Controller_Router_StaticRoute('users/all', 
                    array('controller' => 'ctrl', 'action' => 'act'));

        $values = $route->getDefaults();

        $this->assertType('array', $values);
        $this->assertSame('ctrl', $values['controller']);
        $this->assertSame('act', $values['action']);
    }

    public function testGetDefault()
    {
        $route = new Zend_Controller_Router_StaticRoute('users/all', 
                    array('controller' => 'ctrl', 'action' => 'act'));

        $this->assertSame('ctrl', $route->getDefault('controller'));
        $this->assertSame(null, $route->getDefault('bogus'));
    }

}
