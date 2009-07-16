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
 * @package    Zend_Wildfire
 * @subpackage Plugin
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Controller_Request_Abstract */
require_once('Zend/Controller/Request/Abstract.php');

/** Zend_Controller_Response_Abstract */
require_once('Zend/Controller/Response/Abstract.php');

/** Zend_Wildfire_Channel_HttpHeaders */
require_once 'Zend/Wildfire/Channel/HttpHeaders.php';

/** Zend_Wildfire_Protocol_JsonStream */
require_once 'Zend/Wildfire/Protocol/JsonStream.php';

/** Zend_Wildfire_Plugin_Interface */
require_once 'Zend/Wildfire/Plugin/Interface.php';

/**
 * Primary class for communicating with wildfire-compatible clients.
 *
 * @category   Zend
 * @package    Zend_Wildfire
 * @subpackage Plugin
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Wildfire_Plugin_FrameworkToolbar implements Zend_Wildfire_Plugin_Interface
{

    /**
     * The plugin URI for this plugin
     */
    const PLUGIN_URI = 'http://meta.wildfirehq.org/Plugin/ZendFramework/FrameworkToolbar/1.9';

    /**
     * The protocol URI for this plugin
     */
    const PROTOCOL_URI = Zend_Wildfire_Protocol_JsonStream::PROTOCOL_URI;

    /**
     * The structure URI for the Dump structure
     */
    const STRUCTURE_URI_TOOLBAR = 'http://meta.wildfirehq.org/Structure/FrameworkToolbar/0.1';

    /**
     * Singleton instance
     * @var Zend_Wildfire_Plugin_FrameworkToolbar
     */
    protected static $_instance = null;

    /**
     * Flag indicating whether we should send messages to the user-agent.
     * @var boolean
     */
    protected $_enabled = true;

    /**
     * The channel via which to send the encoded messages.
     * @var Zend_Wildfire_Channel_Interface
     */
    protected $_channel = null;

    /**
     * Create singleton instance.
     *
     * @param string $class OPTIONAL Subclass of Zend_Wildfire_Plugin_FrameworkToolbar
     * @return Zend_Wildfire_Plugin_FrameworkToolbar Returns the singleton Zend_Wildfire_Plugin_FrameworkToolbar instance
     * @throws Zend_Wildfire_Exception
     */
    public static function init($class = null)
    {
        if (self::$_instance !== null) {
            require_once 'Zend/Wildfire/Exception.php';
            throw new Zend_Wildfire_Exception('Singleton instance of Zend_Wildfire_Plugin_FrameworkToolbar already exists!');
        }
        if ($class !== null) {
            if (!is_string($class)) {
                require_once 'Zend/Wildfire/Exception.php';
                throw new Zend_Wildfire_Exception('Third argument is not a class string');
            }

            if (!class_exists($class)) {
                require_once 'Zend/Loader.php';
                Zend_Loader::loadClass($class);
            }
            self::$_instance = new $class();
            if (!self::$_instance instanceof Zend_Wildfire_Plugin_FrameworkToolbar) {
                self::$_instance = null;
                require_once 'Zend/Wildfire/Exception.php';
                throw new Zend_Wildfire_Exception('Invalid class to third argument. Must be subclass of Zend_Wildfire_Plugin_FrameworkToolbar.');
            }
        } else {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Constructor
     * @return void
     */
    protected function __construct()
    {
        $this->_channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
        $this->_channel->getProtocol(self::PROTOCOL_URI)->registerPlugin($this);
    }

    /**
     * Get or create singleton instance
     *
     * @param $skipCreate boolean True if an instance should not be created
     * @return Zend_Wildfire_Plugin_FrameworkToolbar
     */
    public static function getInstance($skipCreate=false)
    {
        if (self::$_instance===null && $skipCreate!==true) {
            return self::init();
        }
        return self::$_instance;
    }

    /**
     * Destroys the singleton instance
     *
     * Primarily used for testing.
     *
     * @return void
     */
    public static function destroyInstance()
    {
        self::$_instance = null;
    }

    /**
     * Enable or disable sending of messages to user-agent.
     * If disabled all headers to be sent will be removed.
     *
     * @param boolean $enabled Set to TRUE to enable sending of messages.
     * @return boolean The previous value.
     */
    public function setEnabled($enabled)
    {
        $previous = $this->_enabled;
        $this->_enabled = $enabled;
        if (!$this->_enabled) {
            $this->_messages = array();
            $this->_channel->getProtocol(self::PROTOCOL_URI)->clearMessages($this);
        }
        return $previous;
    }

    /**
     * Determine if logging to user-agent is enabled.
     *
     * @return boolean Returns TRUE if logging is enabled.
     */
    public function getEnabled()
    {
        return $this->_enabled;
    }

    /**
     * Sends data to the client
     *
     * @param  mixed  $data   The data to send.
     * @return boolean Returns TRUE if the data was added to the response headers or buffered.
     * @throws Zend_Wildfire_Exception
     */
    public static function send($data)
    {
        $instance = self::getInstance();

        if (!$instance->getEnabled()) {
            return false;
        }

        return $instance->_channel->getProtocol(self::PROTOCOL_URI)->
                          recordMessage($instance,
                                        self::STRUCTURE_URI_TOOLBAR,
                                        $data);
    }


    /*
     * Zend_Wildfire_Plugin_Interface
     */

    /**
     * Get the unique indentifier for this plugin.
     *
     * @return string Returns the URI of the plugin.
     */
    public function getUri()
    {
        return self::PLUGIN_URI;
    }

    /**
     * Flush any buffered data.
     *
     * @param string $protocolUri The URI of the protocol that should be flushed to
     * @return void
     */
    public function flushMessages($protocolUri)
    {
    }
}
