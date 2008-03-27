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
 * @package    Zend_Build
 * @subpackage Zend_Build_Resource
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Interface.php 3412 2007-02-14 22:22:35Z darby $
 */

/**
 * @see Zend_Config
 */
require_once 'Zend/Config.php';

/**
 * Singleton class to convert config arrays to XML 1.0 files.
 * 
 * @category   Zend
 * @package    Zend_Build
 * @subpackage Zend_Build_Resource
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Build_Resource_ConfigXmlWriter
{
    /**
     * CONFIG_XML_ROOT_ELEMENT
     *
     * @var string
     */
    const CONFIG_XML_ROOT_ELEMENT   = 'configdata';

    /**
     * writeConfigToXmlFile
     *
     * @param  Zend_Config $config
     * @param  string      $filename
     * @return void
     */
    public static function writeConfigToXmlFile (Zend_Config $config, $filename)
    {
        $xml = self::getXmlForConfig($config);

        // Now write it to the file
        file_put_contents($filename, $xml);
    }
    
    /**
     * Returns a SimpleXMLElement for the given config.
     * 
     * @see    Zend_Build_Resource_Interface
     * @param  Zend_Build_Resource_Interface Resource to convert to XML
     * @return string String in valid XML 1.0 format
     */
    public static function getXmlForConfig (Zend_Config $config)
    {
        // First create the empty XML element        $xml = self::_arrayToXml($config->toArray(), new SimpleXMLElement('<' . self::CONFIG_XML_ROOT_ELEMENT . '/>'), true);

        // Format output for readable XML and save to $filename
        $dom = new DomDocument('1.0');
        $domnode = dom_import_simplexml($xml);
        $domnode = $dom->importNode($domnode, true);
        $domnode = $dom->appendChild($domnode);
        $dom->formatOutput = true;
        return $dom->saveXML();
    }

    /**
     * _arrayToXml
     *
     * @param  array   $array
     * @param  string  $xml
     * @param  boolean $addAsChild
     * @return string
     */
    private static function _arrayToXml (array $array, $xml, $addAsChild = false)
    {
        foreach($array as $key=>$value) {
            if(is_array($value)) {
                $xml->addChild($key);
                self::_arrayToXml($value, $xml->$key);
            } elseif($addAsChild) {
                $xml->addChild($key, $value);
            } else { 
                $xml->addAttribute($key, $value);
            }
        }
        return $xml;
    }
}