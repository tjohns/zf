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
class Zend_Translate_Core {

    // Class wide Constants

    // Actual locale
    private   $_Locale    = FALSE;

    // Table of all supported languages
    protected $_Language  = array();

    /**
     * Array with all options
     * The following options are supported
     * 
     *  - [language] : actual set standard language
     */
    protected $_Options   = array(
        'language' => FALSE
    );

    /**
     * Generates the gettext adaptor
     *
     * @param $adaptor string - Adaptor to use
     * @param $options array  - Options for this adaptor
     * @param $locale string  - OPTIONAL locale to use
     * @throws Zend_Translate_Exception
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
     * Sets new adaptor options
     *
     * @param $options array - Adaptor options
     * @throws Zend_Translate_Exception
     */
    public function setOptions($options)
    {
        if (!is_array($options)) {
            throw new Zend_Translate_Exception('option not set or unknown');
        }

        foreach ($options as $key => $option) {
            switch(strtolower($key)) {
                case 'language' :
                    // todo: check if language exists
                    break;
            }
        }
    }


    /**
     * Returns the adaptors name and it's options
     *
     * @param  $option mixed - String returns this option
     *                         empty/false returns all options
     * @return array
     */
    public function getOptions($option = FALSE)
    {
        if (empty($option)) {
            return $this->_Options;
        }
        if (array_key_exists(strtolower($option), $this->_Options)) {
            return $this->_Options[strtolower($option)];
        }
        throw new Zend_Translate_Exception('Option ' . $option . ' does not exist');
    }


    /**
     * Sets a new locale/language
     *
     * @param $locale string - New locale/language to set
     * @throws Zend_Translate_Exception
     */
    public function setLocale($locale)
    {
        $this->_Locale = $locale;
        $this->setOptions(array('language' => $this->_locale));
    }


    /**
     * Gets the actual locale/language
     *
     * @return $locale string
     */
    public function getLocale()
    {
        return $this->_Locale;
    }


    /**
     * Gets the actual language
     *
     * @return $locale string
     */
    public function getLanguage()
    {
        return $this->_Options['language'];
    }


    /**
     * Returns the avaiable languages from this adaptor
     *
     * @return $locale string
     */
    public function getLanguageList()
    {
        return $this->_Language;
    }
    

    /**
     * is the wished language avaiable ?
     *
     * @param $language    locale - language to use
     * @return boolean
     */
    public function isAvaiable($language)
    {
        if (in_array($language, $this->_Options)) {
            return TRUE;
        }
        return FALSE;
    }


    /**
     * translation
     *
     * @param $translation string - Translationstring
     * @param $language    locale - OPTIONAL language to use
     * @return string
     */
    public function _($translation, $language = FALSE)
    {
        return $this->translate($translation, $language);
    }


    /**
     * translation
     *
     * @param $translation string - Translationstring
     * @param $language    locale - OPTIONAL language to use
     * @return string
     */
    public function translate($translation, $language = FALSE)
    {
        // search the translation table and return the translated string
    }
}