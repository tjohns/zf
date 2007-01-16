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
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Date.php 2498 2006-12-23 22:13:38Z thomas $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/** Zend_Locale */
require_once 'Zend/Locale.php';

/** Zend_Translate_Exception */
require_once 'Zend/Translate/Exception.php';

/**
 * @category   Zend
 * @package    Zend_Translate
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Translate_Adapter {

    /**
     * Actual set language
     *
     * @var string
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
     * @param  string|array        $options   Options for this adapter
     * @param  string|Zend_Locale  $language  OPTIONAL Language to set, identical with Locale identifiers
     *                                        see Zend_Locale for more information
     * @throws Zend_Translate_Exception
     */
    public function __construct($options, $language = null)
    {
        $this->addTranslation($language, $options);
        $this->setLanguage($language);
    }


    /**
     * Sets new adapter options
     *
     * @param  array  $options  Adapter options
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
     * @param  string  $optionKey  String returns this option
     *                             null returns all options
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
     * Gets the actual language
     * 
     * @return  string  Language
     */
    public function getLanguage()
    {
        return $this->_language;
    }


    /**
     * Sets a new language
     *
     * @param  string|Zend_Locale  $language  New language to set, identical with locale identifier,
     *                                        see Zend_Locale for more information
     * @throws Zend_Translate_Exception
     */
    public function setLanguage($language)
    {
        if (!Zend_Locale::isLocale($language)) {
            throw new Zend_Translate_Exception("language ($language) is no proper language", $language);
        }
        if ($language instanceof Zend_Locale) {
            $language = $language->toString();
        }

        if (!in_array($language, $this->_languages)) {
            throw new Zend_Translate_Exception("language ($language) has to be added before it can be used", $language);
        }

        $this->_language = $language;
    }


    /**
     * Returns the avaiable languages from this adapter
     */
    public function getLanguageList()
    {
        return $this->_languages;
    }


    /**
     * Is the wished language avaiable ?
     *
     * @param  string|Zend_Locale  $language  Language to search for, identical with locale identifier,
     *                                        see Zend_Locale for more information
     * @return boolean
     */
    public function isAvaiable($language)
    {
        if (!Zend_Locale::isLocale($language)) {
            throw new Zend_Translate_Exception("language ($language) is no proper language", $language);
        }
        if ($language instanceof Zend_Locale) {
            $language = $language->toString();
        }

        return in_array($language, $this->_languages);
    }

    /**
     * Load translation data
     *
     * @param  string|Zend_Locale  $language
     * @param  mixed $data
     */
    abstract protected function _loadTranslationData($language, $data);

    /**
     * Add translation data
     *
     * It may be a new language or additional data for existing language
     * If $clear parameter is true, then translation data for specified
     * language is replaced and added otherwise
     *
     * @param  string|Zend_Locale  $language  Language to add data for, identical with locale identifier,
     *                                        see Zend_Locale for more information
     * @param  mixed               $data      Translation data
     * @param  boolean             $clear     Empty the table or add if exists
     * @throws Zend_Translate_Exception
     */
    public function addTranslation($language, $data, $clear = false)
    {
        if (!Zend_Locale::isLocale($language)) {
            throw new Zend_Translate_Exception("language ($language) is no proper language", $language);
        }
        if ($language instanceof Zend_Locale) {
            $language = $language->toString();
        }

        if (!in_array($language, $this->_languages)) {
            $this->_languages[] = $language;
        }

        if ($clear  ||  !isset($this->_translate[$language])) {
            $this->_translate[$language] = array();
        }

        $this->_loadTranslationData($language, $data);
    }


    /**
     * Translates the given string
     * returns the translation
     *
     * @param  string              $translation  Translation string
     * @param  string|Zend_Locale  $language  OPTIONAL Language to use, identical with locale identifier,
     *                                        see Zend_Locale for more information
     * @return string
     */
    public function translate($translation, $language = null)
    {
        if ($language === null) {
            $language = $this->_language;
        }
        if (!Zend_Locale::isLocale($language)) {
            throw new Zend_Translate_Exception("language ($language) is no proper language", $language);
        }
        if ($language instanceof Zend_Locale) {
            $language = $language->toString();
        }

        if (array_key_exists($language, $this->_translate)) {
           if (array_key_exists($translation, $this->_translate[$language])) {
                // return original translation
                return $this->_translate[$language][$translation];
           }
        } else if (strlen($language) != 2) {
            // faster than creating a new locale and separate the leading part
            $language = substr($language, 0, -strlen(strrchr($language, '_')));

            if (array_key_exists($language, $this->_translate)) {
                if (array_key_exists($translation, $this->_translate[$language])) {
                    // return regionless translation (en_US -> en)
                    return $this->_translate[$language][$translation];
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
