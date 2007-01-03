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
 * @package    Zend_Translate
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Date.php 2498 2006-12-23 22:13:38Z thomas $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Include needed Translate classes
 */
require_once 'Zend/Translate/Exception.php';
require_once 'Zend/Locale.php';


/**
 * @category   Zend
 * @package    Zend_Translate
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Translate_Gettext extends Zend_Translate_Core {

    // Class wide Constants

    // Internal variables
    private $_BigEndian   = FALSE;
    private $_File        = FALSE;
    private $_Translation = array();


    /**
     * Generates the gettext adaptor
     *
     * @param $adaptor string - Adaptor to use
     * @param $options array  - Options for this adaptor
     * @param $locale string  - OPTIONAL locale to use
     * @return object
     */
    public function __construct($options, $locale = FALSE)
    {
        parent::__construct($options, $locale);
    }


    /**
     * translation
     *
     * @param $translation string - Translationstring
     * @param $language    locale - language to use
     * @return string
     */
    public function _($translation, $language)
    {
        return $this->translate($translation);
    }


    /**
     * translation
     *
     * @param $translation string - Translationstring
     * @param $language    locale - language to use
     * @return string
     */
    public function translate($translation, $language)
    {
        // search the translation table and return the translated string
    }


    /**
     * Read values from the MO file
     *
     * @param unknown_type $bytes
     */
    private function _readMOData($bytes)
    {
        if ($this->_BigEndian === FALSE) {
            return unpack('V' . $bytes, fread($this->_File, 4 * $bytes));
        } else {
            return unpack('N' . $bytes, fread($this->_File, 4 * $bytes));
        }
    }


    /**
     * Internal function for reading the MO file
     * 
     * @param $filename - MO File with path to read from 
     * @throws Zend_Translate_Exception
     */
    public function readFile($filename)
    {
        if (!file_exists($filename)) {
            throw Zend_Translate_Exception('translation file ' . $filename . ' not found');
        }

        $this->_File = @fopen($filename, 'rb');
        if (!$this->_File) {
            throw Zend_Translate_Exception('error opening translation file ' . $filename);
        }

        // get Endian
        $input = $this->_readMOData(1);
        if (dechex($input[1]) == "950412de") {
            $this->_BigEndian = FALSE;
        } else if (dechex($input[1] == "de120495")) {
            $this->_BigEndian = TRUE;
        } else {
            throw Zend_Translate_Exception($filename . ' is not a gettext file');
        }

        // read revision - not supported for now
        $input = $this->_readMOData(1);

        // number of bytes
        $input = $this->_readMOData(1);
        $total = $input[1];

        // number of original strings
        $input = $this->_readMOData(1);
        $OOffset = $input[1];

        // number of translation strings
        $input = $this->_readMOData(1);
        $TOffset = $input[1];

        // fill the original table
        $temporary = array(); 
        fseek($this->_File, $OOffset);
        $origtemp = $this->_readMOData(2 * $total);
        fseek($this->_File, $TOffset);
        $transtemp = $this->_readMOData(2 * $total);
        
        $length = 0;
        $offset = 0;
        for($count = 0; $count < $total; ++$count) {
            fseek($this->_File, $origtemp[$count * 2 + 2]);
            $original = @fread($this->_File, $origtemp[$count * 2 + 1]);
            fseek($this->_File, $transtemp[$count * 2 + 2]);
            $this->_Translation[$original] = fread($this->_File, $transtemp[$count * 2 + 1]);
        }
    }


    /**
     * returns the adaptors name
     */
    public function toString()
    {
        return "Gettext";
    }
}