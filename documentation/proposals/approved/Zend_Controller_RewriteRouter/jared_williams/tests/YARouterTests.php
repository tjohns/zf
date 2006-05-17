<?php

require_once '../YARouter.php';

/**
 * PHPUnit2 test case
 */
require_once 'PHPUnit2/Framework/TestCase.php';

require_once 'Zend/Controller/Dispatcher/Interface.php';
require_once 'Zend/Controller/Dispatcher/Token.php';


class Mock_Zend_Controller_Dispatcher implements Zend_Controller_Dispatcher_Interface
{
	function isDispatchable(Zend_Controller_Dispatcher_Token $token) { return true; }
	function dispatch(Zend_Controller_Dispatcher_Token $token) {}
}

/**
 * @package    Zend_YARewriterRouter
 * @subpackage UnitTests
 */
class Zend_YARouter_Test extends PHPUnit2_Framework_TestCase
{

	function testSimpleRouter()
	{
		$router = new Zend_Controller_YARouter();

		$router->connect('a', '/a', array('controller' => 'a', 'action' => 'aindex'));
		$router->connect('b', '/b', array('controller' => 'b', 'action' => 'bindex'));

		$url = Zend_Uri::factory('http://localhost/b');

		$token = $router->route(new Mock_Zend_Controller_Dispatcher(), $url);

		$this->assertEquals($token->getControllerName(), 'b');
		$this->assertEquals($token->getActionName(), 'bindex');
		$this->assertEquals($token->getParams(), array());
	}

	function testRouterWithBasePathMatching()
	{
		$router = new Zend_Controller_YARouter('/gallery/');

		$router->connect('a', '/a', array('controller' => 'a', 'action' => 'aindex'));
		$router->connect('b', '/b', array('controller' => 'b', 'action' => 'bindex'));

		$url = Zend_Uri::factory('http://localhost/gallery/b');

		$token = $router->route(new Mock_Zend_Controller_Dispatcher(), $url);

		$this->assertEquals($token->getControllerName(), 'b');
		$this->assertEquals($token->getActionName(), 'bindex');
		$this->assertEquals($token->getParams(), array());
	}

	function testRouterWithBasePathUrlGeneration()
	{
		$router = new Zend_Controller_YARouter('/gallery/');

		$route = $router->connect('y', '/:year', array('controller' => 'year', 'action' => 'index'),
											array('year' => '\d{4}'));

		$url = Zend_Uri::factory('http://localhost/');
		$route->generateUrl(array('year' => 2006), $url);

		$this->assertEquals($url->getUri(), 'http://localhost/gallery/2006');
	}

	function testRouterWithBasePathStaticUrl()
	{
		$router = new Zend_Controller_YARouter('/gallery/');
		$this->assertEquals($router->getStaticUrl('/css/default.css'), '/gallery/css/default.css');
		$this->assertEquals($router->getStaticUrl('images/logo.png'), '/gallery/images/logo.png');
	}

	function testRouteDisambiguationByRequirements()
	{
		$router = new Zend_Controller_YARouter();

		$router->connect('y', '/:year', array('controller' => 'year', 'action' => 'index'),
											array('year' => '\d{4}'));

		$router->connect('x', '/:x', array('controller' => 'x', 'action' => 'index'));

		$url = Zend_Uri::factory('http://localhost/foo');

		$token = $router->route(new Mock_Zend_Controller_Dispatcher(), $url);

		$this->assertEquals($token->getControllerName(), 'x');
		$this->assertEquals($token->getActionName(), 'index');
		$this->assertEquals($token->getParams(), array('x' => 'foo'));
	}
}

?>