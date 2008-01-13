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
 * @category   Zend
 * @package    Zend_Form
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
class Zend_Form implements Iterator
{
    /**#@+
     * Plugin loader type constants
     */
    const DECORATOR = 'DECORATOR';
    const ELEMENT = 'ELEMENT';
    /**#@-*/

    /**
     * Form metadata and attributes
     * @var array
     */
    protected $_attribs = array();

    /**
     * Decorators for rendering
     * @var array
     */
    protected $_decorators = array();

    /**
     * Groups of elements grouped for display purposes
     * @var array
     */
    protected $_displayGroups = array();

    /**
     * Form elements
     * @var array
     */
    protected $_elements = array();

    /**
     * Plugin loaders
     * @var array
     */
    protected $_loaders = array();

    /**
     * Sub forms
     * @var array
     */
    protected $_subForms = array();

    /**
     * @var Zend_Translate
     */
    protected $_translator;

    /**
     * @var Zend_View_Interface
     */
    protected $_view;

    /**
     * Constructor
     *
     * Registers form view helper as decorator
     * 
     * @param mixed $options 
     * @return void
     */
    public function __construct($options = null)
    {
        $this->addDecorator('viewHelper', array('helper' => 'form'));
    }

    public function setOptions(array $options)
    {
    }

    public function setConfig(Zend_Config $config)
    {
    }

 
    // Loaders 

    /**
     * Set plugin loaders for use with decorators and elements
     * 
     * @param  Zend_Loader_PluginLoader_Interface $loader 
     * @param  string $type 'decorator' or 'element'
     * @return Zend_Form
     * @throws Zend_Form_Exception on invalid type
     */
    public function setPluginLoader(Zend_Loader_PluginLoader_Interface $loader, $type = null)
    {
        $type = strtoupper($type);
        switch ($type) {
            case self::DECORATOR:
            case self::ELEMENT:
                $this->_loaders[$type] = $loader;
                return $this;
            default:
                require_once 'Zend/Form/Exception.php';
                throw new Zend_Form_Exception(sprintf('Invalid type "%s" provided to setPluginLoader()', $type));
        }
    }

    /**
     * Retrieve plugin loader for given type
     *
     * $type may be one of:
     * - decorator
     * - element
     *
     * If a plugin loader does not exist for the given type, defaults are 
     * created.
     * 
     * @param  string $type 
     * @return Zend_Loader_PluginLoader_Interface
     */
    public function getPluginLoader($type = null)
    {
        $type = strtoupper($type);
        if (!isset($this->_loaders[$type])) {
            switch ($type) {
                case self::DECORATOR:
                    $prefixSegment = 'Form_Decorator';
                    $pathSegment   = 'Form/Decorator';
                    break;
                case self::ELEMENT:
                    $prefixSegment = 'Form_Element';
                    $pathSegment   = 'Form/Element';
                    break;
                default:
                    require_once 'Zend/Form/Exception.php';
                    throw new Zend_Form_Exception(sprintf('Invalid type "%s" provided to getPluginLoader()', $type));
            }

            require_once 'Zend/Loader/PluginLoader.php';
            $this->_loaders[$type] = new Zend_Loader_PluginLoader(
                array('Zend_' . $prefixSegment . '_' => 'Zend/' . $pathSegment . '/')
            );
        }

        return $this->_loaders[$type];
    }

    /**
     * Add prefix path for plugin loader
     *
     * If no $type specified, assumes it is a base path for both filters and 
     * validators, and sets each according to the following rules:
     * - decorators: $prefix = $prefix . '_Decorator'
     * - elements: $prefix = $prefix . '_Element'
     *
     * Otherwise, the path prefix is set on the appropriate plugin loader.
     *
     * If $type is 'decorators', sets the path in the decorator plugin loader 
     * for all elements. Additionally, if no $type is provided, 
     * {@link Zend_Form_Element::addPrefixPath()} is called on each element.
     * 
     * @param  string $path 
     * @return Zend_Form
     * @throws Zend_Form_Exception for invalid type
     */
    public function addPrefixPath($prefix, $path, $type = null) 
    {
        $type = strtoupper($type);
        switch ($type) {
            case self::DECORATOR:
            case self::ELEMENT:
                $loader = $this->getPluginLoader($type);
                $loader->addPrefixPath($prefix, $path);
                return $this;
            case null:
                $prefix = rtrim($prefix, '_');
                $path   = rtrim($path, DIRECTORY_SEPARATOR);
                foreach (array(self::DECORATOR, self::ELEMENT) as $type) {
                    $cType        = ucfirst(strtolower($type));
                    $pluginPath   = $path . DIRECTORY_SEPARATOR . $cType . DIRECTORY_SEPARATOR;
                    $pluginPrefix = $prefix . '_' . $cType;
                    $loader       = $this->getPluginLoader($type);
                    $loader->addPrefixPath($pluginPrefix, $pluginPath);
                }
                foreach ($this->getElements() as $element) {
                    $element->addPrefixPath($prefix, $path);
                }
                return $this;
            default:
                require_once 'Zend/Form/Exception.php';
                throw new Zend_Form_Exception(sprintf('Invalid type "%s" provided to getPluginLoader()', $type));
        }
    }


    // Form metadata:
    
    /**
     * Set form attribute
     * 
     * @param  string $key 
     * @param  mixed $value 
     * @return Zend_Form
     */
    public function setAttrib($key, $value)
    {
        $key = (string) $key;
        $this->_attribs[$key] = $value;
        return $this;
    }

    /**
     * Add multiple form attributes at once
     * 
     * @param  array $attribs 
     * @return Zend_Form
     */
    public function addAttribs(array $attribs)
    {
        foreach ($attribs as $key => $value) {
            $this->setAttrib($key, $value);
        }
        return $this;
    }

    /**
     * Set multiple form attributes at once
     *
     * Overwrites any previously set attributes.
     * 
     * @param  array $attribs 
     * @return Zend_Form
     */
    public function setAttribs(array $attribs)
    {
        $this->clearAttribs();
        return $this->addAttribs($attribs);
    }

    /**
     * Retrieve a single form attribute
     * 
     * @param  string $key 
     * @return mixed
     */
    public function getAttrib($key)
    {
        $key = (string) $key;
        if (!isset($this->_attribs[$key])) {
            return null;
        }

        return $this->_attribs[$key];
    }

    /**
     * Retrieve all form attributes/metadata
     * 
     * @return array
     */
    public function getAttribs()
    {
        return $this->_attribs;
    }

    /**
     * Remove attribute
     * 
     * @param  string $key 
     * @return bool
     */
    public function removeAttrib($key)
    {
        if (isset($this->_attribs[$key])) {
            unset($this->_attribs[$key]);
            return true;
        }

        return false;
    }

    /**
     * Clear all form attributes
     * 
     * @return Zend_Form
     */
    public function clearAttribs()
    {
        $this->_attribs = array();
        return $this;
    }

 
    // Element interaction: 

    /**
     * Add a new element
     *
     * $element may be either a string element type, or an object of type 
     * Zend_Form_Element. If a string element type is provided, $name must be 
     * provided, and $options may be optionally provided for configuring the 
     * element.
     *
     * If a Zend_Form_Element is provided, $name may be optionally provided, 
     * and any provided $options will be ignored.
     * 
     * @param  string|Zend_Form_Element $element 
     * @param  string $name 
     * @param  array|Zend_Config $options 
     * @return Zend_Form
     */
    public function addElement($element, $name = null, $options = null)
    {
        if (is_string($element)) {
            if (null === $name) {
                require_once 'Zend/Form/Exception.php';
                throw new Zend_Form_Exception('Elements specified by string must have an accompanying name');
            }
            $class = $this->getPluginLoader(self::ELEMENT)->load($element);
            $this->_elements[$name] = new $class($name, $options);
        } elseif ($element instanceof Zend_Form_Element) {
            if (null === $name) {
                $name = $element->getName();
            }
            $this->_elements[$name] = $element;
        }
        return $this;
    }

    /**
     * Add multiple elements at once
     * 
     * @param  array $elements 
     * @return Zend_Form
     */
    public function addElements(array $elements)
    {
        foreach ($elements as $key => $spec) {
            $name = null;
            if (!is_numeric($key)) {
                $name = $key;
            }

            if (is_string($spec) || ($spec instanceof Zend_Form_Element)) {
                $this->addElement($spec, $name);
                continue;
            }

            if (is_array($spec)) {
                $argc = count($spec);
                $options = array();
                switch ($argc) {
                    case 0:
                        continue;
                    case (1 <= $argc):
                        $type = array_shift($spec);
                    case (2 <= $argc):
                        $name = array_shift($spec);
                    case (3 <= $argc):
                        $options = array_shift($spec);
                    default:
                        $this->addElement($type, $name, $options);
                }
            }
        }
        return $this;
    }

    /**
     * Set form elements (overwrites existing elements)
     * 
     * @param  array $elements 
     * @return Zend_Form
     */
    public function setElements(array $elements)
    {
        $this->clearElements();
        return $this->addElements($elements);
    }

    /**
     * Retrieve a single element
     * 
     * @param  string $name 
     * @return Zend_Form_Element|null
     */
    public function getElement($name)
    {
        if (isset($this->_elements[$name])) {
            return $this->_elements[$name];
        }
        return null;
    }

    /**
     * Retrieve all elements
     * 
     * @return array
     */
    public function getElements()
    {
        return $this->_elements;
    }

    /**
     * Remove element
     * 
     * @param  string $name 
     * @return boolean
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
     * Remove all form elements
     * 
     * @return Zend_Form
     */
    public function clearElements()
    {
        $this->_elements = array();
        return $this;
    }

    /**
     * Set default values for elements
     * 
     * @param  array $defaults 
     * @return Zend_Form
     */
    public function setDefaults(array $defaults)
    {
        foreach ($defaults as $key => $value) {
            $this->setDefault($key, $value);
        }
        return $this;
    }

    /**
     * Set default value for an element
     * 
     * @param  string $name 
     * @param  mixed $value 
     * @return Zend_Form
     */
    public function setDefault($name, $value)
    {
        $name = (string) $name;
        if ($element = $this->getElement($name)) {
            $element->setValue($value);
        }
        return $this;
    }

    /**
     * Retrieve value for single element
     * 
     * @param  string $name 
     * @return mixed
     */
    public function getValue($name)
    {
        if ($element = $this->getElement($name)) {
            return $element->getValue();
        }
        return null;
    }

    /**
     * Retrieve all form element values
     * 
     * @return array
     */
    public function getValues()
    {
        $values = array();
        foreach ($this->getElements() as $key => $element) {
            $values[$key] = $element->getValue();
        }

        return $values;
    }

    /**
     * Get unfiltered element value
     * 
     * @param  string $name 
     * @return mixed
     */
    public function getUnfilteredValue($name)
    {
        if ($element = $this->getElement($name)) {
            return $element->getUnfilteredValue();
        }
        return null;
    }

    /**
     * Retrive all unfiltered element values
     * 
     * @return array
     */
    public function getUnfilteredValues()
    {
        $values = array();
        foreach ($this->getElements() as $key => $element) {
            $values[$key] = $element->getUnfilteredValue();
        }

        return $values;
    }

 
    // Element groups: 

    /**
     * Add a form group/subform
     * 
     * @param  Zend_Form $form 
     * @param  string $name 
     * @param  int $order 
     * @return Zend_Form
     */
    public function addSubForm(Zend_Form $form, $name, $order = null)
    {
        $name = (string) $name;
        $this->_subForms[$name] = $form;
        return $this;
    }

    /**
     * Add multiple form subForms/subforms at once
     * 
     * @param  array $subForms 
     * @return Zend_Form
     */
    public function addSubForms(array $subForms)
    {
        foreach ($subForms as $key => $spec) {
            $name = null;
            if (!is_numeric($key)) {
                $name = $key;
            }

            if ($spec instanceof Zend_Form) {
                $this->addSubForm($spec, $name);
                continue;
            }

            if (is_array($spec)) {
                $argc  = count($spec);
                $order = null;
                switch ($argc) {
                    case 0: 
                        continue;
                    case (1 <= $argc):
                        $subForm = array_shift($spec);
                    case (2 <= $argc):
                        $name  = array_shift($spec);
                    case (3 <= $argc):
                        $order = array_shift($spec);
                    default:
                        $this->addSubForm($subForm, $name, $order);
                }
            }
        }
        return $this;
    }

    /**
     * Set multiple form subForms/subforms (overwrites)
     * 
     * @param  array $subForms 
     * @return Zend_Form
     */
    public function setSubForms(array $subForms)
    {
        $this->clearSubForms();
        return $this->addSubForms($subForms);
    }

    /**
     * Retrieve a form subForm/subform
     * 
     * @param  string $name 
     * @return Zend_Form|null
     */
    public function getSubForm($name)
    {
        $name = (string) $name;
        if (isset($this->_subForms[$name])) {
            return $this->_subForms[$name];
        }
        return null;
    }

    /**
     * Retrieve all form subForms/subforms
     * 
     * @return array
     */
    public function getSubForms()
    {
        return $this->_subForms;
    }

    /**
     * Remove form subForm/subform
     * 
     * @param  string $name 
     * @return boolean
     */
    public function removeSubForm($name)
    {
        $name = (string) $name;
        if (isset($this->_subForms[$name])) {
            unset($this->_subForms[$name]);
            return true;
        }

        return false;
    }

    /**
     * Remove all form subForms/subforms
     * 
     * @return Zend_Form
     */
    public function clearSubForms()
    {
        $this->_subForms = array();
        return $this;
    }


    // Display groups:

    /**
     * Add a display group
     *
     * Groups named elements for display purposes.
     * 
     * @param  array $elements 
     * @param  string $name 
     * @param  int $order 
     * @return Zend_Form
     * @throws Zend_Form_Exception if no valid elements provided
     */
    public function addDisplayGroup(array $elements, $name, $options = null)
    {
        $group = array();
        foreach ($elements as $element) {
            if (isset($this->_elements[$element])) {
                $group[] = $this->getElement($element);
            }
        }
        if (empty($group)) {
            require_once 'Zend/Form/Exception.php';
            throw new Zend_Form_Exception('No valid elements specified for display group');
        }

        $name = (string) $name;

        if (is_array($options)) {
            $options['elements'] = $group;
        } elseif ($options instanceof Zend_Config) {
            $options = $options->toArray();
            $options['elements'] = $group;
        } else {
            $options = array('elements' => $group);
        }

        require_once 'Zend/Form/DisplayGroup.php';
        $this->_displayGroups[$name] = new Zend_Form_DisplayGroup(
            $name, 
            $this->getPluginLoader(self::DECORATOR), 
            $options
        );
        return $this;
    }

    /**
     * Add multiple display groups at once
     * 
     * @param  array $groups 
     * @return Zend_Form
     * @throws Zend_Form_Exception for invalid groupings
     */
    public function addDisplayGroups(array $groups)
    {
        foreach ($groups as $key => $spec) {
            $name = null;
            if (!is_numeric($key)) {
                $name = $key;
            }

            if (!is_array($spec) || empty($spec)) {
                require_once 'Zend/Form/Exception.php';
                throw new Zend_Form_Exception('Invalid grouping provided to addDisplayGroups()');
            }

            if (is_array($spec[0])) {
                $argc  = count($spec);
                $order = null;
                switch ($argc) {
                    case (1 <= $argc):
                        $elements = array_shift($spec);
                    case (2 <= $argc):
                        $name     = array_shift($spec);
                    case (3 <= $argc):
                        $order    = array_shift($spec);
                    default:
                        $this->addDisplayGroup($elements, $name, $order);
                }
            } else {
                $this->addDisplayGroup($spec, $name);
            }
        }
        return $this;
    }

    /**
     * Add multiple display groups (overwrites)
     * 
     * @param  array $groups 
     * @return Zend_Form
     */
    public function setDisplayGroups(array $groups)
    {
        return $this->clearDisplayGroups()
                    ->addDisplayGroups($groups);
    }

    /**
     * Return a display group
     * 
     * @param  string $name 
     * @return array|null
     */
    public function getDisplayGroup($name)
    {
        $name = (string) $name;
        if (isset($this->_displayGroups[$name])) {
            return $this->_displayGroups[$name];
        }

        return null;
    }

    /**
     * Return all display groups
     * 
     * @return array
     */
    public function getDisplayGroups()
    {
        return $this->_displayGroups;
    }

    /**
     * Remove a display group by name
     * 
     * @param  string $name 
     * @return boolean
     */
    public function removeDisplayGroup($name)
    {
        $name = (string) $name;
        if (isset($this->_displayGroups[$name])) {
            unset($this->_displayGroups[$name]);
            return true;
        }

        return false;
    }

    /**
     * Remove all display groups
     * 
     * @return Zend_Form
     */
    public function clearDisplayGroups()
    {
        $this->_displayGroups = array();
        return $this;
    }

     
    // Processing 

    /**
     * Populate form
     *
     * Proxies to {@link setDefaults()}
     * 
     * @param  array $values 
     * @return Zend_Form
     */
    public function populate(array $values)
    {
        return $this->setDefaults($values);
    }

    /**
     * Validate the form
     * 
     * @param  array $data 
     * @return boolean
     */
    public function isValid(array $data)
    {
        $valid = true;
        foreach ($this->getElements() as $key => $element) {
            if (!isset($data[$key])) {
                if ($element->getRequired()) {
                    $valid = $valid && $element->isValid(null, $data);
                }
            } else {
                $valid = $valid && $element->isValid($data[$key], $data);
            }
        }
        foreach ($this->getSubForms() as $key => $form) {
            if (isset($data[$key])) {
                $valid = $valid && $form->isValid($data[$key]);
            } else {
                $valid = $valid && $form->isValid($data);
            }
        }
        return $valid;
    }

    /**
     * Validate a partial form
     *
     * Does not check for required flags.
     * 
     * @param  array $data 
     * @return boolean
     */
    public function isValidPartial(array $data)
    {
        $valid = true;
        $validatedSubForms = array();
        foreach ($data as $key => $value) {
            if (isset($this->_elements[$key])) {
                $valid = $valid && $this->getElement($key)->isValid($value, $data);
            } elseif (isset($this->_subForms[$key])) {
                $valid = $valid && $this->getSubForm($key)->isValidPartial($data[$key]);
                $validatedSubForms[] = $key;
            } 
        }
        foreach ($this->getSubForms() as $key => $subForm) {
            if (!in_array($key, $validatedSubForms)) {
                $valid = $valid && $subForm->isValidPartial($data);
            }
        }
        return $valid;
    }

    public function processAjax($request)
    {
    }

    public function persistData()
    {
    }

    /**
     * Get error codes for all elements failing validation
     * 
     * @param  string $name 
     * @return array
     */
    public function getErrors($name = null)
    {
        $errors = array();
        if ((null !== $name) && isset($this->_elements[$name])) {
            $errors = $this->getElement($name)->getErrors();
        } elseif ((null !== $name) && isset($this->_subForms[$name])) {
            $errors = $this->getSubForm($name)->getErrors();
        } else {
            foreach ($this->_elements as $key => $element) {
                $errors[$key] = $element->getErrors();
            }
            foreach ($this->getSubForms() as $key => $subForm) {
                $errors[$key] = $subForm->getErrors();
            }
        }
        return $errors;
    }

    /**
     * Retrieve error messages from elements failing validations
     * 
     * @param  string $name 
     * @return array
     */
    public function getMessages($name = null)
    {
        $messages = array();
        if ((null !== $name) && isset($this->_elements[$name])) {
            $messages = $this->getElement($name)->getMessages();
        } elseif ((null !== $name) && isset($this->_subForms[$name])) {
            $messages = $this->getSubForm($name)->getMessages();
        } else {
            foreach ($this->_elements as $key => $element) {
                $messages[$key] = $element->getMessages();
            }
            foreach ($this->getSubForms() as $key => $subForm) {
                $messages[$key] = $subForm->getMessages();
            }
        }
        return $messages;
    }

 
    // Rendering 

    /**
     * Set view object
     * 
     * @param  Zend_View_Interface $view 
     * @return Zend_Form
     */
    public function setView(Zend_View_Interface $view)
    {
        $this->_view = $view;
        return $this;
    }

    /**
     * Retrieve view object
     *
     * If none registered, attempts to pull from ViewRenderer.
     * 
     * @return Zend_View_Interface|null
     */
    public function getView()
    {
        if (null === $this->_view) {
            require_once 'Zend/Controller/Action/HelperBroker.php';
            $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
            $this->_view = $viewRenderer->view;
        }

        return $this->_view;
    }

    /**
     * Add a decorator for rendering the element
     * 
     * @param  string|Zend_Form_Decorator_Interface $decorator 
     * @param  array|Zend_Config $options Options with which to initialize decorator
     * @return Zend_Form_Element
     */
    public function addDecorator($decorator, $options = null)
    {
        if ($decorator instanceof Zend_Form_Decorator_Interface) {
            $name = get_class($decorator);
        } elseif (is_string($decorator)) {
            $name = $this->getPluginLoader(self::DECORATOR)->load($decorator);
            if (null === $options) {
                $decorator = new $name;
            } else {
                $r = new ReflectionClass($name);
                $decorator = $r->newInstance($options);
            }
        } else {
            require_once 'Zend/Form/Exception.php';
            throw new Zend_Form_Exception('Invalid decorator provided to addDecorator; must be string or Zend_Form_Decorator_Interface');
        }

        $this->_decorators[$name] = $decorator;

        return $this;
    }

    /**
     * Add many decorators at once
     * 
     * @param  array $decorators 
     * @return Zend_Form_Element
     */
    public function addDecorators(array $decorators)
    {
        foreach ($decorators as $decoratorInfo) {
            if (is_string($decoratorInfo)) {
                $this->addDecorator($decoratorInfo);
            } elseif ($decoratorInfo instanceof Zend_Form_Decorator_Interface) {
                $this->addDecorator($decoratorInfo);
            } elseif (is_array($decoratorInfo)) {
                $argc    = count($decoratorInfo);
                $options = array();
                switch (true) {
                    case (0 == $argc):
                        break;
                    case (1 >= $argc):
                        $decorator  = array_shift($decoratorInfo);
                    case (2 >= $argc):
                        $options = array_shift($decoratorInfo);
                    default:
                        $this->addDecorator($decorator, $options);
                        break;
                }
            } else {
                require_once 'Zend/Form/Exception.php';
                throw new Zend_Form_Exception('Invalid decorator passed to addDecorators()');
            }
        }

        return $this;
    }

    /**
     * Overwrite all decorators
     * 
     * @param  array $decorators 
     * @return Zend_Form_Element
     */
    public function setDecorators(array $decorators)
    {
        $this->clearDecorators();
        return $this->addDecorators($decorators);
    }

    /**
     * Retrieve a registered decorator
     * 
     * @param  string $name 
     * @return false|Zend_Form_Decorator_Abstract
     */
    public function getDecorator($name)
    {
        if (!isset($this->_decorators[$name])) {
            $decorators = array_keys($this->_decorators);
            $len = strlen($name);
            foreach ($decorators as $decorator) {
                if (0 === substr_compare($decorator, $name, -$len, $len, true)) {
                    return $this->_decorators[$decorator];
                }
            }
            return false;
        }

        return $this->_decorators[$name];
    }

    /**
     * Retrieve all decorators
     * 
     * @return array
     */
    public function getDecorators()
    {
        return $this->_decorators;
    }

    /**
     * Remove a single decorator
     * 
     * @param  string $name 
     * @return bool
     */
    public function removeDecorator($name)
    {
        $decorator = $this->getDecorator($name);
        if ($decorator) {
            $name = get_class($decorator);
            unset($this->_decorators[$name]);
            return true;
        }

        return false;
    }

    /**
     * Clear all decorators
     * 
     * @return Zend_Form_Element
     */
    public function clearDecorators()
    {
        $this->_decorators = array();
        return $this;
    }

    public function render(Zend_View_Interface $view = null)
    {
    }

    public function __toString()
    {
    }

 
    // Localization: 

    /**
     * Set translator object
     * 
     * @param  Zend_Translate_Adapter $translator 
     * @return Zend_Form
     */
    public function setTranslator(Zend_Translate_Adapter $translator)
    {
        $this->_translator = $translator;
        return $this;
    }

    /**
     * Retrieve translator object
     * 
     * @return Zend_Translate_Adapter
     */
    public function getTranslator()
    {
        return $this->_translator;
    }

    /**
     * Overloading: access to elements, form groups, and display groups
     * 
     * @param  string $name 
     * @return Zend_Form_Element|Zend_Form|null
     */
    public function __get($name)
    {
        if (isset($this->_elements[$name])) {
            return $this->_elements[$name];
        } elseif (isset($this->_subForms[$name])) {
            return $this->_subForms[$name];
        } elseif (isset($this->_displayGroups[$name])) {
            return $this->_displayGroups[$name];
        }

        return null;
    }

    /**
     * Overloading: access to elements, form groups, and display groups
     * 
     * @param  string $name 
     * @param  Zend_Form_Element|Zend_Form $value 
     * @return void
     * @throws Zend_Form_Exception for invalid $value
     */
    public function __set($name, $value)
    {
        if ($value instanceof Zend_Form_Element) {
            $this->addElement($value, $name);
            return;
        } elseif ($value instanceof Zend_Form) {
            $this->addSubForm($value, $name);
            return;
        } elseif (is_array($value)) {
            $this->addDisplayGroup($value, $name);
            return;
        }

        require_once 'Zend/Form/Exception.php';
        if (is_object($value)) {
            $type = get_class($value);
        } else {
            $type = gettype($value);
        }
        throw new Zend_Form_Exception('Only form elements and groups may be overloaded; variable of type "' . $type . '" provided');
    }

    /**
     * Overloading: access to elements, form groups, and display groups
     * 
     * @param  string $name 
     * @return boolean
     */
    public function __isset($name)
    {
        if (isset($this->_elements[$name])
            || isset($this->_subForms[$name])
            || isset($this->_displayGroups[$name]))
        {
            return true;
        }

        return false;
    }

    /**
     * Overloading: access to elements, form groups, and display groups
     * 
     * @param  string $name 
     * @return void
     */
    public function __unset($name)
    {
        if (isset($this->_elements[$name])) {
            unset($this->_elements[$name]);
        } elseif (isset($this->_subForms[$name])) {
            unset($this->_subForms[$name]);
        } elseif (isset($this->_displayGroups[$name])) {
            unset($this->_displayGroups[$name]);
        }
    }
 
    // For iteration, countable: 
    public function current()
    {
    }

    public function key()
    {
    }

    public function next()
    {
    }

    public function rewind()
    {
    }

    public function valid()
    {
    }

    public function count()
    {
    }
}
