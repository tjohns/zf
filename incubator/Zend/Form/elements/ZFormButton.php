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
 * ZForInputElement
 */
require_once 'ZForm/elements/ZFormInputElement.php';


/**
 * @package    ZForm
 * @subpackage Elements
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class ZFormButton extends ZFormInputElement
{
    /**
     * Class constructor. Sets up the ZForm element, types it as a 'FORM' element
     *
     * @param string id The optional identifier for the newly created ZFormButton
     * @param ZFormWebElement The optional parent of the newly create ZFormButton.
     * The default value is null which means the ZFormButton is a root element.
     * @return void
     */
    public function __construct($id = null, $parentNode = null)
    {
    	parent::__construct($id, $parentNode, 'BUTTON');
    }


    /**
     * Sets the HTML value of the ZFormButton to the string passed
     *
     * @param string $value
     * @return void
     */
    public function button($value = null)
    {
    	if ($value) {
    	    $this->setValue($value);
    	}
    }
}