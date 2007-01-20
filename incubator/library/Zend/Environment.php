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
 * @category   Zend
 * @package    Zend_Environment
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Environment extends Zend_Environment_Container_Abstract
{
    /**
     * @param  array $modules
     * @param  array $config
     * @throws Zend_Environment_Exception
     * @return void
     */
    public function __construct($modules, $config = array())
    {
        // TO DO - implement caching if set in $config

        if (!is_array($modules)) {
            $modules = array($modules);
        }

        foreach ($modules as $instance) {
            if (!($instance instanceof Zend_Environment_Module_Interface)) {
                throw new Zend_Environment_Exception("Module does not implement Zend_Environment_Module_Interface");
            }
            $this->_data[$instance->getId()] = $instance;
        }
    }
    
    /**
     * Passes the environment to a Zend_View to format as a whole HTML page
     * (using the default HTML rendered) or to a user-supplied Zend_View
     *
     * @param  Zend_View_Abstract $view
     * @return string
     */
	public function toHtml(Zend_View_Abstract $view = null)
	{
	    if ($view === null) {
	        $view = new Zend_Environment_View_Html;
	    }

        $view->environment = $this;
        return $view->render(null);
	}

    /**
     * Sends formatted text-only output of the environment to a file 
     *
     * @param string $path
     * @param  Zend_View_Abstract $view
     * @return string
     */
	public function toFile($path, Zend_View_Abstract $view = null)
	{
        if (!is_writeable(dirname($path))) {
            throw new Zend_Environment_Exception('Cannot write file to ' . $path);
        }

	    if ($view === null) {
	        $view = new Zend_Environment_View_Html;
	    }

        $view->environment = $this;
        return file_put_contents($path, $view->render(null));
	}
	
    /**
     * Returns a string identifier associated to a specific environment
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
	    if (is_null($server) && isset($_SERVER['SERVER_NAME'])) {
	        $server = $_SERVER['SERVER_NAME'];
	    }

	    if (is_null($ip) && isset($_SERVER['SERVER_ADDR'])) {
	        $ip = $_SERVER['SERVER_ADDR'];
	    }

	    if (!is_array($locations)) {
	        throw new exception('Locations must be provided as array');
	    }

	    foreach ($locations as $id => $environment) {
            if (!is_array($environment)) {
                $environment = array($environment);
            }
            foreach ($environment as $host) {
                if (preg_match("/^(\d+\.){3}\d+(\/\d+)?$/", $host)) {
                    list($network, $mask) = explode('/', $host);

                    $network = sprintf("%-032s", decbin(ip2long($network)));
                    $ip = sprintf("%-032s", decbin(ip2long($ip)));

                    if (!is_null($mask)) {
                        $cmp = strncmp($ip, $network, $mask);
                    } else {
                        $cmp = strcmp($ip, $network);
                    }

                    if ($cmp === 0) {
                        return $id;
                    }
                } else {
                    $host = preg_replace("/[^A-Za-z0-9\.\*]+/", '', $host);
                    $host = str_replace('*', '_', $host);
                    $host = preg_quote($host, '/');
                    $host = str_replace('_', '.*', $host);

                    if (preg_match("/^" . $host . "$/", $host)) {
                        return $id;
                    }
                }
            }
	    }

	    return false;
	}
}
