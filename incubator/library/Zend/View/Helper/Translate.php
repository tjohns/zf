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
 * @category   Zend_View
 * @package    Helper
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Zend/Locale.php';
require_once 'Zend/Translate.php';
require_once 'Zend/Translate/Adapter.php';

class Zend_View_Helper_Translate
{

    protected $translate = null;

    /**
     * Constructor for manually handling
     *
     * @param Zend_Translate|Zend_Translate_Adapter $translate
     */
    public function __construct($translate = null)
    {
        if (!empty($translate)) {
            $this->setTranslate($translate);
        } else {
            require_once 'Zend/Registry.php';
            if (Zend_Registry::isRegistered('Zend_Translate')) {
                $this->translate = Zend_Registry::get('Zend_Translate');
            }
        }
        return $this;
    }

    /**
     * Translate a message
     * You can give multiple params or an array of params.
     * If you want to output another locale just set it as last single parameter
     * Example 1: translate('%1\$s + %2\$s', $value1, $value2, $locale);
     * Example 2: translate('%1\$s + %2\$s', array($value1, $value2), $locale); 
     *
     * @param string           $messageid
     * @return string  Translated message
     */
    public function translate($messageid = null)
    {
        if ($this->translate === null) {
            require_once 'Zend/Registry.php';
            if (!Zend_Registry::isRegistered('Zend_Translate')) {
                if (empty($messageid)) {
                    return $this;
                } else {
                    return $messageid;
                }
            } else {
                $this->translate = Zend_Registry::get('Zend_Translate');
            }
        }

        $options = func_get_args();
        array_shift($options);

        $count   = count($options);
        $locale  = null;
        if ($count > 0) {
            if (Zend_Locale::isLocale($options[$count - 1])) {
                $locale = array_pop($options);
            }
        }
        if ((count($options) == 1) and (is_array($options[0]))) {
            $options = $options[0];
        }
        $message = $this->translate->translate($messageid, $locale);
        return vsprintf($message, $options);
    }

    /**
     * Set's an Translation Instance or Adapter for Translation
     *
     * @param Zend_Translate|Zend_Translate_Adapter $translate
     * @return Zend_View_Helper_Translate
     */
    public function setTranslate($translate)
    {
        if (($translate instanceof Zend_Translate_Adapter) or
            ($translate instanceof Zend_Translate)) {
            $this->translate = $translate;
        } else {
            require_once 'Zend/View/Exception.php';
            throw new Zend_View_Exception("You must set an instance of Zend_Translate or Zend_Translate_Adapter");
        }
        return $this;
    }

    /**
     * Set's an new locale for all further translations
     *
     * @param string|Zend_Locale $locale
     * @return Zend_View_Helper_Translate
     */
    public function setLocale($locale = null)
    {
        if ($this->translate === null) {
            require_once 'Zend/View/Exception.php';
            throw new Zend_View_Exception("You must set an instance of Zend_Translate or Zend_Translate_Adapter");
        }
        $this->translate->setLocale($locale);
        return $this;
    }

    /**
     * Returns the set locale for translations
     *
     * @return string|Zend_Locale
     */
    public function getLocale()
    {
        if ($this->translate === null) {
            require_once 'Zend/View/Exception.php';
            throw new Zend_View_Exception("You must set an instance of Zend_Translate or Zend_Translate_Adapter");
        }
        return $this->translate->getLocale();
    }
}
