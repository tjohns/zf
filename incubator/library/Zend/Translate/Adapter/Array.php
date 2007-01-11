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
class Zend_Translate_Adapter_Array {
    /**
     * Generates the adaptor
     *
     * @param array $options - Options for this adaptor
     * @param mixed $locale  - OPTIONAL locale to use
     */
    public function __construct($options, $locale = null)
    {
        parent::__construct($options, $locale);
    }

    /**
     * Sets the translation to the translationtable
     *
     * @param object  $locale - for which locale is the translationtable
     * @param array   $data   - the translationtable to set
     * @param boolean $empty  - Empty the table or add if exists
     */
    public function addLanguage($locale, $data, $empty = false)
    {
        if ($locale instanceof Zend_Locale) {
            $locale = $locale->toString();
        }

        if ($empty  ||  !isset($this->_translate[$locale])) {
            $this->_translate[$locale] = array();
        }

        if (!in_array($locale, $this->_languages)) {
            $this->_languages[] = $locale;
        }
        $this->_translate[$locale] = array_merge($this->_translate[$locale], $data);
    }

    /**
     * returns the adapters name
     */
    public function toString()
    {
        return "Array";
    }
}
