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
 * @subpackage Plugins
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Controller_Plugin_Abstract */
require_once 'Zend/Controller/Plugin/Abstract.php';

/** Zend_Registry */
require_once 'Zend/Registry.php';

/**
 * Manage a stack of actions
 *
 * @uses       Zend_Controller_Plugin_Abstract
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Plugins
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
class Zend_Controller_Plugin_ActionStack extends Zend_Controller_Plugin_Abstract
{
    /** @var Zend_Registry */
    protected $_registry;

    /**
     * Registry key under which actions are stored
     * @var string
     */
    protected $_registryKey = 'Zend_Controller_Plugin_ActionStack';

    /**
     * Valid keys for stack items
     * @var array
     */
    protected $_validKeys = array(
        'module', 
        'controller',
        'action',
        'params'
    );

    /**
     * Constructor
     *
     * @param  Zend_Registry $registry
     * @param  string $key
     * @return void
     */
    public function __construct(Zend_Registry $registry = null, $key = null)
    {
        if (null === $registry) {
            $registry = Zend_Registry::getInstance();
        }
        $this->setRegistry($registry);

        if (null !== $key) {
            $this->setRegistryKey($key);
        } else {
            $key = $this->getRegistryKey();
        }

        $registry[$key] = array();
    }

    /**
     * Set registry object
     * 
     * @param  Zend_Registry $registry 
     * @return Zend_Controller_Plugin_ActionStack
     */
    public function setRegistry(Zend_Registry $registry)
    {
        $this->_registry = $registry;
        return $this;
    }

    /**
     * Retrieve registry object
     * 
     * @return Zend_Registry
     */
    public function getRegistry()
    {
        return $this->_registry;
    }

    /**
     * Retrieve registry key
     *
     * @return string
     */
    public function getRegistryKey()
    {
        return $this->_registryKey;
    }

    /**
     * Set registry key
     *
     * @param  string $key
     * @return Zend_Controller_Plugin_ActionStack
     */
    public function setRegistryKey($key)
    {
        $this->_registryKey = (string) $key;
        return $this;
    }

    /**
     * Retrieve action stack
     * 
     * @return array
     */
    public function getStack()
    {
        $registry = $this->getRegistry();
        $stack    = $registry[$this->getRegistryKey()];
        return $stack;
    }

    /**
     * Save stack to registry
     * 
     * @param  array $stack 
     * @return Zend_Controller_Plugin_ActionStack
     */
    public function saveStack(array $stack)
    {
        $registry = $this->getRegistry();
        $registry[$this->getRegistryKey()] = $stack;
        return $this;
    }

    /**
     * Push an item onto the stack
     * 
     * @param  array $next 
     * @return Zend_Controller_Plugin_ActionStack
     */
    public function pushStack(array $next)
    {
        $stack = $this->getStack();
        array_push($stack, $next);
        return $this->saveStack();
    }

    /**
     * Pop an item off the action stack
     * 
     * @param  array $stack 
     * @return array
     */
    public function popStack(array &$stack)
    {
        if (0 == count($stack)) {
            return false;
        }

        $next = array_pop($stack);
        if (!is_array($next) || !isset($next['action'])) {
            return $this->popStack($stack);
        }

        $request = $this->getRequest();
        if (!isset($next['controller'])) {
            $next['controller'] = $request->getControllerName();
        }
        if (!isset($next['module'])) {
            $next['module'] = $request->getModuleName();
        }

        $params = array();
        if (isset($next['params'])) {
            $params = $next['params'];
        }
        foreach ($next as $key => $value) {
            if(!in_array($key, $this->_validKeys)) {
                $params[$key] = $value;
                unset($next[$key]);
            }
        }
        $next['params'] = $params;

        return $next;
    }

    /**
     * postDispatch() plugin hook -- check for actions in stack, and dispatch if any found
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        $this->setRequest($request);
        $stack = $this->getStack();
        if (empty($stack)) {
            return;
        }
        $next = $this->popStack($stack);
        if (!$next || !is_array($next)) {
            return;
        }

        $this->forward($next);
    }

    /**
     * Forward request with next action
     * 
     * @param  array $next 
     * @return void
     */
    public function forward(array $next)
    {
        $this->getRequest()->setModuleName($next['module'])
                           ->setControllerName($next['controller'])
                           ->setActionName($next['action'])
                           ->setParams($next['params'])
                           ->setDispatched(false);
    }
}
