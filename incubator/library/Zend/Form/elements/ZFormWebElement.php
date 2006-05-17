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
 * @subpackage Elements
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */


/**
 * ZFormElement
 */
require_once 'ZForm/ZFormElement.php';


/**
 * @package    ZForm
 * @subpackage Elements
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class ZFormWebElement extends ZFormElement {

    protected $_tagName;
    protected $_topBlocks;
    protected $_bottomBlocks;
    protected $_wrapExisting;


    /**
     * @todo docblock
     */
    public function __construct($id = null, $parentNode = null,
                                $tagName = null, $wrapExisting = false)
    {
    	parent::__construct($id, $parentNode);

    	$this->_tagName = $tagName;
    	$this->_wrapExisting = $wrapExisting;
    }


    /**
     * ZFormWebElements maintain HTML attributes using the __get method.
     * 
     * @param string $nm The name of the HTML attribute to retrieve
     * @return string 
     */
    public function __get($nm)
    {
    	return $this->_attributes[$nm];
    }


    /**
     * Sets the value of the $nm HTML property on the ZFormWebElement.
     *
     * @param string $nm Name of the HTML element to set
     * @param string $val Value of the attribute
     */
    public function __set($nm, $val)
    {
    	$this->_attributes[$nm] = $val;

    }


    /**
     * Overridden implementation of getValue which returns the 'value' attribute
     * of the ZFormWebElement
     *
     * @return string
     */
    public function getValue()
    {
        return $this->_attributes['value'];
    }


    /**
     * Overridden implementation of setValue which sets the 'value' of the 
     * attribute for the ZFormWebElement
     *
     * @param string $value
     * @return void
     */
    public function setValue($value)
    {
        parent::setValue($value);

        $this->_attributes['value'] = $value;
    }


    /**
     * ZFormWebElements can be used to wraped existing HTML forms so that 
     * validators and behaviors can be added to the page witout changing the
     * original form definition. The setWrapExisting method tells the 
     * ZFormWebElement that to operate in this mode. NOTE: When wrapping existing
     * forms, ZFormWebElements do not generate HTML markup
     *
     * @param boolean $wrapExisting true means to wrap existing markup, false 
     * otherwise
     */
    public function setWrapExisting($wrapExisting)
    {
        $this->_wrapExisting = $wrapExisting;
    }

    /**
     * Generates the attributes of the ZFormWebElement HTML opening stanza. Values
     * are double quoted in the output stream.
     * 
     * @return void
     */
    public function emitAttributes()
    {
    	$result = '';
    	foreach ($this->_attributes as $key => $value) {
    	    $key = strtoupper($key);

    	    if ($key == 'VALUE') {
        		continue;
    	    }

    	    if ($key == 'ID')  {
        		$value = $this->getID();
    	    }

    	    if ($value) {
        		$result .= ' '.$key.'="'.$value.'"';
    	    } else {
        		$result .= ' '.$key;
        	}
    	}

    	if (! $this->name && (strtoupper($this->type) != 'SUBMIT')) {
        	    $result .= ' NAME="'.$this->getID().'"';
    	}

    	$value = $this->getValue();

    	if ($value) {
    	    $result .= ' VALUE="'.$value.'"';
    	}

    	return($result);
    }


    /**
     * Generates the opening stanza of the HTML which consists of the opening
     * tags and the attributes of the element. If the element does not contain
     * children the tag is also closed. Nothing is generated if the tag is
     * wrapping existing markup.
     *
     * @return void
     */
    public function open($renderScriptBlock = true)
    {
	if ($renderScriptBlock) {
	    $this->emitScriptBlocks(true);
	}	    
	$this->_applyClientBehaviors();

    	if (! $this->_wrapExisting) {
    	    $tagName = $this->_tagName;

    	    if ($tagName) {
        		echo '<'.$tagName.' '.$this->emitAttributes().">";
    	    }
    	}
    }


    /**
     * Generates the closing stanza for the HTML which consists of closing tag.
     * Nothing is generated if the element is wrapping existing markup.
     *
     * @return void
     */
    public function close($renderScriptBlock = true)
    {
    	if (! $this->_wrapExisting) {
    	    $tagName = $this->_tagName;

    	    if ($tagName) {
       		echo '</'.$tagName.">\n";
    	    }
    	}
	$this->_emitClientValidators();
	$this->_emitClientBehaviors();
	if ($renderScriptBlock) {
	    $this->emitScriptBlocks(false);
	}

    }


    /**
     * Renders the ZFormWebElement into its HTML into the current output 
     * stream.  Rendering the element consists of 
     *   1) Opening the tag 
     *   2) Rendering its body
     *   3) Closing the tag
     *   4) Emitting validators and behaviors associated with the 
     *      element
     *
     * @param boolean $renderScriptBlock optional parameter used to
     * instruct the element to also render it javascript block 
     * that go before and after the element
     * @return void
     */
    public function render($renderScriptBlock = true)
    {
    	$this->open();
    	$this->renderBody();
    	$this->close();
    }


    /**
     * Renders the body of the element's which by default consists of
     * rendering each of the element's children. Subclasses should implement
     * this methods to perform and specific tasks and then send super
     * for processing the children
     *
     * @param boolean $renderScriptBlock Determines of javascript blocks
     * are emitted, true = script is emitted, false they are not
     */
    public function renderBody($renderScriptBlock = false)
    {
    	$children = $this->_childNodes;

    	if ($children) {
    	    foreach ($children as $child) {
        		$child->render($renderScriptBlock);
    	    }
    	}
    }


    /**
     * Adds a fragment of script (optionally) named to the element either
     * at the top or bottom of the element. Named script blocks are maintained
     * in an assoc array.
     *
     * @param string $script JavaScript fragment
     * @param string $name option name of the script fragment
     * @param boolean $top true (default) add the fragment to the top of 
     * the element otherwise at the bottom
     * @return void
     */
    public function addScriptBlock($script, $name = null, $top = true)
    {
    	if (! $name) {
    	    $name = uniqid('id');
    	}

    	if ($top) {
    	    if ($top && ! $this->_topBlocks) {
        		$this->_topBlocks = array();
    	    }

    	    $this->_topBlocks[$name] = $script;
    	} else {
    	    if (! $this->_bottomBlocks) {
        		$this->_bottomBlocks = array();
    	    }

    	    $this->_bottomBlocks[$name] = $script;
    	}
    }


    /**
     * Iterates over the script fragments associated with the elements and
     * emits them into the current output stream.
     *
     * @param boolean $top true (default) emit the top script fragment, 
     * otherwise the bottom fragments are emitted.
     */
    public function emitScriptBlocks($top = true)
    {
    	$blocks = ($top ? $this->_topBlocks : $this->_bottomBlocks);

    	if ($blocks) {
    	    foreach ($blocks as $block) {
        		echo $block;
    	    }
    	}
    }


    /**
     * Retrieves the data associated with this element from the ZRequest object.
     *
     * @returns void
	 * @todo I don't like getting from both get and post,
	 * Options include getting parent until a form is identified
	 * Potentially pass bucket in as well
	 * I don't like searching up, because for other controls you may not
	 * be contained withing a form
	 */
    public function loadRequestData()
    {
        $id = $this->name ? $this->name : $this->getID();

    	$value  = ZRequest::get($id);

    	if (! $value) {
    	    $value = ZRequest::post($id);
    	}

    	if ($value) {
    	    $this->setValue($value);
    	} 

    	return parent::loadRequestData();
    }


    /**
     * Default implementation of retriving the memento associated with the element
     * that will be used during persistent (@see persist())
     * The default implementation does not persist anything, we implement it here
     * so subclasses are not required to
     *
     * @return mixed null for the default implementation, subclasses should
     * override.
     */
    public function getMemento()
    {
    }


    /**
     * The bookend implementation to @see getMemento(). This function is a void
     * implementation of the protocol to simplify the task of subclassing
     *
     */
    public function setMemento($memento)
    {
    }


    /**
     * Generates the validators associated with the element.
     *
     * @return void
     */
    protected function _emitClientValidators()
    {
    	if ($this->_validators) {
    	    echo "<SCRIPT>\n\tvar element;\n";
    	    echo "\telement = document.getElementById('" .$this->getIDPath(). "');\n";
    	    echo "</SCRIPT>\n";

    	    foreach ($this->_validators as $validator) {
        		if ($validator instanceof ZFormElementValidator) {
        		    $validator->emitClientValidator($this);
        		}
    	    }
    	}
    }


    /**
     * Generates the behaviors associated with the element. Validators
     * are guaranteed to run with the JavaScript variable 'element' bound
     * to the HTML presentation of the form element.
     *
     * @return void
     */
    protected function _emitClientBehaviors()
    {
    	if ($this->_behaviors) {
    	    echo "<SCRIPT>\n\tvar element;\n";
    	    echo "\telement = document.getElementById('" .$this->getIDPath(). "');\n";
    	    echo "</SCRIPT>\n";

    	    foreach ($this->_behaviors as $behavior) {
        		if ($behavior instanceof ZFormElementBehavior) {
        		    $behavior->emitClientBehavior($this);
        		}
    	    }
    	}
    }


    /**
     * This method enables the behaviors associated with element to have a
     * chance to modified the element before it is emitted.
     *
     * @return void
     */
    protected function _applyClientBehaviors()
    {
    	if ($this->_behaviors) {
    	    foreach ($this->_behaviors as $behavior) {
        		if ($behavior instanceof ZFormElementBehavior) {
        		    $behavior->applyClientBehavior($this);
        		}
    	    }
    	}
    }
}

?>