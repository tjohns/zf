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
 * @copyright  Copyright (c) 2007 Zend Technologies USA Inc. (http://www.zend.com)
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

    // Actual set locale
    private   $_Locale    = FALSE;

    // Table of all supported languages
    protected $_Language  = array();

    /**
     * Array with all options
     * The following options are supported
     * 
     * [language] : actual set standard language
     * [tablesize]: maximum size of the table array // not supported for now
     */
    protected $_Options   = array(
        'language' => FALSE,
        'tablesize' => 500
    );

    /**
     * Translation table
     */
    protected $_Translate = array(
    );


    /**
     * Generates the core adaptor
     *
     * @param $adaptor string - Adaptor to use
     * @param $options mixed  - Options for this adaptor
     * @param $locale object  - OPTIONAL locale to use
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

        $this->setLocale($locale);
        $this->addLanguage($locale, $options);
    }


    /**
     * Sets new adaptor options
     *
     * @param $options mixed - Adaptor options
     * @throws Zend_Translate_Exception
     */
    public function setOptions($options)
    {
        if (!is_array($options)) {
            throw new Zend_Translate_Exception('option not set or unknown');
        }

        if (!is_array($options)) {
            $options['language'] = (string) $options;
        }

        foreach ($options as $key => $option) {
            switch(strtolower($key)) {
                case 'language' :
                    if ($option instanceof Zend_Locale) {
                        $option = $option->toString();
                    }

                    if (in_array($option, $this->_Language)) {
                        $this->_Options['language'] = (string) $option;
                    } else {
                        throw new Zend_Translate_Exception('language unknown');
                    }
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
     * @param $locale object - New locale/language to set
     * @throws Zend_Translate_Exception
     */
    public function setLocale($locale)
    {
        if (is_string($locale)) {
            $this->_Locale = new Zend_Locale($locale);
        } else {
            $this->_Locale = $locale;
            $locale = $locale->toString();
        }
        
        if (!in_array($locale, $this->_Language)) {
            $this->_Language[] = $locale;
        }
        $this->setOptions(array('language' => $this->_Locale));
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
     */
    public function getLanguage()
    {
        return (string) $this->_Options['language'];
    }


    /**
     * Returns the avaiable languages from this adaptor
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
        if (in_array($language, $this->_Language)) {
            return TRUE;
        }
        return FALSE;
    }


    /**
     * Sets the translation to the translationtable
     * 
     * @param $locale object - for which locale is the translationtable
     * @param $table   array - the translationtable to set
     * @param $empty boolean - Empty the table or add if exists
     */
    protected function addLanguage($locale, $table, $empty = FALSE)
    {
        if ($locale instanceof Zend_Locale) {
            $locale = $locale->toString();
        }

        if (($empty) or (!isset($this->_Translate[$locale]))) {
            $this->_Translate[$locale] = array();
        }

        if (!in_array($locale, $this->_Language)) {
            $this->_Language[] = $locale;
        }
        $this->_Translate[$locale] = array_merge($this->_Translate[$locale], $table);
    }


    /**
     * translation
     *
     * @param $translation string - Translationstring
     * @param $locale object      - OPTIONAL language to use
     * @return string
     */
    public function _($translation, $locale = FALSE)
    {
        return $this->translate($translation, $locale);
    }


    /**
     * Translates the given string
     * returns the elee
     *
     * @param $translation string - Translationstring
     * @param $locale object      - OPTIONAL language to use
     * @return string
     */
    public function translate($translation, $locale = FALSE)
    {
        if ($locale === FALSE) {
            $locale = $this->_Options['language'];
        } else if ($locale instanceof Zend_Locale) {
            $locale = $locale->toString();
        }

        if (array_key_exists($locale, $this->_Translate)) {
           if (array_key_exists($translation, $this->_Translate[$locale])) {
                // return original locale
                return $this->_Translate[$locale][$translation];
           }
        } else if (strlen($locale) != 2) {
            // faster than creating a new locale and seperate the leading part
            $locale = substr($locale, 0, -strlen(strrchr($locale, '_')));

            if (array_key_exists($locale, $this->_Translate)) {
                if (array_key_exists($translation, $this->_Translate[$locale])) {
                    // return regionless translation (en_US -> en)
                    return $this->_Translate[$locale][$translation];
                }
            }
        }

        // no translation found, return original
        return $translation;
    }


    /**
     * returns the adaptors name
     */
    public function toString()
    {
        return "Array";
    }
}