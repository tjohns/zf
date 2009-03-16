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
 * @package    Zend_Controller
 * @subpackage Dispatcher
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Controller_Dispatcher_Interface */
require_once 'Zend/Controller/Dispatcher/Interface.php';

/** Zend_Controller_Request_Abstract */
require_once 'Zend/Controller/Request/Abstract.php';

/** Zend_Controller_Response_Abstract */
require_once 'Zend/Controller/Response/Abstract.php';

/** Zend_Controller_Front */
require_once 'Zend/Controller/Front.php';

/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Dispatcher
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Controller_Dispatcher_Abstract implements Zend_Controller_Dispatcher_Interface
{
    /**
     * Default action name
     * @var string
     */
    protected $_defaultAction = 'index';

    /**
     * Default controller name
     * @var string
     */
    protected $_defaultController = 'index';

    /**
     * Default module name
     * @var string
     */
    protected $_defaultModule = 'default';
    
    /**
     * Default error action name
     * @var string
     */
    protected $_errorAction = 'error';

    /**
     * Default error controller name
     * @var string
     */
    protected $_errorController = 'index';

    /**
     * Default error module name
     * @var string
     */
    protected $_errorModule = 'default';
    
    /**
     * Front Controller instance
     * @var Zend_Controller_Front
     */
    protected $_frontController;
    
    /**
     * Array of invocation parameters to use when instantiating action
     * controllers
     * @var array
     */
    protected $_invokeParams = array();
    
    /**
     * Path delimiter character
     * @var string
     */
    protected $_pathDelimiter = '_';
    
    /**
     * Word delimiter characters
     * @var array
     */
    protected $_wordDelimiter = array('-', '.');

    /**
     * Response object to pass to action controllers, if any
     * @var Zend_Controller_Response_Abstract|null
     */
    protected $_response = null;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct(array $params = array())
    {
        $this->setParams($params);
    }

    /**
     * Formats a string into a module name. This is used to take a raw
     * module name, such as one stored inside a Zend_Controller_Request_Abstract
     * object.
     *
     * @param string $unformatted
     * @return string
     */
    public function formatModuleName($unformatted)
    {
        return ucfirst($this->_formatName($unformatted));
    }
    
    /**
     * Formats a string into a controller name.  This is used to take a raw
     * controller name, such as one stored inside a Zend_Controller_Request_Abstract
     * object, and reformat it to a proper class name that a class extending
     * Zend_Controller_Action would use.
     *
     * @param string $unformatted
     * @return string
     */
    public function formatControllerName($unformatted)
    {
        $segments = explode($this->getPathDelimiter(), $unformatted);

        foreach ($segments as $key => $segment) {
            $segments[$key] = $this->_formatName($segment);
        }
        
        return implode('_', $segments) . 'Controller';
    }

    
    /**
     * Formats a string into an action name.  This is used to take a raw
     * action name, such as one that would be stored inside a Zend_Controller_Request_Abstract
     * object, and reformat into a proper method name that would be found
     * inside a class extending Zend_Controller_Action.
     *
     * @param string $unformatted
     * @return string
     */
    public function formatActionName($unformatted)
    {
        return $this->_formatName($unformatted, true) . 'Action';
    }

    protected function _formatName($name, $isAction = false)
    {
        $name = str_replace($this->getWordDelimiter(), ' ', strtolower($name));
        $name = preg_replace('/[^a-z0-9 ]/', '', $name);
        $name = str_replace(' ', '', ucwords($name));

        if ($isAction && strlen($name)) {
            $name[0] = strtolower($name[0]); 
        }
        
        return $name;
    }
    
    /**
     * Retrieve front controller instance
     *
     * @return Zend_Controller_Front
     */
    public function getFrontController()
    {
        if (null === $this->_frontController) {
            require_once 'Zend/Controller/Dispatcher/Exception.php';
            throw new Zend_Controller_Dispatcher_Exception('Front controller is not avalable yet');
        }

        return $this->_frontController;
    }

    /**
     * Set front controller instance
     *
     * @param Zend_Controller_Front $controller
     * @return Zend_Controller_Dispatcher_Abstract
     */
    public function setFrontController(Zend_Controller_Front $controller)
    {
        $this->_frontController = $controller;
        return $this;
    }

    /**
     * Set the default module name
     *
     * @param string $module
     * @return Zend_Controller_Dispatcher_Abstract
     */
    public function setDefaultModuleName($name)
    {
        $this->_defaultModule = (string) $name;
        return $this;
    }

    /**
     * Retrieve the default module
     *
     * @return string
     */
        public function getDefaultModuleName()
    {
        return $this->_defaultModule;
    }

    /**
     * Set the default controller name
     *
     * @param string $module
     * @return Zend_Controller_Dispatcher_Abstract
     */
    public function setDefaultControllerName($name)
    {
        $this->_defaultController = (string) $name;
        return $this;
    }

    /**
     * Retrieve the default controller name
     *
     * @return string
     */
    public function getDefaultControllerName()
    {
        return $this->_defaultController;
    }
    
    /**
     * Set the default action name
     *
     * @param string $module
     * @return Zend_Controller_Dispatcher_Abstract
     */
    public function setDefaultActionName($name)
    {
        $this->_defaultAction = (string) $name;
        return $this;
    }

    /**
     * Retrieve the default action name
     *
     * @return string
     */
    public function getDefaultActionName()
    {
        return $this->_defaultAction;
    }

    /* ----- Deprecated methods (BC Compatible) ----- */
    
    public function setDefaultModule($name)
    {
        return $this->setDefaultModuleName($name);
    }

    public function getDefaultModule()
    {
        return $this->getDefaultModuleName();
    }

    public function setDefaultAction($name)
    {
        return $this->setDefaultActionName($name);
    }

    public function getDefaultAction()
    {
        return $this->getDefaultActionName();
    }
    
    /**
     * Set response object to pass to action controllers
     * 
     * May be set without storing in Dispatcher. Actually used 
     * in Front::dispatch() and Dispatcher Interface. 
     *
     * @deprecated
     * @param Zend_Controller_Response_Abstract $response
     * @return unknown
     * @todo Remove this method
     */
    public function setResponse(Zend_Controller_Response_Abstract $response = null) 
    {
        $this->_response = $response;
        return $this;
    }
    
    /**
     * Return the registered response object
     *
     * @return Zend_Controller_Response_Abstract|null
     */
    public function getResponse()
    {
        return $this->_response;
    }
    
    /**
     * Verify delimiter
     *
     * Verify a delimiter to use in controllers or actions. May be a single
     * string or an array of strings.
     *
     * @param string|array $spec
     * @return array
     * @throws Zend_Controller_Dispatcher_Exception with invalid delimiters
     */
    public function _verifyDelimiter($spec)
    {
        if (is_string($spec)) {
            return (array) $spec;
        } elseif (is_array($spec)) {
            $allStrings = true;
            foreach ($spec as $delim) {
                if (!is_string($delim)) {
                    $allStrings = false;
                    break;
                }
            }

            if (!$allStrings) {
                require_once 'Zend/Controller/Dispatcher/Exception.php';
                throw new Zend_Controller_Dispatcher_Exception('Word delimiter array must contain only strings');
            }

            return $spec;
        }

        require_once 'Zend/Controller/Dispatcher/Exception.php';
        throw new Zend_Controller_Dispatcher_Exception('Invalid word delimiter');
    }

    /**
     * Retrieve the word delimiter character(s) used in
     * controller or action names
     *
     * @return array
     */
    public function getWordDelimiter()
    {
        return $this->_wordDelimiter;
    }

    /**
     * Set word delimiter
     *
     * Set the word delimiter to use in controllers and actions. May be a
     * single string or an array of strings.
     *
     * @param string|array $spec
     * @return Zend_Controller_Dispatcher_Abstract
     */
    public function setWordDelimiter($spec)
    {
        $spec = $this->_verifyDelimiter($spec);
        $this->_wordDelimiter = $spec;

        return $this;
    }

    /**
     * Retrieve the path delimiter character(s) used in
     * controller names
     *
     * @return array
     */
    public function getPathDelimiter()
    {
        return $this->_pathDelimiter;
    }

    /**
     * Set path delimiter
     *
     * Set the path delimiter to use in controllers. May be a single string or
     * an array of strings.
     *
     * @param string|array $spec
     * @return Zend_Controller_Dispatcher_Abstract
     */
    public function setPathDelimiter($spec)
    {
        if (!is_string($spec)) {
            require_once 'Zend/Controller/Dispatcher/Exception.php';
            throw new Zend_Controller_Dispatcher_Exception('Invalid path delimiter');
        }
        $this->_pathDelimiter = $spec;

        return $this;
    }
    
    /**
     * Add or modify a parameter to use when instantiating an action controller
     *
     * @param string $name
     * @param mixed $value
     * @return Zend_Controller_Dispatcher_Abstract
     */
    public function setParam($name, $value)
    {
        $name = (string) $name;
        $this->_invokeParams[$name] = $value;
        return $this;
    }

    /**
     * Set parameters to pass to action controller constructors
     *
     * @param array $params
     * @return Zend_Controller_Dispatcher_Abstract
     */
    public function setParams(array $params)
    {
        $this->_invokeParams = array_merge($this->_invokeParams, $params);
        return $this;
    }

    /**
     * Retrieve a single parameter from the controller parameter stack
     *
     * @param string $name
     * @return mixed
     */
    public function getParam($name)
    {
        if(isset($this->_invokeParams[$name])) {
            return $this->_invokeParams[$name];
        }

        return null;
    }

    /**
     * Retrieve action controller instantiation parameters
     *
     * @return array
     */
    public function getParams()
    {
        return $this->_invokeParams;
    }

    /**
     * Clear the controller parameter stack
     *
     * By default, clears all parameters. If a parameter name is given, clears
     * only that parameter; if an array of parameter names is provided, clears
     * each.
     *
     * @param null|string|array single key or array of keys for params to clear
     * @return Zend_Controller_Dispatcher_Abstract
     */
    public function clearParams($name = null)
    {
        if (null === $name) {
            $this->_invokeParams = array();
        } elseif (is_string($name) && isset($this->_invokeParams[$name])) {
            unset($this->_invokeParams[$name]);
        } elseif (is_array($name)) {
            foreach ($name as $key) {
                if (is_string($key) && isset($this->_invokeParams[$key])) {
                    unset($this->_invokeParams[$key]);
                }
            }
        }

        return $this;
    }
    
}
