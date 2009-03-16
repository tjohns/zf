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

/** Zend_Loader */
require_once 'Zend/Loader.php';

/** Zend_Controller_Dispatcher_Abstract */
require_once 'Zend/Controller/Dispatcher/Abstract.php';

/** Zend_Controller_Request_Abstract */
require_once 'Zend/Controller/Request/Abstract.php';

/** Zend_Controller_Response_Abstract */
require_once 'Zend/Controller/Response/Abstract.php';

/** Zend_Controller_Action */
require_once 'Zend/Controller/Action.php';

/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Dispatcher
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Controller_Dispatcher_Standard extends Zend_Controller_Dispatcher_Abstract
{
    
    public function isValidModule($module)
    {
        $dirs = $this->getFrontController()->getControllerDirectory();
        return (is_string($module) && isset($dirs[$module]));
    }
    
    /**
     * Convert a class name to a filename
     *
     * @param string $class
     * @return string
     */
    public function classToFilename($class)
    {
        return str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
    }
    
    /**
     * Returns TRUE if module and controller pair can be dispatched to
     *
     * @param Zend_Controller_Request_Abstract $action
     * @return boolean
     */
    public function isControllerDispatchable($moduleName, $controllerName) 
    {   
        $dirs = $this->getFrontController()->getControllerDirectory();
        if (!$this->isValidModule($moduleName)) return false;
        
        $path = $dirs[$moduleName] . DIRECTORY_SEPARATOR . $this->classToFilename($this->formatControllerName($controllerName));
        return Zend_Loader::isReadable($path);
    }
    
    /**
     * Loads a Controller file given a module and controller name
     *
     * @param Zend_Controller_Request_Abstract $action
     * @return boolean
     */
    protected function loadControllerFile($moduleName, $controllerName)
    {
        $dirs = $this->getFrontController()->getControllerDirectory();
        Zend_Loader::loadFile($this->classToFilename($this->formatControllerName($controllerName)), $dirs[$moduleName], true);
    }

    /**
     * Returns controller object given module and controller name 
     *
     * @param Zend_Controller_Request_Abstract $action
     * @return boolean
     * @todo Change request and response constructor injection to setter injecton
     */
    protected function getController($moduleName, $controllerName, $request, $response) 
    {
        $this->loadControllerFile($moduleName, $controllerName);
        
        $controllerName = $this->formatControllerName($controllerName);
        
        $fullControllerName = $moduleName . '_' . $controllerName;
        
        if (!class_exists($fullControllerName, false)) {
            
            if (!class_exists($controllerName, false)) {
                require_once 'Zend/Controller/Dispatcher/Exception.php';
                throw new Zend_Controller_Dispatcher_Exception('Controller class ("' . $controllerName . '") cannot be found in "'.  $dirs[$moduleName] . '/' . $controllerName . '.php"');
            }
            
            $fullControllerName = $controllerName;
            
        }
        
        $controller = new $fullControllerName($request, $response, $this->getParams());
        
        if (!$controller instanceof Zend_Controller_Action) {
            require_once 'Zend/Controller/Dispatcher/Exception.php';
            throw new Zend_Controller_Dispatcher_Exception("Controller '$className' is not an instance of Zend_Controller_Action");
        }
        
        return $controller;
    }
    
    public function getModuleName(Zend_Controller_Request_Abstract $request)
    {
        $name = $request->getModuleName();
        if (!$name) {
            $name = $this->getDefaultModuleName(); 
        }
        return $name;
    }
    
    public function getControllerName(Zend_Controller_Request_Abstract $request)
    {
        $name = $request->getControllerName();
        if (!$name) {
            $name = $this->getDefaultControllerName(); 
        }
        return $name;
    }
    
    public function getActionName(Zend_Controller_Request_Abstract $request)
    {
        $name = $request->getActionName();
        if (!$name) {
            $name = $this->getDefaultActionName(); 
        }
        return $name;
    }

    /**
     * Dispatch to a controller/action
     *
     * By default, if a controller is not dispatchable, dispatch() will throw
     * an exception. If you wish to use the default controller instead, set the
     * param 'useDefaultControllerAlways' via {@link setParam()}.
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param Zend_Controller_Response_Abstract $response
     * @return boolean
     * @throws Zend_Controller_Dispatcher_Exception
     */
    public function dispatch(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response)
    {
        $this->setResponse($response);
        
        $moduleName = $this->getModuleName($request);
        $controllerName = $this->getControllerName($request);
        $actionName = $this->getActionName($request);
        
        if (!$this->isControllerDispatchable($moduleName, $controllerName)) {
            
            // TODO: Merge error handler action plugin
            // TODO: $moduleName = $this->getErrorModuleName();
            // TODO: $controllerName = $this->getErrorControllerName();
            // TODO: $actionName = $this->getErrorActionName();
            
            if (!$this->getParam('useDefaultControllerAlways')) {
                require_once 'Zend/Controller/Dispatcher/Exception.php';
                throw new Zend_Controller_Dispatcher_Exception('Invalid controller specified (' . $controllerName . ')');
            }
                
            $moduleName = $this->getDefaultModuleName();
            $controllerName = $this->getDefaultControllerName();
            $actionName = $this->getDefaultActionName();
            
            if (!$this->isControllerDispatchable($moduleName, $controllerName)) {
                require_once 'Zend/Controller/Dispatcher/Exception.php';
                throw new Zend_Controller_Dispatcher_Exception('Invalid controller specified (' . $controllerName . ')');
            }
            
        } 

        $controller = $this->getController($moduleName, $controllerName, $request, $response);
        // TODO: $controller->setRequest($request);
        // TODO: $controller->setResponse($response);

        $request->setDispatched(true);
        
        // by default, buffer output
        $disableOb = $this->getParam('disableOutputBuffering');
        $obLevel   = ob_get_level();
        if (empty($disableOb)) {
            ob_start();
        }

        try {
            
            $action = $this->formatActionName($actionName);
            $controller->dispatch($action);
            
        } catch (Exception $e) {
            // Clean output buffer on error
            $curObLevel = ob_get_level();
            if ($curObLevel > $obLevel) {
                do {
                    ob_get_clean();
                    $curObLevel = ob_get_level();
                } while ($curObLevel > $obLevel);
            }

            throw $e;
        }

        if (empty($disableOb)) {
            $content = ob_get_clean();
            $response->appendBody($content);
        }

        // Destroy the page controller instance and reflection objects
        $controller = null;
        
    }
    
    /**
     * Add a single path to the controller directory stack
     *
     * @param string $path
     * @param string $module
     * @return Zend_Controller_Dispatcher_Standard
     * @todo Move directories to the Dispatcher
     */
    public function addControllerDirectory($path, $module = null)
    {
        if (null === $module) {
            $module = $this->_defaultModule;
        }

        $this->getFrontController()->addControllerDirectory($path, $module);
        return $this;
    }

    /**
     * Set controller directory
     *
     * @param array|string $directory
     * @return Zend_Controller_Dispatcher_Standard
     * @todo Move directories to the Dispatcher
     */
    public function setControllerDirectory($directory)
    {
        $this->getFrontController()->setControllerDirectory($directory);
        return $this;
    }

    /**
     * Return the currently set directories for Zend_Controller_Action class
     * lookup
     *
     * If a module is specified, returns just that directory.
     *
     * @param  string $module Module name
     * @return array|string Returns array of all directories by default, single
     * module directory if module argument provided
     * @todo Move directories to the Dispatcher
     */
    public function getControllerDirectory($module = null)
    {
        $directories = $this->getFrontController()->getControllerDirectory();

        if ((null !== $module) && (isset($directories[$module]))) {
            return $directories[$module];
        }

        return $directories;
    }

    /* ----- Deprecated methods (BC Compatible) ----- */
    
    /**
     * Retrieve default controller class
     *
     * Determines whether the default controller to use lies within the
     * requested module, or if the global default should be used.
     *
     * By default, will only use the module default unless that controller does
     * not exist; if this is the case, it falls back to the default controller
     * in the default module.
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return string
     * @deprecated
     */
    public function getDefaultControllerClass(Zend_Controller_Request_Abstract $request) 
    {
        $controller = $this->getDefaultControllerName();
        return $this->formatControllerName($controller);        
    }

    /**
     * Returns TRUE if the Zend_Controller_Request_Abstract object can be
     * dispatched to a controller.
     *
     * @param Zend_Controller_Request_Abstract $action
     * @return boolean
     * @deprecated
     */
    public function isDispatchable(Zend_Controller_Request_Abstract $request) 
    {   
        $moduleName = $this->getModuleName($request);
        $controllerName = $this->getControllerName($request);
        return $this->isControllerDispatchable($moduleName, $controllerName);
    }
}
