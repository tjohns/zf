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
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */


/**
 * ZFormElementException
 */
require_once 'ZForm/ZFormElementException.php';

/**
 * ZFormElementBehavior
 */
require_once 'ZForm/ZFormElementBehavior.php';

/**
 * ZFormElement
 */
require_once 'ZForm/ZFormElement.php';

/**
 * ZFormLink
 */
require_once 'ZForm/elements/ZFormLink.php';

/**
 * ZForm
 */
require_once 'ZForm/elements/ZForm.php';


/**
 * @package    ZForm
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */

class ZFormAjaxBehavior extends ZFormElementBehavior {

    const REPLACE	= 1;
    const PREPEND	= 2;
    const APPEND	= 3;
    const POST		= "POST";
    const GET		= "GET";

    protected $_url;
    protected $_isAsync;
    protected $_callbacks;
    protected $_htmlid;
    protected $_position;
    protected $_method;
    protected $_scriptEventName;
    protected $_eventHook;

    /**
     * Class constructor. Simply initialize the instance variables to
     * the variables passed
     *
     * @param ZFormElement $formElement The element to apply the behavior to
     * @param string $url the URL to invoke when the $eventHook occurs on
     * the client
     * @param string htmlid The id of the HTML component to target the
     * result of the AJAX request at.
     * @param int $position Determins how the content received by the
     * AJAX request is placed into the receiving htmlid. Values are
     * REPLACE, PREPEND & APPEND
     * @param array $callback An array of Javascript code fragments that are
     * called during the lifecycle of the AJAX request.
     *    $callbacks['onSuccess'] = "alert('success!')
     *    $callbacks['onFailure'] = "alert('failure!')
     * @param boolean $isAsync true (default) make the AJAX request asynchronous
     * @param string $method POST or GET
     * @param string $eventHook The name of the client DOM event to hook which
     * invokes the AJAX call.
     *
     * @return void
     */

    public function __construct($formElement,
				$url, 
				$htmlid, 
				$position = self::REPLACE, 
				$callbacks = null, 
				$isAsync = true,
				$method = self::POST,
				$eventHook = 'click') {

	if ($formElement instanceof ZFormElement) {
	    parent::__construct($formElement);
	}
	$this->_isAsync = $isAync;
	$this->_url = $url;
	$this->_htmlid = $htmlid;
	$this->setPosition($position);
	$this->_callbacks = $callbacks;
	$this->_method = $method;
	$this->_scriptEventName = uniqid('zajax');
	$this->_eventHook = $eventHook;
	
    }

    /**
     * ZFormElementBehavior procotol method which emits the javascript
     * necessary to apply the AJAX behavior to the element
     *
     * @param ZFormElement $element
     */
    public function emitClientBehavior($element) {

	echo "<SCRIPT>\n";

	echo "\tfunction " . $this->_scriptEventName . "(evt) {\n";
	echo "\t\tvar target = (evt.srcElement ? evt.srcElement : evt.target);\n";
	echo "\t\tvar parameters;\n";
	if ($element && $element instanceof ZForm) {
	    echo "\t\tparameters = ZAjaxEngine.getFormParameters(target)\n";
	}
	echo "\t\tvar events = null;\n";
	if ($this->_callbacks) {
	    $events = "\t\tevents = {}\n";
	    foreach ($this->_callbacks as $key => $callback) {
		$events .= "\t\tevents.$key = $callback;\n";
	    }
	    echo $events;
		
	}
	echo "\t\tvar request = new ZAjaxEngine.Request('".$this->_url."', '".$this->_method."', ".
	    ($this->_isAsync ? 'true' : 'false').", events, '".$this->_htmlid."', ".$this->_position.")\n";
	echo "\t\trequest.sendRequest(parameters);\n";
	echo "\t\tevt.cancelBubble = true;\n";
	echo "\t\tif (evt.stopPropagation) evt.stopPropagation();\n";
	echo "\t\treturn(false);\n";
	echo "\t}\n";
	if ($element && 
	    !($element instanceof ZFormLink)) {
	    $name = 'element';
	    if (is_string($element)) {
		$name = "document.getElementById('". $element ."')";
	    }
	    echo "\tZAjaxEngine.addEventListener(".$name.", '".$this->_eventHook."', ".$this->_scriptEventName.", false);\n";
	}
	echo "</SCRIPT>\n";
    
    }

    /**
     * Called before the behavior is emitted to the client or server. If the
     * element is a ZFormLink the AJAX behavior replaces the href of the link.
     * If the $element type is a form the submit event is hooked.
     *
     * @param ZFormElement $element
     * @return void
     */
    public function applyClientBehavior(ZFormElement $element) {
	if ($element instanceof ZFormLink) {
	    $element->href = 'javascript:'.$this->_scriptEventName.'()';
	} else if ($element instanceof ZForm) {
	    $element->action = 'javascript:voidFunction()';
	}	
    }


    /**
     * Returns the URL of the AJAX behavior
     *
     * @return string url
     */
    public function getURL() {
	return($this->_url);
    }


    /**
     * Sets the URL of the AJAX behavior
     *
     * @param string $url
     * @return void
     */
    public function setURL($url) {
	$this->_url = $url;
    }


    /**
     * Returns the value of the async instance variable
     *
     * @return boolean
     */
    public function isAsync() {
	return($this->_isAsync);
    }


    /**
     * Set the value of the async instance variable
     *
     * @param boolean $isAsync
     * @return void
     */
    public function setAsync($isAsync) {
	$this->_isAsync = $isAsync;
    }


    /**
     * Returns the value of the callbacks instance variable
     *
     * @return boolean
     */
    public function getCallbacks() {
	return($this->_callbacks);
    }


    /**
     * Set the value of the callback instance variable
     *
     * @param array $callback An array of Javascript code fragments that are
     * called during the lifecycle of the AJAX request.
     *    $callbacks['onSuccess'] = "alert('success!')
     *    $callbacks['onFailure'] = "alert('failure!')
     * @return void
     */
    public function setCallbacks($callbacks) { 
	$this->_callbacks = $callbacks;
    }


    /**
     * Returns the value of the htmlid instance variable
     *
     * @return string
     */
    public function getHTMLTarget() {
	return($this->_htmlid);
    }

    /**
     * Sets the value of the htmlid target instance variable
     *
     * @param string $id
     * @return void
     */
    public function setHTMLTarget($id) {
	$this->_id = $id;
    }


    /**
     * Returns the value of the position instance variable
     *
     * @return string
     */
    public function getPosition() {
	return($this->_position);
    }


    /**
     * Sets the value of the position target instance variable
     *
     * @param string $id
     * @return void
     */
    public function setPosition($position) {
	switch ($position) {
	case self::REPLACE:
	case self::APPEND:
	case self::PREPEND:
	    $this->_position = $position;
	    break;
	default:
	    throw new ZFormElementException('Illegal value for position on ZFormAjaxElement:'. 
					    $position);

	}
    }
}

?>