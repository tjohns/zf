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

class Zend_View_Helper_Translate
{

    protected $translate = null;

    /**
     * Translate a message
     * You can give multiple params or an array of params.
     * If you want to output another locale just set it as last single parameter
     * Example 1: translate('%1\$s + %2\$s', $value1, $value2, $locale);
     * Example 2: translate('%1\$s + %2\$s', array($value1, $value2), $locale); 
     *
     * @param string           $messageid
     * @param int|string|array $options
     * @return string  Translated message
     */
    public function translate($messageid, $options = null)
    {
        if ($translate === null) {
            require_once 'Zend/Registry.php';
            if (!Zend_Registry::isRegistered('Zend_Translate')) {
                return $this;
            } else {
                $translate = Zend_Registry::get('Zend_Translate');
            }
        }

        $options = func_get_args();
        $count   = func_num_args();
        $locale  = null;
        if ($options[$count - 1] instanceof Zend_Locale) {
            $locale = array_pop($options);
        }
        $message = $translate->translate($messageid, $locale);
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
            $this->$trans = $translate;
        } else {
            require_once 'Zend/View/Helper/Exception.php';
            throw new Zend_View_Helper_Exception("You must set an instance of Zend_Translate or Zend_Translate_Adapter");
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
        $this->$translate->setLocale($locale);
        return $this;
    }
}
