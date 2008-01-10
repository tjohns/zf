<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Abstract class for extension
 */
require_once 'Zend/View/Helper/FormElement.php';


/**
 * Helper to render errors for a form element
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_View_Helper_FormErrors extends Zend_View_Helper_FormElement
{
    /**
     * @var Zend_Form_Element
     */
    protected $_element;

    /**#@+
     * @var string Element block start/end tags and separator
     */
    protected $_htmlElementEnd       = '</li></ul>';
    protected $_htmlElementStart     = '<ul class="errors"%s><li>';
    protected $_htmlElementSeparator = '</li><li>';
    /**#@-*/

    /**
     * Render form errors
     * 
     * @param  string|array $errors Error(s) to render
     * @param  array $options
     * @return string
     */
    public function formErrors($errors, array $options = null)
    {
        $attribs = $this->_htmlAttribs($options);
        $start   = sprintf($this->_htmlElementStart, $attribs);

        $html  = $start
               . implode($this->_htmlElementSeparator, (array) $errors)
               . $this->_htmlElementEnd;

        return $html;
    }
}
