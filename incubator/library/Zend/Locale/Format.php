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
     * 
     * @param $input  - string
     * @param $locale - locale
     * @return integer / float
     */
    public static function getNumber($input, $locale)
    {
        // Get correct signs for this locale
        $symbols = Zend_Locale_Data::getContent($locale,'numbersymbols');

        // Parse input locale aware
        $regex = '/('.$symbols['minus'].'){0,1}(\d+(\\'.$symbols['group'].'){0,1})*(\\'.$symbols['decimal'].'){0,1}\d+/';
        preg_match($regex, $input, $found);
        if (!isset($found[0]))
            self::throwException('No value in '.$input.' found');

        // Change locale input to be standard number
        if ($symbols['minus'] != "-")
            $found[0] = strtr($found[0],$symbols['minus'],'-');
        $found[0] = str_replace($symbols['group'],"",$found[0]);

        if ($symbols['decimal'] != '.') {
            $found[0] = str_replace($symbols['decimal'],".",$found[0]);
            $found[0] = (float) $found[0];
        } else {
            $found[0] = (int) $found[0];
        }

        return $found[0];
    }


    /**
     * Returns a locale formatted number
     * 
     * @param $value  - number to localize
     * @param $locale - locale
     * @return string - locale formatted number
     */
    public static function toNumber($value, $locale)
    {
        $this->throwException('function not implemented');

        if (!is_integer($value))
            return self::toFloat($value, $locale);

        // Get correct signs for this locale
        $symbols = Zend_Locale_Data::getContent($locale,'numbersymbols');
        $format  = Zend_Locale_Data::getContent($locale,'decimalnumberformat');
        
        $format = $format['default'];
        $number = '';
        if (strpos($format, ';') !== false)
        {
            if ($value < 0)
                $format = substr($format, strpos($format, ';') + 1);
            else
                $format = substr($format, 0, strpos($format, ';'));
        }

        if ($value < 0)
        {
            
            $number = $symbols['minus'];
        }

        $seperate = substr($format,strpos($format,',')+1);
        $seperate = strlen(substr($seperate,0,strpos($seperate, '.')));
        
        $length = strlen($value);
        
//                    <pattern>#,##0.###</pattern>
//                    <!-- number grouping same as India (thousands, lakhs, crores, etc.) -->
//                    <pattern draft="true">#,##,##0.###</pattern>

        return (string) $number;        
    }


    /**
     * 
     */
    public static function isNumber()
    {
        $this->throwException('function not implemented');
    }


    /**
     * 
     */
    public static function getFloat()
    {
        
    }


    /**
     * 
     */
    public static function toFloat()
    {
        
    }


    /**
     * 
     */
    public static function isFloat()
    {
        
    }


    /**
     * Returns the normalized date from an locale date
     *
     * @param $date   - string : date string
     * @param $type   - string : type of date (full, long, short...)
     * @param $locale - locale : locale for date normalization
     * @return array
     */
    public static function getDate($date, $type, $locale)
    {
        self::throwException('function not implemented');

        // Get correct date for this locale
        $format = Zend_Locale_Data::getContent($locale,'dateformat', array('gregorian', $type));
print $format['pattern']."\n<br>";
        // Parse input locale aware
//
        $pattern = str_split($format['pattern']);
        $last = 0;
        $step = 0;
        $format[0] = $pattern[0];
        for ($key = 1; $key < count($pattern); ++$key)
        {
            if ($pattern[$key] == $pattern[$last])
            {
                $format[$step] .= $pattern[$key];
            } else {
                ++$step;
                $format[$step] = $pattern[$key];
            }
            $last = $key;
        }

        unset($format['pattern']);
        
        foreach($format as $found)
        {
            switch($found)
            {
                // Era
                case 'G'    :
                case 'GG'   :
                case 'GGG'  :
                case 'GGGG' :
                case 'GGGGG':
                    break;

                // Quarter
                case 'Q'    :
                case 'QQ'   :
                case 'QQQ'  :
                case 'QQQQ' :
                // Quarter standalone
                case 'q'    :
                case 'qq'   :
                case 'qqq'  :
                case 'qqqq' :

                // Month
                case 'M'    :
                case 'MM'   :
                case 'MMM'  :
                case 'MMMM' :
                case 'MMMMM':

                // Month standalone
                case 'm'    :
                case 'mm'   :
                case 'mmm'  :
                case 'mmmm' :
                case 'mmmmm':

                // week of year
                case 'w'    :
                case 'ww'   :
                
                // week of month
                case 'W'    :

                // day of month
                case 'd'    :
                case 'dd'   :
            }
            
            if ($found[0] == 'y')
            {}
        }        
print_r($format);
        $regex = '/[GyYuQqMLwWdDFgEecahHKkmsSAzZv]*/';
        $found = preg_match_all($regex, $format['pattern'], $match);

print_r($found);
print_r($match);
    }


    public function toDate()
    {
        $this->throwException('function not implemented');
    }


    public function isDate()
    {
        $this->throwException('function not implemented');
    }


    public function getTime()
    {
        $this->throwException('function not implemented');
    }


    public function toTime()
    {
        $this->throwException('function not implemented');
    }


    public function isTime()
    {
        $this->throwException('function not implemented');
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