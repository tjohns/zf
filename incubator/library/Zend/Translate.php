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


    /**
     * Generates the standard translation object
     *
     * @param $adaptor string - Adaptor to use
     * @param $options array  - Options for this adaptor
     * @param $locale string  - OPTIONAL locale to use
     * @return object
     */
    public function __construct($adaptor, $options, $locale = FALSE)
    {
    }


    /**
     * Sets a new adaptor
     *
     * @param $options array - Adaptor options
     * @return timestamp
     */
    public function setAdaptor($options)
    {
    }


    /**
     * Returns the adaptor and it's options
     *
     * @return array
     */
    public function getAdaptor()
    {
    }


    /**
     * Sets a new locale/language
     *
     * @param $locale string - New locale/language to set
     */
    public function setLocale($locale)
    {
    }


    /**
     * Gets the actual locale/language
     *
     * @return $locale string
     */
    public function getLocale($locale)
    {
    }


    /**
     * Gets the actual locale/language
     *
     * @return $locale string
     */
    public function getLanguageList($locale)
    {
    }


    /**
     * is the wished language avaiable
     *
     * @return boolean
     */
    public function getLanguageList($locale)
    {
    }


    /**
     * translation
     *
     * @return string
     */
    public function _($translation)
    {
        return $this->translate($translation);
    }


    /**
     * translation
     *
     * @return string
     */
    public function translate($translation)
    {
    }
}