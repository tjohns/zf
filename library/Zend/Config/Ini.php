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
class Zend_Config_Ini
{
    /**
     * Load the section $section from the config file $filename into
     * an associative array.
     *
     * If any keys with $section are called "extends", then the section
     * pointed to by the "extends" is then included into the properties.
     * Note that the keys in $section will override any keys of the same
     * name in the sections that have been included via "extends".
     *
     * If any key includes a ".", then this will act as a separator to
     * create a sub-property.
     *
     * example ini file:
     *      [all]
     *      db.connection = database
     *      hostname = live
     *
     *      [staging]
     *      extends = all
     *      hostname = staging
     *
     * after calling $data = Zend_Config_Ini::load($file, 'staging'); then
     *      $data['hostname'] = staging
     *      $data['db']['connection'] = database
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

        $iniArray = parse_ini_file($filename, true);
        if (!isset($iniArray[$section])) {
            throw new Zend_Config_Exception("Section '$section' cannot be found in $filename");
        }

        $self = new self();
        return $self->_processExtends($iniArray, $section);
    }


    /**
     * Helper function to process each element in the section and handle
     * the "extends" inheritance keyword. Passes control to _processKey()
     * to handle the "dot" sub-property syntax in each key.
     *
     * @param array $iniArray
     * @param string $section
     * @param array $config
     * @throws Zend_Config_Exception
     * @return array
     */
    protected function _processExtends($iniArray, $section, $config = array())
    {
        $thisSection = $iniArray[$section];

        foreach ($thisSection as $key => $value) {
            if (strtolower($key) == 'extends') {
                if (isset($iniArray[$value])) {
                    foreach ($iniArray[$value] as $k => $v) {
                        if (strtolower($k) == 'extends') {
                            $config = $this->_processExtends($iniArray, $v, $config);
                        }
                        $config = $this->_processKey($config, $k, $v);
                    }
                } else {
                    throw new Zend_Config_Exception("Section '$section' cannot be found");
                }
            } else {
                $config = $this->_processKey($config, $key, $value);
            }
        }
        return $config;
    }

    /**
     * Assign the key's value to the property list. Handle the "dot"
     * notation for sub-properties by passing control to
     * processLevelsInKey().
     *
     * @param array $config
     * @param string $key
     * @param string $value
     * @throws Zend_Config_Exception
     * @return array
     */
    protected function _processKey($config, $key, $value)
    {
        if (strpos($key, '.') !== false) {
            $pieces = explode('.', $key, 2);
            if (strlen($pieces[0]) && strlen($pieces[1])) {
                if (!isset($config[$pieces[0]])) {
                    $config[$pieces[0]] = array();
                }
                $config[$pieces[0]] = $this->_processLevelsInKey($config[$pieces[0]], $pieces[1], $value);
            } else {
                throw new Zend_Config_Exception("Invalid key '$key'");
            }
        } else {
            $config[$key] = $value;
        }
        return $config;
    }


    /**
     * Helper function to handle the "dot" namespace syntax in the key.
     * Uses "." as the separator.
     *
     * @param array $parent
     * @param string $key
     * @param string $value
     * @return array
     */
    protected function _processLevelsInKey($parent, $key, $value)
    {
        if (strpos($key, '.')) {
            $pieces = explode('.', $key, 2);
            if (strlen($pieces[0]) && strlen($pieces[1])) {
                if (!isset($parent[$pieces[0]])) {
                    $parent[$pieces[0]] = array();
                } else if (!is_array($parent[$pieces[0]])) {
                    throw new Zend_Config_Exception("Cannot create sub-key for '{$pieces[0]}' as key already exists");
                }
                $parent[$pieces[0]] = $this->_processLevelsInKey($parent[$pieces[0]], $pieces[1], $value);
            } else {
                $parent->$key = $value;
            }
        } else if (strlen($key)) {
            $parent[$key] = $value;
        }
        return $parent;
    }
}
