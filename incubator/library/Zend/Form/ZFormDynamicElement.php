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
 * ZFormElementException
 */
require_once 'ZForm/ZFormElementException.php';

/**
 * ZFormWebElement
 */
require_once 'ZForm/elements/ZFormWebElement.php';

/**
 * ZFormFactory
 */
require_once 'ZForm/ZFormFactory.php';


/**
 * @package    ZForm
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class ZFormDynamicElement
{
    const CLASS_PREFIX     = 'ZForm';

    protected $_subject = null;

    protected $_prefix = null;


    /**
     * @todo docblock
     */
    public function __construct($subject, $id = null, $classPrefix = null)
    {
    	if (is_string($subject)) {
    	    $subject  = ZFormFactory::loadElement($subject, $id, null, false);
    	}

    	$this->_subject = $subject;
    	$this->_prefix = $classPrefix  ? $classPrefix : self::CLASS_PREFIX;
    }


    /**
     * @todo docblock
     */
    public function __call($name, $args)
    {
    	if ($this->_subject && method_exists($this->_subject, $name)) {
	        // @todo probably not necessary, investigate later
    	    return call_user_func_array(array($this->_subject, $name), $args);
    	}

    	$upperName   = strtoupper($name);
    	$uname       = ucfirst($name);
    	$clsName     = $this->_prefix . $uname;
    	$methodName  = $name;
    	$formElement = false;

    	if (substr($upperName, -9) == 'VALIDATOR') {
    	    $newObject  = ZFormFactory::loadValidator($clsName, $this->_subject);
    	    $this->_subject->addValidator($newObject);

    	} elseif (substr($upperName, -8) == 'BEHAVIOR') {
    	    $newObject = ZFormFactory::loadBehavior($clsName, $this->_subject);
    	    $this->_subject->addBehavior($newObject);

    	} elseif (substr($upperName, 0, 9) == 'LISTENFOR') {
    	    $eventName = substr($name, 9);
    	    array_unshift($args, $eventName);
	        // @todo probably not necessary, investigate later
    	    call_user_func_array(array($this->_subject, 'addEventListener'), $args);
    	    return $this;

    	} else {
    	    try {
        		$formElement = true;
        		$newObject =  ZFormFactory::loadElement($clsName, null, $this->_subject);

        		if ($newObject) {
        		    $wrappedObject = new ZFormDynamicElement($newObject, null, $this->_prefix);
        		    $result = $wrappedObject;
        		}
    	    } catch (Exception $ex) {
    		// Eat class not found
    		  // @todo what's this?
    	    }
    	}

    	if ($newObject) {
    	    if (method_exists($newObject, $uname)) {
    	        // @todo probably not necessary, investigate later
        		call_user_func_array(array($newObject, $uname), $args);
    	    }

    	    return $formElement ? $result : $this;
    	}

    	throw new ZFormElementException("Method not found:$name on instanceof ".
                    					get_class($this->_subject));

    }


    /**
     * @todo docblock
     */
    public function __get($nm)
    {
        // @todo investigate later
    	return($this->_subject->{$nm});
    }


    /**
     * @todo docblock
     */
    public function __set($nm, $val)
    {
        // @todo investigate later
    	$this->_subject->{$nm} = $val;
    }


    /**
     * @todo docblock
     */
    public function parent()
    {
    	if ($this->_subject) {
    	    return($this->_subject->getParentNode());
    	}

    	return null;
    }
}
?>
