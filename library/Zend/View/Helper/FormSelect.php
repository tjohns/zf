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
 * Helper to generate "select" list of options
 * 
 * @package    Zend_View
 * @subpackage Helpers
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class Zend_View_Helper_FormSelect extends Zend_View_Helper_FormElement {
    
    /**
     * Generates 'select' list of options.
     * 
     * @access public
     * 
     * @param string|array $name If a string, the element name.  If an
     * array, all other parameters are ignored, and the array elements
     * are extracted in place of added parameters.
     * 
     * @param mixed $value The option value to mark as 'selected'; if an 
     * array, will mark all values in the array as 'selected' (used for
     * multiple-select elements).
     * 
     * @param array|string $attribs Attributes added to the 'select' tag.
     * 
     * @param array $options An array of key-value pairs where the array
     * key is the radio value, and the array value is the radio text.
     * 
     * @param string $listsep When disabled, use this list separator string
     * between list values.
     * 
     * @return string The select tag and options XHTML.
     */
    public function formSelect($name, $value = null, $attribs = null,
        $options = null, $listsep = "<br />\n")
    {
        $info = $this->_getInfo($name, $value, $attribs, $options, $listsep);
        extract($info); // name, value, attribs, options, listsep, disable
        
        // force $value to array so we can compare multiple values
        // to multiple options.
        settype($value, 'array');
        
        // check for multiple attrib and change name if needed
        if (isset($attribs['multiple']) &&
            $attribs['multiple'] == 'multiple' &&
            substr($name, -2) != '[]') {
            $name .= '[]';
        }
        
        // check for multiple implied by the name and set attrib if
        // needed
        if (substr($name, -2) == '[]') {
            $attribs['multiple'] = 'multiple';
        }
                
        // now start building the XHTML.
        if ($disable) {
        
            // disabled.
            // generate a plain list of selected options.
            // show the label, not the value, of the option.
            $list = array();
            foreach ($options as $opt_value => $opt_label) {
                if (in_array($opt_value, $value)) {
                    // add the hidden value
                    $opt = $this->_hidden($name, $opt_value);
                    // add the display label
                    $opt .= htmlspecialchars($opt_label);
                    // add to the list
                    $list[] = $opt;
                }
            }
            $xhtml = implode($listsep, $list);
            
        } else {
        
            // enabled.
            // the surrounding select element first.
            $xhtml = '<select'
                   . ' name="' . htmlspecialchars($name) . '"'
                   . $this->_htmlAttribs($attribs)
                   . ">\n\t";
            
            // build the list of options
            $list = array();
            foreach ($options as $opt_value => $opt_label) {
            
                // option value and label
                $opt = '<option'
                     . ' value="' . htmlspecialchars($opt_value) . '"'
                     . ' label="' . htmlspecialchars($opt_label) . '"';
                     
                // selected?
                if (in_array($opt_value, $value)) {
                    $opt .= ' selected="selected"';
                }
                
                // close and add
                $opt .= '>' . htmlspecialchars($opt_label) . "</option>";
                $list[] = $opt;
            }
            
            // add the options to the xhtml and close the select
            $xhtml .= implode("\n\t", $list) . "\n</select>";
            
        }
        
        return $xhtml;
    }
}
