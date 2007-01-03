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
require_once 'Zend.php';
require_once 'Zend/Translate/Exception.php';
require_once 'Zend/Locale.php';


/**
 * @category   Zend
 * @package    Zend_Translate
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Translate {

    // Class wide Constants

    // Internal variables

    /**
     * Locale Object / Setting
     */
    private $_Locale = '';
    private $_Adapter = false;


    /**
     * Generates the standard translation object
     *
     * @param $adapter string - Adapter to use
     * @param $options mixed  - Options for this adapter
     * @param $locale object  - OPTIONAL locale to use
     * @return object
     */
    public function __construct($adapter, $options, $locale = FALSE)
    {
        // set locale
        if ($locale === FALSE) {
            $this->_Locale = new Zend_Locale();
        } else {
            $this->_Locale = $locale;
        }

        $this->setAdapter($adapter, $options, $this->_Locale);
    }


    /**
     * Sets a new adapter
     *
     * @param $adapter string - adapter to use
     * @param $options mixed  - Adapter options
     * @param $locale object  - OPTIONAL locale to use
     * @return timestamp
     */
    public function setAdapter($adapter, $options, $locale = FALSE)
    {
        switch (strtolower($adapter)) {
            case 'array':
                require_once('Zend/Translate/Adapter/Core.php');
                $this->_Adapter = new Zend_Translate_Core($options, $locale);
                break;
            case 'cvs':
                throw new Zend_Translate_Exception('not supported for now');
                break;
            case 'gettext':
                require_once('Zend/Translate/Adapter/Gettext.php');
                $this->_Adapter = new Zend_Translate_Gettext($options, $locale);
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
     * @return string
     */
    public function getAdapter()
    {
        return $this->_Adapter->toString();
    }


    /**
     * Adds a new language to the Adapter
     * 
     * @param $locale mixed  - locale/language to add to this adapter
     * @param $options mixed - option for this adapter depends on the adapter
     *        'array'   - the array to add
     *        'gettext' - the gettext file inclusive the filename
     * @param $empty boolean - add if the language already exists 
     */
    public function addLanguage($locale, $options, $empty = FALSE)
    {
        $this->_Adapter->addLanguage($locale, $options, $empty);
    }


    /**
     * Sets a new locale/language
     *
     * @param $locale string - New locale/language to set
     */
    public function setLocale($locale)
    {
        $this->_Locale = $locale;
        $this->_Adapter->setLocale($locale);
    }


    /**
     * Gets the actual locale/language
     */
    public function getLocale()
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
        return $this->_Adapter->getLanguage();
    }


    /**
     * Returns the avaiable languages from this adapter
     *
     * @return $locale string
     */
    public function getLanguageList()
    {
        return $this->_Adapter->getLanguageList();
    }
    

    /**
     * is the wished language avaiable ?
     *
     * @param $language mixed - is locale or language avaiable 
     * @return boolean
     */
    public function isAvaiable($language)
    {
        return $this->_Adapter->isAvaiable($language);
    }


    /**
     * Translate the given string
     *
     * @param $translation string - string to translate
     * @param $locale object      - OPTIONAL locale/language to translate to 
     * @return string
     */
    public function _($translation, $locale = FALSE)
    {
        return $this->translate($translation, $locale);
    }


    /**
     * Translate the given string
     *
     * @param $translation string - string to translate
     * @param $locale object      - OPTIONAL locale/language to translate to 
     * @return string
     */
    public function translate($translation, $locale = FALSE)
    {
        return $this->_Adapter->translate($translation, $locale);
    }
}