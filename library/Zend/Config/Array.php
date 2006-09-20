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
 * Zend_Config
 */
require_once 'Zend/Config.php';


/**
 * @category   Zend
 * @package    Zend_Config
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Config_Array extends Zend_Config
{
    /**
     * Loads the sub-array having a key of $section from an array called $config
     * within $filename for access facilitated by nested object properties.
     *
     * @param string $filename
     * @param string $section
     * @throws Zend_Config_Exception
     */
    public function __construct($filename, $section, $allowModifications = false)
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

        parent::__construct($config[$section], $allowModifications);
    }

}
