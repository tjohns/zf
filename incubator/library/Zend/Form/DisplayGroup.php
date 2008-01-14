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
 * Zend_Form_DisplayGroup
 * 
 * @category   Zend
 * @package    Zend_Form
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
class Zend_Form_DisplayGroup
{
    /**
     * Display group decorators
     * @var array
     */
    protected $_decorators = array();

    /**
     * Elements
     * @var array
     */
    protected $_elements = array();

    /**
     * Plugin loader for decorators
     * @var Zend_Loader_PluginLoader
     */
    protected $_loader;

    /**
     * Group name
     * @var string
     */
    protected $_name;

    /**
     * Group order
     * @var int
     */
    protected $_order;

    /**
     * @var Zend_Translate
     */
    protected $_translator;

    /**
     * Constructor
     * 
     * @param  string $name 
     * @param  Zend_Loader_PluginLoader $loader 
     * @param  array|Zend_Config $options 
     * @return void
     */
    public function __construct($name, Zend_Loader_PluginLoader $loader, $options = null)
    {
        $this->setName($name);

        $this->setPluginLoader($loader);

        if (is_array($options)) {
            $this->setOptions($options);
        } elseif ($options instanceof Zend_Config) {
            $this->setConfig($options);
        }

        $this->addDecorator('fieldset');
    }

    /**
     * Set options
     * 
     * @param  array $options 
     * @return Zend_Form_DisplayGroup
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
        return $this;
    }

    /**
     * Set options from config object
     * 
     * @param  Zend_Config $config 
     * @return Zend_Form_DisplayGroup
     */
    public function setConfig(Zend_Config $config)
    {
        return $this->setOptions($config->toArray());
    }

    /**
     * Set group name
     * 
     * @param  string $name 
     * @return Zend_Form_DisplayGroup
     */
    public function setName($name)
    {
        $this->_name = (string) $name;
        return $this;
    }

    /**
     * Retrieve group name
     * 
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Set group order
     * 
     * @param  int $order 
     * @return Zend_Form_Element
     */
    public function setOrder($order)
    {
        $this->_order = (int) $order;
        return $this;
    }

    /**
     * Retrieve group order
     * 
     * @return int
     */
    public function getOrder()
    {
        return $this->_order;
    }

    // Elements

    /**
     * Add element to stack
     * 
     * @param  Zend_Form_Element $element 
     * @return Zend_Form_DisplayGroup
     */
    public function addElement(Zend_Form_Element $element)
    {
        $this->_elements[$element->getName()] = $element;
        return $this;
    }

    /**
     * Add multiple elements at once
     * 
     * @param  array $elements 
     * @return Zend_Form_DisplayGroup
     * @throws Zend_Form_Exception if any element is not a Zend_Form_Element
     */
    public function addElements(array $elements)
    {
        foreach ($elements as $element) {
            if (!$element instanceof Zend_Form_Element) {
                require_once 'Zend/Form/Exception.php';
                throw new Zend_Form_Exception('elements passed via array to addElements() must be Zend_Form_Elements only');
            }
            $this->addElement($element);
        }
        return $this;
    }

    /**
     * Set multiple elements at once (overwrites)
     * 
     * @param  array $elements 
     * @return Zend_Form_DisplayGroup
     */
    public function setElements(array $elements)
    {
        $this->clearElements();
        return $this->addElements($elements);
    }

    /**
     * Retrieve element
     * 
     * @param  string $name 
     * @return Zend_Form_Element|null
     */
    public function getElement($name)
    {
        $name = (string) $name;
        if (isset($this->_elements[$name])) {
            return $this->_elements[$name];
        }

        return null;
    }

    /**
     * Retrieve elements
     * @return array
     */
    public function getElements()
    {
        return $this->_elements;
    }

    /**
     * Remove a single element
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
     * Remove all elements
     * 
     * @return Zend_Form_DisplayGroup
     */
    public function clearElements()
    {
        $this->_elements = array();
        return $this;
    }

    // Plugin loader (for decorators)

    /**
     * Set plugin loader
     * 
     * @param  Zend_Loader_PluginLoader $loader 
     * @return Zend_Form_DisplayGroup
     */
    public function setPluginLoader(Zend_Loader_PluginLoader $loader)
    {
        $this->_loader = $loader;
        return $this;
    }

    /**
     * Retrieve plugin loader
     * 
     * @return Zend_Loader_PluginLoader
     */
    public function getPluginLoader()
    {
        return $this->_loader;
    }

    // Decorators

    /**
     * Add a decorator for rendering the group
     * 
     * @param  string|Zend_Form_Decorator_Interface $decorator 
     * @param  array|Zend_Config $options Options with which to initialize decorator
     * @return Zend_Form_DisplayGroup
     */
    public function addDecorator($decorator, $options = null)
    {
        if ($decorator instanceof Zend_Form_Decorator_Interface) {
            $name = get_class($decorator);
        } elseif (is_string($decorator)) {
            $name = $this->getPluginLoader()->load($decorator);
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
     * @return Zend_Form_DisplayGroup
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
     * @return Zend_Form_DisplayGroup
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
     * @return Zend_Form_DisplayGroup
     */
    public function clearDecorators()
    {
        $this->_decorators = array();
        return $this;
    }

    /**
     * Render display group
     * 
     * @return string
     */
    public function render()
    {
        $content = '';
        foreach ($this->getElements() as $element) {
            $content .= $element->render();
        }
        foreach ($this->getDecorators() as $decorator) {
            $decorator->setElement($this);
            $content = $decorator->render($content);
        }
        return $content;
    }

    /**
     * String representation of group
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Set translator object
     * 
     * @param  Zend_Translate_Adapter $translator 
     * @return Zend_Form_DisplayGroup
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
}
