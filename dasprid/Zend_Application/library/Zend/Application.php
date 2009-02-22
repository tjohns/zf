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
 * @package    Zend_Application
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @category   Zend
 * @package    Zend_Application
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Application
{
    /**
     * @var Zend_Loader_Autoloader
     */
    protected $_autoloader;

    /**
     * @var Zend_Application_Bootstrap_Base
     */
    protected $_bootstrap;

    /**
     * @var string
     */
    protected $_environment;

    /**
     * @var array
     */
    protected $_options = array();

    /**
     * Constructor
     *
     * Initialize application. Potentially initializes include_paths, PHP 
     * settings, and bootstrap class.
     * 
     * @param  string $environment 
     * @param  string|array|Zend_Config $options String path to configuration file, or array/Zend_Config of configuration options
     * @return void
     */
    public function __construct($environment, $options = null)
    {
        $this->_environment = (string) $environment;

        require_once 'Zend/Loader/Autoloader.php';
        $this->_autoloader = Zend_Loader_Autoloader::getInstance();

        if (null !== $options) {
            if (is_string($options)) {
                $options = $this->_loadConfig($options);
            } elseif ($options instanceof Zend_Config) {
                $options = $options->toArray();
            } elseif (!is_array($options)) {
                throw new Zend_Application_Exception('Invalid options provided; must be location of config file, a config object, or an array');
            }

            $this->setOptions($options);
        }
    }

    /**
     * Retrieve current environment
     * 
     * @return string
     */
    public function getEnvironment()
    {
        return $this->_environment;
    }

    /**
     * Retrieve autoloader instance
     * 
     * @return Zend_Loader_Autoloader
     */
    public function getAutoloader()
    {
        return $this->_autoloader;
    }

    /**
     * Set application options
     * 
     * @param  array $options 
     * @return Zend_Application
     */
    public function setOptions(array $options)
    {
        $options = array_change_key_case($options, CASE_LOWER);
        $this->_options = $options;

        if (!empty($options['phpsettings'])) {
            $this->setPhpSettings($options['phpsettings']);
        }
        if (!empty($options['includepaths'])) {
            $this->setIncludePaths($options['includepaths']);
        }
        if (!empty($options['autoloadernamespaces'])) {
            $this->setAutoloaderNamespaces($options['autoloadernamespaces']);
        }
        if (!empty($options['bootstrap'])) {
            $bootstrap = $options['bootstrap'];
            if (is_string($bootstrap)) {
                $this->setBootstrap($bootstrap);
            } elseif (is_array($bootstrap)) {
                if (empty($bootstrap['path'])) {
                    throw new Zend_Application_Exception('No bootstrap path provided');
                }
                $path  = $bootstrap['path'];
                $class = null;
                if (!empty($bootstrap['class'])) {
                    $class = $bootstrap['class'];
                }
                $this->setBootstrap($path, $class);
            } else {
                throw new Zend_Application_Exception('Invalid bootstrap information provided');
            }
        }

        return $this;
    }

    /**
     * Retrieve application options (for caching)
     * 
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Set PHP configuration settings
     * 
     * @param  array $settings 
     * @return Zend_Application
     */
    public function setPhpSettings(array $settings)
    {
        foreach ($settings as $key => $value) {
            ini_set($key, $value);
        }
        return $this;
    }

    /**
     * Set include path
     * 
     * @param  array $paths 
     * @return Zend_Application
     */
    public function setIncludePaths(array $paths)
    {
        $path = implode(PATH_SEPARATOR, $paths);
        set_include_path($path . PATH_SEPARATOR . get_include_path());
        return $this;
    }

    /**
     * Set autoloader namespaces
     * 
     * @param  array $namespaces 
     * @return Zend_Application
     */
    public function setAutoloaderNamespaces(array $namespaces)
    {
        $autoloader = $this->getAutoloader();
        foreach ($namespaces as $namespace) {
            $autoloader->registerNamespace($namespace);
        }
        return $this;
    }

    /**
     * Set bootstrap path/class
     * 
     * @param  string $path 
     * @param  null|string $class 
     * @return Zend_Application
     */
    public function setBootstrap($path, $class = null)
    {
        if (empty($class)) {
            $class = 'Bootstrap';
        }
        require_once $path;
        $this->_bootstrap = new $class($this);
        return $this;
    }

    /**
     * Get bootstrap object
     * 
     * @return Zend_Application_Bootstrap_Base
     */
    public function getBootstrap()
    {
        return $this->_bootstrap;
    }

    /**
     * Bootstrap application
     * 
     * @return void
     */
    public function bootstrap()
    {
        $this->getBootstrap()->bootstrap();
    }

    /**
     * Run the application
     * 
     * @return void
     */
    public function run()
    {
        $this->getBootstrap()->run();
    }

    /**
     * Load configuration file of options
     * 
     * @param  string $file 
     * @return array
     */
    protected function _loadConfig($file)
    {
        $environment = $this->getEnvironment();
        $suffix      = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        switch ($suffix) {
            case 'ini':
                $config = new Zend_Config_Ini($file, $environment);
                break;
            case 'xml':
                $config = new Zend_Config_Xml($file, $environment);
                break;
            case 'php':
            case 'inc':
                $array = include $file;
                $config = new Zend_Config($array);
                break;
            default:
                throw new Zend_Application_Exception('Invalid configuration file provided; unknown config type');
        }
        return $config->toArray();
    }
}
