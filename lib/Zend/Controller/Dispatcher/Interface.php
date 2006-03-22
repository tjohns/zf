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
 * @subpackage Dispatcher
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */


/**
 * @package    Zend_Controller
 * @subpackage Dispatcher
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
interface Zend_Controller_Dispatcher_Interface
{
    /**
     * Returns TRUE if an action can be dispatched, or FALSE otherwise.
     *
     * @param  Zend_Controller_Dispatcher_Token $route
     * @return boolean
     */
	public function isDispatchable(Zend_Controller_Dispatcher_Token $route);

	/**
	 * Dispatches a Zend_Controller_Dispatcher_Token object to a controller/action.  If the action
	 * requests a forward to another action, a new Zend_Controller_Dispatcher_Token will be returned.
	 *
	 * @param  Zend_Controller_Dispatcher_Token $route
	 * @return mixed
	 */
	public function dispatch(Zend_Controller_Dispatcher_Token $route);
}
