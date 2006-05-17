<?php
/**
 * @package    Zend_Uri
 * @subpackage UnitTests
 */

/**
 * Zend_Uri
 */
require_once 'Zend/Controller/Router/Route.php';

/**
 * PHPUnit2 test case
 */
require_once 'PHPUnit2/Framework/TestCase.php';


/**
 * @package    Zend_Controller
 * @subpackage UnitTests
 */
class RouteTest extends PHPUnit2_Framework_TestCase
{
    public function testVariables()
    {

        $route = new Zend_Controller_Router_Route(':controller/:action/:year');
        $values = $route->match('c/a/2000');

        $this->assertEquals($values['controller'], 'c', "Controller failed validation");
        $this->assertEquals($values['action'], 'a', "Action failed validation");
        $this->assertEquals($values['year'], '2000', "Year failed validation");

    }

    public function testDefaultWithoutValue()
    {

        $route = new Zend_Controller_Router_Route(':controller/:action/:year', array('year' => 2006));
        $values = $route->match('c/a');

        $this->assertEquals($values['controller'], 'c', "Controller failed validation");
        $this->assertEquals($values['action'], 'a', "Action failed validation");
        $this->assertEquals($values['year'], 2006, "Year failed validation");

    }

    public function testDefaultWithValue()
    {

        $route = new Zend_Controller_Router_Route(':controller/:action/:year', array('year' => 2006));
        $values = $route->match('c/a/2000');

        $this->assertEquals($values['controller'], 'c', "Controller failed validation");
        $this->assertEquals($values['action'], 'a', "Action failed validation");
        $this->assertEquals($values['year'], '2000', "Year failed validation");

    }

    public function testDefaultWithRequirementAndValue()
    {

        $route = new Zend_Controller_Router_Route(':controller/:action/:year', array('year' => 2006), array('year' => '\d+'));
        $values = $route->match('c/a/2000');

        $this->assertEquals($values['controller'], 'c', "Controller failed validation");
        $this->assertEquals($values['action'], 'a', "Action failed validation");
        $this->assertEquals($values['year'], '2000', "Action failed validation");

    }

    public function testDefaultWithRequirementAndIncorrectValue()
    {

        $route = new Zend_Controller_Router_Route(':controller/:action/:year', array('year' => 2006), array('year' => '\d+'));
        $values = $route->match('c/a/2000t');

        $this->assertEquals($values['controller'], 'c', "Controller failed validation");
        $this->assertEquals($values['action'], 'a', "Action failed validation");
        $this->assertEquals($values['year'], 2006, "Year failed validation");

    }

    public function testDefaultWithRequirementAndWithoutValue()
    {

        $route = new Zend_Controller_Router_Route(':controller/:action/:year', array('year' => 2006), array('year' => '\d+'));
        $values = $route->match('c/a');

        $this->assertEquals($values['controller'], 'c', "Controller failed validation");
        $this->assertEquals($values['action'], 'a', "Action failed validation");
        $this->assertEquals($values['year'], 2006, "Year failed validation");

    }

    public function testDefaultInTheMiddle()
    {

        $route = new Zend_Controller_Router_Route(':controller/:action/:year', array('action' => 'action'), array('year' => '\d+'));
        $values = $route->match('c//2000');

        $this->assertEquals($values['controller'], 'c', "Controller failed validation");
        $this->assertEquals($values['action'], 'action', "Action failed validation");
        $this->assertEquals($values['year'], '2000', "Year failed validation");

    }

    public function testRouteNotMatched()
    {

        $route = new Zend_Controller_Router_Route(':controller/:action/:year', array('action' => 'action'), array('year' => '\d+'));
        $values = $route->match('archive/c/a/2000');

        $this->assertEquals($values, false, "Failed validation");

    }

    public function testRouteStaticMatched()
    {

        $route = new Zend_Controller_Router_Route('archive/:controller/:action/:year', array('action' => 'action'), array('year' => '\d+'));
        $values = $route->match('archive/c/a/2000');

        $this->assertEquals($values['controller'], 'c', "Controller failed validation");
        $this->assertEquals($values['action'], 'a', "Action failed validation");
        $this->assertEquals($values['year'], '2000', "Year failed validation");

    }

    public function testRouteStaticNotMatched()
    {

        $route = new Zend_Controller_Router_Route('archive/:controller/:action/:year', array('action' => 'action'), array('year' => '\d+'));
        $values = $route->match('news/c/a/2000');

        $this->assertEquals($values, false, "Failed validation");

    }

    public function testAssemble()
    {

        $route = new Zend_Controller_Router_Route('authors/:name', array('controller' => 'users', 'action' => 'show'));
        $url = $route->assemble(array('name' => 'mike'));

        $this->assertEquals($url, 'authors/mike', "Failed assemble");

    }

    public function testAssembleWithDefault()
    {

        $route = new Zend_Controller_Router_Route('archive/:year', array('year' => 2006, 'controller' => 'archive', 'action' => 'show'), array('year' => '\d+'));
        $url = $route->assemble();

        $this->assertEquals($url, 'archive/2006', "Failed assemble");

    }

    public function testAssembleWithVariableMissing()
    {

        $route = new Zend_Controller_Router_Route('archive/:year', array('controller' => 'archive', 'action' => 'show'), array('year' => '\d+'));

        try {
	        $url = $route->assemble();
        } catch (Exception $e) {}

        $this->assertTrue($e instanceof Zend_Controller_Router_Exception, 'Expected Zend_Controller_Router_Exception to be thrown');

    }

}
