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
class Zend_Config_Xml
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

        $config = simplexml_load_file($filename);
        if (!isset($config->$section)) {
            throw new Zend_Config_Exception("Section '$section' cannot be found in $filename");
        }

        $self = new self();
        return $self->_processExtends($config, $section);
    }


    /**
     * Helper function to process each element in the section and handle
     * the "extends" inheritance attribute.
     *
     * @param SimpleXMLElement $element
     * @param string $section
     * @param array $config
     * @throws Zend_Config_Exception
     * @return array
     */
    protected function _processExtends($element, $section, $config = array())
    {
        if (!$element->$section) {
            throw new Zend_Config_Exception("Section '$section' cannot be found");
        }

        $thisSection = $element->$section;

        if (isset($thisSection['extends'])) {
            $config = $this->_processExtends($element, (string)$thisSection['extends'], $config);
        }

        $config = $this->_arrayMergeRecursive($config, $this->_toArray($thisSection));

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
    protected function _toArray($xmlObject)
    {
        $config = array();
        foreach ($xmlObject->children() as $key => $value) {
            if ($value->children()) {
                $config[$key] = $this->_toArray($value);
            } else {
                $config[$key] = (string)$value;

                // convert to correct type for booleans
                if (strtolower($config[$key]) == 'false') {
                    $config[$key] = false;
                }
                if (strtolower($config[$key]) == 'true') {
                    $config[$key] = true;
                }
            }
        }
        return $config;
    }

    /**
     * Merge two arrays recursively, overwriting keys of the same name name
     * in $array1 with the value in $array2.
     *
     * @param array $array1
     * @param array $array2
     * @return array
     */
    protected function _arrayMergeRecursive($array1, $array2)
    {
        if (is_array($array1) && is_array($array2)) {
            foreach ($array2 AS $key => $value) {
                if(isset($array1[$key])) {
                    $array1[$key] = $this->_arrayMergeRecursive($array1[$key], $value);
                } else {
                    $array1[$key] = $value;
                }
            }
        } else {
            $array1 = $array2;
        }
        return $array1;
    }
}
