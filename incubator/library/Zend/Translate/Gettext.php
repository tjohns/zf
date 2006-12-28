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
class Zend_Translate_Gettext {

    // Class wide Constants

    // Internal variables

    /**
     * Locale Object / Setting
     */
    private $_Locale    = '';
    private $_Options   = array();
    private $_BigEndian = FALSE;
    private $_File      = FALSE;


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
        // set locale
        if ($locale === FALSE) {
            $this->_Locale = new Zend_Locale();
        } else {
            $this->_Locale = $locale;
        }

        $this->setOptions($options);
    }


    /**
     * Sets a new adaptor
     *
     * @param $options array - Adaptor options
     * @return timestamp
     */
    public function setOptions($options)
    {
    }


    /**
     * Returns the adaptors name and it's options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_Options;
    }


    /**
     * Sets a new locale/language
     *
     * @param $locale string - New locale/language to set
     */
    public function setLocale($locale)
    {
        $this->_Locale = $locale;
        // check if locale exists return false if not
    }


    /**
     * Gets the actual locale/language
     *
     * @return $locale string
     */
    public function getLocale($locale)
    {
        return $this->_Locale;
    }


    /**
     * Gets the actual language, can differ from the set locale
     *
     * @return $locale string
     */
    public function getLanguage()
    {
        return $this->_Language();
    }


    /**
     * Returns the avaiable languages from this adaptor
     *
     * @return $locale string
     */
    public function getLanguageList()
    {
        // return which languages are avaiable for translation
    }
    

    /**
     * is the wished language avaiable ?
     *
     * @param $language    locale - language to use
     * @return boolean
     */
    public function isAvaiable($language)
    {
        // return if this language is translatable
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

        $input = array_shift($this->_readMOData(1));
print_r($input);
        if ($input == ())
    }
}