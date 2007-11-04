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
 * @package    Zend_Layout
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Provide Layout support for MVC applications
 *
 * @category   Zend
 * @package    Zend_Layout
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Layout
{
    /**
     * Placeholder container for layout variables
     * @var Zend_View_Helper_Placeholder_Container
     */
    protected $_container;

    /**
     * Are layouts enabled?
     * @var bool
     */
    protected $_enabled = true;

    /**
     * Inflector used to resolve layout script
     * @var Zend_Filter_Inflector
     */
    protected $_inflector;

    /**
     * Flag: is inflector enabled?
     * @var bool
     */
    protected $_inflectorEnabled = true;

    /**
     * Layout view
     * @var string
     */
    protected $_layout = 'layout';

    /**
     * Layout view script path
     * @var string
     */
    protected $_layoutPath;

    /**
     * @var Zend_View_Interface
     */
    protected $_view;

    /**
     * Constructor
     *
     * Accepts either:
     * - A string path to layouts
     * - An array of options
     * - A Zend_Config object with options
     *
     * Layout script path, either as argument or as key in options, is 
     * required.
     *
     * If useMvc flag is false from options, simply sets layout script path. 
     * Otherwise, also instantiates and registers action helper and controller 
     * plugin.
     * 
     * @param  string|array|Zend_Config $options 
     * @return void
     */ 
    public function __construct($options = null) 
    { 
        if (null !== $options) {
            if (is_string($options)) {
                $this->setLayoutPath($options);
            } elseif (is_array($options)) {
                $this->_setOptions($options);
            } elseif ($options instanceof Zend_Config) {
                $this->setConfig($options);
            } else {
                include_once 'Zend/Layout/Exception.php';
                throw new Zend_Layout_Exception('Invalid option provided to constructor');
            }
        }

        $this->_initVarContainer();
    }

    /**
     * Set options en masse
     * 
     * @param  array $options 
     * @return void
     */
    protected function _setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    /**
     * Set options from a config object
     * 
     * @param  Zend_Config $config 
     * @return Zend_Layout
     */
    public function setConfig(Zend_Config $config)
    {
        $this->_setOptions($config->toArray());
        return $this;
    }

    /**
     * Initialize placeholder container for layout vars
     * 
     * @return Zend_View_Helper_Placeholder_Container
     */
    protected function _initVarContainer()
    {
        if (null === $this->_container) {
            if (Zend_Registry::isRegistered(Zend_View_Helper_Placeholder::REGISTRY_KEY)) {
                $registry = Zend_Registry::get(Zend_View_Helper_Placeholder::REGISTRY_KEY);
            } else {
                include_once 'Zend/View/Helper/Placeholder/Registry.php';
                $registry = new Zend_View_Helper_Placeholder_Registry();
                Zend_Registry::set(self::REGISTRY_KEY, $this->_registry);
            }

            $this->_container = $registry->getContainer(__CLASS__);
        }

        return $this->_container;
    }

    /**
     * Set layout script to use
     *
     * Note: enables layout.
     * 
     * @param  string $name 
     * @return Zend_Layout
     */ 
    public function setLayout($name) 
    {
        $this->_layout = (string) $name;
        $this->enableLayout();
        return $this;
    }
 
    /**
     * Get current layout script
     * 
     * @return string
     */ 
    public function getLayout() 
    {
        return $this->_layout;
    } 
 
    /**
     * Disable layout
     *
     * @return Zend_Layout
     */ 
    public function disableLayout() 
    {
        $this->_enabled = false;
        return $this;
    } 

    /**
     * Enable layout 
     * 
     * @return Zend_Layout
     */
    public function enableLayout()
    {
        $this->_enabled = true;
        return $this;
    }
 
    /**
     * Set layout script path
     * 
     * @param  string $path 
     * @return Zend_Layout
     */ 
    public function setLayoutPath($path) 
    {
        $this->_layoutPath = $path;
        return $this;
    } 
 
    /**
     * Get current layout script path
     * 
     * @return string
     */ 
    public function getLayoutPath() 
    {
        return $this->_layoutPath;
    } 
 
    /**
     * Set view object
     * 
     * @param  Zend_View_Interface $view
     * @return Zend_Layout
     */ 
    public function setView(Zend_View_Interface $view) 
    {
        $this->_view = $view;
        return $this;
    } 
 
    /**
     * Get current view object
     *
     * If no view object currently set, retrieves it from the ViewRenderer.
     * 
     * @return Zend_View_Interface
     */ 
    public function getView() 
    {
        if (null === $this->_view) {
            $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
            if (null === $viewRenderer->view) {
                $viewRenderer->initView();
            }
            $this->setView($viewRenderer->view);
        }
        return $this->_view;
    } 
 
    /**
     * Set inflector to use when resolving layout names
     *
     * @param  Zend_Filter_Inflector $inflector
     * @return Zend_Layout
     */
    public function setInflector(Zend_Filter_Inflector $inflector)
    {
        $this->_inflector = $inflector;
        return $this;
    }

    /**
     * Retrieve inflector
     *
     * @return Zend_Filter_Inflector
     */
    public function getInflector()
    {
        return $this->_inflector;
    }

    /**
     * Enable inflector
     * 
     * @return Zend_Layout
     */
    public function enableInflector()
    {
        $this->_inflectorEnabled = true;
        return $this;
    }

    /**
     * Disable inflector
     * 
     * @return Zend_Layout
     */
    public function disableInflector()
    {
        $this->_inflectorEnabled = false;
        return $this;
    }

    /**
     * Return status of inflector enabled flag
     * 
     * @return bool
     */
    public function inflectorEnabled()
    {
        return $this->_inflectorEnabled;
    }

    /**
     * Set layout variable
     * 
     * @param  string $key 
     * @param  mixed $value 
     * @return void
     */ 
    public function __set($key, $value) 
    {
        $this->_container[$key] = $value;
    }
 
    /**
     * Get layout variable
     * 
     * @param  string $key
     * @return mixed
     */ 
    public function __get($key) 
    {
        if (isset($this->_container[$key])) {
            return $this->_container[$key];
        }

        return null;
    }
 
    /**
     * Is a layout variable set?
     *
     * @param  string $key
     * @return bool
     */ 
    public function __isset($key) 
    {
        return (isset($this->_container[$key]));
    } 
 
    /**
     * Unset a layout variable?
     *
     * @param  string $key
     * @return void
     */ 
    public function __unset($key) 
    {
        if (isset($this->_container[$key])) {
            unset($this->_container[$key]);
        }
    } 
 
    /**
     * Assign one or more layout variables
     * 
     * @param  mixed $spec Assoc array or string key; if assoc array, sets each
     * key as a layout variable
     * @param  mixed $value Value if $spec is a key
     * @return Zend_Layout
     * @throws Zend_Layout_Exception if non-array/string value passed to $spec
     */ 
    public function assign($spec, $value = null) 
    {
        if (is_array($spec)) {
            $orig = $this->_container->getArrayCopy();
            $merged = array_merge($orig, $spec);
            $this->_container->exchangeArray($merged);
            return $this;
        }

        if (is_string($spec)) {
            $this->_container[$spec] = $value;
            return $this;
        }

        include_once 'Zend/Layout/Exception.php';
        throw new Zend_Layout_Exception('Invalid values passed to assign()');
    }

    /**
     * Render layout
     *
     * Sets internal script path as last path on script path stack, assigns 
     * layout variables to view, determines layout name using inflector, and 
     * renders layout view script.
     * 
     * @param  mixed $name 
     * @return mixed
     */ 
    public function render($name = null) 
    { 
        if (null === $name) {
            $name = $this->getLayout();
        }

        if ($this->inflectorEnabled() && (null !== ($inflector = $this->getInflector())))
        {
            $name = $this->_inflector->filter($name);
        }

        if (null !== ($path = $this->getLayoutPath())) {
            $view->addScriptPath($path);
        }

        return $this->getView()->render($name);
    }
}
