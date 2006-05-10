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
 * @package    Zend_Controller
 * @subpackage Dispatcher
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
	 * @return Zend_Controller_Dispatcher_Token|boolean
	 */
	public function dispatch(Zend_Controller_Dispatcher_Token $route);
}
