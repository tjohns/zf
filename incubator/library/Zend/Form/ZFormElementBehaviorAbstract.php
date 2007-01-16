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
 * ZFormElemen, used as target of behavior
 */
require_once 'ZForm/ZFormElement.php';


/**
 * @package    ZForm
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
abstract class ZFormElementBehaviorAbstract
{
    /**
     * Class constructor. Initialize $_element to as the target of behavior.
     *
     * @param ZFormElement $targetElement The form element to apply the behavior
     * into.
     */
    public function __construct($targetElement)
    {
    	$this->_element = $targetElement;

    	if ($this->_element) {
    	    $this->_element->addBehavior($this);
    	}
    }


    /**
     * Abstract method. This method is invoked during the render cycle of the
     * behavior. The this method in invoked the context of the DHTML on the page will
     * contain a javascript reference to the element idenfitied by the target element
     * For example:
     * <SCRIPT>
     *    var element;
     *    element = document.getElementById('helloWorld');
     * </SCRIPT>
     *
     * @param ZFormElement $element
     */
    abstract public function emitClientBehavior($element);


    /**
     * Abstract method which must be implemented by subclasses. This method is
     * invoked before any rendering is started of the component. This give the
     * bahavior a chance to make any changes to the layout of the element
     * its children or its parent chain.
     *
     */
    abstract public function applyClientBehavior(ZFormElement $element);
}
?>
