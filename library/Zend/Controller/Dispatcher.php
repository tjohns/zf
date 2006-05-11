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
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */ 


/** Zend */
require_once 'Zend.php';

/** Zend_Controller_Dispatcher_Interface */
require_once 'Zend/Controller/Dispatcher/Interface.php';

/** Zend_Controller_Dispatcher_Exception */
require_once 'Zend/Controller/Dispatcher/Exception.php';

/** Zend_Controller_Action */
require_once 'Zend/Controller/Action.php';


/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Dispatcher
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Controller_Dispatcher implements Zend_Controller_Dispatcher_Interface
{
    /**
     * Directory where Zend_Controller_Action files are stored.
     * @var string
     */
    protected $_directory = null;


    /**
     * Formats a string into a controller name.  This is used to take a raw
     * controller name, such as one that would be packaged inside a Zend_Controller_Dispatcher_Token
     * object, and reformat it to a proper class name that a class extending
     * Zend_Controller_Action would use.
     *
     * @param string $unformatted
     * @return string
     */
    public function formatControllerName($unformatted)
    {
	    return ucfirst($this->_formatName($unformatted)) . 'Controller';
    }


    /**
     * Formats a string into an action name.  This is used to take a raw
     * action name, such as one that would be packaged inside a Zend_Controller_Dispatcher_Token
     * object, and reformat into a proper method name that would be found
     * inside a class extending Zend_Controller_Action.
     *
     * @param string $unformatted
     * @return string
     */
	public function formatActionName($unformatted)
	{
	    $formatted = $this->_formatName($unformatted);
	    return strtolower(substr($formatted, 0, 1)) . substr($formatted, 1) . 'Action';
	}


    /**
     * Formats a string from a URI into a PHP-friendly name.  Replaces words
     * separated by "-", "_", or "." with camelCaps and removes any characters
     * that are not alphanumeric.
     *
     * @param string $unformatted
     * @return string
     */
    protected function _formatName($unformatted)
    {
        $unformatted = str_replace(array('-', '_', '.'), ' ', strtolower($unformatted));
        $unformatted = preg_replace('[^a-z0-9 ]', '', $unformatted);
        return str_replace(' ', '', ucwords($unformatted));
    }


    /**
     * Sets the directory where the Zend_Controller_Action class files are stored.
     *
     * @param string $dir
     */
    public function setControllerDirectory($dir)
    {
        if (!is_dir($dir) or !is_readable($dir)) {
            throw new Zend_Controller_Dispatcher_Exception("Directory \"$dir\" not found or not readable.");
        }

        $this->_directory = rtrim($dir, '/\\');
    }


    /**
     * Returns TRUE if the Zend_Controller_Dispatcher_Token object can be dispatched to a controller.
     * This only verifies that the Zend_Controller_Action can be dispatched and does not
     * guarantee that the action will be accepted by the Zend_Controller_Action.
     *
     * @param Zend_Controller_Dispatcher_Token $action
     * @return unknown
     */
	public function isDispatchable(Zend_Controller_Dispatcher_Token $action)
	{
        return $this->_dispatch($action, false);
	}


	/**
	 * Dispatch to a controller/action
	 *
	 * @param Zend_Controller_Dispatcher_Token $action
	 * @return boolean|Zend_Controller_Dispatcher_Token
	 */
	public function dispatch(Zend_Controller_Dispatcher_Token $action)
	{
	    return $this->_dispatch($action, true);
	}


	/**
	 * If $performDispatch is FALSE, this method will check if a controller
	 * file exists.  This still doesn't necessarily mean that it can be dispatched
	 * in the stricted sense, as file may not contain the controller class or the
	 * controller may reject the action.
	 *
	 * If $performDispatch is TRUE, then this method will actually
	 * instantiate the controller and call its action.  Calling the action
	 * is done by passing a Zend_Controller_Dispatcher_Token to the controller's constructor.
	 *
	 * @param Zend_Controller_Dispatcher_Token $action
	 * @param boolean $performDispatch
	 * @return boolean|Zend_Controller_Dispatcher_Token
	 */
	protected function _dispatch(Zend_Controller_Dispatcher_Token $action, $performDispatch)
	{
	    if ($this->_directory === null) {
	        throw new Zend_Controller_Dispatcher_Exception('Controller directory never set.  Use setControllerDirectory() first.');
	    }

	    $className  = $this->formatControllerName($action->getControllerName());

	    /**
	     * If $performDispatch is FALSE, only determine if the controller file
	     * can be accessed.
	     */
	    if (!$performDispatch) {
	        return Zend::isReadable($this->_directory . DIRECTORY_SEPARATOR . $className . '.php');
	    }

        Zend::loadClass($className, $this->_directory);

        $controller = new $className();
        if (!$controller instanceof Zend_Controller_Action) {
           throw new Zend_Controller_Dispatcher_Exception("Controller \"$className\" is not an instance of Zend_Controller_Action.");
        }
            
        /**
         * Dispatch
         *
         * Call the action of the Zend_Controller_Action.  It will return either null or a
         * new Zend_Controller_Dispatcher_Token object.  If a Zend_Controller_Dispatcher_Token object is returned, this will be returned
         * back to ZFrontController, which will call $this again to forward to
         * another action.
         */
        $nextAction = $controller->run($this, $action);

        // Destroy the page controller instance
        $controller = null;

        // Return either null (finished) or a Zend_Controller_Dispatcher_Token object (forward to another action).
        return $nextAction;
	}
}
