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
 * @subpackage Dispatcher
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */ 

/**
 * Zend_Controller_Request_Abstract
 */
require_once 'Zend/Controller/Request/Abstract.php';

/**
 * Zend_Controller_Response_Abstract
 */
require_once 'Zend/Controller/Response/Abstract.php';

/**
 * @package    Zend_Controller
 * @subpackage Dispatcher
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Zend_Controller_Dispatcher_Interface
{
    /**
     * Formats a string into a controller name.  This is used to take a raw
     * controller name, such as one that would be packaged inside a request
     * object, and reformat it to a proper class name that a class extending
     * Zend_Controller_Action would use.
     *
     * @param string $unformatted
     * @return string
     */
    public function formatControllerName($unformatted);

    /**
     * Formats a string into an action name.  This is used to take a raw
     * action name, such as one that would be packaged inside a request
     * object, and reformat into a proper method name that would be found
     * inside a class extending Zend_Controller_Action.
     *
     * @param string $unformatted
     * @return string
     */
    public function formatActionName($unformatted);

    /**
     * Returns TRUE if an action can be dispatched, or FALSE otherwise.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return boolean
     */
	public function isDispatchable(Zend_Controller_Request_Abstract $request);

    /**
     * Add a parameter with which to instantiate an Action Controller
     * 
     * @param mixed $param 
     * @return void
     */
    public function addParam($param);

    /**
     * Set an array of a parameters to pass to the Action Controller constructor
     * 
     * @param array $params 
     * @return void
     */
    public function setParams(array $params);

    /**
     * Retrieve the parameters to pass to the Action Controller constructor
     * 
     * @return array
     */
    public function getParams();

    /**
     * Set the response object to use, if any
     * 
     * @param Zend_Controller_Response_Abstract|null $response 
     * @return void
     */
    public function setResponse(Zend_Controller_Response_Abstract $response = null);

    /**
     * Retrieve the response object, if any
     * 
     * @return Zend_Controller_Response_Abstract|null
     */
    public function getResponse();

    /**
     * Set default controller name (minus formatting)
     * 
     * @param string $controller 
     * @return self
     */
    public function setDefaultController($controller);

    /**
     * Retrieve the default controller name (minus formatting)
     * 
     * @return string
     */
    public function getDefaultController();

    /**
     * Set default action name (minus formatting)
     * 
     * @param string $action 
     * @return self
     */
    public function setDefaultAction($action);

    /**
     * Retrieve the default action name (minus formatting)
     * 
     * @return string
     */
    public function getDefaultAction();

	/**
     * Dispatches a request object to a controller/action.  If the action
     * requests a forward to another action, a new request will be returned.
	 *
	 * @param  Zend_Controller_Request_Abstract $request
	 * @param  Zend_Controller_Response_Abstract $response
	 * @return Zend_Controller_Request_Abstract|boolean
	 */
	public function dispatch(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response);
}
