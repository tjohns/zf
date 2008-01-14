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
 * Zend_Form_Decorator_Form
 *
 * Render a Zend_Form object. 
 *
 * Accepts following options:
 * - separator: Separator to use between elements
 * - helper: which view helper to use when rendering form. Should accept three
 *   arguments, string content, a name, and an array of attributes.
 *
 * Any other options passed will be used as HTML attributes of the form tag.
 * 
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Decorator
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */
class Zend_Form_Decorator_Form extends Zend_Form_Decorator_Abstract
{
    /**
     * Default view helper
     * @var string
     */
    protected $_helper = 'form';

    /**
     * Set view helper for rendering form
     * 
     * @param  string $helper 
     * @return Zend_Form_Decorator_Form
     */
    public function setHelper($helper)
    {
        $this->_helper = (string) $helper;
        return $this;
    }

    /**
     * Get view helper for rendering form
     * 
     * @return string
     */
    public function getHelper()
    {
        if (isset($this->_options['helper'])) {
            $this->setHelper($this->_options['helper']);
            unset($this->_options['helper']);
        }
        return $this->_helper;
    }

    /**
     * Retrieve decorator options
     *
     * Assures that form action and method are set, and sets appropriate 
     * encoding type if current method is POST.
     * 
     * @return array
     */
    public function getOptions()
    {
        if (null !== ($element = $this->getElement())) {
            if ($element instanceof Zend_Form) {
                $element->getAction();
                $method = $element->getMethod();
                if ($method == Zend_Form::METHOD_POST) {
                    $this->_options['enctype'] = 'application/x-www-form-urlencoded';
                }
                $this->_options = array_merge($this->_options, $element->getAttribs());
            } elseif ($element instanceof Zend_Form_DisplayGroup) {
                $this->_options = array_merge($this->_options, $element->getAttribs());
            }
        }

        return $this->_options;
    }

    /**
     * Render a form
     *
     * Replaces $content entirely from currently set element.
     * 
     * @param  string $content 
     * @return string
     */
    public function render($content)
    {
        $form    = $this->getElement();
        $view    = $form->getView();
        if (null === $view) {
            return $content;
        }

        $content = '';
        $items   = array();
        foreach ($form as $item) {
            $item->setView($view);
            $items[] = $item->render();
        }
        $content = implode($this->getSeparator(), $items);
        $helper  = $this->getHelper();
        return $view->$helper($form->getName(), $content, $this->getOptions()); 
    }
}
