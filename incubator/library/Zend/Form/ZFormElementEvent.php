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
 * @package    ZForm
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */


/**
 * ZFormElementEventListenerInterface
 */
require_once 'ZForm/ZFormElementEventListenerInterface.php';


/**
 * @package    ZForm
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class ZFormElementEvent
{

    /**
     * Event constants supported by the framework
     * @var string
     */
    const ONCLICK     		= 'Click';
    const ONVALUECHANGE		= 'Change';
    const ONBLUR       		= 'Blur';

    /**
     * Contains the type of the event see constants above
     * @var string
     */
    protected $_type;

    /**
     * The $_source variable contains the ZFormElement that
     * trigged the event.
     * @var ZFormElement
     */
    protected $_source;

    /**
     * The $_data variable contains a mixed variable the is
     * opaque to the event structure. This can be used to communicate
     * specialized data such as old & new value in the ONVALUECHANGE
     * event.
     * @var mixed
     */
    protected $_data;


    /**
     * Class constructor. Simply initialize the instance variables to
     * the variables passed
     *
     * @param string $type
     * @param ZFormElement $source
     * @param mixed $data
     */
    public function __construct($type, $source, $data)
    {
    	$this->_type   = $type;
    	$this->_source = $source;
    	$this->_data = $data;
    }


    /**
     * Returns the type of the event. See constants in ZFormElementEvent
     * 
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }


    /**
     * Returns the ZFormElement which is the source of the event
     *
     * @return ZFormElement
     */
    public function getSource()
    {
    	return $this->_source;
    }


    /**
     * Returns the opaque user data associated with the event
     *
     * @return mixed
     */
    public function getData()
    {
    	return $this->_data;
    }


    /**
     * Delivers the event to the registered event listeners for the type
     * specified by the event from the subject.
     * If a listeners is an object fire attempts to invoke the methods
     * defined by concatenating 'on' with the id of the source object
     * and the type of the method. For example if:
     *     the source id = test
     *     the type of ONVALUECHANGE
     * the method name would be -> onTestChange
     * If that method does not exist and the listener is an instanceof
     * ZFormElementEventListenerInterface the general handleEvent is
     * invoked.
     * Finally if the object listener is a string and a callable the
     * function is invoked with the event as an argument
     *
     * @return boolean true no listeners found or the the result of
     * invoking the listeners method/function.
     */
    public function fire()
    {
    	$listeners = $this->_source->getEventListeners($this->_type);

    	if ($listeners) {
    	    foreach ($listeners as $listener) {
                $methodName = 'on' . ucfirst($this->_source->getID()).$this->_type;
                /**
                 * @todo check visibility - method_exists() returns true on priv/prot as well
                 */
                if (is_object($listener)) {
                    if (is_callable(array($listener, $methodName))) {
                        return $listener->{$methodName}($this);
                    } else if ($listener instanceof ZFormElementEventListenerInterface) {
                        return $listener->handleEvent($this);
                    }  else {
                        throw new ZFormElementException("Event handler not found for ");
                    }
                } elseif (is_string($listener) && is_callable($listener)) {
                    // @todo can this work?
                    return $listener($this);
                }
            }
    	}

    	return true;
    }

}

?>
