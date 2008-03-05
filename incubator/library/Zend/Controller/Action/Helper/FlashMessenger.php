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
 * @subpackage Action_Helper
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Session */
require_once 'Zend/Session.php';

/**
 * Flash Messenger - implement session-based messages
 *
 * @uses       Zend_Controller_Action_Helper_Abstract
 * @category   Zend
 * @package    Zend_Controller
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */
class Zend_Controller_Action_Helper_FlashMessenger extends Zend_Controller_Action_Helper_Abstract implements IteratorAggregate, Countable
{
    /**
     * $_messages - Messages from previous request
     *
     * @var array
     */
    static protected $_messages = array();

    /**
     * $_session - Zend_Session_Namespace storage object
     *
     * @var Zend_Session_Namespace
     */
    static protected $_sessionNamespace = null;

    /**
     * $_messageAdded - Wether a message has been previously added
     *
     * @var unknown_type
     */
    static protected $_messageAdded = false;

    /**
     * $_namespace - Instance namespace, default is 'default'
     *
     * @var string
     */
    protected $_namespace = 'default';

    /**
     * initSessionNamespace() - setup the namespace container
     *
     * @param Zend_Session_Namespace $sessionNamespace
     */
    public static function initSessionNamespace(Zend_Session_Namespace $sessionNamespace)
    {
        self::$_sessionNamespace = $sessionNamespace;
        
        foreach (self::$_sessionNamespace as $namespace => $messages) {
            self::$_messages[$namespace] = $messages;
            unset(self::$_sessionNamespace->{$namespace});
        }        
    }
    
    protected static function _unsetSessionNamespace()
    {
        self::$_sessionNamespace = null; 
    }
    
    /**
     * __construct() - Instance constructor, needed to get iterators, etc
     *
     * @param string $namespace
     */
    public function __construct($options = array())
    {
        
        if (isset($options['unsetSessionNamespace']) && $options['unsetSessionNamespace'] === true) {
            // only use this in testing
            self::_unsetSessionNamespace();
        }
        
        if (!self::$_sessionNamespace && $options instanceof Zend_Session_Namespace) {
            // allow $options to be an actual Zend_Session_Namespace
            self::initSessionNamespace($options);
            
        } elseif (!self::$_sessionNamespace && (isset($options['sessionNamespace']) && $options['sessionNamespace'] instanceof Zend_Session_Namespace)) {
            // allow for an options key called 'sessionNamespace' to be present to process 
            self::initSessionNamespace($options['sessionNamespace']);
            
        } elseif (isset($options['sessionNamespace']) && $options['sessionNamespace'] === false) {
            // this allows for the passing of false as a sessionNamespace, only useful for testing
            // and delaying the initialization of a sessionNamespace;
            return;
            
        } elseif (!self::$_sessionNamespace) {
            // otherwise create one from scratch if none exist (getName() comes from Abstract)
            self::initSessionNamespace(new Zend_Session_Namespace($this->getName()));
        }

    }

    /**
     * postDispatch() - runs after action is dispatched, in this
     * case, it is resetting the namespace in case we have forwarded to a different
     * action, Flashmessage will be 'clean' (default namespace)
     *
     * @return Zend_Controller_Action_Helper_FlashMessenger
     */
    public function postDispatch()
    {
        $this->resetNamespace();
        return $this;
    }

    /**
     * setNamespace() - change the namespace messages are added to, useful for
     * per action controller messaging between requests
     *
     * @param string $namespace
     * @return Zend_Controller_Action_Helper_FlashMessenger
     */
    public function setNamespace($namespace = 'default')
    {
        $this->_namespace = $namespace;
        return $this;
    }

    /**
     * resetNamespace() - reset the namespace to the default
     *
     * @return Zend_Controller_Action_Helper_FlashMessenger
     */
    public function resetNamespace()
    {
        $this->setNamespace();
        return $this;
    }

    /**
     * addMessage() - Add a message to flash message
     *
     * @param string $message
     * @param string $namespace OPTIONAL
     */
    public function addMessage($message, $namespace = null)
    {
        $namespace = (isset($namespace)) ? (string) $namespace : $this->_namespace; 
        
        if (self::$_messageAdded === false) {
            self::$_sessionNamespace->setExpirationHops(1, null, true);
        }

        if (!is_array(self::$_sessionNamespace->{$namespace})) {
            self::$_sessionNamespace->{$namespace} = array();
        }

        self::$_sessionNamespace->{$namespace}[] = $message;

        return;
    }

    /**
     * hasMessages() - Wether a specific namespace has messages
     *
     * @param string $namespace
     * @return bool
     */
    public function hasMessages($namespace = null)
    {
        $namespace = (isset($namespace)) ? (string) $namespace : $this->_namespace; 
        
        return isset(self::$_messages[$namespace]);
    }

    /**
     * getMessages() - Get messages from a specific namespace
     *
     * @param unknown_type $namespace
     * @return array
     */
    public function getMessages($namespace = null)
    {
        $namespace = (isset($namespace)) ? (string) $namespace : $this->_namespace;
        
        if ($this->hasMessages()) {
            return self::$_messages[$namespace];
        }

        return array();
    }

    /**
     * Clear all messages from the previous request & current namespace
     *
     * @return bool True if messages were cleared, false if none existed
     */
    public function clearMessages($namespace = null)
    {
        $namespace = (isset($namespace)) ? (string) $namespace : $this->_namespace; 
        
        if ($this->hasMessages()) {
            unset(self::$_messages[$namespace]);
            return true;
        }

        return false;
    }

    /**
     * Clear all messages from the current request
     *
     * @param unknown_type $namespace
     * @return unknown
     */
    public function clearCurrentMessages($namespace = null)
    {
        $namespace = (isset($namespace)) ? (string) $namespace : $this->_namespace; 
        
        unset(self::$_sessionNamespace->{$namespace});
        return $this;
    }
    
    
    /**
     * hasCurrentMessages() - check to see if messages have been added to current
     * namespace within this request
     *
     * @return bool
     */
    public function hasCurrentMessages($namespace = null)
    {
        $namespace = (isset($namespace)) ? (string) $namespace : $this->_namespace; 

        return isset(self::$_sessionNamespace->{$namespace});
    }

    /**
     * getCurrentMessages() - get messages that have been added to the current
     * namespace within this request
     *
     * @return array
     */
    public function getCurrentMessages($namespace = null)
    {
        $namespace = (isset($namespace)) ? (string) $namespace : $this->_namespace; 
        
        if ($this->hasCurrentMessages()) {
            return self::$_sessionNamespace->{$namespace};
        }

        return array();
    }
    
    /**
     * getIterator() - complete the IteratorAggregate interface, for iterating
     *
     * @return ArrayObject
     */
    public function getIterator()
    {
        if ($this->hasMessages()) {
            return new ArrayObject($this->getMessages());
        }

        return new ArrayObject();
    }

    /**
     * count() - Complete the countable interface
     *
     * @return int
     */
    public function count()
    {
        if ($this->hasMessages()) {
            return count($this->getMessages());
        }

        return 0;
    }

    /**
     * Strategy pattern: proxy to addMessage()
     * 
     * @param  string $message 
     * @return void
     */
    public function direct($message)
    {
        return $this->addMessage($message);
    }
}
