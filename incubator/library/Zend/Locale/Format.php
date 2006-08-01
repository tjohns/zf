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
 * @package    Zend_Locale
 * @subpackage Format
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * @category   Zend
 * @package    Zend_Locale 
 * @subpackage Format
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Locale_Format
{
    /**
     * Returns the first found number from an string (or integer/float)
     * Parsing depends on given locale (grouping and decimal)
     * 
     * Examples for input:
     * '  2345.4356,1234' = 23455456.1234
     * '+23,3452.123' = 233452.123
     * ' 12343 ' = 12343
     * '-9456km' = -9456
     * '0' = 0
     * '(-){0,1}(\d+(\.){0,1})*(\,){0,1})\d+' 
     */
    public static function getNumber($input, $locale)
    {
        // Get correct signs for this locale
        $symbols = Zend_Locale_Data::getContent($locale,'numbersymbols');

        // Parse input locale aware
        $regex = '/('.$symbols['minus'].'){0,1}(\d+(\\'.$symbols['group'].'){0,1})*(\\'.$symbols['decimal'].'){0,1}\d+/';
        preg_match($regex, $input, $treffer);
        if (!isset($treffer[0]))
            $this->throwException('No value in '.$input.' found');

        // Change locale input to be standard number 
        if ($symbols['minus'] != "-")
            $treffer[0] = strtr($treffer[0],$symbols['minus'],'-');
        $treffer[0] = str_replace($symbols['group'],"",$treffer[0]);

        if ($symbols['decimal'] != '.')
            $treffer[0] = str_replace($symbols['decimal'],".",$treffer[0]);
            
        return $treffer[0];
    }


    /**
     * Throw an exception
     * 
     * Note : for performance reasons, the "load" of Zend/Measure/Exception is dynamic
     */
    public static function throwException($message)
    {
        // For performance reasons, we use this dynamic inclusion
        require_once('Zend/Locale/Exception.php');
        throw new Zend_Locale_Exception($message);
    }
}