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
 * @package    Zend_Environment
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Exception.php 2794 2007-01-16 01:29:51Z bkarwin $
 */


/**
 * Zend_Environment_Exception
 */
require_once 'Zend/Environment/Exception.php';


/**
 * Zend_Environment_Container_Abstract
 */
require_once 'Zend/Environment/Container/Abstract.php';


/**
 * @category   Zend
 * @package    Zend_Environment
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Environment extends Zend_Environment_Container_Abstract
{
    /**
     * Optional cache instance.
     */
    protected $_cache;

    /**
     * Cache prefix (to avoid namespace clashes).
     */
    protected $_cachePrefix = '_zf_environment_';

    /**
     * @param  array $modules
     * @param  array $config
     * @throws Zend_Environment_Exception
     * @return void
     */
    public function __construct($modules = null, $config = array())
    {
        if (is_array($config)) {
            if (isset($config['cache'])) {
                $this->_cache = $config['cache'];
            }
        }
        
        if (isset($this->_cache)) {
            $data = $this->_cache->load($this->_cachePrefix . 'module');
            if ($data) {
                $this->_data = unserialize($data);
                return;
            }
        }

        if ($modules === null) {
			// Load module registry
            require_once 'Zend/Environment/ModuleRegistry.php';
            $registry = new Zend_Environment_ModuleRegistry();

            $modules = array();
            foreach ($registry as $file) {
                $class = rtrim($file->getFilename(), '.php');
                $module = "Zend_Environment_Module_{$class}";
                Zend_Loader::loadClass($module);
                $modules[] = new $module(strtolower($class));
            }
        } elseif (!is_array($modules)) {
            $modules = array($modules);
        }

        foreach ($modules as $instance) {
            if (!($instance instanceof Zend_Environment_Module_Interface)) {
                throw new Zend_Environment_Exception("Module does not implement Zend_Environment_Module_Interface");
            }
            $this->_data[$instance->getId()] = $instance;
        }
        
        $this->_cache('module', serialize($this->_data));
    }
    
    /**
     * Passes the environment to a Zend_View to format as a whole HTML page
     * (using the default HTML renderer) or to a user-supplied Zend_View.
     *
     * @param  Zend_View_Abstract $view
     * @param  string $script
     * @return string
     */
    public function toHtml(Zend_View_Abstract $view = null, $script = 'Html.php')
    {
        return $this->_render($view, $script);
    }
    
    /**
     * Passes the environment to a Zend_View to format as a whole Text page
     * (using the default Text renderer) or to a user-supplied Zend_View.
     *
     * @param  Zend_View_Abstract $view
     * @param  string $script
     * @return string
     */
    public function toText(Zend_View_Abstract $view = null, $script = 'Text.php')
    {
        return $this->_render($view, $script);
    }

    /**
     * Sends formatted text-only output of the environment to a file.
     *
     * Will create a default text view if no text is provided.
     *
     * @param string $path
     * @param  Zend_View_Abstract $view
     * @return string
     */
    public function toFile($path, $text = null)
    {
        if (!is_writeable(dirname($path))) {
            throw new Zend_Environment_Exception('Cannot write file to ' . $path);
        }
        
        if ($text === null) {
            $text = $this->toText();
        }

        return file_put_contents($path, $text);
    }
    
    /**
     * Returns a string identifier associated to a specific environment.
     *
     * Zend Environment can determine which 'environment' a current webserver is
     * running by parsing an array of identifiers and hostnames. Returns a
     * string on success or an exception if the location cannot be determined.
     * Each location can be specified by a string (or array of strings) with
     * a regular expression (no delimiter necessary). The server's hostname,
     * address and port number are used for matching.
     *
     * E.g.
     * $locations = array('live' => array('www.site.com', '192.168.1.0/24'),
     *                    'staging' => array('*.site.com', 'site.staging.com'),
     *                    'development' => 'www.site.test');
     * $environment_id = Zend_Environment::match($locations);
     *
     * @param array $locations
     * @param string $server
     * @param string $ip
     * @throws Zend_Environment_Exception
     * @return string|boolean
     */
    public function match($locations, $server = null, $ip = null)
    {
        if (isset($this->_config)) {
            $id = $this->_config->load($this->_cachePrefix . 'match');
            if ($id) {
                return $id;
            }
        }

        if (is_null($server) && isset($_SERVER['SERVER_NAME'])) {
            $server = $_SERVER['SERVER_NAME'];
        }

        if (is_null($ip) && isset($_SERVER['SERVER_ADDR'])) {
            $ip = $_SERVER['SERVER_ADDR'];
        }

        if (!is_array($locations)) {
            throw new exception('Locations must be provided as array');
        }
        
        $lip = ip2long($ip);
        $cidr = array("0.0.0.0", "128.0.0.0", "192.0.0.0", "224.0.0.0",
                      "240.0.0.0", "248.0.0.0", "252.0.0.0", "254.0.0.0",
                      "255.0.0.0", "255.128.0.0", "255.192.0.0", "255.224.0.0",
                      "255.240.0.0", "255.248.0.0", "255.252.0.0", "255.254.0.0",
                      "255.255.0.0", "255.255.128.0", "255.255.192.0", "255.255.224.0",
                      "255.255.240.0", "255.255.248.0", "255.255.252.0", "255.255.254.0",
                      "255.255.255.0", "255.255.255.128", "255.255.255.192", "255.255.255.224",
                      "255.255.255.240", "255.255.255.248", "255.255.255.252", "255.255.255.254",
                      "255.255.255.255");
        
        foreach ($locations as $id => $environment) {

            if (!is_array($environment)) {
                $environment = array($environment);
            }

            foreach ($environment as $host) {

                if (preg_match('/^(\d+\.){3}\d+(\/\d+)?$/', $host)) {

                    if (strpos($host, '/') === false) {
                        // If not in CIDR notation then perform straight compare
                        if ($host == $ip) {
                            return $this->_cache('match', $id);
                        }
                    } else {
                        // Parse CIDR notation and calculate
                        list($network, $mask) = explode('/', $host);
                        $lmask = ip2long($cidr[$mask]);
                        $lnetwork = ip2long($network) & $lmask;
                        $lbroadcast = $lnetwork | $lmask ^ ip2long($cidr[32]);
    
                        if ($lip >= $lnetwork && $lip <= $lbroadcast) {
                            return $this->_cache('match', $id);
                        }
                    }

                } else {

                    $host = str_replace('*', '_', $host);
                    $host = preg_quote($host, '/');
                    $host = str_replace('_', '.*', $host);

                    if (preg_match("/^" . $host . "$/", $server)) {
                        return $this->_cache('match', $id);
                    }
                }
            }
        }

        return $this->_cache('match', false);
    }
    
    /**
     * Internal method to retrieve environment view.
     *
     * @param  Zend_View_Abstract $view
     * @param  string $script
     * @return string
     */
    protected function _render($view, $script)
    {
        if ($view === null) {
            $view = $this->_getDefaultView();
        }

        $view->environment = $this;
        return $view->render($script);
    }
    
    /**
     * Creates instance of default environment view.
     *
     * @return Zend_View_Abstract $view
     */
    protected function _getDefaultView()
    {
    	require_once 'Zend/Environment/View.php';
        $view = new Zend_Environment_View();
        return $view;
    }
    
    /**
     * Save contents of operation to cache if it has been instantiated.
     *
     * @param  string $id
     * @param  string $value
     * @return string $value
     */
    protected function _cache($id, $value)
    {
        if (isset($this->_cache)) {
            $this->_cache->save($value, $this->_cachePrefix . $id);
        }
        
        return $value;
    }

}
