<?php

require_once 'Zend/Uri.php';

class Zend_Controller_Router_Exception extends Exception
{
}

class Zend_Controller_YARouter_Route
{
	protected $name;
	protected $pattern;
	protected $defaults;
	protected $requirements;

	private $_parts;
	private $_regexp;

	const RX_DELIMITER = "#";

	const DYNAMIC_INTRODUCER = ':';
	const RX_DYNAMIC_REQUIREMENT = '(?:[-a-zA-Z0-9_.!~*\'():@&=+$,;~]|%[0-9a-fA-F]{2})+';

	const WILDCARD_INTRODUCER = '*';
	const RX_WILDCARD_REQUIREMENT = '.*';

	const RX_NAME = '[a-zA-Z_][a-zA-Z_0-9-]*';

	public function __construct($name, $pattern, $defaults = array(), $requirements = array())
	{
		$this->name = $name;
		$this->pattern = $pattern;
		$this->defaults = $defaults;
		$this->requirements = $requirements;
	}

	protected function getParts()
	{
		if (!isset($this->_parts))
		{
			// Returns an array, with a repeating pattern
			// static, type (dynamic or wildcard character), name , ending a static
			$this->_parts = preg_split(self::RX_DELIMITER.'(['.self::WILDCARD_INTRODUCER.self::DYNAMIC_INTRODUCER.'])'.
											'('.self::RX_NAME.'|\('.self::RX_NAME.'\))'.self::RX_DELIMITER,
						$this->pattern,
						-1,
						PREG_SPLIT_DELIM_CAPTURE);
			// Remove any parenthesis around names
			for($i = 2; $i < count($this->_parts); $i += 3)
				$this->_parts[$i] = trim($this->_parts[$i], '()');
		}
		return $this->_parts;
	}

	protected function getRegularExpression()
	{
		if (!isset($this->_regexp))
		{
			$parts = $this->getParts();
			$n = count($parts);

			$rx = preg_quote($parts[0], self::RX_DELIMITER);
			$rxEnd = '';
			for($i = 1; $i < $n; $i += 3)
			{
				$type = $parts[$i];
				$name = $parts[$i + 1];
				$static = $parts[$i + 2];
				switch ($type)
				{
					case self::DYNAMIC_INTRODUCER:
						$requirement = isset($this->requirements[$name])
							? $this->requirements[$name] : self::RX_DYNAMIC_REQUIREMENT;

						if (array_key_exists($name, $this->defaults))
						{
							// Optional item, means remaining parts of the become optional also
							if (!empty($rx) && $rx[strlen($rx) - 1] == '/')
								$rx = rtrim($rx, '/').'(?:/|/';
							else
								$rx .= '(?:';

							$rxEnd .= ')?';
						}
						break;

					case self::WILDCARD_INTRODUCER:
						$requirement = self::RX_WILDCARD_REQUIREMENT;
						break;
				}
				$rx .= '(?P<'.$name.'>'.$requirement.')'.preg_quote($static, self::RX_DELIMITER);
			}
			if (!empty($rx))
				$rx .= $rx[strlen($rx) - 1] == '/' ? '?' : '/?';

			$this->_regexp = self::RX_DELIMITER.'^'.$rx.$rxEnd.'$'.self::RX_DELIMITER;
		}
		return $this->_regexp;
	}

	public function getName() { return $this->name; }

	public function isMatch(Zend_Uri_Http $url)
	{
		if (preg_match($this->getRegularExpression(), $url->getPath(), $params))
		{
			// Remove all integer indexed values.
			foreach($params as $index => $value)
				if (is_int($index))
					unset($params[$index]);

			/**
			 *	Zend_Uri_Http is lacking an getter for the query string as an array
			*/
			$queryParams = array();
			if ($url->getQuery() !== false)
				parse_str($url->getQuery(), $queryParams);

			return array_merge($this->defaults, $queryParams, $params);
		}
		return false;
	}

	private function encode($type, $string)
	{
		$r = urlencode($string);
		if ($type == self::WILDCARD_INTRODUCER)
			$r = str_replace('%2F', '/', $r);
		return $r;
	}

	public function generateUrl($parameters, Zend_Uri_Http $url)
	{
		$parts = $this->getParts();
		$n = count($parts);

		$path = $parts[0];
		for($i = 1; $i < $n; $i += 3)
		{
			$type = $parts[$i];
			$name = $parts[$i + 1];
			$static = $parts[$i + 2];

			if (isset($parameters[$name]))
			{
				$path .= $this->encode($type, $parameters[$name]);
				unset($parameters[$name]);
			}
			else if (array_key_exists($name, $this->defaults))
			{
				if (is_null($this->defaults[$name]))
				{
					// Remove rest of params so they dont appear in querystring
					for(; $i < $n; $i += 3)
						unset($parameters[$parts[$i + 1]]);
					break;
				}
				else
				{
					$path .= $this->encode($type, $this->defaults[$name]);
				}
			}
			else
			{
				throw new Zend_Controller_Router_Exception("Missing required parameter $name");
			}
			$path .= $static;
		}

		// Validate the path (and therefore parameters used so far) for this route
		if (0 == preg_match($this->getRegularExpression(), $path))
		{
			throw new Zend_Controller_Router_Exception("$path does not match route {$this->getName()} {$this->getRegularExpression()}");
		}

		// Remove parameters that are at their defaults for this route
		if (!empty($this->defaults))
		{
			$parameters = array_diff_assoc($parameters, $this->defaults);
		}

		$url->setPath($path);
		$url->setQueryArray($parameters);

		return $url;
	}
}

class Zend_Controller_YARouter
{
	protected $basePath;
	protected $routes;

	function __construct($basePath = '/')
	{
		$this->basePath = rtrim($basePath, '/').'/';
		$this->routes = array();
	}

	public function connect($name, $route, $defaults = array(), $requirements = array())
	{
		if (isset($this->routes[$name]))
			throw new Zend_Controller_Router_Exception("Route '$name' already exists");
		return $this->routes[$name] = new Zend_Controller_YARouter_Route($name, $this->basePath.ltrim($route, '/'), $defaults, $requirements);
	}

	public function getRoute($name)
	{
		if (!isset($this->routes[$name]))
			throw new Zend_Controller_Router_Exception("Unknown route $name");
		return $this->routes[$name];
	}

	public function getStaticUrl($path)
	{
		return $this->basePath.ltrim($path, '/');
	}

	public function route(Zend_Controller_Dispatcher_Interface $dispatcher, Zend_Uri_Http $url)
	{
		$params = false;
		foreach($this->routes as $route)
		{
			$params = $route->isMatch($url);
			if ($params !== false)
				break;
		}
		if ($params)
		{
			$controller = 'index';
			$action = 'index';

			if (isset($params['controller']) && strlen($params['controller']))
			{
				$controller = $params['controller'];
				if (isset($params['action']))
				{
					$action = $params['action'];
				}
			}
			unset($params['controller'], $params['action']);

			$token = new Zend_Controller_Dispatcher_Token($controller, $action, $params);
			if ($dispatcher->isDispatchable($token))
			{
				return $token;
			}
			else
			{
				throw new Zend_Controller_Router_Exception('Request could not be mapped to a dispatchable route.');
			}
		}
		else
		{
			throw new Zend_Controller_Router_Exception('Request could not be mapped to a route.');
		}
	}
}