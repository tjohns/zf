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

require_once 'Zend/Locale/Data.php';

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
     * Returns the first found number from an string
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
     * @param $locale - OPTIONAL locale 
     * @param $precision - OPTIONAL precision of float value
     * @return string
     */
    public static function getNumber($input, $precision = false, $locale = false)
    {
        if (!is_string($input))
            return $input;

        if (!is_int($precision) and ($locale == false)) {
            $locale    = $precision;
            $precision = false;
        }

        // Get correct signs for this locale
        $symbols = Zend_Locale_Data::getContent($locale,'numbersymbols');

        // Parse input locale aware
        $regex = '/(' . $symbols['minus'] . '){0,1}(\d+(\\' . $symbols['group'] . '){0,1})*(\\' .
                        $symbols['decimal'] . '){0,1}\d+/';
        preg_match($regex, $input, $found);
        if (!isset($found[0]))
            self::throwException('No value in ' . $input . ' found');
        $found = $found[0];

        // Change locale input to be standard number
        if ($symbols['minus'] != "-")
            $found = strtr($found,$symbols['minus'],'-');
        $found = str_replace($symbols['group'],'', $found);

        // Do precision
        if (strpos($found, $symbols['decimal']) !== false) {
            if ($symbols['decimal'] != '.') {
                $found = str_replace($symbols['decimal'], ".", $found);
            }

            $pre = substr($found, strpos($found, '.') + 1);
            if ($precision === false) {
                $precision = strlen($pre);
            }

            if (strlen($pre) >= $precision) {
                $found = substr($found, 0, strlen($found) - strlen($pre) + $precision);
            }
        }

        return $found;
    }


    /**
     * Returns a locale formatted number
     * 
     * @param $value     - number to localize
     * @param $precision - OPTIONAL precision
     * @param $locale    - OPTIONAL locale
     * @return string    - locale formatted number
     */
    public static function toNumber($value, $precision = false, $locale = false)
    {
        if (!is_int($precision) and ($locale == false)) {
            $locale    = $precision;
            $precision = false;
        }

        // Get correct signs for this locale
        $symbols = Zend_Locale_Data::getContent($locale, 'numbersymbols');
        $format  = Zend_Locale_Data::getContent($locale, 'decimalnumberformat');
        $format  = $format['default'];
        iconv_set_encoding('internal_encoding', 'UTF-8');
        
        // seperate negative format pattern when avaiable 
        if (iconv_strpos($format, ';') !== false) {
            if (bccomp($value, 0) < 0) {
                $format = iconv_substr($format, iconv_strpos($format, ';') + 1);
            } else {
                $format = iconv_substr($format, 0, iconv_strpos($format, ';'));
            }
        }
        
        // set negative sign
        if (bccomp($value, 0) < 0) {
            if (iconv_strpos($format, '-') === false) {
                $format = $symbols['minus'] . $format;
            } else {
                $format = str_replace('-', $symbols['minus'], $format);
            }
        }
        
        // get number parts
        if (iconv_strpos($value, '.') !== false) {
            if ($precision === false) {
                $precision = iconv_substr($value, iconv_strpos($value, '.') + 1);
            } else {
                $precision = iconv_substr($value, iconv_strpos($value, '.') + 1, $precision);
            }
        } else {
            $precision = '';
        }

        // get fraction and format lengths
        bcscale(iconv_strlen($precision));
        $prec = bcsub($value, bcsub($value, '0', 0));
        if (iconv_strpos($prec, '-') !== false) {
            $prec = iconv_substr($prec, 1);
        }
        $number = bcsub($value, 0, 0);
        if (iconv_strpos($number, '-') !== false) {
            $number = iconv_substr($number, 1);
        }
        $group  = iconv_strrpos($format, ',');
        $group2 = iconv_strpos ($format, ',');
        $point  = iconv_strpos ($format, '.');

        // Add fraction
        if ($prec == 0) {
            $format = iconv_substr($format, 0, $point) . iconv_substr($format, iconv_strrpos($format, '#') + 1);
        } else {
            $format = iconv_substr($format, 0, $point) . $symbols['decimal'] . iconv_substr($prec, 2).
                      iconv_substr($format, iconv_strrpos($format, '#') + 1);
        }
        
        // Add seperation
        if ($group == 0) {
            // no seperation
            $format = $number . iconv_substr($format, $point);

        } else if ($group == $group2) {
            
            // only 1 seperation
            $seperation = ($point - $group - 1);
            for ($x = iconv_strlen($number); $x > $group2; $x -= $seperation) {
                 $number = iconv_substr($number, 0, $x - $seperation) . $symbols['group']
                         . iconv_substr($number, $x - $seperation);
            }
            $format = iconv_substr($format, 0, iconv_strpos($format, '#')) . $number . iconv_substr($format, $point);
            
        } else {
            
            // 2 seperations
            if (iconv_strlen($number) > ($point - $group - 1)) { 
                $seperation = ($point - $group - 1);
                $number = iconv_substr($number, 0, iconv_strlen($number) - $seperation) . $symbols['group']
                        . iconv_substr($number, iconv_strlen($number) - $seperation);

                if ((iconv_strlen($number) - 1) > ($point - $group)) {
                    $seperation2 = ($group - $group2 - 1);
                    
                    for ($x = iconv_strlen($number) - $seperation2 - 2; $x > $seperation2; $x -= $seperation2) {
                         $number = iconv_substr($number, 0, $x - $seperation2) . $symbols['group']
                                 . iconv_substr($number, $x - $seperation2);
                    }
                }

            }
            $format = iconv_substr($format, 0, iconv_strpos($format, '#')) . $number . iconv_substr($format, $point);

        }
        return (string) $format;        
    }


    /**
     * Returns if a number was found
     * 
     * @param  $input  - localized number string
     * @param  $locale - OPTIONAL locale
     * @return boolean
     */
    public static function isNumber($input, $locale = false)
    {
        // Get correct signs for this locale
        $symbols = Zend_Locale_Data::getContent($locale,'numbersymbols');

        // Parse input locale aware
        $regex = '/('.$symbols['minus'].'){0,1}(\d+(\\'.$symbols['group'].'){0,1})*(\\'.$symbols['decimal'].'){0,1}\d+/';
        preg_match($regex, $input, $found);

        if (!isset($found[0]))
            return false;
        return true;
    }


    /**
     * Alias for getNumber
     * 
     * @param $input     - string
     * @param $locale    - OPTIONAL locale 
     * @param $precision - OPTIONAL precision of float value
     * @return float
     */
    public static function getFloat($input, $precision = false, $locale = false)
    {
        return floatval(self::getNumber($input, $precision, $locale));
    }


    /**
     * Returns a locale formatted integer number
     * Alias for toNumber()
     * 
     * @param $value  - number to localize
     * @param $precision - OPTIONAL precision
     * @param $locale - OPTIONAL locale
     * @return string - locale formatted number
     */
    public static function toFloat($value, $precision = false, $locale = false)
    {
        return self::toNumber($value, $precision, $locale);
    }


    /**
     * Returns if a float was found
     * Alias for isNumber()
     * 
     * @param  $input  - localized number string
     * @param  $locale - OPTIONAL locale
     * @return boolean
     */
    public static function isFloat($value, $locale = false)
    {
        return self::isNumber($value, $locale);
    }


    /**
     * Returns the first found integer from an string
     * Parsing depends on given locale (grouping and decimal)
     *
     * Examples for input:
     * '  2345.4356,1234' = 23455456
     * '+23,3452.123' = 233452
     * ' 12343 ' = 12343
     * '-9456km' = -9456
     * '0' = 0
     * '(-){0,1}(\d+(\.){0,1})*(\,){0,1})\d+'
     * 
     * @param $input     - string
     * @param $locale    - OPTIONAL locale 
     * @return float
     */
    public static function getInteger($input, $locale = false)
    {
        return intval(self::getFloat($input, 0, $locale));
    }


    /**
     * Returns a localized number
     * 
     * @param $value  - number to localize
     * @param $locale - OPTIONAL locale
     * @return string - locale formatted number
     */
    public static function toInteger($value, $locale = false)
    {
        return self::toNumber($value, 0, $locale);
    }


    /**
     * Returns if a integer was found
     * 
     * @param  $input  - localized number string
     * @param  $locale - OPTIONAL locale
     * @return boolean
     */
    public static function isInteger($value, $locale = false)
    {
        return self::isNumber($value, $locale);
    }


    /**
     * Split numbers in proper array fields
     * @param string  $number   Number to parse
     * @param string  $format   Format to parse
     */
    private static function _parseDate($number, $format, $locale)
    {
        $day   = iconv_strpos($format, 'd');
        $month = iconv_strpos($format, 'M');
        $year  = iconv_strpos($format, 'y');
        $hour  = iconv_strpos($format, 'H');
        $min   = iconv_strpos($format, 'm');
        $sec   = iconv_strpos($format, 's');
        if ($hour === false) {
            $hour = iconv_strpos($format, 'h');
        }
        
        if ($day !== false) {
            $parse[$day]   = 'd';
            $parse[$month] = 'M';
            $parse[$year]  = 'y';
        }
        if ($hour !== false) {
            $parse[$hour] = 'H';
            $parse[$min]  = 'm';
            $parse[$sec]  = 's';
        }

        // format unknown wether date nor time
        if (empty($parse)) {
            self::throwException('unknown format, wether date nor time in ' . $format . ' found');
        }
        ksort($parse);

        // convert month string to number
        $monthlist = Zend_Locale_Data::getContent($locale, 'monthlist', array('gregorian', 'abbreviated'));
        $position = false;
        foreach($monthlist as $key => $name) {
            if (iconv_strpos($number, $name) !== false) {
                $position = iconv_strpos($number, $name);
                $number   = str_replace($name, $key, $number);
                break;
            }
        }

        // split number parts 
        $split = false;
        preg_match_all('/\d+/', $number, $splitted);

        if (count($splitted[0]) == 0) {
            self::throwException('No date part in ' . $number . ' found');
        }
        
        if (count($splitted[0]) == 1) {
            $split = 0;
        }
        $cnt = 0;
        foreach($parse as $key => $value) {

            switch($value) {
                case 'd':
                    if ($split === false) {
                        if (count($splitted[0]) > $cnt) {
                            $result['day']    = (int) $splitted[0][$cnt];
                        }
                    } else {
                        $result['day']    = (int) iconv_substr($splitted[0][0], $split, 2);
                        $split += 2;
                    }
                    ++$cnt;
                    break;
                case 'M':
                    if ($split === false) {
                        if (count($splitted[0]) > $cnt) {
                            $result['month']  = (int) $splitted[0][$cnt];
                        }
                    } else {
                        $result['month']  = (int) iconv_substr($splitted[0][0], $split, 2);
                        $split += 2;
                    }
                    ++$cnt;
                    break;
                case 'y':
                    $length = 2;
                    if (iconv_substr($format, $year, 4) == 'yyyy') {
                        $length = 4;
                    }
                    if ($split === false) {
                        if (count($splitted[0]) > $cnt) {
                            $result['year']   = (int) $splitted[0][$cnt];
                        }
                    } else {
                        $result['year']   = (int) iconv_substr($splitted[0][0], $split, $length);
                        $split += $length;
                    }
                    ++$cnt;
                    break;
                case 'H':
                    if ($split === false) {
                        if (count($splitted[0]) > $cnt) {
                            $result['hour']   = (int) $splitted[0][$cnt];
                        }
                    } else {
                        $result['hour']   = (int) iconv_substr($splitted[0][0], $split, 2);
                        $split += 2;
                    }
                    ++$cnt;
                    break;
                case 'm':
                    if ($split === false) {
                        if (count($splitted[0]) > $cnt) {
                            $result['minute'] = (int) $splitted[0][$cnt];
                        }
                    } else {
                        $result['minute'] = (int) iconv_substr($splitted[0][0], $split, 2);
                        $split += 2;
                    }
                    ++$cnt;
                    break;
                case 's':
                    if ($split === false) {
                        if (count($splitted[0]) > $cnt) {
                            $result['second'] = (int) $splitted[0][$cnt];
                        }
                    } else {
                        $result['second'] = (int) iconv_substr($splitted[0][0], $split, 2);
                        $split += 2;
                    }
                    ++$cnt;
                    break;
            }
        }

        if ($day !== false) {
            // fix false month
            if (isset($result['day']) and isset($result['month'])) {
                if (($position !== false) && ($position != $month)) {
                    $temp = $result['day'];
                    $result['day']   = $result['month'];
                    $result['month'] = $temp;
                }
            }

            // fix switched values d <> y
            if (isset($result['day']) and isset($result['year'])) {
                if ($result['day'] > 31) {
                    $temp = $result['year'];
                    $result['year'] = $result['day'];
                    $result['day']  = $temp;
                }
            }

            // fix switched values M <> y
            if (isset($result['month']) and isset($result['year'])) {
                if ($result['month'] > 31) {
                    $temp = $result['year'];
                    $result['year']  = $result['month'];
                    $result['month'] = $temp;
                }
            }

            // fix switched values M <> y
            if (isset($result['month']) and isset($result['day'])) {
                if ($result['month'] > 12) {
                    $temp = $result['day'];
                    $result['day']   = $result['month'];
                    $result['month'] = $temp;
                }
            }
        }
        return $result;
    }


    /**
     * Returns an array with the normalized date from an locale date
     * a input of 10.01.2006 for locale 'de' would return
     * array ('day' => 10, 'month' => 1, 'year' => 2006)
     *
     * @param string $date    date string
     * @param string $format  date type CLDR format !!!
     * @param locale $locale  OPTIONAL locale of date string
     * @return array
     */
    public static function getDate($date, $format = false, $locale = false)
    {
        if ($format === false) {
            $format = Zend_Locale_Data::getContent($locale, 'defdateformat', 'gregorian');
            $format = $format['default'];

            $format = Zend_Locale_Data::getContent($locale, 'dateformat', array('gregorian', $format));
            $format = $format['pattern'];
        }

        $date = self::_parseDate($date, $format, $locale);

        return $date;
    }

    /**
     * Returns is the given string is a date
     *
     * @param string $date    date string
     * @param string $format  date type CLDR format !!!
     * @param locale $locale  OPTIONAL locale of date string
     * @return boolean
     */
    public static function isDate($date, $format = false, $locale = false)
    {
        try {
            $date = self::getDate($date, $format, $locale);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }


    /**
     * Returns an array with the normalized time from an locale time
     * a input of 11:20:55 for locale 'de' would return
     * array ('hour' => 11, 'minute' => 20, 'second' => 55)
     *
     * @param string $time    time string
     * @param string $format  time type CLDR format !!!
     * @param locale $locale  OPTIONAL locale of time string
     * @return array
     */
    public static function getTime($time, $format = false, $locale = false)
    {
        if ($format === false) {
            $format = Zend_Locale_Data::getContent($locale, 'deftimeformat', 'gregorian');
            $format = $format['default'];

            $format = Zend_Locale_Data::getContent($locale, 'timeformat', array('gregorian', $format));
            $format = $format['pattern'];
        }

        $time = self::_parseDate($time, $format, $locale);

        return $time;
    }


    /**
     * Returns is the given string is a time
     *
     * @param string $time    time string
     * @param string $format  time type CLDR format !!!
     * @param locale $locale  OPTIONAL locale of time string
     * @return boolean
     */
    public static function isTime($time, $format = false, $locale = false)
    {
        try {
            $date = self::getTime($time, $format, $locale);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }


    /**
     * Throw an exception
     *
     * Note : for performance reasons, the "load" of Zend/Measure/Exception is dynamic
     */
    public static function throwException($message)
    {
        // For performance reasons, we use this dynamic inclusion
        require_once 'Zend/Locale/Exception.php';
        throw new Zend_Locale_Exception($message);
    }
}