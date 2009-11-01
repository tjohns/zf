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
 * @package    Zend_Db_Xml
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Zend_Db_Xml_XmlUtil provides a set of utility convenience methods to convert to/from various
 * XML representations and Zend_Db_Xml_XmlContent
 * 
 * @category   Zend
 * @package    Zend_Db_Xml
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Xml_XmlUtil
{
    const DATA = 'data';
    const ABOUT = 'about';

    /**
     * Convert the string parameter, $str, into a DOM
     *
     * @param string $str
     * 
     * @return DOMDocument $document
     */
    public static function importString($str)
    {
        $document = new DOMDocument();
        $document->loadXML($str);
        return $document;
    }

    /**
     * Convert SimpleXML object, $sXML, into a DOM
     *
     * @param SimpleXML $sXML
     * 
     * @return DOMDocument $document
     */
    public static function importSimpleXML($sXML)
    {
        $document = new DOMDocument();
        $document->loadXML($sXML->asXML());
        return $document;
    }

    /**
     * Open the file associated with file at local path, $fXML.
     * Convert it to a DOM
     *
     * parameter represents path to the file
     *
     * @param string $fXML
     *
     * @return DOMDocument $document
     */
    public static function importXMLFile($fXML)
    {
        $document = new DOMDocument();
        $document->load($fXML);
        return $document;
    }

    /**
     * $fsXML is a file stream.  Convert the stream to a
     * DOM
     *
     * @param resource $fsXML
     * 
     * @return DOMDocument $document
     */
    public static function importXMLFileStream($fsXML)
    {
        return Zend_Db_Xml_XmlUtil::importString(stream_get_contents($fsXML));
    }

    /**
     * Save the DOM to a file in the local directory path
     * 
     * @param Zend_Db_Xml_XmlContent $xmlContent
     * @param string $which, DATA or ABOUT
     * @param string $name of the file
     * 
     * @return mixed
     */
    public static function exportToFile($xmlContent, $which, $name)
    {
        if ($which == self::DATA ) {
            if (!is_null($xmlContent->data)) {
                return $xmlContent->data->save($name);	
            } else {
                return false;
            }
        } else if ($which == self::ABOUT ) {
            if (!is_null($xmlContent->about)) {
                return $xmlContent->about->save($name);
            } else {
                return false;
            }
        }

        throw new Zend_Db_Xml_XmlException("Must Specify DATA or ABOUT XML to save");
    }

    /**
     * Convenience method to get a SimpleXML object representation of a DOM
     *
     * @param Zend_Db_Xml_XmlContent $xmlContent
     * @param string $which, get the DOM associated with either data or about
     * 
     * @return SimpleXML
     */	
    public static function exportToSimpleXML($xmlContent, $which)
    {
        if ($which == self::DATA ) {
            if (!is_null($xmlContent->data)) {
                return simplexml_import_dom($xmlContent->data);	
            } else {
                return null;
            }
        } else if ($which == self::ABOUT ) {
            if (!is_null($xmlContent->about)) {
                return simplexml_import_dom($xmlContent->about);
            } else {
                return null;
            }
        }

        throw new Zend_Db_Xml_XmlException("Must Specify DATA or ABOUT XML to save");
    }

    /**
     * Convenience method to get a string representation of a DOM
     * Same as saveXML()
     *
     * @param Zend_Db_Xml_XmlContent $xmlContent
     * @param string $which, get the DOM associated with either data or about
     * 
     * @return string
     */
    public static function exportToString($xmlContent, $which)
    {
        return self::saveXML($xmlContent, $which);
    }

    /**
     * Convenience method to get a string representation of a DOM
     *
     * @param Zend_Db_Xml_XmlContent $xmlContent
     * @param string $which, get the DOM associated with either data or about
     * 
     * @return string
     */
    public static function saveXML($xmlContent, $which)
    {
        if ($which == self::DATA) {
            if (!is_null($xmlContent->data)) {
                return $xmlContent->data->saveXML();
            } else {
                return null;
            }
        } elseif ($which == self::ABOUT) {
            if (!is_null($xmlContent->about)) {
                return $xmlContent->about->saveXML();
            } else {
                return null;
            }
        } 

        throw new Zend_Db_Xml_XmlException("Must Specify DATA or ABOUT XML to retrieve");
    }

    /**
     * Convenience method to get a DOM
     *
     * @param Zend_Db_Xml_XmlContent $xmlContent
     * @param string $which, get the DOM associated with either data or about
     * 
     * @return DOMDocument
     */
    public static function getDOM($xmlContent, $which)
    {
        if ($which == self::DATA ) {
            return $xmlContent->data;
        } elseif ($which == self::ABOUT ) {
            return $xmlContent->about;
        } 

        throw new Zend_Db_Xml_XmlException("Must Specify DATA or ABOUT XML to retrieve");
    }

    /**
     * convenience method for setting a DOM
     *
     * @param Zend_Db_Xml_XmlContent $xmlContent
     * @param string $which indicating which field to set the dom, 
     *        either data or about
     * @param DOMDocument $dom
     * 
     * @return void
     */
    public static function setDOM($xmlContent, $which, $dom)
    {
        if ($which == self::DATA) {
            $xmlContent->setData($dom);
        } else if ($which == self::ABOUT) {
            $xmlContent->setAbout($dom);
        } else {
            throw new Zend_Db_Xml_XmlException("Must Specify DATA or ABOUT XML to set");	
        }
    }

    /**
     * Convenience method
     * to create an Zend_Db_Xml_XmlContent() object
     * 
     * @return Zend_Db_Xml_XmlContent
     */
    public static function createDocument()
    {
        return new Zend_Db_Xml_XmlContent();
    }

    /**
     * Retrieve the DOM from either DATA or ABOUT as an array
     *
     * @param Zend_Db_Xml_XmlContent $xmlContent
     * @param string $which
     * 
     * @return array $map
     */
    public static function getDOMAsMap($xmlContent, $which)
    {
        /**
         * @todo convert dom to array, including nested elements
         */
    }

    /**
     * Set the DOM for either DATA or ABOUT from an array
     *
     * @param array $map
     * @param Zend_Db_Xml_XmlContent $xmlContent
     * @param string $which
     * 
     * @return void
     */
    public static function setDOMFromMap($map, $xmlContent, $which)
    {
        /**
         * @todo convert an array into a DOM including nested 
         */
    }

    /**
     * Convert an array into an Zend_Db_Xml_XmlContent object
     * 
     * @param array $xmlRow
     * 
     * @return Zend_Db_Xml_XmlContent
     */
    public static function getXmlResult($xmlRow)
    {
        $xmlResult = new Zend_Db_Xml_XmlContent();
        $xmlResult->setId($xmlRow['ID']);

        $dbxml = $xmlRow['DATA'];
        if(is_null($dbxml)) {
            $domdoc = null;
        } else {
            $domdoc = new DOMDocument();	
            $domdoc->loadXML($xmlRow['DATA']);
        }

        Zend_Db_Xml_XmlUtil::setDOM($xmlResult, Zend_Db_Xml_XmlUtil::DATA, $domdoc);

        $about = $xmlRow['ABOUT'];
        if (!is_null($about)) {
            $domAbt = new DOMDocument();
            $domAbt->loadXML($about);
            Zend_Db_Xml_XmlUtil::setDOM($xmlResult, Zend_Db_Xml_XmlUtil::ABOUT, $domAbt);
        }

        $attachment = $xmlRow['ATTACHMENT'];
        if (!is_null($attachment)) {
            $xmlResult->attachment = $attachment;
        }

        return $xmlResult;
    }
}