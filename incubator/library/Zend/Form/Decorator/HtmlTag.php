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
 * @package    Zend_Form
 * @subpackage Decorator
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Form_Decorator_Abstract */
require_once 'Zend/Form/Decorator/Abstract.php';

/**
 * Zend_Form_Decorator_Element_HtmlTag
 *
 * Wraps content in an HTML block tag.
 *
 * Options accepted are:
 * - tag: tag to use in decorator
 *
 * Any other options passed are processed as HTML attributes of the tag.
 * 
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Decorator
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
class Zend_Form_Decorator_HtmlTag extends Zend_Form_Decorator_Abstract
{
    /**
     * Convert options to tag attributes
     * 
     * @return string
     */
    protected function _htmlAttribs(array $attribs)
    {
        $xhtml = '';
        foreach ((array) $attribs as $key => $val) {
            $key = htmlspecialchars($key, ENT_COMPAT, 'UTF-8');
            if (is_array($val)) {
                $val = implode(' ', $val);
            }
            $val =htmlspecialchars($val, ENT_COMPAT, 'UTF-8');
            $xhtml .= " $key=\"$val\"";
        }
        return $xhtml;
    }

    /**
     * Normalize tag
     *
     * Ensures tag is alphabetical characters only, and all lowercase.
     * 
     * @param  string $tag 
     * @return string
     */
    public function normalizeTag($tag)
    {
        if (!isset($this->_tagFilter)) {
            require_once 'Zend/Filter.php';
            require_once 'Zend/Filter/Alpha.php';
            require_once 'Zend/Filter/StringToLower.php';
            $this->_filter = new Zend_Filter();
            $this->_filter->addFilter(new Zend_Filter_Alpha())
                   ->addFilter(new Zend_Filter_StringToLower());
        }
        return $this->_filter->filter($tag);
    }

    /**
     * Render content wrapped in an HTML tag
     * 
     * @param  string $content 
     * @return string
     */
    public function render($content)
    {
        $options = $this->getOptions();
        $tag     = 'div';
        if (isset($options['tag'])) {
            $tag = $this->normalizeTag($options['tag']);
            unset($options['tag']);
        }

        return '<' . $tag . $this->_htmlAttribs($options) . '>'
               . $content
               . '</' . $tag . '>';
    }
}
