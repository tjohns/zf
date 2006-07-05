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
 * @package    Zend_Config
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Zend_Config_Exception
 */
require_once 'Zend/Config/Exception.php';


/**
 * @category   Zend
 * @package    Zend_Config
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Config_Array
{
    /**
     * Load the sub-array called $section from an array called $config
     * within $filename.
     *
     * @param string $filename
     * @param string $section
     * @throws Zend_Config_Exception
     * @return array
     */
    public static function load($filename, $section)
    {
        if (empty($filename)) {
            throw new Zend_Config_Exception('Filename is not set');
        }
        if (empty($section)) {
            throw new Zend_Config_Exception('Section is not set');
        }

        include $filename;

        if (!isset($config)) {
            throw new Zend_Config_Exception("Array variable '\$config' cannot be found in $filename");
        }
        if (!isset($config[$section])) {
            throw new Zend_Config_Exception("Section '$section' cannot be found in $filename");
        }
        if (!is_array($config[$section])) {
            throw new Zend_Config_Exception("Section '$section' is not an array");
        }

        return $config[$section];
    }

}
