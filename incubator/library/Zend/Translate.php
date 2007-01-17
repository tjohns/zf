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

/** Zend_Translate_Exception */
require_once 'Zend/Translate/Exception.php';

/** Zend_Locale */
require_once 'Zend/Locale.php';


/**
 * @category   Zend
 * @package    Zend_Translate
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Translate {
    /**
     * Adapter names constants
     */
     const AN_GETTEXT = 'gettext';
     const AN_ARRAY   = 'array';

    /**
     * Adapter
     *
     * @var Zend_Translate_Adapter
     */
    private $_adapter;


    /**
     * Generates the standard translation object
     *
     * @param string $adapter - Adapter to use
     * @param mixed  $options - Options for this adapter
     * @param mixed  $locale  - OPTIONAL locale to use
     */
    public function __construct($adapter, $options, $locale = null)
    {
        $this->setAdapter($adapter, $options, $locale);
    }


    /**
     * Sets a new adapter
     *
     * @param string $adapter - adapter to use
     * @param mixed  $options - Adapter options
     * @param mixed  $locale  - OPTIONAL locale to use
     * @return timestamp
     */
    public function setAdapter($adapter, $options, $locale = null)
    {
        if (!$locale = Zend_Locale::isLocale($locale)) {
            throw new Zend_Translate_Exception("language ($locale) is a unknown language", $locale);
        }

        switch (strtolower($adapter)) {
            case 'array':
                /** Zend_Translate_Adapter_Array */
                require_once('Zend/Translate/Adapter/Array.php');
                $this->_adapter = new Zend_Translate_Adapter_Array($options, $locale);
                break;
            case 'cvs':
                throw new Zend_Translate_Exception('not supported for now');
                break;
            case 'gettext':
                /** Zend_Translate_Adapter_Gettext */
                require_once('Zend/Translate/Adapter/Gettext.php');
                $this->_adapter = new Zend_Translate_Adapter_Gettext($options, $locale);
                break;
            case 'qt':
                throw new Zend_Translate_Exception('not supported for now');
                break;
            case 'sql':
                throw new Zend_Translate_Exception('not supported for now');
                break;
            case 'tbx':
                throw new Zend_Translate_Exception('not supported for now');
                break;
            case 'tmx':
                throw new Zend_Translate_Exception('not supported for now');
                break;
            case 'xliff':
                throw new Zend_Translate_Exception('not supported for now');
                break;
            case 'xmltm':
                throw new Zend_Translate_Exception('not supported for now');
                break;
            default:
                throw new Zend_Translate_Exception('no adapter selected');
                break;
        }
    }


    /**
     * Returns the adapters name and it's options
     *
     * @return Zend_Translate_Adapter
     */
    public function getAdapter()
    {
        return $this->_adapter;
    }


    /**
     * Add translation data.
     *
     * It may be a new language or additional data for existing language
     * If $clear parameter is true, then translation data for specified
     * language is replaced and added otherwise
     *
     * @param mixed $locale  - locale/language to add to this adapter
     * @param mixed $options - option for this adapter depends on the adapter
     *        'array'   - the array to add
     *        'gettext' - the gettext file inclusive the filename
     * @param boolean $empty - add if the language already exists
     */
    public function addTranslation($locale, $options, $clear = false)
    {
        $this->_adapter->addTranslation($locale, $options, $clear);
    }


    /**
     * Sets a new locale/language
     *
     * @param mixed $locale - Locale to set
     */
    public function setLocale($locale)
    {
        $this->_adapter->setLocale($locale);
    }


    /**
     * Gets the actual locale/language
     *
     * @return Zend_Locale|null
     */
    public function getLocale()
    {
        return $this->_adapter->getLocale();
    }


    /**
     * Sets the actual language, can differ from the set locale
     *
     * @param string $language - Language to set
     */
    public function setLanguage($language)
    {
        return $this->_adapter->setLanguage($language);
    }

    /**
     * Gets the actual language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->_adapter->getLanguage();
    }


    /**
     * Returns the avaiable languages from this adapter
     *
     * @return string
     */
    public function getLanguageList()
    {
        return $this->_adapter->getLanguageList();
    }


    /**
     * is the wished language avaiable ?
     *
     * @param mixed $language - is locale or language avaiable
     * @return boolean
     */
    public function isAvaiable($language)
    {
        return $this->_adapter->isAvaiable($language);
    }


    /**
     * Translate the given string
     *
     * @param string $translation - string to translate
     * @param mixed  $locale      - OPTIONAL locale/language to translate to
     * @return string
     */
    public function _($translation, $locale = null)
    {
        return $this->_adapter->translate($translation, $locale);
    }


    /**
     * Translate the given string
     *
     * @param string $translation - string to translate
     * @param mixed  $locale      - OPTIONAL locale/language to translate to
     * @return string
     */
    public function translate($translation, $locale = null)
    {
        return $this->_adapter->translate($translation, $locale);
    }
}
