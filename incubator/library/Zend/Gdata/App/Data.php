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
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Class to store Zend_Gdata constants
 *
 * @category   Zend
 * @package    Zend_Gdata_App
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

class Zend_Gdata_App_Data
{

    /**
     * @var array
     */
    protected static $_namespaces = array(
        'opensearch' => 'http://a9.com/-/spec/opensearchrss/1.0/',
        'atom'       => 'http://www.w3.org/2005/Atom',
        'rss'        => 'http://blogs.law.harvard.edu/tech/rss',
        'gd'      => 'http://schemas.google.com/g/2005'
    );

    /**
     * Get the full version of a namespace prefix
     *
     * Looks up a prefix (atom:, etc.) in the list of registered
     * namespaces and returns the full namespace URI if
     * available. Returns the prefix, unmodified, if it's not
     * registered.
     *
     * @return string
     */
    public static function lookupNamespace($prefix)
    {
        return isset(self::$_namespaces[$prefix]) ?
            self::$_namespaces[$prefix] :
            $prefix;
    }


    /**
     * Add a namespace and prefix to the registered list
     *
     * Takes a prefix and a full namespace URI and adds them to the
     * list of registered namespaces for use by
     * Zend_Gdata_App_Data::lookupNamespace().
     *
     * @param  string $prefix The namespace prefix
     * @param  string $namespaceURI The full namespace URI
     * @return void
     */
    public static function registerNamespace($prefix, $namespaceURI)
    {
        self::$_namespaces[$prefix] = $namespaceURI;
    }

}
