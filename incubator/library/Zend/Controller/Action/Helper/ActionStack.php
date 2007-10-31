<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to version 1.0 of the Zend Framework
 * license, that is bundled with this package in the file LICENSE.txt, and
 * is available through the world-wide-web at the following URL:
 * http://framework.zend.com/license/new-bsd. If you did not receive
 * a copy of the Zend Framework license and are unable to obtain it
 * through the world-wide-web, please send a note to license@zend.com
 * so we can mail you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Action_Helper
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Controller_Action_Helper_Abstract */
require_once 'Zend/Controller/Action/Helper/Abstract.php';

/** Zend_Registry */
require_once 'Zend/Registry.php';

/**
 * Add to action stack
 *
 * @uses       Zend_Controller_Action_Helper_Abstract
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Action_Helper
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Controller_Action_Helper_ActionStack extends Zend_Controller_Action_Helper_Abstract
{
    /** @var Zend_Controller_Plugin_ActionStack */
    protected $_actionStack;

    /**
     * Constructor
     *
     * Register action stack plugin
     * 
     * @return void
     */
    public function __construct()
    {
        $front = Zend_Controller_Front::getInstance();
        if (!$front->hasPlugin('Zend_Controller_Plugin_ActionStack')) {
            include_once 'Zend/Controller/Plugin/ActionStack.php';
            $this->_actionStack = new Zend_Controller_Plugin_ActionStack();
            $front->registerPlugin($this->_actionStack);
        } else {
            $this->_actionStack = $front->getPlugin('Zend_Controller_Plugin_ActionStack');
        }
    }

    /**
     * Push onto the stack 
     * 
     * @param  array $next 
     * @return Zend_Controller_Action_Helper_ActionStack
     */
    public function pushStack(array $next)
    {
        $this->_actionStack->pushStack($next);
        return $this;
    }

    /**
     * Push a new action onto the stack
     * 
     * @param  string $action 
     * @param  string $controller 
     * @param  string $module 
     * @param  array $params 
     * @return Zend_Controller_Action_Helper_ActionStack
     */
    public function actionToStack($action, $controller = null, $module = null, array $params = array())
    {
        $next = array(
            'action'     => $action,
            'controller' => $controller,
            'module'     => $module,
            'params'     => $params
        );
        return $this->pushArrayToStack($next);
    }

    /**
     * Perform helper when called as $this->_helper->actionStack() from an action controller
     *
     * Proxies to {@link simple()}
     *
     * @param  string $action
     * @param  string $controller
     * @param  string $module
     * @param  array $params
     * @return bool
     */
    public function direct($action, $controller = null, $module = null, array $params = array())
    {
        return $this->actionToStack($action, $controller, $module, $params);
    }
}
