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
 * @see Zend_Build_XMLConvertor
 */
require_once 'Zend/Build/Resource/Interface.php';

/**
 * @see Zend_Loader
 */
require_once 'Zend/Loader.php';

/**
 * Static class to convert from build resources to XML and back.
 * 
 * @category   Zend
 * @package    Zend_Build
 * @subpackage Zend_Build_Resource
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Build_Resource_ConfigConvertor
{
    /**
     * EXTENDS_KEYWORD
     *
     * @const string
     */
    const EXTENDS_KEYWORD = Zend_Config::EXTENDS_KEYWORD;

    /**
     * CONFIG_XML_ROOT_ELEMENT
     *
     * @const string
     */
    const CONFIG_XML_ROOT_ELEMENT   = 'configdata';

    /**
     * Converts build resource to XML.
     * 
     * @see    Zend_Build_Resource_Interface
     * @param  Zend_Build_Resource_Interface $resource Resource to convert to XML
     * @param  string                        $filename
     * @return string String in valid XML 1.0 format
     */
    public static function writeResourceToConfigXml (Zend_Build_Resource_Interface $resource, $filename)
    {
        // First create the empty DOM document        $dom = new DOMDocument();
        
        // Now create the root element for XML config files and append it
        $configRoot = $dom->createElement(self::CONFIG_XML_ROOT_ELEMENT);
        $dom->appendChild($configRoot);
        
        // Now recursively create all document elements        $configRoot->appendChild(self::_resourceToDOMElement($dom, $resource));
        
        // Format output for readable XML        $dom->formatOutput = true;
        return $dom->saveXML();
    }

    /**
     * Converts XML 1.0 string to Zend_Build_Resource
     * 
     * @param  string $filename String in valid XML 1.0 format
     * @return Zend_Build_Resource_Interface Resource to convert to XML
     */
    public static function readConfigXmlToResource ($filename)
    {
        // Reuse Zend_Config here
        $config = new Zend_Config_Xml();
        // Return resource tree        return $resource;
    }

    /**
     * _resourceToDomElement
     *
     * @param  mixed                         $dom
     * @param  Zend_Build_Resource_Interface $resource
     * @return mixed
     */
    private static function _resourceToDomElement ($dom, Zend_Build_Resource_Interface $resource)
    {
        $dom_element = $dom->createElement(get_class($resource));
        foreach ($resource as $name => $value) {
            $dom_element->setAttribute($name, is_object($value) ? $value->__toString() : $value);
        }

        // Recurse on children        $children = $resource->getChildren();
        if (isset($children)) {
            foreach ($children as $child) {
                $dom_element->appendChild(self::_resourceToDomElement($dom, $child));
            }
        }
        return $dom_element;
    }

    /**
     * _configToResource
     *
     * @param  Zend_Config $config
     * @return Zend_Build_Resource_Interface
     */
    private static function _configToResource ($config)
    {
        if (! isset($dom_element))
            return null;
        $classname = $dom_element->tagName;
        // Load the right class        Zend_Loader::loadClass($classname);
        // Get the resource name        $name = $dom_element->getAttribute('name');
        // Instantiate the object        $resource = new $classname($name);
        // Set the attributes        foreach ($resource as $name => $value) {
            if (! isset($value)) {
                $resource->$name = $dom_element->getAttribute($value);
            }
        }
        // Now create the children        foreach ($dom_element->childNodes as $child) {
            if ($child instanceof DOMElement) {
                $resource->addChild(self::_domElementToResource($child));
            }
        }
        // Now return the resource        return $resource;
    }
}