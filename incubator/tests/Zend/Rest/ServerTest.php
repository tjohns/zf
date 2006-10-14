<?php
/**
 * @package Zend_Rest
 * @subpackage UnitTests
 */

/**
 * Zend_Rest_Server
 */
require_once 'Zend/Rest/Server.php';

/**
 * PHPUnit Test Case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Test cases for Zend_Rest_Server
 *
 * @package Zend_Rest
 * @subpackage UnitTests
 */
class Zend_Rest_ServerTest extends PHPUnit_Framework_TestCase 
{
	function testAddFunctionSimple()
	{
		$server = new Zend_Rest_Server();
		$server->addFunction('Zend_Rest_Server_TestFunc');
		$funcs = $server->getFunctions();
		$this->assertTrue(isset($funcs['Zend_Rest_Server_TestFunc']), "Function not registered.");
	}
	
	function testSetClass()
	{
		$server = new Zend_Rest_Server();
		$server->setClass('Zend_Rest_Server_Test');
		$funcs = $server->getFunctions();
		$this->assertTrue(isset($funcs['testFunc']), "Class Not Registered. testFunc not found");
		$this->assertTrue(isset($funcs['testFunc2']), "Class Not Registered. testFunc2 not found");
		$this->assertTrue(isset($funcs['testFunc3']), "Class Not Registered. testFunc3 not found");
	}
	
	function testHandleNamedArgFunction()
	{
		$server = new Zend_Rest_Server();
		$server->addFunction('Zend_Rest_Server_TestFunc');
		ob_start();
		$server->handle(array('method' => 'Zend_Rest_Server_TestFunc', 'who' => 'Davey'));
		$result = ob_get_clean();
		$this->assertEquals("<Zend_Rest_Server_TestFunc generator='zend' version='1.0'><response>Hello Davey</response><status>success</status></Zend_Rest_Server_TestFunc>", $result, 'Bad Result');
	}
	
	function testHandleFunctionNoArgs()
	{
		$server = new Zend_Rest_Server();
		$server->addFunction('Zend_Rest_Server_TestFunc2');
		ob_start();
		$server->handle(array('method' => 'Zend_Rest_Server_TestFunc2'));
		$result = ob_get_clean();
		$this->assertEquals("<Zend_Rest_Server_TestFunc2 generator='zend' version='1.0'><response>Hello World</response><status>success</status></Zend_Rest_Server_TestFunc2>", $result, 'Bad Result');
	}
	
	function testHandleAnonymousArgFunction()
	{
		$server = new Zend_Rest_Server();
		$server->addFunction('Zend_Rest_Server_TestFunc');
		ob_start();
		$server->handle(array('method' => 'Zend_Rest_Server_TestFunc', 'arg1' => 'Davey'));
		$result = ob_get_clean();
		$this->assertEquals("<Zend_Rest_Server_TestFunc generator='zend' version='1.0'><response>Hello Davey</response><status>success</status></Zend_Rest_Server_TestFunc>", $result, 'Bad Result');
	}
	
	function testHandleMultipleFunction()
	{
		
		$server = new Zend_Rest_Server();
		$server->addFunction('Zend_Rest_Server_TestFunc2');
		$server->addFunction('Zend_Rest_Server_TestFunc');
		ob_start();
		$server->handle(array('method' => 'Zend_Rest_Server_TestFunc2'));
		$result = ob_get_clean();
		$this->assertEquals("<Zend_Rest_Server_TestFunc2 generator='zend' version='1.0'><response>Hello World</response><status>success</status></Zend_Rest_Server_TestFunc2>", $result, 'Bad Result');
		ob_start();
		$server->handle(array('method' => 'Zend_Rest_Server_TestFunc', 'arg1' => 'Davey'));
		$result = ob_get_clean();
		$this->assertEquals("<Zend_Rest_Server_TestFunc generator='zend' version='1.0'><response>Hello Davey</response><status>success</status></Zend_Rest_Server_TestFunc>", $result, 'Bad Result');
	}
	
	function testHandleMethodNoArgs()
	{
		$server = new Zend_Rest_Server();
		$server->setClass('Zend_Rest_Server_Test');
		ob_start();
		$server->handle(array('method' => 'testFunc'));
		$result = ob_get_clean();
		$this->assertEquals("<Zend_Rest_Server_Test generator='zend' version='1.0'><testFunc><response>Hello World</response><status>success</status></testFunc></Zend_Rest_Server_Test>", $result, "Bad Result");
	}
	
	function testHandleAnonymousArgMethod()
	{
		$server = new Zend_Rest_Server();
		$server->setClass('Zend_Rest_Server_Test');
		ob_start();
		$server->handle(array('method' => 'testFunc2', 'arg1' => "Davey"));
		$result = ob_get_clean();
		$this->assertEquals("<Zend_Rest_Server_Test generator='zend' version='1.0'><testFunc2><response>Hello Davey</response><status>success</status></testFunc2></Zend_Rest_Server_Test>", $result, "Bad Result");
	}
	
	function testHandleNamedArgMethod()
	{
		$server = new Zend_Rest_Server();
		$server->setClass('Zend_Rest_Server_Test');
		ob_start();
		$server->handle(array('method' => 'testFunc3', 'who' => "Davey", 'when' => 'today'));
		$result = ob_get_clean();
		$this->assertEquals("<Zend_Rest_Server_Test generator='zend' version='1.0'><testFunc3><response>Hello Davey, How are you today</response><status>success</status></testFunc3></Zend_Rest_Server_Test>", $result, "Bad Result");
	}
	
	function testHandleStaticNoArgs()
	{
		$server = new Zend_Rest_Server();
		$server->setClass('Zend_Rest_Server_Test');
		ob_start();
		$server->handle(array('method' => 'testFunc4'));
		$result = ob_get_clean();
		$this->assertEquals("<Zend_Rest_Server_Test generator='zend' version='1.0'><testFunc4><response>Hello World</response><status>success</status></testFunc4></Zend_Rest_Server_Test>", $result, "Bad Result");
	}
	
	function testHandleAnonymousArgStatic()
	{
		$server = new Zend_Rest_Server();
		$server->setClass('Zend_Rest_Server_Test');
		ob_start();
		$server->handle(array('method' => 'testFunc5', 'arg1' => "Davey"));
		$result = ob_get_clean();
		$this->assertEquals("<Zend_Rest_Server_Test generator='zend' version='1.0'><testFunc5><response>Hello Davey</response><status>success</status></testFunc5></Zend_Rest_Server_Test>", $result, "Bad Result");
	}
	
	function testHandleNamedArgStatic()
	{
		$server = new Zend_Rest_Server();
		$server->setClass('Zend_Rest_Server_Test');
		ob_start();
		$server->handle(array('method' => 'testFunc6', 'who' => "Davey", 'when' => 'today'));
		$result = ob_get_clean();
		$this->assertEquals("<Zend_Rest_Server_Test generator='zend' version='1.0'><testFunc6><response>Hello Davey, How are you today</response><status>success</status></testFunc6></Zend_Rest_Server_Test>", $result, "Bad Result");
	}
	
	function testHandleMultipleAnonymousArgs()
	{
		$server = new Zend_Rest_Server();
		$server->addFunction('Zend_Rest_Server_TestFunc9');
		ob_start();
		$server->handle(array('method' => 'Zend_Rest_Server_TestFunc9', 'arg1' => "Hello", 'arg2' => "Davey"));
		$result = ob_get_clean();
		$this->assertEquals("<Zend_Rest_Server_TestFunc9 generator='zend' version='1.0'><response>Hello Davey</response><status>success</status></Zend_Rest_Server_TestFunc9>", $result, 'Bad Result');
	}
	
	function testHandleReturnFalse()
	{
		$server = new Zend_Rest_Server();
		$server->addFunction('Zend_Rest_Server_TestFunc3');
		ob_start();
		$server->handle(array('method' => 'Zend_Rest_Server_TestFunc3'));
		$result = ob_get_clean();
		$this->assertEquals("<Zend_Rest_Server_TestFunc3 generator='zend' version='1.0'><response>0</response><status>success</status></Zend_Rest_Server_TestFunc3>", $result, "Bas Response");
	}
	
	function testHandleReturnTrue()
	{
		$server = new Zend_Rest_Server();
		$server->addFunction('Zend_Rest_Server_TestFunc4');
		ob_start();
		$server->handle(array('method' => 'Zend_Rest_Server_TestFunc4'));
		$result = ob_get_clean();
		$this->assertEquals("<Zend_Rest_Server_TestFunc4 generator='zend' version='1.0'><response>1</response><status>success</status></Zend_Rest_Server_TestFunc4>", $result, "Bas Response");
	}
	
		
	function testHandleReturnInteger()
	{
		$server = new Zend_Rest_Server();
		$server->addFunction('Zend_Rest_Server_TestFunc5');
		ob_start();
		$server->handle(array('method' => 'Zend_Rest_Server_TestFunc5'));
		$result = ob_get_clean();
		$this->assertEquals("<Zend_Rest_Server_TestFunc5 generator='zend' version='1.0'><response>123</response><status>success</status></Zend_Rest_Server_TestFunc5>", $result, "Bas Response");
	}
	
	function testHandleReturnString()
	{
		$server = new Zend_Rest_Server();
		$server->addFunction('Zend_Rest_Server_TestFunc6');
		ob_start();
		$server->handle(array('method' => 'Zend_Rest_Server_TestFunc6'));
		$result = ob_get_clean();
		$this->assertEquals("<Zend_Rest_Server_TestFunc6 generator='zend' version='1.0'><response>string</response><status>success</status></Zend_Rest_Server_TestFunc6>", $result, "Bas Response");
	}
	
	function testHandleReturnArray()
	{
		$server = new Zend_Rest_Server();
		$server->addFunction('Zend_Rest_Server_TestFunc7');
		ob_start();
		$server->handle(array('method' => 'Zend_Rest_Server_TestFunc7'));
		$result = ob_get_clean();
		$this->assertEquals("<Zend_Rest_Server_TestFunc7 generator='zend'><foo>bar</foo><baz>1</baz><key_1>0</key_1><bat>123</bat><status>success</status></Zend_Rest_Server_TestFunc7>", $result, "Bas Response");
	}
	
	function testHandleReturnObject()
	{
		$server = new Zend_Rest_Server();
		$server->addFunction('Zend_Rest_Server_TestFunc8');
		ob_start();
		$server->handle(array('method' => 'Zend_Rest_Server_TestFunc8'));
		$result = ob_get_clean();
		$this->assertEquals("<Zend_Rest_Server_TestFunc8 generator='zend'><foo>bar</foo><baz>1</baz><bat>123</bat><qux>0</qux><status>success</status></Zend_Rest_Server_TestFunc8>", $result, "Bas Response");
	}
}

/* Test Functions */

/**
 * Test Function
 *
 * @param string $arg
 * @return string
 */
function Zend_Rest_Server_TestFunc($who) 
{
	return "Hello $who";
}

/**
 * Test Function 2
 */
function Zend_Rest_Server_TestFunc2()
{
	return "Hello World";
}

/**
 * Return false
 *
 * @return bool
 */
function Zend_Rest_Server_TestFunc3()
{
	return false;
}

/**
 * Return true
 *
 * @return bool
 */
function Zend_Rest_Server_TestFunc4()
{
	return true;
}

/**
 * Return integer
 *
 * @return int
 */
function Zend_Rest_Server_TestFunc5()
{
	return 123;
}

/**
 * Return string
 *
 * @return string
 */
function Zend_Rest_Server_TestFunc6()
{
	return "string";
}

/**
 * Return array
 *
 * @return array
 */
function Zend_Rest_Server_TestFunc7()
{
	return array('foo' => 'bar', 'baz' => true, 1 => false, 'bat' => 123);
}

/**
 * Return Object
 *
 * @return StdClass
 */
function Zend_Rest_Server_TestFunc8()
{
	$return = (object) array('foo' => 'bar', 'baz' => true, 'bat' => 123, 'qux' => false);
	return $return;
}

/**
 * Multiple Args
 * 
 * @param string $foo
 * @param string $bar
 * @return string
 */
function Zend_Rest_Server_TestFunc9($foo, $bar)
{
	return "$foo $bar";
}

/**
 * Test Class
 */
class Zend_Rest_Server_Test {
	/**
	 * Test Function
	 */
	function testFunc()
	{
		return "Hello World";
	}
	
	/**
	 * Test Function 2
	 * 
	 * @param string $who Some Arg
	 */
	function testFunc2($who)
	{
		return "Hello $who";
	}
	
	/**
	 * Test Function 3
	 * 
	 * @param string $who Some Arg
	 * @param int $when Some Arg2
	 */
	function testFunc3($who, $when)
	{
		return "Hello $who, How are you $when";
	}
	
	/**
	 * Test Function 4
	 */
	static function testFunc4()
	{
		return "Hello World";
	}
	
	/**
	 * Test Function 5
	 * 
	 * @param string $who Some Arg
	 */
	static function testFunc5($who)
	{
		return "Hello $who";
	}
	
	/**
	 * Test Function 6
	 * 
	 * @param string $who Some Arg
	 * @param int $when Some Arg2
	 */
	static function testFunc6($who, $when)
	{
		return "Hello $who, How are you $when";
	}
}