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
class ZFormOption extends ZFormWebElement
{

    protected $_text;

    /**
     * Class constructor. Sets up the ZForm element, types it as a 'OPTION' element.
     * This is the base class for all the INPUT types supported by ZForm.
     *
     * @param string id The optional identifier for the newly created ZFormOption
     * @param ZFormWebElement The optional parent of the newly create ZFormOption
     * The default value is null which means the ZFormOption is a root element.
     * @param string $value The value of the HTML option field
     * @param string $text Text to be displayed as the option
     * @return     void
     */
    public function __construct($id = null, $parentNode = null, $value = null, $text = null)
    {
        parent::__construct($id, $parentNode, 'OPTION');

        $this->_text = $text;
        if ($value) {
            $this->value = $value;
        }
    }


    /**
     * Render the body of the ZFormOptions which is the $text property of the
     * ZFormOption object
     *
     * @param boolean $renderScriptBlock true of the element should emit any
     * JavaScript associated with component
     * @return void
     */
    public function renderBody($renderScriptBlock = false)
    {
        if ($this->_text) {
            echo $this->_text;
        }
    }


    /**
     * Sets the text property of the option. The text is what is displayed in the 
     * select list
     *
     * @param string $text
     * @return void
     */
    public function setText($text)
    {
        $this->_text = $text;
    }


    /**
     * Returns the text which will be displayed in the selection box.
     *
     * @return string
     */
    public function getText()
    {
        return($this->_text);
    }
}
?>