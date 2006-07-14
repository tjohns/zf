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
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */

/** Zend_Controller_Router_Exception */
require_once 'Zend/Controller/Router/Route/Interface.php';

/**
 * Route
 *
 * @package    Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 * @see        http://manuals.rubyonrails.com/read/chapter/65
 */
class Zend_Controller_Router_Route implements Zend_Controller_Router_Route_Interface
{

    const URL_VARIABLE = ':';
    const REGEX_DELIMITER = '#';
    
    // TODO: Support for reserved URI characters (per RFC 3986). 
    // All unreserved characters are already supported.
    // http://en.wikipedia.org/wiki/URL_encoding
    const DEFAULT_REGEX = '[a-z0-9\-\._~%]+';

    protected $_parts;
    protected $_defaults = array();
    protected $_requirements = array();
    protected $_staticCount = 0;
    protected $_vars = array();
    protected $_values = null;

    /**
     * Prepares the route for mapping by splitting (exploding) it 
     * to a corresponding atomic parts. These parts are assigned 
     * a position which is later used for matching and preparing values.  
     *
     * @param string Map used to match with later submitted URL path 
     * @param array Defaults for map variables with keys as variable names
     * @param array Regular expression requirements for variables (keys as variable names)
     */
    public function __construct($route, $defaults = array(), $reqs = array())
    {

        $route = trim($route, '/');
        $this->_defaults = (array) $defaults;
        $this->_requirements = (array) $reqs;

        foreach (explode('/', $route) as $pos => $part) {

            if (substr($part, 0, 1) == self::URL_VARIABLE) {
                $name = substr($part, 1);
                $regex = (isset($reqs[$name]) ? $reqs[$name] : self::DEFAULT_REGEX);
                $this->_parts[$pos] = array('name' => $name, 'regex' => $regex);
                $this->_vars[] = $name;
            } else {
                $this->_parts[$pos] = array('regex' => preg_quote($part, self::REGEX_DELIMITER));
                if ($part != '*') $this->_staticCount++;
            }

        }

    }

    /**
     * Matches a user submitted path with parts defined by a map. Assigns and 
     * returns an array of variables on a succesfull match.  
     *
     * @param string Path used to match against this routing map 
     * @return array|false An array of assigned values or a false on a mismatch
     */
    public function match($path)
    {

        $pathStaticCount = 0;
        $values = $this->_defaults;
        $capture = false; 

        $path = explode('/', trim($path,'/'));
    
        foreach ($path as $pos => $pathPart) {
            
            if (!$capture && !isset($this->_parts[$pos])) return false;
            
            if (!$capture && $this->_parts[$pos]['regex'] == '\*') {
                $capture = true;
                $var = null;
            }
            
            if ($capture === true) {
                // Wildcard found. Capturing variable and value pairs
            	
                if (is_null($var)) {
                    $var = $pathPart;
                    if (!array_key_exists($var, $values)) 
                        $values[$pathPart] = null;
                } else {
                    if (is_null($values[$var])) 
                        $values[$var] = $pathPart;
                    $var = null;	
                }
                
            } else {
                
                $part = $this->_parts[$pos];
    
                $name = isset($part['name']) ? $part['name'] : null;
                $regex = self::REGEX_DELIMITER . '^'.$part['regex'].'$' . self::REGEX_DELIMITER . 'i';
    
                if (!preg_match($regex, $pathPart)) return false;
                
                if ($name !== null) {
                    // It's a variable. Setting a value
                    $values[$name] = $pathPart;
                } else {
                    $pathStaticCount++;
                }
                    
            }

        }

        // Check if all static mappings have been met
        if ($this->_staticCount != $pathStaticCount) return false;
        
        // Check if all map variables have been initialized
        foreach ($this->_vars as $var) {
            if (!array_key_exists($var, $values)) return false; 
        }

        $this->_values = $values;
        return $values;

    }

    /**
     * Assembles user submitted parameters forming a URL path defined by this route 
     *
     * @param array An array of variable and value pairs used as parameters 
     * @return string Route path with user submitted parameters
     */
    public function assemble($data = array())
    {

        $url = array();

        foreach ($this->_parts as $key => $part) {
            
            if (isset($part['name'])) {

                if (isset($data[$part['name']])) {
                    $url[$key] = $data[$part['name']];
                    unset($data[$part['name']]);
                } elseif (isset($this->_values[$part['name']])) {
                    $url[$key] = $this->_values[$part['name']];
                } elseif (isset($this->_defaults[$part['name']])) {
                    $url[$key] = $this->_defaults[$part['name']];
                } else
                    throw new Zend_Controller_Router_Exception($part['name'] . ' is not specified');

            } else {
                
                if ($part['regex'] != '\*') {
                    $url[$key] = $part['regex'];
                } else {
                    foreach ($data as $var => $value) {
                        $url[$var] = $var . '/' . $value;
                    } 
                }

            }
            
        }

        return implode('/', $url);

    }

}

?>
