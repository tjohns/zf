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
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */ 

/** Zend_Controller_Dispatcher */
require_once 'Zend/Controller/Dispatcher.php';

/**
 * Module-aware dispatcher
 *
 * Use in place of Zend_Controller_Dispatcher when needing the ability to 
 * dispatch to controllers located in module directories.
 *
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Dispatcher
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Controller_ModuleDispatcher extends Zend_Controller_Dispatcher
{
    /**
     * Current dispatchable directory
     * @var string
     */
    protected $_curDirectory;

    /**
     * Current module (formatted)
     * @var string
     */
    protected $_curModule;

    /**
     * Directories where Zend_Controller_Action files are stored.
     * @var array
     */
    protected $_directories = array('default' => array());

    /**
     * Format the module name.
     * 
     * @param string $unformatted 
     * @return string
     */
    public function formatModuleName($unformatted)
    {
        return ucfirst($this->_formatName($unformatted));
    }

    /**
     * Add a single path to the controller directory stack
     * 
     * @param string $path 
     * @param mixed $args
     * @return Zend_Controller_Dispatcher
     */
    public function addControllerDirectory($path, $args = null)
    {
        if ('default' == $args) {
            foreach ((array) $path as $dir) {
                if (!is_string($dir) || !is_dir($dir) || !is_readable($dir)) {
                    require_once 'Zend/Controller/Dispatcher/Exception.php';
                    throw new Zend_Controller_Dispatcher_Exception("Directory \"$dir\" not found or not readable");
                }
                $this->_directories['default'][] = rtrim($dir, '/\\');
            }
        } else {
            if (!is_string($path) || !is_dir($path) || !is_readable($path)) {
                require_once 'Zend/Controller/Dispatcher/Exception.php';
                throw new Zend_Controller_Dispatcher_Exception("Directory \"$path\" not found or not readable");
            }

            if (is_int($args) || empty($args) || ('default' == $args)) {
                $this->_directories['default'][] = rtrim($path, '/\\');
            } else {
                $this->_directories[$args] = rtrim($path, '/\\');
            }
        }

        return $this;
    }

    /**
     * Sets the directory(ies) where the Zend_Controller_Action class files are stored.
     *
     * @param string|array $path
     * @return Zend_Controller_Dispatcher
     */
    public function setControllerDirectory($path)
    {
        $this->_directories = array('default' => array());

        foreach ((array) $path as $key => $dir) {
            $this->addControllerDirectory($dir, $key);
        }

        return $this;
    }

    /**
     * Return the currently set directory for Zend_Controller_Action class 
     * lookup
     * 
     * @return array
     */
    public function getControllerDirectory()
    {
        return $this->_directories;
    }

    /**
     * Returns TRUE if the Zend_Controller_Request_Abstract object can be 
     * dispatched to a controller.
     *
     * Use this method wisely. By default, the dispatcher will fall back to the 
     * default controller (either in the module specified or the global default) 
     * if a given controller does not exist. This method returning false does 
     * not necessarily indicate the dispatcher will not still dispatch the call.
     *
     * @param Zend_Controller_Request_Abstract $action
     * @return boolean
     */
    public function isDispatchable(Zend_Controller_Request_Abstract $request)
    {
        $className = $this->getController($request);
        if (!$className) {
            return true;
        }

        $fileSpec    = $this->classToFilename($className);
        $dispatchDir = $this->getDispatchDirectory();
        if (is_string($dispatchDir)) {
            // module controller found
            $test = $dispatchDir . DIRECTORY_SEPARATOR . $fileSpec;
            return Zend::isReadable($test);
        }

        // Test for controller in default controller directories
        $found = false;
        foreach ($dispatchDir as $dir) {
            $test = $dir . DIRECTORY_SEPARATOR . $fileSpec;
            if (Zend::isReadable($test)) {
                $found = true;
                break;
            }
        }

        return $found;
    }

    /**
     * Load a controller class
     * 
     * Attempts to load the controller class file from {@link getControllerDirectory()}.
     *
     * @param string $className 
     * @return string Class name loaded
     */
    public function loadClass($className)
    {
        $dispatchDir = $this->getDispatchDirectory();
        if (is_string($dispatchDir)) {
            // module found
            $loadFile = $dispatchDir . DIRECTORY_SEPARATOR . $this->classToFilename($className);
            $dir      = dirname($loadFile);
            $file     = basename($loadFile);
            Zend::loadFile($file, $dir, true);
            $moduleClass = $this->_curModule . '_' . $className;
            if (!class_exists($moduleClass)) {
                require_once 'Zend/Controller/Dispatcher/Exception.php';
                throw new Zend_Controller_Dispatcher_Exception('Invalid controller class ("' . $moduleClass . '")');
            }
            $className = $moduleClass;
        } else {
            Zend::loadClass($className, $dispatchDir);
        }

        return $className;
    }

    /**
     * Get controller name
     *
     * Try request first; if not found, try pulling from request parameter; 
     * if still not found, fallback to default
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return string|false Returns class name on success
     */
    public function getController(Zend_Controller_Request_Abstract $request)
    {
        $controllerName = $request->getControllerName();
        if (empty($controllerName)) {
            return false;
        }

        $className = $this->formatControllerName($controllerName);
        $controllerDirectory = $this->getControllerDirectory();

        /**
         * Check to see if a module name is present in the request, and that 
         * the module has been defined in the controller directory list; if so, 
         * prepend module to controller class name, using underscore as 
         * separator. 
         */
        $module = $request->getModuleName();
        if ($this->_isValidModule($module)) {
            $this->_curModule    = $this->formatModuleName($module);
            $this->_curDirectory = $controllerDirectory[$module];
        } else {
            $this->_curDirectory = $controllerDirectory['default'];
        }

        return $className;
    }

    /**
     * Determine if a given module is valid
     * 
     * @param string $module 
     * @return bool
     */
    protected function _isValidModule($module)
    {
        $controllerDir = $this->getControllerDirectory();
        return ((null !== $module) && ('default' != $module) && isset($controllerDir[$module]));
    }

    /**
     * Retrieve default controller
     *
     * Determines whether the default controller to use lies within the 
     * requested module, or if the global default should be used.
     *
     * By default, will only use the module default unless the developer has 
     * specified to use the global default:
     * <code>
     * $dispatcher->setParam('useGlobalDefault', true);
     *
     * // OR
     * $front->setParam('useGlobalDefault', true);
     * </code>
     * 
     * @param Zend_Controller_Request_Abstract $request 
     * @return string
     */
    public function getDefaultControllerName(Zend_Controller_Request_Abstract $request)
    {
        $controller = $this->getDefaultController();
        $default    = $this->formatControllerName($controller);
        $request->setControllerName($controller)
                ->setActionName(null);

        $useGlobalDefault = $this->getParam('useGlobalDefault');

        $module      = $request->getModuleName();
        $validModule = $this->_isValidModule($module);
        if (!$useGlobalDefault && $validModule) {
            $controllerDirs = $this->getControllerDirectory();
            $moduleDir      = $controllerDirs[$module];
            $fileSpec       = $moduleDir . DIRECTORY_SEPARATOR . $this->classToFilename($default);
            if (Zend::isReadable($fileSpec)) {
                $this->_curModule    = $this->formatModuleName($module);
                $this->_curDirectory = $controllerDirs[$module];
            } else {
                $this->_curDirectory = $controllerDirs['default'];
            }
        } else {
            $dirs = $this->getControllerDirectory();
            $this->_curDirectory = $dirs['default'];
        }

        return $default;
    }

   
    /**
     * Return the value of the currently selected dispatch directory (as set by 
     * {@link getController()})
     * 
     * @return string
     */
    public function getDispatchDirectory()
    {
        return $this->_curDirectory;
    }
}
