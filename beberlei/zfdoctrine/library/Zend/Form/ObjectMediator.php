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
 * @package    Form
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Mediator between filtered array return value of a form and fields of an object.
 *
 * @category   Zend
 * @package    Form
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Form_ObjectMediator
{
    const MULTIOPTIONS_CALLBACK = 1;
    const MULTIOPTIONS_ITERATOR = 2;
    const MULTIOPTIONS_INSTANCE_METHOD = 3;

    /**
     * @var Zend_Form
     */
    protected $_form = null;

    /**
     * @var string
     */
    protected $_className = null;

    /**
     * @var array
     */
    protected $_fields = array();

    /**
     * @var object
     */
    protected $_instance = null;

    /**
     * @param Zend_Form $form
     */
    public function __construct(Zend_Form $form, $className, array $fields = array())
    {
        $this->_form = $form;
        $this->_className = $className;

        foreach ($fields AS $name => $options) {
            $this->addField($name, $options);
        }
    }

    /**
     * @param string $name
     * @param array $options
     * @return Zend_Form_Mediator
     */
    public function addField($name, array $options = array())
    {
        if (!isset($options['setMethod'])) {
            $options['setMethod'] = "set".ucfirst($name);
        }
        if (!isset($options['getMethod'])) {
            $options['getMethod'] = "get".ucfirst($name);
        }

        $defaultOptions = array(
            'populateFilters' => array(),
            'filterMethod' => false,
            'validatorMethod' => false,
            'validatorErrorMessage' => 'Object validator method failed.',
        );
        $options = array_merge($defaultOptions, $options);
        $this->_fields[$name] = $options;
        return $this;
    }

    /**
     * @return void
     */
    public function populate()
    {
        $instance = $this->getInstance();

        $data = array();
        foreach ($this->_fields AS $name => $field) {
            $get = $field['getMethod'];
            if (!method_exists($instance, $get)) {
                throw new Zend_Form_Exception("Get method for field ".$name." on ".$this->_className." does not exist!");
            }

            $data[$name] = $instance->$get();
            if (count($field['populateFilters'])) {
                foreach ($field['populateFilters'] AS $filter) {
                    /* @var $filter Zend_Filter_Interface */
                    $data[$name] = $filter->filter($data[$name]);
                }
            }
        }
        $this->_form->populate($data);
    }

    /**
     * @param  array $data
     * @return bool
     */
    public function isValid($data)
    {
        $instance = $this->getInstance();

        $isValid = false;
        if ($this->_form->isValid($data)) {
            $isValid = true;
            foreach ($this->_fields AS $name => $field) {
                if ($field['validatorMethod'] !== false) {
                    if (call_user_func_array(array($instance, $field['validatorMethod']), array($data[$name])) == false) {
                        $element = $this->_form->getElement($name);
                        $element->addErrorMessage($field['validatorErrorMessage']);

                        $isValid = false;
                    }
                }
            }
        }
        return $isValid;
    }

    /**
     * Transfer the current form element values onto the attached instance
     *
     * @param bool $suppressArrayNotation
     */
    public function transferValues($suppressArrayNotation = false)
    {
        $instance = $this->getInstance();

        $values = $this->_form->getValues($suppressArrayNotation);
        foreach ($values AS $name => $value) {
            $field = $this->_fields[$name];

            if ($field['filterMethod'] !== false) {
                if (!method_exists($instance, $field['filterMethod'])) {
                    throw new Zend_Form_Exception("Filter method for field ".$name." on ".$this->_className." does not exist!");
                }
                $filterMethod = $field['filterMethod'];
                $value = $instance->$filterMethod($value);
            }

            $set = $field['setMethod'];
            if (!method_exists($instance, $set)) {
                throw new Zend_Form_Exception("Set method for field ".$name." on ".$this->_className." does not exist!");
            }

            $instance->$set($value);
        }
    }

    /**
     * @return object
     */
    public function getInstance()
    {
        if ($this->_instance == null) {
            $this->_instance = new $this->_className;
        }
        return $this->_instance;
    }

    /**
     * @param  object $object
     * @return Zend_Form_ObjectMeditor
     */
    public function setInstance($object)
    {
        if (!($object instanceof $this->_className)) {
            throw new Zend_Form_Exception("Given instance is not of the prefered type '.$this->_className.' of the mediator.");
        }

        $this->_instance = $object;
        return $this;
    }

    /**
     * @return Zend_Form
     */
    public function getForm()
    {
        return $this->_form;
    }
}