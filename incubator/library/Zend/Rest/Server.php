<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Rest
 * @subpackage Server
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Zend_Server_Interface
 */
require_once 'Zend/Server/Interface.php';

/**
 * Zend_Server_Reflection
 */
require_once 'Zend/Server/Reflection.php';

/**
 * Zend_Rest_Server_Exception
 */
require_once 'Zend/Rest/Server/Exception.php';

/**
 * Zend_Server_Abstract
 */
require_once 'Zend/Server/Abstract.php';

/**
 * @category   Zend
 * @package    Zend_Rest
 * @subpackage Server
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Rest_Server extends Zend_Server_Abstract implements Zend_Server_Interface {
	/**
	 * @var Zend_Server_Reflection
	 */
	private $_reflection = null;
	
	/**
	 * Class Constructor Args
	 */
	private $_args = array();
	
	/**
	 * @var array An array of Zend_Server_Reflect_Method
	 */
	private $_functions = array();
	
	/**
	 * @var string Current Method
	 */
	private $_method;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		set_exception_handler(array($this, "fault"));
		$this->_reflection = new Zend_Server_Reflection();
	}

	/**
	 * Implement Zend_Server_Interface::handle()
	 *
	 * @param array $request
	 */
	public function handle($request = false)
	{
		if (!$request) {
			$request = $_REQUEST;
		}
		if (isset($request['method'])) {
			$this->_method = $request['method'];
			if (isset($this->_functions[$this->_method])) {
				if ($this->_functions[$this->_method] instanceof Zend_Server_Reflection_Function || $this->_functions[$this->_method] instanceof Zend_Server_Reflection_Method && $this->_functions[$this->_method]->isPublic()) {
					$request_keys = array_keys($request);
					array_walk($request_keys, array(__CLASS__, "lowerCase"));
					$request = array_combine($request_keys, $request);
					
					$func_args = $this->_functions[$this->_method]->getParameters();
					
					$calling_args = array();
					foreach ($func_args as $arg) {
						if (isset($request[strtolower($arg->getName())])) {
							$calling_args[] = $request[strtolower($arg->getName())];
						}
					}
					
					foreach ($request as $key => $value) {
						if (substr($key, 0, 3) == 'arg') {
							$key = str_replace('arg', '', $key);
							$calling_args[$key]= $value;
						}
					}
					
					if (sizeof($calling_args) < sizeof($func_args)) {
						throw new Zend_Rest_Server_Exception('Invalid Method Call to ' .$method. '. Requires ' .sizeof($func_args). ', ' .sizeof($calling_args). ' given.', 400);
					}
					
					if ($this->_functions[$this->_method] instanceof Zend_Server_Reflection_Method) {
						// Get class
		                $class = $this->_functions[$this->_method]->getDeclaringClass()->getName();
		
		                if ($this->_functions[$this->_method]->isStatic()) {
		                    // for some reason, invokeArgs() does not work the same as 
		                    // invoke(), and expects the first argument to be an object. 
		                    // So, using a callback if the method is static.
		                    $result = call_user_func_array(array($class, $this->_functions[$this->_method]->getName()), $calling_args);
		                }
		
		                // Object methods
		                try {
		                	if ($this->_functions[$this->_method]->getDeclaringClass()->getConstructor()) {
		                    	$object = $this->_functions[$this->_method]->getDeclaringClass()->newInstanceArgs($this->_args);
		                	} else {
		                		$object = $this->_functions[$this->_method]->getDeclaringClass()->newInstance();
		                	}
		                } catch (Exception $e) {
		                	echo $e->getMessage();
		                    throw new Zend_Rest_Server_Exception('Error instantiating class ' . $class . ' to invoke method ' . $this->_functions[$this->_method]->getName(), 500);
		                }
		                
		                $result = $this->_functions[$this->_method]->invokeArgs($object, $calling_args);
					} else {
						$result = call_user_func_array($this->_functions[$this->_method]->getName(), $calling_args); //$this->_functions[$this->_method]->invokeArgs($calling_args);
					}
					
					if (!headers_sent()) {
						header("Content-Type: text/xml");
					}
	                
					if ($result instanceof SimpleXMLElement) {
						echo $result->asXML();
					} elseif ($result instanceof DOMDocument) {
						echo $result->saveXML();
					} elseif ($result instanceof DOMNode) {
						echo $result->ownerDocument->saveXML($result);
					} elseif (is_array($result) || is_object($result)) {
						echo $this->_handleStruct($result);
					} else {
						echo $this->_handleScalar($result);
					}
				} else {
					throw new Zend_Rest_Server_Exception("Unknown Method '$this->_method'.", 404);
				}
			} else {
				throw new Zend_Rest_Server_Exception("Unknown Method '$this->_method'.", 404);
			}
		} else {
			throw new Zend_Rest_Server_Exception("No Method Specified.", 404);
		}
	}
	
	/**
	 * Implement Zend_Server_Interface::setClass()
	 *
	 * @param string $classname Class name
	 * @param string $namespace Class namespace (unused)
	 * @param array $argv An array of Constructor Arguments
	 */
	public function setClass($classname, $namespace = '', $argv = array())
	{
		$this->_args = $argv;
		foreach ($this->_reflection->reflectClass($classname, $argv)->getMethods() as $method) {
			$this->_functions[$method->getName()] = $method;
		}
	}
	
	/**
	 * Handle an array or object result
	 *
	 * @param array|object $struct Result Value
	 * @return string XML Response
	 */
	private function _handleStruct($struct)
	{
		$function = $this->_functions[$this->_method];
		if ($function instanceof Zend_Server_Reflection_Method) {
			$class = $function->getDeclaringClass()->getName();
		} else {
			$class = false;
		}
		
		$method = $function->getName();
		
		if ($class) {
			$xml = "<$class generator='zend' version='1.0'>";
			$xml .= "<$method>";
		} else {
			$xml = "<$method generator='zend'>";
		}
		
		$has_status = false;
		
		foreach ($struct as $key => $value) {
			if ($key == 'status') {
				$has_status = true;
			}
			if ($value === false) {
				$value = 0;
			} elseif ($value === true) {
				$value = 1;
			}
			
			if (ctype_digit((string) $key)) {
				$key = 'key_' . $key;
			}
			$xml .= "<$key>$value</$key>";
		}
		
		if (!$has_status) {
			$xml .= "<status>success</status>";
		}
		$xml .= "</$method>";
		
		if ($class) {
			$xml .= "</$class>";
		}
		return $xml;
	}
	
	/**
	 * Handle a single value
	 *
	 * @param string|int|boolean $value Result value
	 * @return string XML Response
	 */
	private function _handleScalar($value)
	{
		$function = $this->_functions[$this->_method];
		if ($function instanceof Zend_Server_Reflection_Method) {
			$class = $function->getDeclaringClass()->getName();
		} else {
			$class = false;
		}
		
		$method = $function->getName();
		
		if ($class) {
			$xml = "<$class generator='zend' version='1.0'>";
			$xml .= "<$method>";
		} else {
			$xml = "<$method generator='zend' version='1.0'>";
		}
		
		if ($value == false) {
			$value = 0;
		} elseif ($value === true) {
			$value = 1;
		}
		
		$xml .= "<response>$value</response>";

		$xml .= "<status>success</status>";

		$xml .= "</$method>";
		
		if ($class) {
			$xml .= "</$class>";
		}
		return $xml;
	}
	
	/**
	 * Implement Zend_Server_Interface::fault()
	 *
	 * @param string|Exception $fault Message
	 * @param int $code Error Code
	 */
	public function fault($exception = null, $code = null)
	{
		if (isset($this->_functions[$this->_method])) {
			$function = $this->_functions[$this->_method];
		} else {
			$function = $this->_method;
		}
		
		if ($function instanceof Zend_Server_Reflection_Method) {
			$class = $function->getDeclaringClass()->getName();
		} else {
			$class = false;
		}
		
		if ($function instanceof Zend_Server_Reflection_Function_Abstract) {
			$method = $function->getName();
		} else {
			$method = $function;
		}
		
		if ($class) {
			$xml = "<$class generator='zend' version='1.0'>";
			$xml .= "<$method>";
		} else {
			$xml = "<$method generator='zend' version='1.0'>";
		}
		
		$xml .= '<response>';
		
		if ($exception instanceof Exception) {
			$xml .= "<message>" .$exception->getMessage(). "</message>";
			$code = $exception->getCode();
		} elseif (!is_null($exception)) {
			$xml .= "<message>An unknown error occured. Please try again.</message>";
		} else {
			$xml .= "<message>Call to $method failed.</message>"; 
		}
		
		$xml .= '</response>';
		$xml .= "<status>failed</status>";
		$xml .= "</$method>";
		
		if ($class) {
			$xml .= "</$class>";
		}
		
		if (!headers_sent()) {
			if (is_null($code)) {
				header("HTTP/1.0 400 Bad Request");
			} else {
				if ($code == 404) {
					header("HTTP/1.0 $code File Not Found");
				} else {
					header("HTTP/1.0 $code Bad Request");
				}
			}
			
			header("Content-Type: text/xml");
		}
		
		echo $xml;
	}
	
	/**
	 * Implement Zend_Server_Interface::addFunction()
	 *
	 * @param string $function Function Name
	 * @param string $namespace Function namespace (unused)
	 */
	public function addFunction($function, $namespace = '')
	{
		if (!is_array($function)) {
			$function = (array) $function;
		}
		
		foreach ($function as $func) {
			if (is_callable($func) && !in_array($func, self::$magic_methods)) {
				$this->_functions[$func] = $this->_reflection->reflectFunction($func);
			} else {
				throw new Zend_Rest_Server_Exception("Invalid Method Added to Service.");
			}
		}
	}
	
	/**
	 * Implement Zend_Server_Interface::getFunctions()
	 *
	 * @return array An array of Zend_Server_Reflection_Method's
	 */
	public function getFunctions()
	{
		return $this->_functions;
	}
	
	/**
	 * Implement Zend_Server_Interface::loadFunctions()
	 *
	 * @todo Implement
	 * @param array $functions
	 */
	public function loadFunctions($functions)
	{
	}
	
	/**
	 * Implement Zend_Server_Interface::setPersistence()
	 * 
	 * @todo Implement
	 * @param int $mode
	 */
	public function setPersistence($mode)
	{
	}
}