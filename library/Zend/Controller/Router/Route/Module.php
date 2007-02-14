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
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id$
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */

/** Zend_Controller_Router_Exception */
require_once 'Zend/Controller/Router/Exception.php';

/** Zend_Controller_Router_Route_Interface */
require_once 'Zend/Controller/Router/Route/Interface.php';

/**
 * Module Route
 *
 * Default route for module functionality
 *
 * @package    Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 * @see        http://manuals.rubyonrails.com/read/chapter/65
 */
class Zend_Controller_Router_Route_Module implements Zend_Controller_Router_Route_Interface
{
    /**
     * @const string URI delimiter
     */
    const URI_DELIMITER = '/';
    
    /**
     * Default values for module, controller, and action
     * @var array
     */
    protected $_defaults;

    /**
     * Front controller instance
     * @var Zend_Controller_Front
     */
    protected $_frontController;

    /**#@+
     * Array keys to use for module, controller, and action
     * @var string
     */
    protected $_moduleKey     = 'module';
    protected $_controllerKey = 'controller';
    protected $_actionKey     = 'action';
    /**#@-*/

    /**
     * Request object, if any registered
     * @var Zend_Controller_Request_Abstract
     */
    protected $_request;

    /**
     * Get front controller instance
     * 
     * @return Zend_Controller_Front
     */
    public function getFrontController()
    {
        if (null == $this->_frontController) {
            require_once 'Zend/Controller/Front.php';
            $this->_frontController = Zend_Controller_Front::getInstance();
        }

        return $this->_frontController;
    }

    /**
     * Constructor
     *
     * Determines the current module, action, and controller keys, and uses 
     * them to set defaults.
     *
     * @param array Defaults for map variables with keys as variable names
     */
    public function __construct(array $defaults = array())
    {
        $request = $this->getFrontController()->getRequest();
        if (null !== $request) {
            $this->_request       = $request;
            $this->_moduleKey     = $request->getModuleKey();
            $this->_controllerKey = $request->getControllerKey();
            $this->_actionKey     = $request->getActionKey();
        }

        $this->_defaults = array($this->_moduleKey => 'default');
        $this->_defaults = array_merge($this->_defaults, $defaults);
    }

    /**
     * Is a module valid?
     * 
     * @param string $module 
     * @return boolean
     */
    public function isValidModule($module)
    {
        require_once 'Zend/Controller/Front.php';
        $controllerDirs = $this->getFrontController()->getControllerDirectory();
        return isset($controllerDirs[$module]);
    }

    /**
     * Matches a user submitted path with parts defined by a map. Assigns and 
     * returns an array of variables on a successful match.  
     *
     * If a request object is registered, it uses its setModuleName(), 
     * setControllerName(), and setActionName() accessors to set those values. 
     * Always returns the values as an array.
     *
     * @param string Path used to match against this routing map 
     * @return array An array of assigned values or a false on a mismatch
     */
    public function match($path)
    {
        $values = array();
        $params = array();
        $path   = trim($path, self::URI_DELIMITER);

        if ($path != '') {
            $path = explode(self::URI_DELIMITER, $path);
        
            // Module
            if (count($path) && $this->isValidModule($path[0])) {
                $values[$this->_moduleKey] = array_shift($path);
            } elseif ('default' != $this->_defaults[$this->_moduleKey]
                && $this->isValidModule($this->_defaults[$this->_moduleKey]))
            {
                $values[$this->_moduleKey] = $this->_defaults[$this->_moduleKey];
            }

            // Controller
            if (count($path) && !empty($path[0])) {
                $values[$this->_controllerKey] = array_shift($path);
            } elseif (isset($this->_defaults[$this->_controllerKey])) {
                $values[$this->_controllerKey] = $this->_defaults[$this->_controllerKey];
            }

            // Action
            if (count($path) && !empty($path[0])) {
                $values[$this->_actionKey] = array_shift($path);
            } elseif (isset($this->_defaults[$this->_actionKey])) {
                $values[$this->_actionKey] = $this->_defaults[$this->_actionKey];
            }

            // Path info
            $numSegs = count($path);
            if ($numSegs) {
                for ($i = 0; $i < $numSegs; $i = $i + 2) {
                    $key = urldecode($path[$i]);
                    $val = isset($path[$i + 1]) ? urldecode($path[$i + 1]) : null;
                    $params[$key] = $val;
                }
            }
        }
        
        if (null !== $this->_request) {
            if (isset($values[$this->_moduleKey])) {
                $this->_request->setModuleName($values[$this->_moduleKey]);
            }
            if (isset($values[$this->_controllerKey])) {
                $this->_request->setControllerName($values[$this->_controllerKey]);
            }
            if (isset($values[$this->_actionKey])) {
                $this->_request->setActionName($values[$this->_actionKey]);
            }
        } 

        $values = array_merge($params, $values);

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

        if (isset($data[$this->_moduleKey]) && $this->isValidModule($data[$this->_moduleKey])) {
            $url[] = $data[$this->_moduleKey];
            unset($data[$this->_moduleKey]);
        }

        if (isset($data[$this->_controllerKey])) {
            $url[] = $data[$this->_controllerKey];
            unset($data[$this->_controllerKey]);

            if (isset($data[$this->_actionKey])) {
                $url[] = $data[$this->_actionKey];
                unset($data[$this->_actionKey]);

                foreach ($data as $key => $value) {
                    $url[] = $key;
                    $url[] = $value;
                }
            }
        }

        return implode(self::URI_DELIMITER, $url);
    }
}
