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
 * ZFormWebElement
 */
require_once 'ZForm/elements/ZFormWebElement.php';


/**
 * @package    ZForm
 * @subpackage Elements
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class ZFormLink extends ZFormWebElement
{

    protected $_text;


    /**
     * Class constructor. 
     *
     * @param string id The optional identifier for the newly created ZFormButton
     * @param ZFormWebElement The optional parent of the newly create ZFormButton.
     * The default value is null which means the ZFormButton is a root element.
     * @param string $text The text to appear in the HTML anchor
     * @return     void
     */
    public function __construct($id = null, $parentNode = null, $text = '')
    {
    	parent::__construct($id, $parentNode, 'A');

    	$this->_text = $text;
    }


    /**
     * Renders the text content of the link.
     *
     * @param boolean $renderScriptBlock 
     *
     * @return void
     */
    public function renderBody($renderScriptBlock = false)
    {
    	if ($this->_text) {
    	    echo $this->_text;
    	}

    	parent::renderBody($renderScriptBlock);
    }


    /**
     * Returns the memento used to persist the state of the link,
     * which is simply the text. 
     *
     * @return string
     */
    public function getMemento()
    {
        return($this->_text);
    }


    /**
     * Retores the state of the link from its memento
     *
     * @param string $memento
     * @return void
     */
    public function setMemento($memento)
    {
        $this->_text = $memento;
    }


    /**
     * Property setter for the link text field
     * 
     * @param string $text
     * @return void
     */
    public function setText($text)
    {
        $this->_text = $text;
    }


    /**
     * Property getter for the link text field
     * 
     * @return string
     */
    public function getText()
    {
        return($this->_text);
    }
}
?>