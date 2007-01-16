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


/** Zend_Locale */
require_once 'Zend/Locale.php';

/** Zend_Translate_Exception */
require_once 'Zend/Translate/Exception.php';

/** Zend_Translate_Adapter */
require_once 'Zend/Translate/Adapter.php';


/**
 * @category   Zend
 * @package    Zend_Translate
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Translate_Adapter {
    /**
     * Actual set locale
     *
     * @var Zend_Locale|string
     */
    protected $_locale;

    /**
     * Actual set language
     *
     * @var Zend_Locale|string
     */
    protected $_language;

    /**
     * Table of all supported languages
     *
     * @var array
     */
    protected $_languages  = array();

    /**
     * Array with all options
     * The following options are supported
     *
     * [tablesize]: maximum size of the table array // not supported for now
     *
     * @var array
     */
    protected $_options = array('tablesize' => 500);

    /**
     * Translation table
     *
     * @var array
     */
    protected $_translate = array();


    /**
     * Generates the adapter
     *
     * @param mixed  $options - Options for this adapter
     * @param mixed  $locale  - OPTIONAL locale to use
     * @throws Zend_Translate_Exception
     */
    public function __construct($options, $locale = null)
    {
        $this->setLocale($locale);
        $this->addTranslation($locale, $options);
    }


    /**
     * Sets new adapter options
     *
     * @param array $options - Adapter options
     * @throws Zend_Translate_Exception
     */
    public function setOptions($options)
    {
        foreach ($options as $key => $option) {
            $this->_options[strtolower($key)] = $option;
        }
    }

    /**
     * Returns the adapters name and it's options
     *
     * @param  string $optionKey - String returns this option
     *                              returns all options
     * @return mixed
     */
    public function getOptions($optionKey = null)
    {
        if ($optionKey === null) {
            return $this->_options;
        }
        if (array_key_exists(strtolower($optionKey), $this->_options)) {
            return $this->_options[strtolower($optionKey)];
        }
        return null;
    }


    /**
     * Sets a new locale/language
     *
     * @param mixed $locale - New locale/language to set
     * @throws Zend_Translate_Exception
     */
    public function setLocale($locale)
    {
        $this->_locale = $locale;

        if (is_string($locale)) {
            $this->_language = $locale;
        } else if ($locale instanceof Zend_Locale) {
            $this->_language = $locale->toString();
        } else {
            throw new Zend_Translate_Exception('Locale must be a string or Zend_Locale object');
        }

        if (!in_array($this->_language, $this->_languages)) {
            $this->_languages[] = $this->_language;
        }
    }


    /**
     * Gets the actual locale/language
     *
     * @return $locale string|Zend_Locale
     */
    public function getLocale()
    {
        return $this->_locale;
    }


    /**
     * Sets a new language
     *
     * @param string $language - New locale/language to set
     * @throws Zend_Translate_Exception
     */
    public function setLanguage($language)
    {
        $this->_locale   = $language;
        $this->_language = $language;

        if (!in_array($this->_language, $this->_languages)) {
            $this->_languages[] = $this->_language;
        }
    }

    /**
     * Gets the actual language
     */
    public function getLanguage()
    {
        return $this->_language;
    }


    /**
     * Returns the avaiable languages from this adapter
     */
    public function getLanguageList()
    {
        return $this->_languages;
    }


    /**
     * is the wished language avaiable ?
     *
     * @param string $language - language to use
     * @return boolean
     */
    public function isAvaiable($language)
    {
        return in_array($language, $this->_languages);
    }

    /**
     * Load translation data
     *
     * @param string $language
     * @param mixed $data
     */
    abstract protected function _loadTranslationData($language, $data);

    /**
     * Add translation data
     *
     * It may be a new language or additional data for existing language
     * If $clear parameter is true, then translation data for specified
     * language is replaced and added otherwise
     *
     * @param mixed   $locale - for which locale is the translationtable
     * @param mixed   $data   - translation data
     * @param boolean $clear  - Empty the table or add if exists
     * @throws Zend_Translate_Exception
     */
    public function addTranslation($locale, $data, $clear = false)
    {
        if (is_string($locale)) {
            $language = $locale;
        } else if ($locale instanceof Zend_Locale) {
            $language = $locale->toString();
        } else {
            throw new Zend_Translate_Exception('Locale must be a string or Zend_Locale object');
        }

        if (!in_array($this->_language, $this->_languages)) {
            $this->_languages[] = $language;
        }

        if ($clear  ||  !isset($this->_translate[$language])) {
            $this->_translate[$language] = array();
        }

        $this->_loadTranslationData($language, $data);
    }


    /**
     * Translates the given string
     * returns the elee
     *
     * @param string $translation - Translationstring
     * @param mixed  $locale      - OPTIONAL language to use
     * @return string
     */
    public function translate($translation, $locale = null)
    {
        if ($locale === null) {
            $locale = $this->_language;
        } else if ($locale instanceof Zend_Locale) {
            $locale = $locale->toString();
        }

        if (array_key_exists($locale, $this->_translate)) {
           if (array_key_exists($translation, $this->_translate[$locale])) {
                // return original locale
                return $this->_translate[$locale][$translation];
           }
        } else if (strlen($locale) != 2) {
            // faster than creating a new locale and separate the leading part
            $locale = substr($locale, 0, -strlen(strrchr($locale, '_')));

            if (array_key_exists($locale, $this->_translate)) {
                if (array_key_exists($translation, $this->_translate[$locale])) {
                    // return regionless translation (en_US -> en)
                    return $this->_translate[$locale][$translation];
                }
            }
        }

        // no translation found, return original
        return $translation;
    }


    /**
     * Returns the adapter name
     *
     * @return string
     */
    abstract public function toString();
}
