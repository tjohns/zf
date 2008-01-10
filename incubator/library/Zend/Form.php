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
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Zend_Form
 * 
 * @category   Aend
 * @package    Zend_Form
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id:$
 */
class Zend_Form implements Iterator
{
    /**
     * Form elements
     * @var array
     */
    protected $_elements = array();

    /**
     * Current elements with errors
     * @var array
     */
    protected $_errors = array();

    /**
     * Constructor
     * 
     * @param  array $elements 
     * @return void
     */
    public function __construct(array $elements = array())
    {
        $this->setElements($elements);
    }

    /**
     * Set all elements at once
     * 
     * @param  array $elements 
     * @return Zend_Form_Abstract
     */
    public function setElements(array $elements)
    {
        foreach ($elements as $name => $element) {
            $this->addElement($name, $element);
        }

        return $this;
    }

    /**
     * Add a single named element
     *
     * @param  string $name Element name
     * @param  
     */
    public function addElement($element, $name = null)
    {
        if (is_string($element)) {
            // would need a helper registry to load the element
            // if (null === $name): throw an exception
            // $element = new $class($name)
        }

        if (!$element instanceof Zend_Form_Element) {
            require_once 'Zend/Form/Exception.php';
            throw new Zend_Form_Exception('Invalid element type provided');
        }

        if (null === $name) {
            $name = $element->getName();
        }

        $this->_elements[$name] = $element;
        return $this;
    }

    /**
     * Get form element
     *
     * @param  string $name Name of element
     * @return Zend_Form_Element|false
     */
    public function getElement($name)
    {
        $name = (string) $name;
        if (isset($this->_elements[$name])) {
            return $this->_elements[$name];
        }

        return false;
    }

    /**
     * Get all form elements
     *
     * @todo return in element order
     * @return array
     */
    public function getElements()
    {
        return $this->_elements;
    }

    /**
     * Remove a form element
     *
     * @param  string $name Element name to remove
     * @return bool
     */
    public function removeElement($name)
    {
        $name = (string) $name;
        if (isset($this->_elements[$name])) {
            unset($this->_elements[$name]);
            return true;
        }

        return false;
    }

    /**
     * Iterator: current item
     * 
     * @return Zend_Form_Element
     */
    public function current()
    {
        return current($this->_elements);
    }

    /**
     * Iterator: key
     * 
     * @return string
     */
    public function key()
    {
        return key($this->_elements);
    }

    /**
     * Iterator: next
     * 
     * @return mixed
     */
    public function next()
    {
        return next($this->_elements);
    }

    /**
     * Iterator: rewind
     * 
     * @return mixed
     */
    public function rewind()
    {
        return reset($this->_elements);
    }

    /**
     * Iterator: valid
     * 
     * @return mixed
     */
    public function valid()
    {
        return current($this->_elements);
    }

    /**
     * Populate elements with values
     *
     * @param  array $values Key/value pairs
     * @return Zend_Form_Abstract
     */
    public function populate(array $values)
    {
        foreach ($values as $name => $value) {
            if (isset($this->_elements[$name])) {
                $this->_elements[$name]->setValue($value);
            }
        }

        return $this;
    }

    /**
     * Divide a form into sections
     *
     * @todo   Unimplemented
     * @param  string $label
     * @param  array $elements
     * @param  string $page
     * @return Zend_Form_Abstract
     */
    public function addSection($label, array $elements, $page = null)
    {
        trigger_error(__CLASS__ . '::' . __FUNCTION__ . '() is currently unimplemented', E_USER_NOTICE);
        return $this;
    }

    /**
     * Retrieve a form section
     * 
     * @todo   Unimplemented
     * @param  string $label 
     * @return void
     */
    public function getSection($label)
    {
        trigger_error(__CLASS__ . '::' . __FUNCTION__ . '() is currently unimplemented', E_USER_NOTICE);
        return $this;
    }

    /**
     * Divide a form into pages
     *
     * @todo   Unimplemented
     * @param  string $label
     * @param  array  $elements
     * @return Zend_Form_Abstract
     */
    public function addPage($label, array $elements)
    {
        trigger_error(__CLASS__ . '::' . __FUNCTION__ . '() is currently unimplemented', E_USER_NOTICE);
        return $this;
    }

    /**
     * Retrieve a form page
     * 
     * @todo   Unimplemented
     * @param  string $label 
     * @return void
     */
    public function getPage($label)
    {
        trigger_error(__CLASS__ . '::' . __FUNCTION__ . '() is currently unimplemented', E_USER_NOTICE);
        return $this;
    }

    /**
     * Set view
     *
     * @param  Zend_View_Interface
     * @return Zend_Form_Abstract
     */
    public function setView(Zend_View_Interface $view)
    {
        $this->_view = $view;
        return $this;
    }

    /**
     * Get view
     *
     * @return Zend_View_Interface
     */
    public function getView()
    {
        return $this->_view;
    }

    /**
     * Validate form
     */
    public function isValid($spec = null, $value = null)
    {
        if (null === $spec) {
            $spec = $this->getValues();
        } elseif (is_string($spec)) {
            $element = $this->getElement($spec);
            if (!$element) {
                require_once 'Zend/Form/Exception.php';
                throw new Zend_Form_Exception(sprintf('Cannot validate element "%s" as it does not exist', $spec));
            }

            return $element->isValid($value);
        } elseif (!is_array($spec)) {
            require_once 'Zend/Form/Exception.php';
            throw new Zend_Form_Exception(sprintf('isValid() expects a key/value pair or array of key/value pairs; received type %s', gettype($spec)));
        }

        $this->_errors = array();
        foreach ($this as $name => $element) {
            $value = (isset($spec[$name])) ? $spec[$name] : null;
            if (!$element->isValid($spec[$name])) {
                $this->_errors[$name] = $element;
            }
        }

        return (empty($this->_errors));
    }

    /**
     * Get filtered element values as array
     *
     * @return array
     */
    public function getValues()
    {
        $values = array();
        foreach ($this as $name => $element) {
            $values[$name] = $element->getValue();
        }

        return $values;
    }

    /**
     * Get raw element values as array
     *
     * @return array
     */
    public function getRawValues()
    {
        $values = array();
        foreach ($this as $name => $element) {
            $values[$name] = $element->getRawValue();
        }

        return $values;
    }

    /**
     * Get all current element errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * Set default values
     *
     * @param  array $defaults
     * @return Zend_Form_Abstract
     */
    public function setDefaults(array $defaults)
    {
        foreach ($defaults as $key => $value) {
            if (isset($this[$key])) {
                $this[$key]->setValue($value);
            }
        }

        return $this;
    }

    /**
     * Render
     *
     * @todo   Order elements according to internal order
     * @param  Zend_View_Interface
     * @return string
     */
    public function render(Zend_View_Interface $view = null)
    {
        if (null !== $view) {
            $this->setView($view);
        } elseif ((null === $view) && (null === ($view = $this->getView()))) {
            require_once 'Zend/Form/Exception.php';
            throw new Zend_Form_Exception('No view object present to render');
        }

        $form = '';
        foreach ($this as $element) {
            $form .= $element->render($view);
        }

        return $form;
    }

    /**
     * __toString
     */
    public function __toString()
    {
        return $this->render();
    }
}
