<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to version 1.0 of the Zend Framework
 * license, that is bundled with this package in the file LICENSE, and
 * is available through the world-wide-web at the following URL:
 * http://www.zend.com/license/framework/1_0.txt. If you did not receive
 * a copy of the Zend Framework license and are unable to obtain it
 * through the world-wide-web, please send a note to license@zend.com
 * so we can mail you a copy immediately.
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
 * @author     Michael Minicki aka Martel Valgoerad (martel@post.pl)
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 * @see        http://manuals.rubyonrails.com/read/chapter/65
 */
class Zend_Controller_Router_Route implements Zend_Controller_Router_Route_Interface
{

    const URL_VARIABLE = ':';
    const REGEX_DELIMITER = '#';
    const DEFAULT_REGEX = '[a-z0-9\-_]+';

    private $_parts;
    private $_defaults = array();
    private $_requirements = array();

    public function __construct($route, $defaults = array(), $reqs = array())
    {

        $route = trim($route, '/');
        if (is_array($defaults)) $this->_defaults = array_merge($this->_defaults, $defaults);
        if (is_array($reqs)) $this->_requirements = array_merge($this->_requirements, $reqs);

        foreach (explode('/', $route) as $pos => $part) {

            if (self::URL_VARIABLE == $part[0]) {
                $name = substr($part, 1);
                $regex = (isset($reqs[$name]) ? $reqs[$name] : self::DEFAULT_REGEX);
                $this->_parts[$pos] = array('name' => $name, 'regex' => $regex);
            } else {
                $this->_parts[$pos] = array('regex' => preg_quote($part, self::REGEX_DELIMITER));
            }

        }

    }

    public function match($path)
    {

        $values = $this->_defaults;

        $path = explode('/', trim($path,'/'));

        foreach ($this->_parts as $pos => $part) {

            $name = isset($part['name']) ? $part['name'] : null;
            $regex = self::REGEX_DELIMITER . '^'.$part['regex'].'$' . self::REGEX_DELIMITER . 'i';

            if (!empty($path[$pos]) && preg_match($regex, $path[$pos])) {

                if ($name !== null) {
                    $values[$name] = $path[$pos];
                }

            } elseif ($name !== null && isset($this->_defaults[$name])) {
                continue;
            } else return false;

        }

        return $values;

    }

    public function assemble($data = array())
    {

        $url = array();

        foreach ($this->_parts as $key => $part) {
            
            if (isset($part['name'])) {

                if (isset($data[$part['name']])) {
                    $url[$key] = $data[$part['name']];
                } elseif (isset($this->_defaults[$part['name']])) {
                    $url[$key] = $this->_defaults[$part['name']];
                } else
                    throw new Zend_Controller_Router_Exception($part['name'] . ' is not specified');

            } else {
                $url[$key] = $part['regex'];
            }
            
        }

        return implode('/', $url);

    }

}

?>
