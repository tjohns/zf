<?php

require_once '../YARouter.php';

/**
 * PHPUnit2 test case
 */
require_once 'PHPUnit2/Framework/TestCase.php';


/**
 * @package    Zend_Controller
 * @subpackage UnitTests
 */
class Zend_YARoute_Test extends PHPUnit2_Framework_TestCase
{
	function testSimpleMatching()
	{
		$route = new Zend_Controller_YARouter_Route('a', '/static');
		$url = Zend_Uri::factory('http://localhost/static');
		$this->assertNotEquals($route->isMatch($url), false);
	}

	function testStaticWildCardMatching()
	{
		$route = new Zend_Controller_YARouter_Route('b', '/static/:dynamic/*rest');

		$url = Zend_Uri::factory('http://localhost/static/first_dynamic/some/more/parts');

		$this->assertEquals($route->isMatch($url), array('dynamic' => 'first_dynamic',
															'rest' => 'some/more/parts'));
	}

	function testStaticWildCardGenerating()
	{
		$route = new Zend_Controller_YARouter_Route('b', '/static/:dynamic/*rest');
		$url = Zend_Uri::factory('http://localhost/static/first_dynamic/some/more/parts');

		$route->generateUrl(array('dynamic' => 'part1', 'rest' => 'rest/of/the/path'), $url);

		$this->assertEquals($url->getUri(), 'http://localhost/static/part1/rest/of/the/path');
	}

	function testDynamicParenthesisMatching()
	{
		$route = new Zend_Controller_YARouter_Route('page', '/some/path/:(page).htm');
		$url = Zend_Uri::factory('http://localhost/some/path/test.htm');

		$this->assertEquals($route->isMatch($url), array('page' => 'test'));
	}

	function testDynamicParenthesisGenerating()
	{
		$route = new Zend_Controller_YARouter_Route('page', '/some/path/:(page).htm');
		$url = Zend_Uri::factory('http://localhost/some/path/test.htm');
		$route->generateUrl(array('page' => 'other'), $url);
		$this->assertEquals($url->getUri(), 'http://localhost/some/path/other.htm');
	}

	function testWildcardParenthesisMatching()
	{
		$route = new Zend_Controller_YARouter_Route('page', '*(path)/:(page).htm');
		$url = Zend_Uri::factory('http://localhost/some/path/test.htm');

		$this->assertEquals($route->isMatch($url), array('path' => '/some/path', 'page' => 'test'));
	}

	function testWildcardParenthesisGenerating()
	{
		$route = new Zend_Controller_YARouter_Route('page', '*(path)/:(page).htm');
		$url = Zend_Uri::factory('http://localhost/some/path/test.htm');

		$params = array('path' => '/some/other/path', 'page' => 'other');

		$route->generateUrl($params, $url);
		$this->assertEquals($url->getUri(), 'http://localhost/some/other/path/other.htm');
	}

	function testTwoDynamicsSeperatedByStaticMatching()
	{
		$route = new Zend_Controller_YARouter_Route('a', '/static/:dynamic/another_static/:another_dynamic');

		$url = Zend_Uri::factory('http://localhost/static/first_dynamic/another_static/second_dynamic');

		$this->assertEquals($route->isMatch($url), array('dynamic' => 'first_dynamic',
															'another_dynamic' => 'second_dynamic'));
	}

	function testTwoDynamicsSeperatedByStaticGenerating()
	{
		$route = new Zend_Controller_YARouter_Route('a', '/static/:dynamic/another_static/:another_dynamic');

		$url = Zend_Uri::factory('http://localhost/static/first_dynamic/another_static/second_dynamic');

		$route->generateUrl(array('dynamic' => 'one', 'another_dynamic' => 'two'), $url);

		$this->assertEquals($url->getUri(), 'http://localhost/static/one/another_static/two');
	}

	function testWildcardWithDefaultMatching()
	{
		$route = new Zend_Controller_YARouter_Route('userblog', '*url/:username',
				array('controller' => 'blog', 'action' => 'view', 'username' => 'george'));

		/*
			Due to the default username, the whole path is treated as part of the wildcard.
		*/
		$url = Zend_Uri::factory('http://localhost/some/long/url/george');
		$this->assertEquals($route->isMatch($url),
								array('controller' => 'blog', 'action' => 'view', 'username' => 'george',
											'url' => '/some/long/url/george'));

		$url = Zend_Uri::factory('http://localhost/some/other/stuff/fred');
		$this->assertEquals($route->isMatch($url),
							array('controller' => 'blog', 'action' => 'view', 'username' => 'george',
									'url' => '/some/other/stuff/fred'));
	}

	function testWildcardWithoutDefaultMatching()
	{
		$route = new Zend_Controller_YARouter_Route('userblog', '*url/:username',
					array('controller' => 'blog', 'action' => 'view'));
		/*
			Because the username now doesn't have a default, it is always extracted from path.
		*/
		$url = Zend_Uri::factory('http://localhost/some/long/url/george');
		$this->assertEquals($route->isMatch($url),
				array('controller' => 'blog', 'action' => 'view',
						'username' => 'george',
						'url' => '/some/long/url'));

		$url = Zend_Uri::factory('http://localhost/some/other/stuff/fred');
		$this->assertEquals($route->isMatch($url),
							array('controller' => 'blog', 'action' => 'view', 'username' => 'fred',
									'url' => '/some/other/stuff'));
	}

	function testPairsOfGroupingsMatching()
	{
		$route = new Zend_Controller_YARouter_Route('groupings', '/path/:(action)-:(id)', array(), array('id' => '\d+'));

		$url = Zend_Uri::factory('http://localhost/path/view-24');
		$this->assertEquals($route->isMatch($url), array('action' => 'view', 'id' => '24'));
	}

	function testPairsOfGroupingsGenerating()
	{
		$route = new Zend_Controller_YARouter_Route('groupings', '/path/:(action)-:(id)', array(), array('id' => '\d+'));

		$url = Zend_Uri::factory('http://localhost/path/view-24');

		$route->generateUrl(array('action' => 'delete', 'id' => 103), $url);
		$this->assertEquals($url->getUri(), 'http://localhost/path/delete-103');
	}

	function testUrlShorteningWithNullDefaults1()
	{
		$route = new Zend_Controller_YARouter_Route('blog', '/blog/:year/:month/:day',
			array('controller' => 'blog', 'action' => 'showDate', 'month' => NULL, 'day' => NULL),
			array('year'=> '\d{4}', 'month' => '\d{1,2}', 'day' => '\d{1,2}')
		);

		$url = Zend_Uri::factory('http://localhost/');

		/* As month & day have defaults of NULL, the url is shortened to just the year */
		$route->generateUrl(array('year' => 2004), $url);
		$this->assertEquals($url->getUri(), 'http://localhost/blog/2004/');
	}

	function testUrlShorteningWithNullDefaults2()
	{
		$route = new Zend_Controller_YARouter_Route('blog', '/blog/:year/:month/:day',
				array('controller' => 'blog', 'action' => 'showDate', 'month' => NULL, 'day' => NULL),
				array('year'=> '\d{4}', 'month' => '\d{1,2}', 'day' => '\d{1,2}')
		);

		$url = Zend_Uri::factory('http://localhost/');

		/* As day has a default of NULL, the url is shortened to the year & month */
		$route->generateUrl(array('year' => 2004, 'month' => 5), $url);
		$this->assertEquals($url->getUri(), 'http://localhost/blog/2004/5/');
	}

	function testUrlShorteningWithNullDefaults3()
	{
		$route = new Zend_Controller_YARouter_Route('blog', '/blog/:year/:month/:day',
				array('controller' => 'blog', 'action' => 'showDate', 'month' => NULL, 'day' => NULL),
				array('year'=> '\d{4}', 'month' => '\d{1,2}', 'day' => '\d{1,2}')
		);

		$url = Zend_Uri::factory('http://localhost/');
		$route->generateUrl(array('year' => 2004, 'month' => 5, 'day' => 1), $url);
		$this->assertEquals($url->getUri(), 'http://localhost/blog/2004/5/1');
	}

	function testInvalidParameterForRoute()
	{
		$route = new Zend_Controller_YARouter_Route('blog', '/blog/:year/:month/:day',
				array('controller' => 'blog', 'action' => 'showDate', 'month' => NULL, 'day' => NULL),
				array('year' => '\d{4}', 'month' => '\d{1,2}', 'day' => '\d{1,2}')
		);

		$url = Zend_Uri::factory('http://localhost/');

		// Should not generate a Url, as month is invalid.
		try
		{
			$route->generateUrl(array('year' => 2004, 'month' => 'Jan', 'day' => 1), $url);
		}
		catch (Zend_Controller_Router_Exception $e)
		{
			$this->assertRegexp('/does not match route/i', $e->getMessage());
			return;
		}
		$this->fail('No exception was returned; expected Zend_Controller_Router_Exception');
	}

	function testMissingRequiredPathParameter()
	{
		$route = new Zend_Controller_YARouter_Route('blog', '/blog/:year/:month/:day',
				array('controller' => 'blog', 'action' => 'showDate'),
				array('year' => '\d{4}', 'month' => '\d{1,2}', 'day' => '\d{1,2}')
		);

		$url = Zend_Uri::factory('http://localhost/');

		// Should not generate a Url, as day is missing and without default.
		try
		{
			$route->generateUrl(array('year' => 2004, 'month' => 1), $url);
		}
		catch (Zend_Controller_Router_Exception $e)
		{
			$this->assertRegexp('/Missing required parameter/i', $e->getMessage());
			return;
		}
		$this->fail('No exception was returned; expected Zend_Controller_Router_Exception');
	}

	function testQueryStringParameterGeneration()
	{
		$route = new Zend_Controller_YARouter_Route('foo', '/path/:bar');
		$url = Zend_Uri::factory('http://localhost/');
		$route->generateUrl(array('bar' => 'foo', 'a' => 1, 'b' => 2, 'c' => 3), $url);

		$this->assertEquals($url->getUri(), 'http://localhost/path/foo?a=1&b=2&c=3');
	}

	function testDefaultQueryStringParameterRemoval()
	{
		$route = new Zend_Controller_YARouter_Route('foo', '/path/:bar',
													array('b' => 2));
		$url = Zend_Uri::factory('http://localhost/');
		$route->generateUrl(array('bar' => 'foo', 'a' => 1, 'b' => 2, 'c' => 3), $url);

		// As b is set at its default for this route, it shouldn't appear in the url.
		$this->assertEquals($url->getUri(), 'http://localhost/path/foo?a=1&c=3');
	}

	function testDefaultQueryStringParameterMatching()
	{
		$route = new Zend_Controller_YARouter_Route('foo', '/path/:bar',
													array('b' => 2));

		$url = Zend_Uri::factory('http://localhost/path/foo?a=1&c=3');
		$this->assertEquals($route->isMatch($url), array('bar' => 'foo', 'a' => '1', 'b' => 2, 'c' => '3'));
	}
}