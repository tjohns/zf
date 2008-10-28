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
 * @package    Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Controller_Router_Route_Abstract */
require_once 'Zend/Controller/Router/Route/Abstract.php';

/**
 * Route
 *
 * @package    Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @see        http://manuals.rubyonrails.com/read/chapter/65
 */
class Zend_Controller_Router_Route extends Zend_Controller_Router_Route_Abstract
{

    protected $_urlVariable = ':';
    protected $_urlDelimiter = '/';
    protected $_regexDelimiter = '#';
    protected $_defaultRegex = '[^\/]+';

    /**
     * Holds names of all route's pattern variable names. Array index holds a position in URL.
     * @var array
     */
    protected $_variables = array();

    /**
     * Holds Route patterns for all URL parts. In case of a variable it stores it's regex
     * requirement or null. In case of a static part, it holds only it's direct value.
     * In case of a wildcard, it stores an asterisk (*)
     * @var array
     */
    protected $_parts = array();

    /**
     * Holds user submitted default values for route's variables. Name and value pairs.
     * @var array
     */
    protected $_defaults = array();

    /**
     * Holds user submitted regular expression patterns for route's variables' values.
     * Name and value pairs.
     * @var array
     */
    protected $_requirements = array();

    /**
     * Associative array filled on match() that holds matched path values
     * for given variable names.
     * @var array
     */
    protected $_values = array();

    /**
     * Associative array filled on match() that holds wildcard variable
     * names and values.
     * @var array
     */
    protected $_wildcardData = array();

    /**
     * Helper var that holds a count of route pattern's static parts
     * for validation
     * @var int
     */
    private $_catch = array();

    public function getVersion() {
        return 1;
    }
    
    /**
     * Instantiates route based on passed Zend_Config structure
     *
     * @param Zend_Config $config Configuration object
     */
    public static function getInstance(Zend_Config $config)
    {
        $reqs = ($config->reqs instanceof Zend_Config) ? $config->reqs->toArray() : array();
        $defs = ($config->defaults instanceof Zend_Config) ? $config->defaults->toArray() : array();
        return new self($config->route, $defs, $reqs);
    }

    /**
     * Prepares the route for mapping by splitting (exploding) it
     * to a corresponding atomic parts. These parts are assigned
     * a position which is later used for matching and preparing values.
     *
     * @param string $route Map used to match with later submitted URL path
     * @param array $defaults Defaults for map variables with keys as variable names
     * @param array $reqs Regular expression requirements for variables (keys as variable names)
     */
    public function __construct($route, $defaults = array(), $reqs = array())
    {

        $route = trim($route, $this->_urlDelimiter);
        $this->_defaults = (array) $defaults;
        $this->_requirements = (array) $reqs;
        $this->_regex = '';
        
        $delim = preg_quote($this->_urlDelimiter);

        if ($route != '') {
            
            $parts = preg_split('/(:\w+|\*)/i', $route, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
            $catch = 1;
            
            foreach ($parts as $pos => $part) {
                
                $optional = false;
                
                if (substr($part, 0, 1) == $this->_urlVariable) {

                    $name = substr($part, 1);
                    $this->_parts[$pos] = (isset($reqs[$name]) ? $reqs[$name] : $this->_defaultRegex);
                    $this->_variables[$pos] = $name;
                    $this->_catch[$name] = $catch++;
                    $regex = '(' . $this->_parts[$pos] . ')';
                    if (array_key_exists($name, $defaults)) {
                        $optional = true;
                        $regex .= '?';
                    }
                    
                } else {
                    
                    $this->_parts[$pos] = $part;

                    if ($part == '*') {
                        $optional = true;
                        $regex = '(.*)';
                        $this->_catch['*'] = $catch++; 
                    } else {
                        $regex = preg_quote($part);
                    }
                    
                }
                
                if ($optional) {
                    if (substr($this->_regex, -strlen($delim)) == $delim) {
                        $regex = '?' . $regex;   
                    }
                }

                $this->_regex .= $regex; 
                
            }

        }
        
    }

    /**
     * Matches a user submitted path with parts defined by a map. Assigns and
     * returns an array of variables on a successful match.
     *
     * @param string $path Path used to match against this routing map
     * @return array|false An array of assigned values or a false on a mismatch
     */
    public function match($path)
    {
        $values = array();
        $matches = null;

        $path = urldecode(trim($path, $this->_urlDelimiter));
        
        $res = preg_match($this->_regexDelimiter . '^' . $this->_regex . '$' . $this->_regexDelimiter . 'iu', $path, $matches);
        if (!$res) return false;
        
        foreach ($this->_catch as $name => $pos) {
            if (isset($matches[$pos]) && $name != '*' && $matches[$pos] != '') {
                $values[$name] = $matches[$pos];
            }
        }

        if (isset($this->_catch['*'])) {
            $innerPath = explode($this->_urlDelimiter, $matches[$this->_catch['*']]);
            $count = count($innerPath);
            for($i = 0; $i < $count; $i+=2) {
                $var = $innerPath[$i];
                if (!isset($this->_wildcardData[$var]) && !isset($this->_defaults[$var]) && !isset($values[$var])) {
                    $this->_wildcardData[$var] = (isset($innerPath[$i+1])) ? $innerPath[$i+1] : null;
                }
            }
        }
        
        $return = $values + $this->_wildcardData + $this->_defaults;

        // Check if all map variables have been initialized
        foreach ($this->_variables as $var) {
            if (!array_key_exists($var, $return)) {
                return false;
            }
        }

        // var_dump($return);
        
        $this->_values = $values;
        
        return $return;

    }

    /**
     * Assembles user submitted parameters forming a URL path defined by this route
     *
     * @param  array $data An array of variable and value pairs used as parameters
     * @param  boolean $reset Whether or not to set route defaults with those provided in $data
     * @return string Route path with user submitted parameters
     */
    public function assemble($data = array(), $reset = false, $encode = false)
    {

        $url = array();
        $flag = false;

        foreach ($this->_parts as $key => $part) {

            $name = isset($this->_variables[$key]) ? $this->_variables[$key] : null;

            $useDefault = false;
            if (isset($name) && array_key_exists($name, $data) && $data[$name] === null) {
                $useDefault = true;
            }

            if (isset($name)) {

                if (isset($data[$name]) && !$useDefault) {
                    $url[$key] = $data[$name];
                    unset($data[$name]);
                } elseif (!$reset && !$useDefault && isset($this->_values[$name])) {
                    $url[$key] = $this->_values[$name];
                } elseif (isset($this->_defaults[$name])) {
                    $url[$key] = $this->_defaults[$name];
                } else {
                    require_once 'Zend/Controller/Router/Exception.php';
                    throw new Zend_Controller_Router_Exception($name . ' is not specified');
                }

                if ($encode) $url[$key] = urlencode($url[$key]);

            } elseif ($part != '*') {
                $url[$key] = $part;
            } else {
                if (!$reset) $data += $this->_wildcardData;
                foreach ($data as $var => $value) {
                    if ($value !== null) {
                        $url[$key++] = $var . $this->_urlDelimiter;
                        if ($encode) $value = urlencode($value);
                        $url[$key++] = $value . $this->_urlDelimiter;
                        $flag = true;
                    }
                }
            }

        }

        $return = '';

        foreach (array_reverse($url, true) as $key => $value) {
            if ($flag 
                || ($value != $this->_urlDelimiter && !isset($this->_variables[$key])) 
                || (isset($this->_variables[$key]) && $value !== $this->getDefault($this->_variables[$key]))
               ) 
            {
                $return = $value . $return;
                $flag = true;
            }
        }

        return trim($return, $this->_urlDelimiter);

    }

    /**
     * Return a single parameter of route's defaults
     *
     * @param string $name Array key of the parameter
     * @return string Previously set default
     */
    public function getDefault($name) {
        if (isset($this->_defaults[$name])) {
            return $this->_defaults[$name];
        }
        return null;
    }

    /**
     * Return an array of defaults
     *
     * @return array Route defaults
     */
    public function getDefaults() {
        return $this->_defaults;
    }

}
