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
 * @package    Zend_View
 * @subpackage Helpers
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */


/**
 * Abstract class for extension
 */
require_once 'Zend/View/Helper/FormElement.php';


/**
 * Helper to generate a "reset" button
 *
 * @package    Zend_View
 * @subpackage Helpers
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class Zend_View_Helper_FormReset extends Zend_View_Helper_FormElement {
    
    /**
     * Generates a 'reset' button.
     * 
     * @access public
     * 
     * @param string|array $name If a string, the element name.  If an
     * array, all other parameters are ignored, and the array elements
     * are extracted in place of added parameters.
     * 
     * @param mixed $value The element value.
     * 
     * @param array $attribs Attributes for the element tag.
     * 
     * @return string The element XHTML.
     */
    public function formReset($name = '', $value = 'Reset', $attribs = null)
    {
        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable
        
        // always enabled
        $xhtml = '<input type="reset"'
               . ' name="' . htmlspecialchars($name) . '"';
        
        // add a value if one is given
        if (! empty($value)) {
            $xhtml .= ' value="' . htmlspecialchars($value) . '"';
        }
        
        // add attributes, close, and return
        $xhtml .= $this->_htmlAttribs($attribs) . ' />';
        return $xhtml;
    }
}
