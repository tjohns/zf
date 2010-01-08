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
 * @category    ZendX
 * @package     ZendX_JQuery
 * @subpackage  View
 * @copyright   Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license     http://framework.zend.com/license/new-bsd     New BSD License
 * @version     $Id$
 */

/**
 * @see ZendX_JQuery_View_Helper_UiWidget
 */
require_once "ZendX/JQuery/View/Helper/UiWidget.php";

/**
 * jQuery Accordion View Helper
 *
 * @uses 	   Zend_Json
 * @package    ZendX_JQuery
 * @subpackage View
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
  */
class ZendX_JQuery_View_Helper_AccordionContainer extends ZendX_JQuery_View_Helper_UiWidget
{
    protected $_panes = array();

    protected $_elementHtmlTemplate = '<li class="ui-accordion-group"><a href="#" class="ui-accordion-header">%s</a><div class="ui-accordion-content">%s</div></li>';

    /**
     * Add Accordion Pane for the Accordion-Id
     *
     * @param  string $id
     * @param  string $name
     * @param  string $content
     * @return ZendX_JQuery_View_Helper_AccordionContainer
     */
    public function addPane($id, $name, $content, array $options=array())
    {
        if(!isset($this->_panes[$id])) {
            $this->_panes[$id] = array();
        }
        if(strlen($name) == 0 && isset($options['title'])) {
            $name = $options['title'];
        }
        $this->_panes[$id][] = array('name' => $name, 'content' => $content, 'options' => $options);
        return $this;
    }

    /**
     * Render Accordion with the currently registered elements.
     *
     * If no arguments are given, the accordion object is returned so that
     * chaining the {@link addPane()} function allows to register new elements
     * for an accordion.
     *
     * @link   http://docs.jquery.com/UI/Accordion
     * @param  string $id
     * @param  array  $params
     * @param  array  $attribs
     * @return string|ZendX_JQuery_View_Helper_AccordionContainer
     */
    public function accordionContainer($id=null, array $params=array(), array $attribs=array())
    {
        if(0 === func_num_args()) {
            return $this;
        }

        if(!isset($attribs['id'])) {
            $attribs['id'] = $id;
        }

        if(isset($this->_panes[$id])) {
            $html = "";
            foreach($this->_panes[$id] AS $element) {
                $html .= sprintf($this->_elementHtmlTemplate, $element['name'], $element['content']);
            }

            if(count($params) > 0) {
    	        /**
    	         * @see Zend_Json
    	         */
                require_once "Zend/Json.php";
                $params = Zend_Json::encode($params);
            } else {
                $params = "{}";
            }

            $js = sprintf('%s("#%s").accordion(%s);',
                ZendX_JQuery_View_Helper_JQuery::getJQueryHandler(),
                $attribs['id'],
                $params
            );
            $this->jquery->addOnLoad($js);

            $html = '<ul'
                  . $this->_htmlAttribs($attribs)
                  . '>'.PHP_EOL
                  . $html
                  . '</ul>'.PHP_EOL;
            return $html;
            unset($this->_panes[$id]);
        }
        return '';
    }
}