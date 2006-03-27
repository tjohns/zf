<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to version 1.0 of the Zend Framework
 * license, that is bundled with this package in the file LICENSE, and
 * is available through the world-wide-web at the following URL:
 * http://www.zend.com/license/framework/1_0.txt. If you did not receive
 * a copy of the Zend Framework license and are unable to obtain it
 * through the world-wide-web, please send a note to license@zend.com
 * so we can mail you a copy immediately.
 *
 * @package    Zend_Pdf
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */


/** Zend_Pdf_Element_Dictionary */
require_once 'Zend/Pdf/Element/Dictionary.php';

/** Zend_Pdf_Element_Reference_Context */
require_once 'Zend/Pdf/Element/Reference/Context.php';


/**
 * PDF file trailer
 *
 * @package    Zend_Pdf
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
abstract class Zend_Pdf_Trailer
{
    static private $_allowedKeys = array('Size', 'Prev', 'Root', 'Encrypt', 'Info', 'ID', 'Index', 'W');

    /**
     * Trailer dictionary.
     *
     * @var Zend_Pdf_Element_Dictionary
     */
    private $_dict;

    /**
     * Check if key is correct
     *
     * @param string $key
     * @throws Zend_Pdf_Exception
     */
    private function _checkDictKey($key)
    {
        if ( !in_array($key, self::$_allowedKeys) ) {
            throw new Zend_Pdf_Exception("Unknown trailer dictionary key: '$key'.");
        }
    }


    /**
     * Object constructor
     *
     * @param Zend_Pdf_Element_Dictionary $dict
     */
    public function __construct(Zend_Pdf_Element_Dictionary $dict)
    {
        $this->_dict   = $dict;

        foreach ($this->_dict->getKeys() as $dictKey) {
            $this->_checkDictKey($dictKey);
        }
    }

    /**
     * Get handler
     *
     * @param string $property
     * @return mixed
     */
    public function __get($property)
    {
        return $this->_dict->$property;
    }

    /**
     * Set handler
     *
     * @param string $property
     * @param  mixed $value
     */
    public function __set($property, $value)
    {
        $this->_checkDictKey($property);
        $this->_dict->$property = $value;
    }

    /**
     * Return string trailer representation
     *
     * @return string
     */
    public function toString()
    {
        return "trailer\r" . $this->_dict->toString() . "\r";
    }


    /**
     * Get length of source PDF
     *
     * @return string
     */
    abstract public function getPDFLength();

    /**
     * Get PDF String
     *
     * @return string
     */
    abstract public function getPDFString();

    /**
     * Get header of free objects list
     * Returns object number of last free object
     *
     * @return integer
     */
    abstract public function getLastFreeObject();
}
