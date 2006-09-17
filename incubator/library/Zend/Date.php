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
 * @package    Zend_Date
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Include needed Date classes
 */
require_once('Zend/Date/DateObject.php');


/**
 * Include needed Locale classes
 */
require_once('Zend/Locale.php');
require_once('Zend/Locale/Data.php');


/**
 * @category   Zend
 * @package    Zend_Date
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Date {

    // Class wide Date Constants

    // day formats
    const DAY            = 'Zend_Date::DAY';            // d - 2 digit day of month, 01-31
    const WEEKDAY_SHORT  = 'Zend_Date::WEEKDAY_SHORT';  // D - 3 letter day of week - locale aware, Mon-Sun
    const DAY_SHORT      = 'Zend_Date::DAY_SHORT';      // j - 1,2 digit day of month, 1-31
    const WEEKDAY        = 'Zend_Date::WEEKDAY';        // l - full day name - locale aware, Monday - Sunday
    const WEEKDAY_8601   = 'Zend_Date::WEEKDAY_8601';   // N - digit weekday ISO 8601, 1-7
    const DAY_SUFFIX     = 'Zend_Date::DAY_SUFFIX';     // S - english suffix day of month, st-th
    const WEEKDAY_DIGIT  = 'Zend_Date::WEEKDAY_DIGIT';  // w - weekday, 0-6
    const DAY_OF_YEAR    = 'Zend_Date::DAY_OF_YEAR';    // z - Number of day of year

    const WEEKDAY_NARROW = 'Zend_Date::WEEKDAY_NARROW'; // --- 1 letter day name - locale aware, M-S
    const WEEKDAY_NAME   = 'Zend_Date::WEEKDAY_NAME';   // --- 2 letter day name - locale aware,Mo-Su

    // week formats
    const WEEK           = 'Zend_Date::WEEK';           // W - number of week ISO8601, 1-53

    // month formats
    const MONTH          = 'Zend_Date::MONTH';          // F - full month name - locale aware, January-December
    const MONTH_SHORT    = 'Zend_Date::MONTH_SHORT';    // m - 2 digit month, 01-12
    const MONTH_NAME     = 'Zend_Date::MONTH_NAME';     // M - 3 letter monthname - locale aware, Jan-Dec
    const MONTH_DIGIT    = 'Zend_Date::MONTH_DIGIT';    // n - 1 digit month, no leading zeros, 1-12
    const MONTH_DAYS     = 'Zend_Date::MONTH_DAYS';     // t - Number of days this month

    const MONTH_NARROW   = 'Zend_Date::MONTH_NARROW';   // --- 1 letter month name - locale aware, J-D

    // year formats
    const LEAPYEAR       = 'Zend_Date::LEAPYEAR';       // L - is leapyear ?, 0-1
    const YEAR_8601      = 'Zend_Date::YEAR_8601';      // o - number of year ISO8601
    const YEAR           = 'Zend_Date::YEAR';           // Y - 4 digit year
    const YEAR_SHORT     = 'Zend_Date::YEAR_SHORT';     // y - 2 digit year, leading zeros 00-99

    const YEAR_SHORT_8601= 'Zend_Date::YEAR_SHORT_8601';// --- 2 digit number of year ISO8601

    // time formats
    const MERIDIEM       = 'Zend_Date::MERIDIEM';       // A,a - AM/PM - locale aware, AM/PM
    const SWATCH         = 'Zend_Date::SWATCH';         // B - Swatch Internet Time
    const HOUR_SHORT_AM  = 'Zend_Date::HOUR_SHORT_AM';  // g - 1 digit hour, no leading zero, 1-12 am/pm
    const HOUR_SHORT     = 'Zend_Date::HOUR_SHORT';     // G - 1 digit hour, no leading zero, 0-23
    const HOUR_AM        = 'Zend_Date::HOUR_AM';        // h - 2 digit hour, leading zeros, 01-12 am/pm
    const HOUR           = 'Zend_Date::HOUR';           // H - 2 digit hour, leading zeros, 00-23
    const MINUTE         = 'Zend_Date::MINUTE';         // i - 2 digit minute, leading zeros, 00-59
    const SECOND         = 'Zend_Date::SECOND';         // s - 2 digit second, leading zeros, 00-59

    const MINUTE_SHORT   = 'Zend_Date::MINUTE_SHORT';   // --- 1 digit minute, no leading zero, 0-59
    const SECOND_SHORT   = 'Zend_Date::SECOND_SHORT';   // --- 1 digit second, no leading zero, 0-59

    // timezone formats
    const TIMEZONE_NAME  = 'Zend_Date::TIMEZONE_NAME';  // e - timezone string
    const DAYLIGHT       = 'Zend_Date::DAYLIGHT';       // I - is Daylight saving time ?, 0-1
    const GMT_DIFF       = 'Zend_Date::GMT_DIFF';       // O - GMT difference, -1200 +1200
    const GMT_DIFF_SEP   = 'Zend_Date::GMT_DIFF_SEP';   // P - seperated GMT diff, -12:00 +12:00
    const TIMEZONE       = 'Zend_Date::TIMEZONE';       // T - timezone, EST, GMT, MDT
    const TIMEZONE_SECS  = 'Zend_Date::TIMEZONE_SECS';  // Z - timezone offset in seconds, -43200 +43200

    // date strings
    const ISO_8601       = 'Zend_Date::ISO_8601';       // c - ISO 8601 date string
    const RFC_2822       = 'Zend_Date::RFC_2822';       // r - RFC 2822 date string
    const TIMESTAMP      = 'Zend_Date::TIMESTAMP';      // U - unix timestamp

    // additional formats
    const ERA            = 'Zend_Date::ERA';            // --- short name of era, locale aware,
    const ERA_NAME       = 'Zend_Date::ERA_NAME';       // --- full name of era, locale aware,
    const DATES          = 'Zend_Date::DATES';          // --- standard date, locale aware
    const DATE_FULL      = 'Zend_Date::DATE_FULL';      // --- full date, locale aware
    const DATE_LONG      = 'Zend_Date::DATE_LONG';      // --- long date, locale aware
    const DATE_MEDIUM    = 'Zend_Date::DATE_MEDIUM';    // --- medium date, locale aware
    const DATE_SHORT     = 'Zend_Date::DATE_SHORT';     // --- short date, locale aware
    const TIMES          = 'Zend_Date::TIMES';          // --- standard time, locale aware
    const TIME_FULL      = 'Zend_Date::TIME_FULL';      // --- full time, locale aware
    const TIME_LONG      = 'Zend_Date::TIME_LONG';      // --- long time, locale aware
    const TIME_MEDIUM    = 'Zend_Date::TIME_MEDIUM';    // --- medium time, locale aware
    const TIME_SHORT     = 'Zend_Date::TIME_SHORT';     // --- short time, locale aware
    const ATOM           = 'Zend_Date::ATOM';           // --- DATE_ATOM
    const COOKIE         = 'Zend_Date::COOKIE';         // --- DATE_COOKIE
    const RFC_822        = 'Zend_Date::RFC_822';        // --- DATE_RFC822
    const RFC_850        = 'Zend_Date::RFC_850';        // --- DATE_RFC850
    const RFC_1036       = 'Zend_Date::RFC_1036';       // --- DATE_RFC1036
    const RFC_1123       = 'Zend_Date::RFC_1123';       // --- DATE_RFC1123
    const RSS            = 'Zend_Date::RSS';            // --- DATE_RSS
    const W3C            = 'Zend_Date::W3C';            // --- DATE_W3C


    private $_Const = array(
        // day formats
        'Zend_Date::DAY'            => 'd',
        'Zend_Date::WEEKDAY_SHORT'  => 'D',
        'Zend_Date::DAY_SHORT'      => 'j',
        'Zend_Date::WEEKDAY'        => 'l',
        'Zend_Date::WEEKDAY_8601'   => 'N',
        'Zend_Date::DAY_SUFFIX'     => 'S',
        'Zend_Date::WEEKDAY_DIGIT'  => 'w',
        'Zend_Date::DAY_OF_YEAR'    => 'z',

        'Zend_Date::WEEKDAY_NARROW' => 'xxw',
        'Zend_Date::WEEKDAY_NAME'   => 'xxn',

        // week formats
        'Zend_Date::WEEK'           => 'W',

        // month formats
        'Zend_Date::MONTH'          => 'F',
        'Zend_Date::MONTH_SHORT'    => 'm',
        'Zend_Date::MONTH_NAME'     => 'M',
        'Zend_Date::MONTH_DIGIT'    => 'n',
        'Zend_Date::MONTH_DAYS'     => 't',

        'Zend_Date::MONTH_NARROW'   => 'xxm',

        // year formats
        'Zend_Date::LEAPYEAR'       => 'L',
        'Zend_Date::YEAR_8601'      => 'o',
        'Zend_Date::YEAR'           => 'Y',
        'Zend_Date::YEAR_SHORT'     => 'y',
        
        'Zend_Date::YEAR_SHORT_8601'=> 'xya',

        // time formats
        'Zend_Date::MERIDIEM'       => 'A',
        'Zend_Date::SWATCH'         => 'B',
        'Zend_Date::HOUR_SHORT_AM'  => 'g',
        'Zend_Date::HOUR_SHORT'     => 'G',
        'Zend_Date::HOUR_AM'        => 'h',
        'Zend_Date::HOUR'           => 'H',
        'Zend_Date::MINUTE'         => 'i',
        'Zend_Date::SECOND'         => 's',

        'Zend_Date::MINUTE_SHORT'   => 'xxi',
        'Zend_Date::SECOND_SHORT'   => 'xxs',

        // timezone formats
        'Zend_Date::TIMEZONE_NAME'  => 'e',
        'Zend_Date::DAYLIGHT'       => 'I',
        'Zend_Date::GMT_DIFF'       => 'O',
        'Zend_Date::GMT_DIFF_SEP'   => 'P',
        'Zend_Date::TIMEZONE'       => 'T',
        'Zend_Date::TIMEZONE_SECS'  => 'Z',

        // date strings
        'Zend_Date::ISO_8601'       => 'c',
        'Zend_Date::RFC_2822'       => 'r',
        'Zend_Date::TIMESTAMP'      => 'U',

        // additional formats
        'Zend_Date::ERA'            => 'xxe',
        'Zend_Date::ERA_NAME'       => 'xxf',
        'Zend_Date::DATES'          => 'xdd',
        'Zend_Date::DATE_FULL'      => 'xdf',
        'Zend_Date::DATE_LONG'      => 'xdl',
        'Zend_Date::DATE_MEDIUM'    => 'xdm',
        'Zend_Date::DATE_SHORT'     => 'xds',
        'Zend_Date::TIMES'          => 'xtt',
        'Zend_Date::TIME_FULL'      => 'xtf',
        'Zend_Date::TIME_LONG'      => 'xtl',
        'Zend_Date::TIME_MEDIUM'    => 'xtm',
        'Zend_Date::TIME_SHORT'     => 'xts',
        'Zend_Date::ATOM'           => 'xxa',
        'Zend_Date::COOKIE'         => 'xxc',
        'Zend_Date::RFC_822'        => 'xr2',
        'Zend_Date::RFC_850'        => 'xr5',
        'Zend_Date::RFC_1036'       => 'xr6',
        'Zend_Date::RFC_1123'       => 'xr3',
        'Zend_Date::RSS'            => 'xxr',
        'Zend_Date::W3C'            => 'xx3'
    );


    /**
     * Date Object
     */
    public $_Date;


    /**
     * Locale Object / Setting
     */
    private $_Locale = '';


    /**
     * Generates the standard date object
     * could be
     *   - Unix timestamp
     *   - ISO
     *   - Locale
     *
     * @param $date string   - OPTIONAL date string depending on $parameter
     * @param $locale string - OPTIONAL locale for parsing input
     * @param $part mixed    - OPTIONAL defines the input format of $date
     * @return object
     */
    public function __construct($date, $part = false, $locale = false)
    {
        if (empty($locale))
            $this->_Locale = new Zend_Locale();
        else
            $this->_Locale = $locale;

        if (empty($part))
        {
            if (!is_numeric($date))
                $this->throwException('no timestamp found');

            $this->_Date = new Zend_Date_DateObject($date);
        } else {
            $this->_Date = new Zend_Date_DateObject(0);
            $this->set($date, $part, $this->_Locale, FALSE);
        }
    }


    /**
     * Serialization Interface
     */
    public function serialize()
    {
        return serialize($this);
    }


    /**
     * Returns the unix timestamp
     *
     * @return timestamp
     */
    public function getTimestamp()
    {
        return $this->_Date->getTimestamp();
    }


    /**
     * Sets a new timestamp
     *
     * @param $timestamp timestamp to set
     * @return object
     */
    public function setTimestamp($timestamp)
    {
        $this->_Date->setTimestamp($timestamp);
        return $this;
    }


    /**
     * Adds a timestamp
     *
     * @param $timestamp timestamp to add
     * @return object
     */
    public function addTimestamp($timestamp)
    {
        $stamp = bcadd($this->_Date->getTimestamp(),$timestamp);
        return new Zend_Date($stamp);
    }


    /**
     * Substracts a timestamp
     *
     * @param $timestamp timestamp  to substract
     * @return object
     */
    public function subTimestamp($timestamp)
    {
        return new Zend_Date($this->compareTimestamp($timestamp));
    }


    /**
     * Compares two timestamps, returning the difference as integer
     *
     * @param $timestamp timestamp to compare
     * @return object
     */
    public function compareTimestamp($timestamp)
    {
        return bcsub($this->_Date->getTimestamp(),$timestamp);
    }


    /**
     * Returns a string representation of the object
     * Supported format tokens are
     * G - era, y - year, Y - ISO year, M - month, w - week of year, D - day of year, d - day of month
     * E - day of week, e - number of weekday, h - hour 1-12, H - hour 0-23, m - minute, s - second
     * A - milliseconds of day, z - timezone, Z - timezone offset
     * 
     * Not supported tokens are
     * u - extended year, Q - quarter, q - quarter, L - stand alone month, W - week of month
     * F - day of week of month, g - modified julian, c - stand alone weekday, k - hour 0-11, K - hour 1-24
     * S - fractional second, v - wall zone
     * 
     * @param  $locale  locale  - OPTIONAL locale for parsing input
     * @param  $format  string  - OPTIONAL an rule for formatting the output
     * @param  $gmt     boolean - OPTIONAL, TRUE = actual timezone time, FALSE = UTC time
     * @return string
     */
    public function toString($locale = false, $format, $gmt)
    {
        if ($locale === false)
            $locale = $this->_Locale;

        // get format tokens
        $j = 0;
        $comment = FALSE;
        for($i = 0; $i < strlen($format); ++$i)
        {
            if ($format[$i] == "'")
            {
                if ($comment == FALSE)
                {
                    $comment = TRUE;
                    ++$j;
                    $output[$j] = "'";
                } else if ($format[$i+1] == "'") {
                    $output[$j] .= "'";
                    ++$i;
                } else {
                    $comment = FALSE;
                }
                continue;
            }
            if (($output[$j][0] == $format[$i]) or
                ($comment == TRUE)) { 
                $output[$j] .= $format[$i];
            } else {
                ++$j;
                $output[$j] = $format[$i];
            }
        }

        // fill format tokens with date information
        for($i = 0; $i < count($output); ++$i)
        {
            // fill fixed tokens
            switch ($output[$i])
            {
                // eras
                case 'GGGGG' :
                    $output[i] = substr($this->get(Zend_Date::ERA, $locale, $gmt), 0, 1);
                    break;
                case 'GGGG' :
                    $output[$i] = $this->get(Zend_Date::ERA_NAME, $locale, $gmt);
                    break;
                case 'GGG' :
                case 'GG' :
                case 'G' :
                    $output[$i] = $this->get(Zend_Date::ERA, $locale, $gmt);
                    break; 

                // years
                case 'yy' :
                    $output[$i] = $this->get(Zend_Date::YEAR_SHORT, $locale, $gmt);
                    break; 

                // ISO years
                case 'YY' :
                    $output[$i] = $this->get(Zend_Date::YEAR_SHORT_8601, $locale, $gmt);
                    break;

                // months
                case 'MMMMM' :
                    $output[i] = substr($this->get(Zend_Date::MONTH_NARROW, $locale, $gmt), 0, 1);
                    break;
                case 'MMMM' :
                    $output[$i] = $this->get(Zend_Date::MONTH, $locale, $gmt);
                    break;
                case 'MMM' :
                    $output[$i] = $this->get(Zend_Date::MONTH_NAME, $locale, $gmt);
                    break;
                case 'MM' :
                    $output[$i] = $this->get(Zend_Date::MONTH_SHORT, $locale, $gmt);
                    break;
                case 'M' :
                    $output[$i] = $this->get(Zend_Date::MONTH_DIGIT, $locale, $gmt);
                    break;

                // week
                case 'ww' :
                    $output[$i] = str_pad($this->get(Zend_Date::WEEK, $locale, $gmt),2,'0', STR_PAD_LEFT);
                    break;
                case 'w' :
                    $output[$i] = $this->get(Zend_Date::WEEK, $locale, $gmt);
                    break;

                // monthday
                case 'dd' :
                    $output[$i] = $this->get(Zend_Date::DAY, $locale, $gmt);
                    break;
                case 'd' :
                    $output[$i] = $this->get(Zend_Date::DAY_SHORT, $locale, $gmt);
                    break;

                // yearday
                case 'DDD' :
                    $output[$i] = str_pad($this->get(Zend_Date::DAY_OF_YEAR, $locale, $gmt),3,'0', STR_PAD_LEFT);
                    break;
                case 'DD' :
                    $output[$i] = str_pad($this->get(Zend_Date::DAY_OF_YEAR, $locale, $gmt),2,'0', STR_PAD_LEFT);
                    break;
                case 'D' :
                    $output[$i] = $this->get(Zend_Date::DAY_OF_YEAR, $locale, $gmt);
                    break;

                // weekday
                case 'EEEEE' :
                    $output[$i] = $this->get(Zend_Date::WEEKDAY_NARROW, $locale, $gmt);
                    break;
                case 'EEEE' :
                    $output[$i] = $this->get(Zend_Date::WEEKDAY, $locale, $gmt);
                    break;
                case 'EEE' :
                    $output[$i] = $this->get(Zend_Date::WEEKDAY_SHORT, $locale, $gmt);
                    break;
                case 'EE' :
                    $output[$i] = $this->get(Zend_Date::WEEKDAY_NAME, $locale, $gmt);
                    break;
                case 'E' :
                    $output[$i] = $this->get(Zend_Date::WEEKDAY_NARROW, $locale, $gmt);
                    break;

                // weekday number
                case 'ee' :
                    $output[$i] = str_pad($this->get(Zend_Date::WEEKDAY_8601, $locale, $gmt), 2, '0', STR_PAD_LEFT);
                    break;
                case 'e' :
                    $output[$i] = $this->get(Zend_Date::WEEKDAY_8601, $locale, $gmt);
                    break;

                // period
                case 'a' :
                    $output[$i] = $this->get(Zend_Date::MERIDIEM, $locale, $gmt);
                    break;

                // hour
                case 'hh' :
                    $output[$i] = $this->get(Zend_Date::HOUR_AM, $locale, $gmt);
                    break;
                case 'h' :
                    $output[$i] = $this->get(Zend_Date::HOUR_SHORT_AM, $locale, $gmt);
                    break;
                case 'HH' :
                    $output[$i] = $this->get(Zend_Date::HOUR, $locale, $gmt);
                    break;
                case 'H' :
                    $output[$i] = $this->get(Zend_Date::HOUR_SHORT, $locale, $gmt);
                    break;

                // minute
                case 'mm' :
                    $output[$i] = $this->get(Zend_Date::MINUTE, $locale, $gmt);
                    break;
                case 'm' :
                    $output[$i] = $this->get(Zend_Date::MINUTE_SHORT, $locale, $gmt);
                    break;

                // second
                case 'ss' :
                    $output[$i] = $this->get(Zend_Date::SECOND, $locale, $gmt);
                    break;
                case 's' :
                    $output[$i] = $this->get(Zend_Date::SECOND_SHORT, $locale, $gmt);
                    break;

                // zone
                case 'zzzz' :
                    $output[$i] = $this->get(Zend_Date::TIMEZONE_NAME, $locale, $gmt);
                    break;
                case 'zzz' :
                case 'zz' :
                case 'z' :
                    $output[$i] = $this->get(Zend_Date::TIMEZONE, $locale, $gmt);
                    break;

                // zone offset
                case 'ZZZZ' :
                    $output[$i] = $this->get(Zend_Date::GMT_DIFF_SEP, $locale, $gmt);
                    break;
                case 'ZZZ' :
                case 'ZZ' :
                case 'Z' :
                    $output[$i] = $this->get(Zend_Date::GMT_DIFF, $locale, $gmt);
                    break;
            }
            
            // fill variable tokens
            if (preg_match('/y+/', $output[$i]))
            {
                $length = strlen($output[$i]);
                $output[$i] = $this->get(Zend_Date::YEAR, $locale, $gmt);
                $output[$i] = str_pad($output[$i], $length, '0', STR_PAD_LEFT);
            }

            if (preg_match('/Y+/', $output[$i]))
            {
                $length = strlen($output[$i]);
                $output[$i] = $this->get(Zend_Date::YEAR_8601, $locale, $gmt);
                $output[$i] = str_pad($output[$i], $length, '0', STR_PAD_LEFT);
            }

            if (preg_match('/A+/', $output[$i]))
            {
                $length = strlen($output[$i]);
                $seconds = $this->get(Zend_Date::TIMESTAMP, $locale, $gmt);
                $month = $this->get(Zend_Date::MONTH_DIGIT, $locale, $gmt);
                $day   = $this->get(Zend_Date::DAY_SHORT, $locale, $gmt);
                $year  = $this->get(Zend_Date::YEAR, $locale, $gmt);
                $seconds -= $this->mktime(0, 0, 0, $month, $day, $year, FALSE, $gmt);
                $output[$i] = str_pad($seconds, $length, '0', STR_PAD_LEFT);
            }
        }
        
        return implode('', $output);
    }


    /**
     * Returns a string representation of the object
     * Alias for toString
     *
     * @param  $locale  locale  - OPTIONAL locale for parsing input
     * @param  $format  string  - OPTIONAL an rule for formatting the output
     * @param  $gmt     boolean - OPTIONAL, TRUE = actual timezone time, FALSE = UTC time
     * @return string
     */
    public function __toString($locale = false, $format, $gmt)
    {
        return $this->toString($locale, $format, $gmt);
    }


    /**
     * Returns a integer representation of the object
     * Returns false when the given part is no value f.e. Month-Name
     *
     * @param  $part   part of date to return as integer
     * @param  $gmt    OPTIONAL, TRUE = actual timezone time, FALSE = UTC time
     * @return integer or false
     */
    public function toValue($part, $gmt=false)
    {
        if (empty($part))
            $part = Zend_Date::TIMESTAMP;

        $result = $this->get($part, false, $gmt);
        if (is_numeric($result))
          return intval("$result");
        else
          return false;
    }


    /**
     * Returns a timestamp or a part of a date
     *
     * @param  $part   - datepart, if empty the timestamp will be returned
     * @param  $locale - OPTIONAL, locale for output
     * @param  $gmt    - OPTIONAL, TRUE = actual timezone time, FALSE = UTC time
     * @return mixed   timestamp or datepart as string or integer
     */
    public function get($part, $locale = false, $gmt = false)
    {
        if ($locale === false)
            $locale = $this->_Locale;

        switch($part)
        {
            // day formats
            case Zend_Date::DAY :
                return $this->_Date->date('d',$this->_Date->getTimestamp(), $gmt);
                break;
            case Zend_Date::WEEKDAY_SHORT :
                $weekday = strtolower($this->_Date->date('D',$this->_Date->getTimestamp(), $gmt));
                $day = Zend_Locale_Data::getContent($locale, 'day', array('gregorian', 'abbreviated', $weekday));
                return substr($day[$weekday],0,3);
                break;
            case Zend_Date::DAY_SHORT :
                return $this->_Date->date('j',$this->_Date->getTimestamp(), $gmt);
                break;
            case Zend_Date::WEEKDAY :
                $weekday = strtolower($this->_Date->date('D',$this->_Date->getTimestamp(), $gmt));
                $day = Zend_Locale_Data::getContent($locale, 'day', array('gregorian', 'wide', $weekday));
                return $day[$weekday];
                break;
            case Zend_Date::WEEKDAY_8601 :
                return $this->_Date->date('N',$this->_Date->getTimestamp(), $gmt);
                break;
            case Zend_Date::DAY_SUFFIX :
                return $this->_Date->date('S',$this->_Date->getTimestamp(), $gmt);
                break;
            case Zend_Date::WEEKDAY_DIGIT :
                return $this->_Date->date('w',$this->_Date->getTimestamp(), $gmt);
                break;
            case Zend_Date::DAY_OF_YEAR :
                return $this->_Date->date('z',$this->_Date->getTimestamp(), $gmt);
                break;


            case Zend_Date::WEEKDAY_NARROW :
                $weekday = strtolower($this->_Date->date('D',$this->_Date->getTimestamp(), $gmt));
                $day = Zend_Locale_Data::getContent($locale, 'day', array('gregorian', 'abbreviated', $weekday));
                return substr($day[$weekday],0,1);
                break;
            case Zend_Date::WEEKDAY_NAME :
                $weekday = strtolower($this->_Date->date('D',$this->_Date->getTimestamp(), $gmt));
                $day = Zend_Locale_Data::getContent($locale, 'day', array('gregorian', 'abbreviated', $weekday));
                return $day[$weekday];
                break;


            // week formats
            case Zend_Date::WEEK :
                return $this->_Date->date('W',$this->_Date->getTimestamp(), $gmt);
                break;


            // month formats
            case Zend_Date::MONTH :
                $month = $this->_Date->date('n',$this->_Date->getTimestamp(), $gmt);
                $mon = Zend_Locale_Data::getContent($locale, 'month', array('gregorian', 'format', 'wide', $month));
                return $mon[$month];
                break;
            case Zend_Date::MONTH_SHORT :
                return $this->_Date->date('m',$this->_Date->getTimestamp(), $gmt);
                break;
            case Zend_Date::MONTH_NAME :
                $month = $this->_Date->date('n',$this->_Date->getTimestamp(), $gmt);
                $mon = Zend_Locale_Data::getContent($locale, 'month', array('gregorian', 'format', 'abbreviated', $month));
                return $mon[$month];
                break;
            case Zend_Date::MONTH_DIGIT :
                return $this->_Date->date('n',$this->_Date->getTimestamp(), $gmt);
                break;
            case Zend_Date::MONTH_DAYS :
                return $this->_Date->date('t',$this->_Date->getTimestamp(), $gmt);
                break;


            case Zend_Date::MONTH_NARROW :
                $month = $this->_Date->date('n',$this->_Date->getTimestamp(), $gmt);
                $mon = Zend_Locale_Data::getContent($locale, 'month', array('gregorian', 'format', 'abbreviated', $month));
                return substr($mon[$month],0,1);
                break;


            // year formats
            case Zend_Date::LEAPYEAR :
                return $this->_Date->date('L',$this->_Date->getTimestamp(), $gmt);
                break;
            case Zend_Date::YEAR_8601 :
                return $this->_Date->date('o',$this->_Date->getTimestamp(), $gmt);
                break;
            case Zend_Date::YEAR :
                return $this->_Date->date('Y',$this->_Date->getTimestamp(), $gmt);
                break;
            case Zend_Date::YEAR_SHORT :
                return $this->_Date->date('y',$this->_Date->getTimestamp(), $gmt);
                break;


            case Zend_Date::YEAR_SHORT_8601 :
                $year = $this->_Date->date('o',$this->_Date->getTimestamp(), $gmt);
                return substr($year, -2);
                break;


            // time formats
            case Zend_Date::MERIDIEM :
                $am = $this->_Date->date('a',$this->_Date->getTimestamp(), $gmt);
                $amlocal = Zend_Locale_Data::getContent($locale, 'daytime', 'gregorian');
                return $amlocal[$am];
                break;
            case Zend_Date::SWATCH :
                return $this->_Date->date('B',$this->_Date->getTimestamp(), $gmt);
                break;
            case Zend_Date::HOUR_SHORT_AM :
                return $this->_Date->date('g',$this->_Date->getTimestamp(), $gmt);
                break;
            case Zend_Date::HOUR_SHORT :
                return $this->_Date->date('G',$this->_Date->getTimestamp(), $gmt);
                break;
            case Zend_Date::HOUR_AM :
                return $this->_Date->date('h',$this->_Date->getTimestamp(), $gmt);
                break;
            case Zend_Date::HOUR :
                return $this->_Date->date('H',$this->_Date->getTimestamp(), $gmt);
                break;
            case Zend_Date::MINUTE :
                return $this->_Date->date('i',$this->_Date->getTimestamp(), $gmt);
                break;
            case Zend_Date::SECOND :
                return $this->_Date->date('s',$this->_Date->getTimestamp(), $gmt);
                break;


            case Zend_Date::MINUTE_SHORT :
                return (int) $this->_Date->date('i',$this->_Date->getTimestamp(), $gmt);
                break;
            case Zend_Date::SECOND_SHORT :
                return (int) $this->_Date->date('s',$this->_Date->getTimestamp(), $gmt);
                break;


            // timezone formats
            case Zend_Date::TIMEZONE_NAME :
                // TODO: should be locale aware, but CLDR does not provide the proper information 'til now
                return $this->_Date->date('e',$this->_Date->getTimestamp(), $gmt);
                break;
            case Zend_Date::DAYLIGHT :
                return $this->_Date->date('I',$this->_Date->getTimestamp(), $gmt);
                break;
            case Zend_Date::GMT_DIFF :
                return $this->_Date->date('O',$this->_Date->getTimestamp(), $gmt);
                break;
            case Zend_Date::GMT_DIFF_SEP :
                return $this->_Date->date('P',$this->_Date->getTimestamp(), $gmt);
                break;
            case Zend_Date::TIMEZONE :
                // TODO: should be locale aware, but CLDR does not provide the proper information 'til now
                return $this->_Date->date('T',$this->_Date->getTimestamp(), $gmt);
                break;
            case Zend_Date::TIMEZONE_SECS :
                return $this->_Date->date('Z',$this->_Date->getTimestamp(), $gmt);
                break;


            // date strings
            case Zend_Date::ISO_8601 :
                return $this->_Date->date('c',$this->_Date->getTimestamp(), $gmt);
                break;
            case Zend_Date::RFC_2822 :
                return $this->_Date->date('r',$this->_Date->getTimestamp(), $gmt);
                break;
            case Zend_Date::TIMESTAMP :
                return $this->_Date->getTimestamp();
                break;


            // additional formats
            case Zend_Date::ERA :
                $year = $this->_Date->date('Y',$this->_Date->getTimestamp(), $gmt);
                if ($year < 0)
                {
                    $era = Zend_Locale_Data::getContent($locale, 'erashort', array('gregorian','0'));
                    return $era['0'];
                }
                $era = Zend_Locale_Data::getContent($locale, 'erashort', array('gregorian','1'));
                return $era['1'];
                break;
            case Zend_Date::ERA_NAME :
                $year = $this->_Date->date('Y',$this->_Date->getTimestamp(), $gmt);
                if ($year < 0)
                {
                    $era = Zend_Locale_Data::getContent($locale, 'era', array('gregorian','0'));
                    return $era['0'];
                }
                $era = Zend_Locale_Data::getContent($locale, 'era', array('gregorian','1'));
                return $era['1'];
                break;
            case Zend_Date::DATES :
                $default = Zend_Locale_Data::getContent($locale, 'defdateformat', 'gregorian');
                $date = Zend_Locale_Data::getContent($locale, 'dateformat', array('gregorian',$default['default']));
                return $this->toString($date[$default['default']]);
                break;
            case Zend_Date::DATE_FULL :
                $date = Zend_Locale_Data::getContent($locale, 'dateformat', array('gregorian','full'));
                return $this->toString($date['full']);
                break;
            case Zend_Date::DATE_LONG :
                $date = Zend_Locale_Data::getContent($locale, 'dateformat', array('gregorian','long'));
                return $this->toString($date['long']);
                break;
            case Zend_Date::DATE_MEDIUM :
                $date = Zend_Locale_Data::getContent($locale, 'dateformat', array('gregorian','medium'));
                return $this->toString($date['medium']);
                break;
            case Zend_Date::DATE_SHORT :
                $date = Zend_Locale_Data::getContent($locale, 'dateformat', array('gregorian','short'));
                return $this->toString($date['short']);
                break;
            case Zend_Date::TIMES :
                $default = Zend_Locale_Data::getContent($locale, 'deftimeformat', 'gregorian');
                $time = Zend_Locale_Data::getContent($locale, 'timeformat', array('gregorian',$default['default']));
                return $this->toString($time[$default['default']]);
                break;
            case Zend_Date::TIME_FULL :
                $time = Zend_Locale_Data::getContent($locale, 'timeformat', array('gregorian','full'));
                return $this->toString($time['full']);
                break;
            case Zend_Date::TIME_LONG :
                $time = Zend_Locale_Data::getContent($locale, 'timeformat', array('gregorian','long'));
                return $this->toString($time['long']);
                break;
            case Zend_Date::TIME_MEDIUM :
                $time = Zend_Locale_Data::getContent($locale, 'timeformat', array('gregorian','medium'));
                return $this->toString($time['medium']);
                break;
            case Zend_Date::TIME_SHORT :
                $time = Zend_Locale_Data::getContent($locale, 'timeformat', array('gregorian','short'));
                return $this->toString($time['short']);
                break;
            case Zend_Date::ATOM :
                return $this->_Date->date('Y\-m\-d\TH\:i\:sP',$this->_Date->getTimestamp(), $gmt);
                break;
            case Zend_Date::COOKIE :
                return $this->_Date->date('l\, d\-M\-y H\:i\:s e',$this->_Date->getTimestamp(), $gmt);
                break;
            case Zend_Date::RFC_822 :
                return $this->_Date->date('D\, d M y H\:m\:s O',$this->_Date->getTimestamp(), $gmt);
                break;
            case Zend_Date::RFC_850 :
                return $this->_Date->date('l\, d\-M\-y H\:m\:s e',$this->_Date->getTimestamp(), $gmt);
                break;
            case Zend_Date::RFC_1036 :
                return $this->_Date->date('D\, d M y H\:m\:s O',$this->_Date->getTimestamp(), $gmt);
                break;
            case Zend_Date::RFC_1123 :
                return $this->_Date->date('D\, d M Y H\:m\:s O',$this->_Date->getTimestamp(), $gmt);
                break;
            case Zend_Date::RSS :
                return $this->_Date->date('D\, d M Y H\:m\:s O',$this->_Date->getTimestamp(), $gmt);
                break;
            case Zend_Date::W3C :
                return $this->_Date->date('Y\-m\-d\TH\:m\:sP',$this->_Date->getTimestamp(), $gmt);
                break;


            default :
                return $this->_Date->date($part,$this->_Date->getTimestamp(), $gmt);
                break;
        }
    }


    /**
     * Sets the given date as new date
     *
     * @param $date string   - date which shall be our new date object
     * @param  $part         - datepart, if empty the timestamp will be returned
     * @param  $locale       - OPTIONAL, locale for output
     * @param  $gmt          - OPTIONAL, TRUE = actual timezone time, FALSE = UTC time
     * @return timestamp
     */
    public function set($date, $part, $locale = false, $gmt = false)
    {
        if ($locale === false)
            $locale = $this->_Locale;

        // create date parts
        $year   = $this->get(Zend_Date::YEAR);
        $month  = $this->get(Zend_Date::MONTH_DIGIT);
        $day    = $this->get(Zend_Date::DAY_SHORT);
        $hour   = $this->get(Zend_Date::HOUR_SHORT);
        $minute = $this->get(Zend_Date::MINUTE_SHORT);
        $second = $this->get(Zend_Date::SECOND_SHORT);

        // if object extract value
        if (is_object($date))
            $date = $date->get($part, $locale, $gmt);

        // $date as object, part of foreign date as own date
        switch($part)
        {
            // day formats
            case Zend_Date::DAY :
                $this->setTimestamp($this->_Date->mktime($hour, $minute, $second, $month, intval($date), $year, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::WEEKDAY_SHORT :
                $daylist = Zend_Locale_Data::getContent($locale, 'daylist', array('gregorian', 'wide'));
                $weekday = (int) $this->get(Zend_Date::WEEKDAY_DIGIT, $locale, $gmt);
                $cnt = 0;
                foreach ($daylist as $key => $value)
                {
                    if (strtoupper(substr($value,0,3)) == strtoupper($date))
                    {
                        $found = $cnt;
                        break;
                    }
                    ++$cnt;
                }
                // Weekday found
                if ($cnt < 7)
                {
                    $difference = $found - $weekday;
                    if ($difference < 0)
                       $difference += 7;
                    $this->setTimestamp($this->_Date->mktime($hour, $minute, $second, $month, $day + $difference, $year, -1, $gmt));
                    return $this->getTimestamp();
                }
                else
                    // Weekday not found
                    return false; 
                break;
            case Zend_Date::DAY_SHORT :
                $this->setTimestamp($this->_Date->mktime($hour, $minute, $second, $month, intval($date), $year, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::WEEKDAY :
                $daylist = Zend_Locale_Data::getContent($locale, 'daylist', array('gregorian', 'wide'));
                $weekday = (int) $this->get(Zend_Date::WEEKDAY_DIGIT, $locale, $gmt);
                $cnt = 0;
                foreach ($daylist as $key => $value)
                {
                    if (strtoupper($value) == strtoupper($date))
                    {
                        $found = $cnt;
                        break;
                    }
                    ++$cnt;
                }
                // Weekday found
                if ($cnt < 7)
                {
                    $difference = $found - $weekday;
                    if ($difference < 0)
                       $difference += 7;
                    $this->setTimestamp($this->_Date->mktime($hour, $minute, $second, $month, $day + $difference, $year, -1, $gmt));
                    return $this->getTimestamp();
                }
                else
                    // Weekday not found
                    return false; 
                break;
            case Zend_Date::WEEKDAY_8601 :
                $weekday = (int) $this->get(Zend_Date::WEEKDAY_DIGIT, $locale, $gmt);
                if ((intval($date) > 0) and (intval($date) < 8))
                {
                    $difference = $date - $weekday;
                    if ($difference < 0)
                       $difference += 7;
                    $this->setTimestamp($this->_Date->mktime($hour, $minute, $second, $month, $day + $difference, $year, -1, $gmt));
                    return $this->getTimestamp();
                }
                else
                    // Weekday not found
                    return false; 
                break;
            case Zend_Date::DAY_SUFFIX :
                return false;
                break;
            case Zend_Date::WEEKDAY_DIGIT :
                $weekday = (int) $this->get(Zend_Date::WEEKDAY_DIGIT, $locale, $gmt);
                if ((intval($date) > 0) and (intval($date) < 8))
                {
                    $difference = ($date + 1) - $weekday;
                    if ($difference < 0)
                       $difference += 7;
                    $this->setTimestamp($this->_Date->mktime($hour, $minute, $second, $month, $day + $difference, $year, -1, $gmt));
                    return $this->getTimestamp();
                }
                else
                    // Weekday not found
                    return false; 
                break;
            case Zend_Date::DAY_OF_YEAR :
                $this->setTimestamp($this->_Date->mktime($hour, $minute, $second, 1, intval($date), $year, -1, $gmt));
                return $this->getTimestamp();
                break;


            case Zend_Date::WEEKDAY_NARROW :
                $daylist = Zend_Locale_Data::getContent($locale, 'daylist', array('gregorian', 'abbreviated'));
                $weekday = (int) $this->get(Zend_Date::WEEKDAY_DIGIT, $locale, $gmt);
                $cnt = 0;
                foreach ($daylist as $key => $value)
                {
                    if (strtoupper(substr($value,0,1)) == strtoupper($date))
                    {
                        $found = $cnt;
                        break;
                    }
                    ++$cnt;
                }
                // Weekday found
                if ($cnt < 7)
                {
                    $difference = $found - $weekday;
                    if ($difference < 0)
                       $difference += 7;
                    $this->setTimestamp($this->_Date->mktime($hour, $minute, $second, $month, $day + $difference, $year, -1, $gmt));
                    return $this->getTimestamp();
                }
                else
                    // Weekday not found
                    return false; 
                break;
            case Zend_Date::WEEKDAY_NAME :
                $daylist = Zend_Locale_Data::getContent($locale, 'daylist', array('gregorian', 'abbreviated'));
                $weekday = (int) $this->get(Zend_Date::WEEKDAY_DIGIT, $locale, $gmt);
                $cnt = 0;
                foreach ($daylist as $key => $value)
                {
                    if (strtoupper($value) == strtoupper($date))
                    {
                        $found = $cnt;
                        break;
                    }
                    ++$cnt;
                }
                // Weekday found
                if ($cnt < 7)
                {
                    $difference = $found - $weekday;
                    if ($difference < 0)
                       $difference += 7;
                    $this->setTimestamp($this->_Date->mktime($hour, $minute, $second, $month, $day + $difference, $year, -1, $gmt));
                    return $this->getTimestamp();
                }
                else
                    // Weekday not found
                    return false; 
                break;


            // week formats
            case Zend_Date::WEEK :
                $week = (int) $this->get(Zend_Date::WEEK, $locale, $gmt);
                $day = ((intval($date) - $week) * 7) + $day;
                $this->setTimestamp($this->_Date->mktime($hour, $minute, $second, $month, $day, $year, -1, $gmt));
                return $this->getTimestamp();
                break;


            // month formats
            case Zend_Date::MONTH :
                $monthlist = Zend_Locale_Data::getContent($locale, 'monthlist', array('gregorian', 'wide'));
                $monthnr = (int) ($this->get(Zend_Date::MONTH_DIGIT, $locale, $gmt)) - 1;
                $cnt = 0;
                foreach ($monthlist as $key => $value)
                {
                    if (strtoupper($value) == strtoupper($date))
                    {
                        $found = $cnt;
                        break;
                    }
                    ++$cnt;
                }
                // Monthname found
                if ($cnt < 12)
                {
                    $difference = $found - $monthnr;
                    if ($difference < 0)
                       $difference += 12;
                    $this->setTimestamp($this->_Date->mktime($hour, $minute, $second, $month + $difference, $day, $year, -1, $gmt));
                    return $this->getTimestamp();
                }
                else
                    // Monthname not found
                    return false; 
                break;
            case Zend_Date::MONTH_SHORT :
                $this->setTimestamp($this->_Date->mktime($hour, $minute, $second, intval($date), $day, $year, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::MONTH_NAME :
                $monthlist = Zend_Locale_Data::getContent($locale, 'monthlist', array('gregorian', 'abbreviated'));
                $monthnr = (int) ($this->get(Zend_Date::MONTH_DIGIT, $locale, $gmt)) - 1;
                $cnt = 0;
                foreach ($monthlist as $key => $value)
                {
                    if (strtoupper($value) == strtoupper($date))
                    {
                        $found = $cnt;
                        break;
                    }
                    ++$cnt;
                }
                // Monthname found
                if ($cnt < 12)
                {
                    $difference = $found - $monthnr;
                    if ($difference < 0)
                       $difference += 12;
                    $this->setTimestamp($this->_Date->mktime($hour, $minute, $second, $month + $difference, $day, $year, -1, $gmt));
                    return $this->getTimestamp();
                }
                else
                    // Monthname not found
                    return false; 
                break;
            case Zend_Date::MONTH_DIGIT :
                $this->setTimestamp($this->_Date->mktime($hour, $minute, $second, intval($date), $day, $year, -1, $gmt));
                return $this;
                break;
            case Zend_Date::MONTH_DAYS :
                return false;
                break;


            case Zend_Date::MONTH_NARROW :
                $monthlist = Zend_Locale_Data::getContent($locale, 'monthlist', array('gregorian', 'abbreviated'));
                $monthnr = (int) ($this->get(Zend_Date::MONTH_DIGIT, $locale, $gmt)) - 1;
                $cnt = 0;
                foreach ($monthlist as $key => $value)
                {
                    if (strtoupper(substr($value,0,1)) == strtoupper(substr($date,0,1)))
                    {
                        $found = $cnt;
                        break;
                    }
                    ++$cnt;
                }
                // Monthname found
                if ($cnt < 12)
                {
                    $difference = $found - $monthnr;
                    if ($difference < 0)
                       $difference += 12;
                    $this->setTimestamp($this->_Date->mktime($hour, $minute, $second, $month + $difference, $day, $year, -1, $gmt));
                    return $this->getTimestamp();
                }
                else
                    // Monthname not found
                    return false; 
                break;


            // year formats
            case Zend_Date::LEAPYEAR :
                return false;
                break;
            case Zend_Date::YEAR_8601 :
                $this->setTimestamp($this->_Date->mktime($hour, $minute, $second, $month, $day, intval($date), -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::YEAR :
                $this->setTimestamp($this->_Date->mktime($hour, $minute, $second, $month, $day, intval($date), -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::YEAR_SHORT :
                $year = intval($date);
                if (($year > 0) and ($year < 100))
                {
                    $year += 1900;
                    if ($year < 70)
                        $year += 100;
                }
                $this->setTimestamp($this->_Date->mktime($hour, $minute, $second, $month, $day, $year, -1, $gmt));
                return $this->getTimestamp();
                break;


            case Zend_Date::YEAR_SHORT_8601 :
                $this->setTimestamp($this->_Date->mktime($hour, $minute, $second, $month, $day, intval($date), -1, $gmt));
                return $this->getTimestamp();
                break;


            // time formats
            case Zend_Date::MERIDIEM :
                $meridiemlist = Zend_Locale_Data::getContent($locale, 'daytime', 'gregorian');
                $meridiem = strtoupper($this->get(Zend_Date::MERIDIEM, $locale, $gmt));

                if (($meridiem == strtoupper($meridiemlist['am'])) && (strtoupper($date) == strtoupper($meridiemlist['pm'])))
                    $hour += 12;
                else if (($meridiem == strtoupper($meridiemlist['pm'])) && (strtoupper($date) == strtoupper($meridiemlist['am'])))
                    $hour -= 12;
                    
                $this->setTimestamp($this->_Date->mktime($hour, $minute, $second, $month, $day, $year, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::SWATCH :
                $rest = intval($date);
                $hour = floor($rest / 3600);
                $rest = $rest - ($hour * 3600);
                $minute = floor($rest / 60);
                $second = $rest - ($minute * 60); 
                $this->setTimestamp($this->_Date->mktime($hour, $minute, $second, $month, $day, $year, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::HOUR_SHORT_AM :
                $this->setTimestamp($this->_Date->mktime(intval($date), $minute, $second, $month, $day, $year, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::HOUR_SHORT :
                $this->setTimestamp($this->_Date->mktime(intval($date), $minute, $second, $month, $day, $year, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::HOUR_AM :
                $this->setTimestamp($this->_Date->mktime(intval($date), $minute, $second, $month, $day, $year, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::HOUR :
                $this->setTimestamp($this->_Date->mktime(intval($date), $minute, $second, $month, $day, $year, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::MINUTE :
                $this->setTimestamp($this->_Date->mktime($hour, intval($date), $second, $month, $day, $year, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::SECOND :
                $this->setTimestamp($this->_Date->mktime($hour, $minute, intval($date), $month, $day, $year, -1, $gmt));
                return $this->getTimestamp();
                break;


            case Zend_Date::MINUTE_SHORT :
                $this->setTimestamp($this->_Date->mktime($hour, intval($date), $second, $month, $day, $year, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::SECOND_SHORT :
                $this->setTimestamp($this->_Date->mktime($hour, $minute, intval($date), $month, $day, $year, -1, $gmt));
                return $this->getTimestamp();
                break;


            // timezone formats
            case Zend_Date::TIMEZONE_NAME :
                return false;
                break;
            case Zend_Date::DAYLIGHT :
                return false;
                break;
            case Zend_Date::GMT_DIFF :
                return false;
                break;
            case Zend_Date::GMT_DIFF_SEP :
                return false;
                break;
            case Zend_Date::TIMEZONE :
                return false;
                break;
            case Zend_Date::TIMEZONE_SECS :
                return false;
                break;


            // date strings
            case Zend_Date::ISO_8601 :
                $result = preg_match('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+\d{4}/', $date, $match);
                if (!$result)
                    return false;

                $year   = substr($match[0], 0, 4);
                $month  = substr($match[0], 5, 2);
                $day    = substr($match[0], 8, 2);
                $hour   = substr($match[0], 11, 2);
                $minute = substr($match[0], 14, 2);
                $second = substr($match[0], 17, 2);

                $this->setTimestamp($this->_Date->mktime($hour, $minute, $second, $month, $day, $year, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::RFC_2822 :
                $result = preg_match('/\w{3},\s\d{2}\s\w{3}\s\d{4}\s\d{2}:\d{2}:\d{2}\s\+\d{4}/', $date, $match);
                if (!$result)
                    return false;

                $day    = substr($match[0], 5, 2);
                $month  = $this->getDigitFromName(substr($match[0], 8, 3));
                $year   = substr($match[0], 12, 4);
                $hour   = substr($match[0], 17, 2);
                $minute = substr($match[0], 20, 2);
                $second = substr($match[0], 23, 2);

                $this->setTimestamp($this->_Date->mktime($hour, $minute, $second, $month, $day, $year, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::TIMESTAMP :
                if (is_numeric($date))
                {
                    $this->setTimestamp($date);
                    return $this->getTimestamp();
                } else
                    return false;
                break;


            // additional formats
            case Zend_Date::ERA :
                return false;
                break;
            case Zend_Date::ERA_NAME :
                return false;
                break;
            case Zend_Date::DATES :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::DATE_FULL :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::DATE_LONG :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::DATE_MEDIUM :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::DATE_SHORT :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::TIMES :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::TIME_FULL :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::TIME_LONG :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::TIME_MEDIUM :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::TIME_SHORT :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::ATOM :
                $result = preg_match('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+\d{2}:\d{2}/', $date, $match);
                if (!$result)
                    return false;

                $year   = substr($match[0], 0, 4);
                $month  = substr($match[0], 5, 2);
                $day    = substr($match[0], 8, 2);
                $hour   = substr($match[0], 11, 2);
                $minute = substr($match[0], 14, 2);
                $second = substr($match[0], 17, 2);

                $this->setTimestamp($this->_Date->mktime($hour, $minute, $second, $month, $day, $year, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::COOKIE :
                $result = preg_match('/\w{6,9},\s\d{2}-\w{3}-\d{2}\s\d{2}:\d{2}:\d{2}\s\w{3}/', $date, $match);
                if (!$result)
                    return false;
                $match[0] = substr($match[0], strpos(' '+1));
                
                $day    = substr($match[0], 0, 2);
                $month  = $this->getDigitFromName(substr($match[0], 3, 3));
                $year   = substr($match[0], 7, 4);
                $year  += 2000;
                $hour   = substr($match[0], 10, 2);
                $minute = substr($match[0], 13, 2);
                $second = substr($match[0], 16, 2);

                $this->setTimestamp($this->_Date->mktime($hour, $minute, $second, $month, $day, $year, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::RFC_822 :
                $result = preg_match('/\w{3},\s\d{2}\s\w{3}\s\d{2}\s\d{2}:\d{2}:\d{2}\s\+\d{4}/', $date, $match);
                if (!$result)
                    return false;
                
                $day    = substr($match[0], 5, 2);
                $month  = $this->getDigitFromName(substr($match[0], 8, 3));
                $year   = substr($match[0], 12, 4);
                $year  += 2000;
                $hour   = substr($match[0], 15, 2);
                $minute = substr($match[0], 18, 2);
                $second = substr($match[0], 21, 2);

                $this->setTimestamp($this->_Date->mktime($hour, $minute, $second, $month, $day, $year, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::RFC_850 :
                $result = preg_match('/\w{6,9},\s\d{2}-\w{3}-\d{2}\s\d{2}:\d{2}:\d{2}\s\w{3}/', $date, $match);
                if (!$result)
                    return false;
                $match[0] = substr($match[0], strpos(' '+1));
                
                $day    = substr($match[0], 0, 2);
                $month  = $this->getDigitFromName(substr($match[0], 3, 3));
                $year   = substr($match[0], 7, 4);
                $year  += 2000;
                $hour   = substr($match[0], 10, 2);
                $minute = substr($match[0], 13, 2);
                $second = substr($match[0], 16, 2);

                $this->setTimestamp($this->_Date->mktime($hour, $minute, $second, $month, $day, $year, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::RFC_1036 :
                $result = preg_match('/\w{3},\s\d{2}\s\w{3}\s\d{2}\s\d{2}:\d{2}:\d{2}\s\+\d{4}/', $date, $match);
                if (!$result)
                    return false;
                
                $day    = substr($match[0], 5, 2);
                $month  = $this->getDigitFromName(substr($match[0], 8, 3));
                $year   = substr($match[0], 12, 4);
                $year  += 2000;
                $hour   = substr($match[0], 15, 2);
                $minute = substr($match[0], 18, 2);
                $second = substr($match[0], 21, 2);

                $this->setTimestamp($this->_Date->mktime($hour, $minute, $second, $month, $day, $year, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::RFC_1123 :
                $result = preg_match('/\w{3},\s\d{2}\s\w{3}\s\d{4}\s\d{2}:\d{2}:\d{2}\s\+\d{4}/', $date, $match);
                if (!$result)
                    return false;

                $day    = substr($match[0], 5, 2);
                $month  = $this->getDigitFromName(substr($match[0], 8, 3));
                $year   = substr($match[0], 12, 4);
                $hour   = substr($match[0], 17, 2);
                $minute = substr($match[0], 20, 2);
                $second = substr($match[0], 23, 2);

                $this->setTimestamp($this->_Date->mktime($hour, $minute, $second, $month, $day, $year, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::RSS :
                $result = preg_match('/\w{3},\s\d{2}\s\w{3}\s\d{4}\s\d{2}:\d{2}:\d{2}\s\+\d{4}/', $date, $match);
                if (!$result)
                    return false;

                $day    = substr($match[0], 5, 2);
                $month  = $this->getDigitFromName(substr($match[0], 8, 3));
                $year   = substr($match[0], 12, 4);
                $hour   = substr($match[0], 17, 2);
                $minute = substr($match[0], 20, 2);
                $second = substr($match[0], 23, 2);

                $this->setTimestamp($this->_Date->mktime($hour, $minute, $second, $month, $day, $year, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::W3C :
                $result = preg_match('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+\d{2}:\d{2}/', $date, $match);
                if (!$result)
                    return false;

                $year   = substr($match[0], 0, 4);
                $month  = substr($match[0], 5, 2);
                $day    = substr($match[0], 8, 2);
                $hour   = substr($match[0], 11, 2);
                $minute = substr($match[0], 14, 2);
                $second = substr($match[0], 17, 2);

                $this->setTimestamp($this->_Date->mktime($hour, $minute, $second, $month, $day, $year, -1, $gmt));
                return $this->getTimestamp();
                break;
            default :
                break;
        }
    }


    /**
     * Return digit from standard names (english)
     * Faster implementation than locale aware searching
     */
    private function getDigitFromName($name)
    {
        switch($name)
        {
            case "Jan":
                return 1;
            case "Feb":
                return 2;
            case "Mar":
                return 3;
            case "Apr":
                return 4;
            case "May":
                return 5;
            case "Jun":
                return 6;
            case "Jul":
                return 7;
            case "Aug":
                return 8;
            case "Sep":
                return 9;
            case "Oct":
                return 10;
            case "Nov":
                return 11;
            case "Dec":
                return 12;
        }
    }


    /**
     * Adds a date to another date. Could add f.e. minutes, hours, days, months to a date object
     *
     * @param  $date string  - date which shall be our new date object
     * @param  $part         - datepart, if empty the timestamp will be returned
     * @param  $locale       - OPTIONAL, locale for output
     * @param  $gmt          - OPTIONAL, TRUE = actual timezone time, FALSE = UTC time
     * @return timestamp
     */
    public function add($date, $part, $locale = false, $gmt = false)
    {
        if ($locale === false)
            $locale = $this->_Locale;

        // if object extract value
        if (is_object($date))
            $date = $date->get($part, $locale, $gmt);

        // $date as object, part of foreign date as own date
        switch($part)
        {
            // day formats
            case Zend_Date::DAY :
                $this->addTimestamp($this->_Date->mktime(0, 0, 0, 1, intval($date), 0, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::WEEKDAY_SHORT :
                $daylist = Zend_Locale_Data::getContent($locale, 'daylist', array('gregorian', 'wide'));
                $weekday = (int) $this->get(Zend_Date::WEEKDAY_DIGIT, $locale, $gmt);
                $cnt = 0;
                foreach ($daylist as $key => $value)
                {
                    if (strtoupper(substr($value,0,3)) == strtoupper($date))
                    {
                        $found = $cnt + 1;
                        break;
                    }
                    ++$cnt;
                }
                // Weekday found
                if ($cnt < 7)
                {
                    $this->addTimestamp($this->_Date->mktime(0, 0, 0, 1, $found, 0, -1, $gmt));
                    return $this->getTimestamp();
                }
                else
                    // Weekday not found
                    return false; 
                break;
            case Zend_Date::DAY_SHORT :
                $this->addTimestamp($this->_Date->mktime(0, 0, 0, 1, intval($date), 0, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::WEEKDAY :
                $daylist = Zend_Locale_Data::getContent($locale, 'daylist', array('gregorian', 'wide'));
                $weekday = (int) $this->get(Zend_Date::WEEKDAY_DIGIT, $locale, $gmt);
                $cnt = 0;
                foreach ($daylist as $key => $value)
                {
                    if (strtoupper($value) == strtoupper($date))
                    {
                        $found = $cnt + 1;
                        break;
                    }
                    ++$cnt;
                }
                // Weekday found
                if ($cnt < 7)
                {
                    $this->addTimestamp($this->_Date->mktime(0, 0, 0, 1, $found, 0, -1, $gmt));
                    return $this->getTimestamp();
                }
                else
                    // Weekday not found
                    return false; 
                break;
            case Zend_Date::WEEKDAY_8601 :
                $weekday = (int) $this->get(Zend_Date::WEEKDAY_DIGIT, $locale, $gmt);
                if ((intval($date) > 0) and (intval($date) < 8))
                {
                    $this->addTimestamp($this->_Date->mktime(0, 0, 0, 1, $date, 0, -1, $gmt));
                    return $this->getTimestamp();
                }
                else
                    // Weekday not found
                    return false; 
                break;
            case Zend_Date::DAY_SUFFIX :
                return false;
                break;
            case Zend_Date::WEEKDAY_DIGIT :
                $weekday = (int) $this->get(Zend_Date::WEEKDAY_DIGIT, $locale, $gmt);
                if ((intval($date) > 0) and (intval($date) < 8))
                {
                    $this->addTimestamp($this->_Date->mktime(0, 0, 0, 1, $date, 0, -1, $gmt));
                    return $this->getTimestamp();
                }
                else
                    // Weekday not found
                    return false; 
                break;
            case Zend_Date::DAY_OF_YEAR :
                $this->addTimestamp($this->_Date->mktime(0, 0, 0, 1, $date, 0, -1, $gmt));
                return $this->getTimestamp();
                break;


            case Zend_Date::WEEKDAY_NARROW :
                $daylist = Zend_Locale_Data::getContent($locale, 'daylist', array('gregorian', 'abbreviated'));
                $weekday = (int) $this->get(Zend_Date::WEEKDAY_DIGIT, $locale, $gmt);
                $cnt = 0;
                foreach ($daylist as $key => $value)
                {
                    if (strtoupper(substr($value,0,1)) == strtoupper($date))
                    {
                        $found = $cnt + 1;
                        break;
                    }
                    ++$cnt;
                }
                // Weekday found
                if ($cnt < 7)
                {
                    $this->addTimestamp($this->_Date->mktime(0, 0, 0, 1, $found, 0, -1, $gmt));
                    return $this->getTimestamp();
                }
                else
                    // Weekday not found
                    return false; 
                break;
            case Zend_Date::WEEKDAY_NAME :
                $daylist = Zend_Locale_Data::getContent($locale, 'daylist', array('gregorian', 'abbreviated'));
                $weekday = (int) $this->get(Zend_Date::WEEKDAY_DIGIT, $locale, $gmt);
                $cnt = 0;
                foreach ($daylist as $key => $value)
                {
                    if (strtoupper($value) == strtoupper($date))
                    {
                        $found = $cnt + 1;
                        break;
                    }
                    ++$cnt;
                }
                // Weekday found
                if ($cnt < 7)
                {
                    $this->addTimestamp($this->_Date->mktime(0, 0, 0, 1, $found, 0, -1, $gmt));
                    return $this->getTimestamp();
                }
                else
                    // Weekday not found
                    return false; 
                break;


            // week formats
            case Zend_Date::WEEK :
                $week = (int) $this->get(Zend_Date::WEEK, $locale, $gmt);
                $this->addTimestamp($this->_Date->mktime(0, 0, 0, 1, ($week * 7), 0, -1, $gmt));
                return $this->getTimestamp();
                break;


            // month formats
            case Zend_Date::MONTH :
                $monthlist = Zend_Locale_Data::getContent($locale, 'monthlist', array('gregorian', 'wide'));
                $monthnr = (int) ($this->get(Zend_Date::MONTH_DIGIT, $locale, $gmt)) - 1;
                $cnt = 0;
                foreach ($monthlist as $key => $value)
                {
                    if (strtoupper($value) == strtoupper($date))
                    {
                        $found = $cnt + 1;
                        break;
                    }
                    ++$cnt;
                }
                // Monthname found
                if ($cnt < 12)
                {
                    $this->addTimestamp($this->_Date->mktime(0, 0, 0, $found, 1, 0, -1, $gmt));
                    return $this->getTimestamp();
                }
                else
                    // Monthname not found
                    return false; 
                break;
            case Zend_Date::MONTH_SHORT :
                $this->addTimestamp($this->_Date->mktime(0, 0, 0, intval($date), 1, 0, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::MONTH_NAME :
                $monthlist = Zend_Locale_Data::getContent($locale, 'monthlist', array('gregorian', 'abbreviated'));
                $monthnr = (int) ($this->get(Zend_Date::MONTH_DIGIT, $locale, $gmt)) - 1;
                $cnt = 0;
                foreach ($monthlist as $key => $value)
                {
                    if (strtoupper($value) == strtoupper($date))
                    {
                        $found = $cnt + 1;
                        break;
                    }
                    ++$cnt;
                }
                // Monthname found
                if ($cnt < 12)
                {
                    $this->addTimestamp($this->_Date->mktime(0, 0, 0, $found, 1, 0, -1, $gmt));
                    return $this->getTimestamp();
                }
                else
                    // Monthname not found
                    return false; 
                break;
            case Zend_Date::MONTH_DIGIT :
                $this->addTimestamp($this->_Date->mktime(0, 0, 0, intval($date), 1, 0, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::MONTH_DAYS :
                return false;
                break;


            case Zend_Date::MONTH_NARROW :
                $monthlist = Zend_Locale_Data::getContent($locale, 'monthlist', array('gregorian', 'abbreviated'));
                $monthnr = (int) ($this->get(Zend_Date::MONTH_DIGIT, $locale, $gmt)) - 1;
                $cnt = 0;
                foreach ($monthlist as $key => $value)
                {
                    if (strtoupper(substr($value,0,1)) == strtoupper(substr($date,0,1)))
                    {
                        $found = $cnt + 1;
                        break;
                    }
                    ++$cnt;
                }
                // Monthname found
                if ($cnt < 12)
                {
                    $this->addTimestamp($this->_Date->mktime(0, 0, 0, $found, 1, 0, -1, $gmt));
                    return $this->getTimestamp();
                }
                else
                    // Monthname not found
                    return false; 
                break;


            // year formats
            case Zend_Date::LEAPYEAR :
                return false;
                break;
            case Zend_Date::YEAR_8601 :
                $this->addTimestamp($this->_Date->mktime(0, 0, 0, 1, 1, intval($date), -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::YEAR :
                $this->addTimestamp($this->_Date->mktime(0, 0, 0, 1, 1, intval($date), -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::YEAR_SHORT :
                $year = intval($date);
                if (($year > 0) and ($year < 100))
                {
                    $year += 1900;
                    if ($year < 70)
                        $year += 100;
                }
                $this->addTimestamp($this->_Date->mktime(0, 0, 0, 1, 1, $year, -1, $gmt));
                return $this->getTimestamp();
                break;


            case Zend_Date::YEAR_SHORT_8601 :
                $this->addTimestamp($this->_Date->mktime(0, 0, 0, 1, 1, intval($date), -1, $gmt));
                return $this->getTimestamp();
                break;


            // time formats
            case Zend_Date::MERIDIEM :
                $meridiemlist = Zend_Locale_Data::getContent($locale, 'daytime', 'gregorian');
                $meridiem = strtoupper($this->get(Zend_Date::MERIDIEM, $locale, $gmt));

                if (($meridiem == strtoupper($meridiemlist['am'])) && (strtoupper($date) == strtoupper($meridiemlist['pm'])))
                    $hour = 12;
                else if (($meridiem == strtoupper($meridiemlist['pm'])) && (strtoupper($date) == strtoupper($meridiemlist['am'])))
                    $hour = -12;
                    
                $this->addTimestamp($this->_Date->mktime($hour, 0, 0, 1, 1, 0, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::SWATCH :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::HOUR_SHORT_AM :
                $this->addTimestamp($this->_Date->mktime(intval($date), 0, 0, 1, 1, 0, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::HOUR_SHORT :
                $this->addTimestamp($this->_Date->mktime(intval($date), 0, 0, 1, 1, 0, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::HOUR_AM :
                $this->addTimestamp($this->_Date->mktime(intval($date), 0, 0, 1, 1, 0, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::HOUR :
                $this->addTimestamp($this->_Date->mktime(intval($date), 0, 0, 1, 1, 0, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::MINUTE :
                $this->addTimestamp($this->_Date->mktime(0, intval($date), 0, 1, 1, 0, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::SECOND :
                $this->addTimestamp($this->_Date->mktime(0, 0, intval($date), 1, 1, 0, -1, $gmt));
                return $this->getTimestamp();
                break;


            case Zend_Date::MINUTE_SHORT :
                $this->addTimestamp($this->_Date->mktime(0, intval($date), 0, 1, 1, 0, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::SECOND_SHORT :
                $this->addTimestamp($this->_Date->mktime(0, 0, intval($date), 1, 1, 0, -1, $gmt));
                return $this->getTimestamp();
                break;


            // timezone formats
            case Zend_Date::TIMEZONE_NAME :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::DAYLIGHT :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::GMT_DIFF :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::GMT_DIFF_SEP :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::TIMEZONE :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::TIMEZONE_SECS :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;


            // date strings
            case Zend_Date::ISO_8601 :
                $result = preg_match('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+\d{4}/', $date, $match);
                if (!$result)
                    return false;

                $year   = substr($match[0], 0, 4);
                $month  = substr($match[0], 5, 2);
                $day    = substr($match[0], 8, 2);
                $hour   = substr($match[0], 11, 2);
                $minute = substr($match[0], 14, 2);
                $second = substr($match[0], 17, 2);

                $this->addTimestamp($this->_Date->mktime($hour, $minute, $second, $month, $day, $year, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::RFC_2822 :
                $result = preg_match('/\w{3},\s\d{2}\s\w{3}\s\d{4}\s\d{2}:\d{2}:\d{2}\s\+\d{4}/', $date, $match);
                if (!$result)
                    return false;

                $day    = substr($match[0], 5, 2);
                $month  = $this->getDigitFromName(substr($match[0], 8, 3));
                $year   = substr($match[0], 12, 4);
                $hour   = substr($match[0], 17, 2);
                $minute = substr($match[0], 20, 2);
                $second = substr($match[0], 23, 2);

                $this->addTimestamp($this->_Date->mktime($hour, $minute, $second, $month, $day, $year, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::TIMESTAMP :
                if (is_numeric($date))
                {
                    $this->addTimestamp($date);
                    return $this->getTimestamp();
                } else
                    return false;
                break;


            // additional formats
            case Zend_Date::ERA :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::ERA_NAME :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::DATES :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::DATE_FULL :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::DATE_LONG :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::DATE_MEDIUM :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::DATE_SHORT :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::TIMES :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::TIME_FULL :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::TIME_LONG :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::TIME_MEDIUM :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::TIME_SHORT :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::ATOM :
                $result = preg_match('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+\d{2}:\d{2}/', $date, $match);
                if (!$result)
                    return false;

                $year   = substr($match[0], 0, 4);
                $month  = substr($match[0], 5, 2);
                $day    = substr($match[0], 8, 2);
                $hour   = substr($match[0], 11, 2);
                $minute = substr($match[0], 14, 2);
                $second = substr($match[0], 17, 2);

                $this->addTimestamp($this->_Date->mktime($hour, $minute, $second, $month, $day, $year, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::COOKIE :
                $result = preg_match('/\w{6,9},\s\d{2}-\w{3}-\d{2}\s\d{2}:\d{2}:\d{2}\s\w{3}/', $date, $match);
                if (!$result)
                    return false;
                $match[0] = substr($match[0], strpos(' '+1));
                
                $day    = substr($match[0], 0, 2);
                $month  = $this->getDigitFromName(substr($match[0], 3, 3));
                $year   = substr($match[0], 7, 4);
                $year  += 2000;
                $hour   = substr($match[0], 10, 2);
                $minute = substr($match[0], 13, 2);
                $second = substr($match[0], 16, 2);

                $this->addTimestamp($this->_Date->mktime($hour, $minute, $second, $month, $day, $year, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::RFC_822 :
                $result = preg_match('/\w{3},\s\d{2}\s\w{3}\s\d{2}\s\d{2}:\d{2}:\d{2}\s\+\d{4}/', $date, $match);
                if (!$result)
                    return false;
                
                $day    = substr($match[0], 5, 2);
                $month  = $this->getDigitFromName(substr($match[0], 8, 3));
                $year   = substr($match[0], 12, 4);
                $year  += 2000;
                $hour   = substr($match[0], 15, 2);
                $minute = substr($match[0], 18, 2);
                $second = substr($match[0], 21, 2);

                $this->addTimestamp($this->_Date->mktime($hour, $minute, $second, $month, $day, $year, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::RFC_850 :
                $result = preg_match('/\w{6,9},\s\d{2}-\w{3}-\d{2}\s\d{2}:\d{2}:\d{2}\s\w{3}/', $date, $match);
                if (!$result)
                    return false;
                $match[0] = substr($match[0], strpos(' '+1));
                
                $day    = substr($match[0], 0, 2);
                $month  = $this->getDigitFromName(substr($match[0], 3, 3));
                $year   = substr($match[0], 7, 4);
                $year  += 2000;
                $hour   = substr($match[0], 10, 2);
                $minute = substr($match[0], 13, 2);
                $second = substr($match[0], 16, 2);

                $this->addTimestamp($this->_Date->mktime($hour, $minute, $second, $month, $day, $year, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::RFC_1036 :
                $result = preg_match('/\w{3},\s\d{2}\s\w{3}\s\d{2}\s\d{2}:\d{2}:\d{2}\s\+\d{4}/', $date, $match);
                if (!$result)
                    return false;
                
                $day    = substr($match[0], 5, 2);
                $month  = $this->getDigitFromName(substr($match[0], 8, 3));
                $year   = substr($match[0], 12, 4);
                $year  += 2000;
                $hour   = substr($match[0], 15, 2);
                $minute = substr($match[0], 18, 2);
                $second = substr($match[0], 21, 2);

                $this->addTimestamp($this->_Date->mktime($hour, $minute, $second, $month, $day, $year, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::RFC_1123 :
                $result = preg_match('/\w{3},\s\d{2}\s\w{3}\s\d{4}\s\d{2}:\d{2}:\d{2}\s\+\d{4}/', $date, $match);
                if (!$result)
                    return false;

                $day    = substr($match[0], 5, 2);
                $month  = $this->getDigitFromName(substr($match[0], 8, 3));
                $year   = substr($match[0], 12, 4);
                $hour   = substr($match[0], 17, 2);
                $minute = substr($match[0], 20, 2);
                $second = substr($match[0], 23, 2);

                $this->addTimestamp($this->_Date->mktime($hour, $minute, $second, $month, $day, $year, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::RSS :
                $result = preg_match('/\w{3},\s\d{2}\s\w{3}\s\d{4}\s\d{2}:\d{2}:\d{2}\s\+\d{4}/', $date, $match);
                if (!$result)
                    return false;

                $day    = substr($match[0], 5, 2);
                $month  = $this->getDigitFromName(substr($match[0], 8, 3));
                $year   = substr($match[0], 12, 4);
                $hour   = substr($match[0], 17, 2);
                $minute = substr($match[0], 20, 2);
                $second = substr($match[0], 23, 2);

                $this->addTimestamp($this->_Date->mktime($hour, $minute, $second, $month, $day, $year, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::W3C :
                $result = preg_match('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+\d{2}:\d{2}/', $date, $match);
                if (!$result)
                    return false;

                $year   = substr($match[0], 0, 4);
                $month  = substr($match[0], 5, 2);
                $day    = substr($match[0], 8, 2);
                $hour   = substr($match[0], 11, 2);
                $minute = substr($match[0], 14, 2);
                $second = substr($match[0], 17, 2);

                $this->addTimestamp($this->_Date->mktime($hour, $minute, $second, $month, $day, $year, -1, $gmt));
                return $this->getTimestamp();
                break;
            default :
                break;
        }
    }


    /**
     * Substracts a date from another date. Could sub f.e. minutes, hours, days from a date object
     *
     * @param  $date string  - date which shall be our new date object
     * @param  $part         - datepart, if empty the timestamp will be returned
     * @param  $locale       - OPTIONAL, locale for output
     * @param  $gmt          - OPTIONAL, TRUE = actual timezone time, FALSE = UTC time
     * @return object
     */
    public function sub($date, $part, $locale = false, $gmt = false)
    {
        if ($locale === false)
            $locale = $this->_Locale;

        // if object extract value
        if (is_object($date))
            $date = $date->get($part, $locale, $gmt);

        // $date as object, part of foreign date as own date
        switch($part)
        {
            // day formats
            case Zend_Date::DAY :
                $this->subTimestamp($this->_Date->mktime(0, 0, 0, 1, intval($date), 0, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::WEEKDAY_SHORT :
                $daylist = Zend_Locale_Data::getContent($locale, 'daylist', array('gregorian', 'wide'));
                $weekday = (int) $this->get(Zend_Date::WEEKDAY_DIGIT, $locale, $gmt);
                $cnt = 0;
                foreach ($daylist as $key => $value)
                {
                    if (strtoupper(substr($value,0,3)) == strtoupper($date))
                    {
                        $found = $cnt + 1;
                        break;
                    }
                    ++$cnt;
                }
                // Weekday found
                if ($cnt < 7)
                {
                    $this->subTimestamp($this->_Date->mktime(0, 0, 0, 1, $found, 0, -1, $gmt));
                    return $this->getTimestamp();
                }
                else
                    // Weekday not found
                    return false; 
                break;
            case Zend_Date::DAY_SHORT :
                $this->subTimestamp($this->_Date->mktime(0, 0, 0, 1, intval($date), 0, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::WEEKDAY :
                $daylist = Zend_Locale_Data::getContent($locale, 'daylist', array('gregorian', 'wide'));
                $weekday = (int) $this->get(Zend_Date::WEEKDAY_DIGIT, $locale, $gmt);
                $cnt = 0;
                foreach ($daylist as $key => $value)
                {
                    if (strtoupper($value) == strtoupper($date))
                    {
                        $found = $cnt + 1;
                        break;
                    }
                    ++$cnt;
                }
                // Weekday found
                if ($cnt < 7)
                {
                    $this->subTimestamp($this->_Date->mktime(0, 0, 0, 1, $found, 0, -1, $gmt));
                    return $this->getTimestamp();
                }
                else
                    // Weekday not found
                    return false; 
                break;
            case Zend_Date::WEEKDAY_8601 :
                $weekday = (int) $this->get(Zend_Date::WEEKDAY_DIGIT, $locale, $gmt);
                if ((intval($date) > 0) and (intval($date) < 8))
                {
                    $this->subTimestamp($this->_Date->mktime(0, 0, 0, 1, $date, 0, -1, $gmt));
                    return $this;
                }
                else
                    // Weekday not found
                    return false; 
                break;
            case Zend_Date::DAY_SUFFIX :
                return false;
                break;
            case Zend_Date::WEEKDAY_DIGIT :
                $weekday = (int) $this->get(Zend_Date::WEEKDAY_DIGIT, $locale, $gmt);
                if ((intval($date) > 0) and (intval($date) < 8))
                {
                    $this->subTimestamp($this->_Date->mktime(0, 0, 0, 1, $date, 0, -1, $gmt));
                    return $this->getTimestamp();
                }
                else
                    // Weekday not found
                    return false; 
                break;
            case Zend_Date::DAY_OF_YEAR :
                $this->subTimestamp($this->_Date->mktime(0, 0, 0, 1, $date, 0, -1, $gmt));
                return $this->getTimestamp();
                break;


            case Zend_Date::WEEKDAY_NARROW :
                $daylist = Zend_Locale_Data::getContent($locale, 'daylist', array('gregorian', 'abbreviated'));
                $weekday = (int) $this->get(Zend_Date::WEEKDAY_DIGIT, $locale, $gmt);
                $cnt = 0;
                foreach ($daylist as $key => $value)
                {
                    if (strtoupper(substr($value,0,1)) == strtoupper($date))
                    {
                        $found = $cnt + 1;
                        break;
                    }
                    ++$cnt;
                }
                // Weekday found
                if ($cnt < 7)
                {
                    $this->subTimestamp($this->_Date->mktime(0, 0, 0, 1, $found, 0, -1, $gmt));
                    return $this->getTimestamp();
                }
                else
                    // Weekday not found
                    return false; 
                break;
            case Zend_Date::WEEKDAY_NAME :
                $daylist = Zend_Locale_Data::getContent($locale, 'daylist', array('gregorian', 'abbreviated'));
                $weekday = (int) $this->get(Zend_Date::WEEKDAY_DIGIT, $locale, $gmt);
                $cnt = 0;
                foreach ($daylist as $key => $value)
                {
                    if (strtoupper($value) == strtoupper($date))
                    {
                        $found = $cnt + 1;
                        break;
                    }
                    ++$cnt;
                }
                // Weekday found
                if ($cnt < 7)
                {
                    $this->subTimestamp($this->_Date->mktime(0, 0, 0, 1, $found, 0, -1, $gmt));
                    return $this->getTimestamp();
                }
                else
                    // Weekday not found
                    return false; 
                break;


            // week formats
            case Zend_Date::WEEK :
                $week = (int) $this->get(Zend_Date::WEEK, $locale, $gmt);
                $this->subTimestamp($this->_Date->mktime(0, 0, 0, 1, ($week * 7), 0, -1, $gmt));
                return $this->getTimestamp();
                break;


            // month formats
            case Zend_Date::MONTH :
                $monthlist = Zend_Locale_Data::getContent($locale, 'monthlist', array('gregorian', 'wide'));
                $monthnr = (int) ($this->get(Zend_Date::MONTH_DIGIT, $locale, $gmt)) - 1;
                $cnt = 0;
                foreach ($monthlist as $key => $value)
                {
                    if (strtoupper($value) == strtoupper($date))
                    {
                        $found = $cnt + 1;
                        break;
                    }
                    ++$cnt;
                }
                // Monthname found
                if ($cnt < 12)
                {
                    $this->subTimestamp($this->_Date->mktime(0, 0, 0, $found, 1, 0, -1, $gmt));
                    return $this->getTimestamp();
                }
                else
                    // Monthname not found
                    return false; 
                break;
            case Zend_Date::MONTH_SHORT :
                $this->subTimestamp($this->_Date->mktime(0, 0, 0, intval($date), 1, 0, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::MONTH_NAME :
                $monthlist = Zend_Locale_Data::getContent($locale, 'monthlist', array('gregorian', 'abbreviated'));
                $monthnr = (int) ($this->get(Zend_Date::MONTH_DIGIT, $locale, $gmt)) - 1;
                $cnt = 0;
                foreach ($monthlist as $key => $value)
                {
                    if (strtoupper($value) == strtoupper($date))
                    {
                        $found = $cnt + 1;
                        break;
                    }
                    ++$cnt;
                }
                // Monthname found
                if ($cnt < 12)
                {
                    $this->subTimestamp($this->_Date->mktime(0, 0, 0, $found, 1, 0, -1, $gmt));
                    return $this->getTimestamp();
                }
                else
                    // Monthname not found
                    return false; 
                break;
            case Zend_Date::MONTH_DIGIT :
                $this->subTimestamp($this->_Date->mktime(0, 0, 0, intval($date), 1, 0, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::MONTH_DAYS :
                return false;
                break;


            case Zend_Date::MONTH_NARROW :
                $monthlist = Zend_Locale_Data::getContent($locale, 'monthlist', array('gregorian', 'abbreviated'));
                $monthnr = (int) ($this->get(Zend_Date::MONTH_DIGIT, $locale, $gmt)) - 1;
                $cnt = 0;
                foreach ($monthlist as $key => $value)
                {
                    if (strtoupper(substr($value,0,1)) == strtoupper(substr($date,0,1)))
                    {
                        $found = $cnt + 1;
                        break;
                    }
                    ++$cnt;
                }
                // Monthname found
                if ($cnt < 12)
                {
                    $this->subTimestamp($this->_Date->mktime(0, 0, 0, $found, 1, 0, -1, $gmt));
                    return $this->getTimestamp();
                }
                else
                    // Monthname not found
                    return false; 
                break;


            // year formats
            case Zend_Date::LEAPYEAR :
                return false;
                break;
            case Zend_Date::YEAR_8601 :
                $this->subTimestamp($this->_Date->mktime(0, 0, 0, 1, 1, intval($date), -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::YEAR :
                $this->subTimestamp($this->_Date->mktime(0, 0, 0, 1, 1, intval($date), -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::YEAR_SHORT :
                $year = intval($date);
                if (($year > 0) and ($year < 100))
                {
                    $year += 1900;
                    if ($year < 70)
                        $year += 100;
                }
                $this->subTimestamp($this->_Date->mktime(0, 0, 0, 1, 1, $year, -1, $gmt));
                return $this->getTimestamp();
                break;


            case Zend_Date::YEAR_SHORT_8601 :
                $this->subTimestamp($this->_Date->mktime(0, 0, 0, 1, 1, intval($date), -1, $gmt));
                return $this->getTimestamp();
                break;


            // time formats
            case Zend_Date::MERIDIEM :
                $meridiemlist = Zend_Locale_Data::getContent($locale, 'daytime', 'gregorian');
                $meridiem = strtoupper($this->get(Zend_Date::MERIDIEM, $locale, $gmt));

                if (($meridiem == strtoupper($meridiemlist['am'])) && (strtoupper($date) == strtoupper($meridiemlist['pm'])))
                    $hour = 12;
                else if (($meridiem == strtoupper($meridiemlist['pm'])) && (strtoupper($date) == strtoupper($meridiemlist['am'])))
                    $hour = -12;
                    
                $this->subTimestamp($this->_Date->mktime($hour, 0, 0, 1, 1, 0, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::SWATCH :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::HOUR_SHORT_AM :
                $this->subTimestamp($this->_Date->mktime(intval($date), 0, 0, 1, 1, 0, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::HOUR_SHORT :
                $this->subTimestamp($this->_Date->mktime(intval($date), 0, 0, 1, 1, 0, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::HOUR_AM :
                $this->subTimestamp($this->_Date->mktime(intval($date), 0, 0, 1, 1, 0, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::HOUR :
                $this->subTimestamp($this->_Date->mktime(intval($date), 0, 0, 1, 1, 0, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::MINUTE :
                $this->subTimestamp($this->_Date->mktime(0, intval($date), 0, 1, 1, 0, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::SECOND :
                $this->subTimestamp($this->_Date->mktime(0, 0, intval($date), 1, 1, 0, -1, $gmt));
                return $this->getTimestamp();
                break;


            case Zend_Date::MINUTE_SHORT :
                $this->subTimestamp($this->_Date->mktime(0, intval($date), 0, 1, 1, 0, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::SECOND_SHORT :
                $this->subTimestamp($this->_Date->mktime(0, 0, intval($date), 1, 1, 0, -1, $gmt));
                return $this->getTimestamp();
                break;


            // timezone formats
            case Zend_Date::TIMEZONE_NAME :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::DAYLIGHT :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::GMT_DIFF :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::GMT_DIFF_SEP :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::TIMEZONE :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::TIMEZONE_SECS :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;


            // date strings
            case Zend_Date::ISO_8601 :
                $result = preg_match('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+\d{4}/', $date, $match);
                if (!$result)
                    return false;

                $year   = substr($match[0], 0, 4);
                $month  = substr($match[0], 5, 2);
                $day    = substr($match[0], 8, 2);
                $hour   = substr($match[0], 11, 2);
                $minute = substr($match[0], 14, 2);
                $second = substr($match[0], 17, 2);

                $this->subTimestamp($this->_Date->mktime($hour, $minute, $second, $month, $day, $year, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::RFC_2822 :
                $result = preg_match('/\w{3},\s\d{2}\s\w{3}\s\d{4}\s\d{2}:\d{2}:\d{2}\s\+\d{4}/', $date, $match);
                if (!$result)
                    return false;

                $day    = substr($match[0], 5, 2);
                $month  = $this->getDigitFromName(substr($match[0], 8, 3));
                $year   = substr($match[0], 12, 4);
                $hour   = substr($match[0], 17, 2);
                $minute = substr($match[0], 20, 2);
                $second = substr($match[0], 23, 2);

                $this->subTimestamp($this->_Date->mktime($hour, $minute, $second, $month, $day, $year, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::TIMESTAMP :
                if (is_numeric($date))
                {
                    $this->subTimestamp($date);
                    return $this->getTimestamp();
                } else
                    return false;
                break;


            // additional formats
            case Zend_Date::ERA :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::ERA_NAME :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::DATES :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::DATE_FULL :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::DATE_LONG :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::DATE_MEDIUM :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::DATE_SHORT :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::TIMES :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::TIME_FULL :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::TIME_LONG :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::TIME_MEDIUM :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::TIME_SHORT :
                // TODO: implement function
                $this->_Date->throwException('function yet not implemented');
                break;
            case Zend_Date::ATOM :
                $result = preg_match('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+\d{2}:\d{2}/', $date, $match);
                if (!$result)
                    return false;

                $year   = substr($match[0], 0, 4);
                $month  = substr($match[0], 5, 2);
                $day    = substr($match[0], 8, 2);
                $hour   = substr($match[0], 11, 2);
                $minute = substr($match[0], 14, 2);
                $second = substr($match[0], 17, 2);

                $this->subTimestamp($this->_Date->mktime($hour, $minute, $second, $month, $day, $year, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::COOKIE :
                $result = preg_match('/\w{6,9},\s\d{2}-\w{3}-\d{2}\s\d{2}:\d{2}:\d{2}\s\w{3}/', $date, $match);
                if (!$result)
                    return false;
                $match[0] = substr($match[0], strpos(' '+1));
                
                $day    = substr($match[0], 0, 2);
                $month  = $this->getDigitFromName(substr($match[0], 3, 3));
                $year   = substr($match[0], 7, 4);
                $year  += 2000;
                $hour   = substr($match[0], 10, 2);
                $minute = substr($match[0], 13, 2);
                $second = substr($match[0], 16, 2);

                $this->subTimestamp($this->_Date->mktime($hour, $minute, $second, $month, $day, $year, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::RFC_822 :
                $result = preg_match('/\w{3},\s\d{2}\s\w{3}\s\d{2}\s\d{2}:\d{2}:\d{2}\s\+\d{4}/', $date, $match);
                if (!$result)
                    return false;
                
                $day    = substr($match[0], 5, 2);
                $month  = $this->getDigitFromName(substr($match[0], 8, 3));
                $year   = substr($match[0], 12, 4);
                $year  += 2000;
                $hour   = substr($match[0], 15, 2);
                $minute = substr($match[0], 18, 2);
                $second = substr($match[0], 21, 2);

                $this->subTimestamp($this->_Date->mktime($hour, $minute, $second, $month, $day, $year, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::RFC_850 :
                $result = preg_match('/\w{6,9},\s\d{2}-\w{3}-\d{2}\s\d{2}:\d{2}:\d{2}\s\w{3}/', $date, $match);
                if (!$result)
                    return false;
                $match[0] = substr($match[0], strpos(' '+1));
                
                $day    = substr($match[0], 0, 2);
                $month  = $this->getDigitFromName(substr($match[0], 3, 3));
                $year   = substr($match[0], 7, 4);
                $year  += 2000;
                $hour   = substr($match[0], 10, 2);
                $minute = substr($match[0], 13, 2);
                $second = substr($match[0], 16, 2);

                $this->subTimestamp($this->_Date->mktime($hour, $minute, $second, $month, $day, $year, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::RFC_1036 :
                $result = preg_match('/\w{3},\s\d{2}\s\w{3}\s\d{2}\s\d{2}:\d{2}:\d{2}\s\+\d{4}/', $date, $match);
                if (!$result)
                    return false;
                
                $day    = substr($match[0], 5, 2);
                $month  = $this->getDigitFromName(substr($match[0], 8, 3));
                $year   = substr($match[0], 12, 4);
                $year  += 2000;
                $hour   = substr($match[0], 15, 2);
                $minute = substr($match[0], 18, 2);
                $second = substr($match[0], 21, 2);

                $this->subTimestamp($this->_Date->mktime($hour, $minute, $second, $month, $day, $year, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::RFC_1123 :
                $result = preg_match('/\w{3},\s\d{2}\s\w{3}\s\d{4}\s\d{2}:\d{2}:\d{2}\s\+\d{4}/', $date, $match);
                if (!$result)
                    return false;

                $day    = substr($match[0], 5, 2);
                $month  = $this->getDigitFromName(substr($match[0], 8, 3));
                $year   = substr($match[0], 12, 4);
                $hour   = substr($match[0], 17, 2);
                $minute = substr($match[0], 20, 2);
                $second = substr($match[0], 23, 2);

                $this->subTimestamp($this->_Date->mktime($hour, $minute, $second, $month, $day, $year, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::RSS :
                $result = preg_match('/\w{3},\s\d{2}\s\w{3}\s\d{4}\s\d{2}:\d{2}:\d{2}\s\+\d{4}/', $date, $match);
                if (!$result)
                    return false;

                $day    = substr($match[0], 5, 2);
                $month  = $this->getDigitFromName(substr($match[0], 8, 3));
                $year   = substr($match[0], 12, 4);
                $hour   = substr($match[0], 17, 2);
                $minute = substr($match[0], 20, 2);
                $second = substr($match[0], 23, 2);

                $this->subTimestamp($this->_Date->mktime($hour, $minute, $second, $month, $day, $year, -1, $gmt));
                return $this->getTimestamp();
                break;
            case Zend_Date::W3C :
                $result = preg_match('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+\d{2}:\d{2}/', $date, $match);
                if (!$result)
                    return false;

                $year   = substr($match[0], 0, 4);
                $month  = substr($match[0], 5, 2);
                $day    = substr($match[0], 8, 2);
                $hour   = substr($match[0], 11, 2);
                $minute = substr($match[0], 14, 2);
                $second = substr($match[0], 17, 2);

                $this->subTimestamp($this->_Date->mktime($hour, $minute, $second, $month, $day, $year, -1, $gmt));
                return $this->getTimestamp();
                break;
            default :
                break;
        }
    }


    /**
     * Compares a date with another date. Returns a date object with the difference date
     *
     * @param $date object   - date which shall be compared with our actual date object
     * @param $part datepart - OPTIONAL datepart to set
     * @return object
     */
    public function compare($date, $part)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Returns a duplicate date object
     *
     * @param $part datepart - OPTIONAL the part of date to clone
     * @return object
     */
    public function cloneIt($part)
    {
        // TODO: clone only dateparts
        return new Zend_Date($this->getTimestamp());
    }


    /**
     * Returns true when both date objects have equal dates set
     *
     * @param $date object
     * @return boolean
     */
    public function equals($date)
    {
        // TODO: equals only dateparts
        return ($date->getTimestamp() == $this->getTimestamp);
    }


    /**
     * Returns the maximum date or datepart for the set date object/calendar type
     *
     * @param $part  datepart / string - OPTIONAL
     * @return object / datepart
     */
    public function getMaximum($part)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Returns the minimum date or datepart for the set date object/calendar type
     *
     * @param $part  datepart / string - OPTIONAL
     * @return object / datepart
     */
    public function getMinimum($part)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Returns the first day of the week
     *
     * @param $locale string   - OPTIONAL locale for parsing input
     * @param $format string   - OPTIONAL how to return the day (Number, Name, DateObject)
     * @return mixed - object / datepart / integer
     */
    public function getFirstDayOfWeek($locale, $format)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Returns the time
     * Alias for clone(Zend_Date::TIME);
     *
     * @return object
     */
    public function getTime()
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Sets a new time
     * Alias for set($time, Zend_Date::TIME);
     *
     * @param $time string     - OPTIONAL time to set, when null the actual time is set
     * @param $locale string   - OPTIONAL locale for parsing input
     * @param $format          - OPTIONAL an rule for parsing the input
     * @return object
     */
    public function setTime($time, $locale, $format)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Adds a time
     * Alias for add($time,Zend_Date::TIME);
     *
     * @param $time object     - OPTIONAL time to add, when null the actual time is add
     * @return object
     */
    public function addTime($time)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Substracts a time
     * Alias for sub($time,Zend_Date::TIME);
     *
     * @param $time object     - OPTIONAL time to sub, when null the actual time is sub
     * @return object
     */
    public function subTime($time)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Compares only the time, returning the difference
     * Alias for compare($time,Zend_Date::TIME);
     *
     * @param $time object     - OPTIONAL time to compare, when null the actual time is used for compare
     * @return object
     */
    public function compareTime($time)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Returns the date
     * Alias for clone(Zend_Date::DATE);
     *
     * @return object
     */
    public function getDate()
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Sets a new date
     * Alias for set($date, Zend_Date::DATE);
     *
     * @param $date string     - OPTIONAL date to set, when null the actual date is set
     * @param $locale string   - OPTIONAL locale for parsing input
     * @param $format          - OPTIONAL an rule for parsing the input
     * @return object
     */
    public function setDate($date, $locale, $format)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Adds a date
     * Alias for add($date,Zend_Date::DATE);
     *
     * @param $date object     - OPTIONAL date to add, when null the actual date is add
     * @return object
     */
    public function addDate($time)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Substracts a date
     * Alias for sub($date,Zend_Date::DATE);
     *
     * @param $date object     - OPTIONAL date to sub, when null the actual date is sub
     * @return object
     */
    public function subDate($date)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Compares only the date, returning the difference date
     * Alias for compare($date,Zend_Date::DATE);
     *
     * @param $date object     - OPTIONAL date to compare, when null the actual date is used for compare
     * @return object
     */
    public function compareDate($date)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Returns a ISO8601 formatted date - ISO is locale-independent
     *
     * @param $format - OPTIONAL an rule for formatting the output for different ISO Formats
     * @return string
     */
    public function getIso($format)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Sets a new ISOdate
     * Alias for set($date);
     *
     * @param $date string     - OPTIONAL ISOdate to set, when null the actual date is set
     * @param $format          - OPTIONAL an rule for parsing the ISOinput
     * @return object
     */
    public function setIso($date, $format)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Adds a ISOdate
     * Alias for add($date);
     *
     * @param $date string  - OPTIONAL ISOdate to add, when null the actual date is add
     * @param $format       - OPTIONAL an rule for parsing the ISOinput
     * @return object
     */
    public function addIso($date, $format)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Substracts a ISOdate
     * Alias for sub($date);
     *
     * @param $date string  - OPTIONAL ISOdate to sub, when null the actual date is sub
     * @param $format       - OPTIONAL an rule for parsing the ISOinput
     * @return object
     */
    public function subIso($date, $format)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Compares IsoDate with date object, returning the difference date
     * Alias for compare($date);
     *
     * @param $date string - OPTIONAL ISOdate to compare, when null the actual date is used for compare
     * @return object
     */
    public function compareIso($date)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Returns a RFC822 formatted date - RFC822 is locale-independent
     *
     * @return string
     */
    public function getArpa()
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Sets a new RFC822 formatted date
     * Alias for set($date);
     *
     * @param $date string     - OPTIONAL RFC822 date to set, when null the actual date is set
     * @return object
     */
    public function setArpa($date)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Adds a RFC822 formatted date
     * Alias for add($date);
     *
     * @param $date string  - OPTIONAL RFC822 date to add, when null the actual date is add
     * @return object
     */
    public function addArpa($date)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Substracts a RFC822 formatted date
     * Alias for sub($date);
     *
     * @param $date string  - OPTIONAL RFC822 date to sub, when null the actual date is sub
     * @return object
     */
    public function subArpa($date)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Compares a RFC822 formatted date with date object, returning the difference date
     * Alias for compare($date);
     *
     * @param $date string - OPTIONAL RFC822 date to compare, when null the actual date is used for compare
     * @return object
     */
    public function compareArpa($date)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Returns a UTC/GMT formatted date
     *
     * @param $timezone string   - OPTIONAL locale or timezone for setting output timezone
     * @return string
     */
    public function getUtc($timezone)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Sets a new UTC/GMT formatted date
     * Alias for set($date);
     *
     * @param $date string     - OPTIONAL UTC/GMT formatted date to set, when null the actual date is set
     * @return object
     */
    public function setUtc($date)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Adds a UTC/GMT formatted date
     * Alias for add($date);
     *
     * @param $date string  - OPTIONAL UTC/GMT formatted date to add, when null the actual date is add
     * @return object
     */
    public function addUtc($date)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Substracts a UTC/GMT formatted date
     * Alias for sub($date);
     *
     * @param $date string  - OPTIONAL UTC/GMT formatted date to sub, when null the actual date is sub
     * @return object
     */
    public function subUtc($date)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Compares UTC/GMT formatted date with date object, returning the difference date
     * Alias for compare($date);
     *
     * @param $date string - OPTIONAL UTC/GMT date to compare, when null the actual date is used for compare
     * @return object
     */
    public function compareUtc($date)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Returns the time of sunrise for this locale or an optional location
     *
     * @param  $location array - OPTIONAL location of sunrise
     * @return object
     */
    public function getSunRise($location)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Returns the time of sunset for this locale or an optional location
     *
     * @param  $location array - OPTIONAL location of sunset
     * @return object
     */
    public function getSunSet($location)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Returns an array with all sun-infos for a time and location
     *
     * @param  $location array - location of suninfo
     * @return object
     */
    public function getSunInfo($location)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Returns the timezone
     *
     * @param $locale string   - OPTIONAL locale timezone
     * @return object
     */
    public function getTimeZone($locale)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Sets the timezone
     *
     * @param $timezone timezone - OPTIONAL timezone to set
     * @param $locale string   - OPTIONAL locale for timezone
     * @return object
     */
    public function setTimeZone($locale)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Returns if the date is a leap year
     *
     * @return boolean
     */
    public function isLeapYear()
    {
        return $this->_Date->isLeapYear();
    }


    /**
     * Returns if the date is todays date
     *
     * @return boolean
     */
    public function isToday()
    {
        $today = $this->_Date->mktime();
        return ($today == $this->_Date->getTimestamp());
    }


    /**
     * Returns if the date is yesterdays date
     *
     * @return boolean
     */
    public function isYesterday()
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Returns if the date is tomorrows date
     *
     * @return boolean
     */
    public function isTomorrow()
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Returns if the date is after this date
     *
     * @param $date object     - date to compare
     * @return boolean
     */
    public function isAfter($date)
    {
        if ($this->get() < $date->get())
            return true;

        return false;
    }


    /**
     * Returns if the date is before this date
     *
     * @param $date object     - date to compare
     * @return boolean
     */
    public function isBefore($date)
    {
        if ($this->get() > $date->get())
            return true;

        return false;
    }


    /**
     * Returns actual date as object
     *
     * @return object
     */
    public function now()
    {
        return new Zend_Date('',$this->_Locale);
    }


    /**
     * Returns the year
     * Alias for get(Zend_Date::YEAR)
     *
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return object
     */
    public function getYear($locale = false)
    {
        if (empty($locale))
            $locale = $this->_Locale;
        $year = $this->get(Zend_Date::YEAR, $locale, FALSE);
        return new Zend_Date($this->_Date->mktime(0, 0, 0, 1, 1, $year, $locale, FALSE), $locale);
    }


    /**
     * Sets a new year
     * Alias for set($year, Zend_Date::YEAR);
     *
     * @param $year string/integer - OPTIONAL year to set, when null the actual year is set
     * @param $locale locale   - OPTIONAL locale for parsing input
     * @return object
     */
    public function setYear($year = false, $locale = false)
    {
        if (empty($locale))
            $locale = $this->_Locale;
        if (empty($year))
            $year = $this->_Date->date('Y');
        if (is_object($year))
            $year = $year->get(Zend_Date::YEAR, $locale, FALSE);
        $this->set($year, Zend_Date::YEAR, $locale, FALSE);
        return $this;
    }


    /**
     * Adds a year
     * Alias for add($year,Zend_Date::YEAR);
     *
     * @param $year object     - OPTIONAL year to add, when null the actual year is add
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return object
     */
    public function addYear($year = false, $locale = false)
    {
        if (empty($locale))
            $locale = $this->_Locale;
        if (empty($year))
            $year = $this->_Date->date('Y');
        if (is_object($year))
            $year = $year->get(Zend_Date::YEAR, $locale, FALSE);
        $this->add($year, Zend_Date::YEAR, $locale, FALSE);
        return $this;
    }


    /**
     * Substracts a year
     * Alias for sub($year,Zend_Date::YEAR);
     *
     * @param $year object     - OPTIONAL year to sub, when null the actual year is sub
     * @return object
     */
    public function subYear($year = false, $locale = false)
    {
        if (empty($locale))
            $locale = $this->_Locale;
        if (empty($year))
            $year = $this->_Date->date('Y');
        if (is_object($year))
            $year = $year->get(Zend_Date::YEAR, $locale, FALSE);
        $this->sub($year, Zend_Date::YEAR, $locale, FALSE);
        return $this;
    }


    /**
     * Compares only the year part, returning the difference
     * Alias for compare($year,Zend_Date::YEAR);
     *
     * @param $year string/integer - OPTIONAL year to compare, when null the actual year is used for compare
     * @return string
     */
    public function compareYear($year)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Compares only the year part, returning boolean true
     * Alias for compare($year,Zend_Date::YEAR);
     *
     * @param $year string/integer - OPTIONAL year to compare, when null the actual year is used for compare
     * @return string
     */
    public function isYear($year)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Returns the month
     *
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return object
     */
    public function getMonth($locale = false)
    {
        if (empty($locale))
            $locale = $this->_Locale;
        $month = $this->get(Zend_Date::MONTH_DIGIT, $locale, FALSE);
        return new Zend_Date($this->_Date->mktime(0, 0, 0, $month, 1, 0, $locale, FALSE), $locale);
    }


    /**
     * Sets a new month
     * Alias for set($month, Zend_Date::MONTH);
     *
     * @param $month string/integer - OPTIONAL month to set, when null the actual month is set
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return object
     */
    public function setMonth($month = false, $locale = false)
    {
        if (empty($locale))
            $locale = $this->_Locale;
        if (empty($month))
            $month = $this->_Date->date('n');
        if (is_object($month))
            $month = $month->get(Zend_Date::MONTH_DIGIT, $locale, FALSE);
        if (is_numeric($month))
            $this->set($month, Zend_Date::MONTH_DIGIT, $locale, FALSE);
        else if (strlen($month) == 1)
            $this->set($month, Zend_Date::MONTH_NARROW, $locale, FALSE);
        else if (strlen($month) == 3)
            $this->set($month, Zend_Date::MONTH_NAME, $locale, FALSE);
        else
            $this->set($month, Zend_Date::MONTH, $locale, FALSE);
        return $this;
    }


    /**
     * Adds a month
     * Alias for add($month,Zend_Date::MONTH);
     *
     * @param $month object     - OPTIONAL month to add, when null the actual month is add
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return object
     */
    public function addMonth($month = false, $locale = false)
    {
        if (empty($locale))
            $locale = $this->_Locale;
        if (empty($month))
            $month = $this->_Date->date('n');
        if (is_object($month))
            $this->add($month, Zend_Date::MONTH_DIGIT, $locale, FALSE);
        if (is_numeric($month))
            $this->add($month, Zend_Date::MONTH_DIGIT, $locale, FALSE);
        else if (strlen($month) == 1)
            $this->add($month, Zend_Date::MONTH_NARROW, $locale, FALSE);
        else if (strlen($month) == 3)
            $this->add($month, Zend_Date::MONTH_NAME, $locale, FALSE);
        else
            $this->add($month, Zend_Date::MONTH, $locale, FALSE);
        return $this;
    }


    /**
     * Substracts a month
     * Alias for sub($month,Zend_Date::MONTH);
     *
     * @param $month object     - OPTIONAL month to sub, when null the actual month is sub
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return object
     */
    public function subMonth($month = false, $locale = false)
    {
        if (empty($locale))
            $locale = $this->_Locale;
        if (empty($month))
            $month = $this->_Date->date('n');
        if (is_object($month))
            $this->sub($month, Zend_Date::MONTH_DIGIT, $locale, FALSE);
        if (is_numeric($month))
            $this->sub($month, Zend_Date::MONTH_DIGIT, $locale, FALSE);
        else if (strlen($month) == 1)
            $this->sub($month, Zend_Date::MONTH_NARROW, $locale, FALSE);
        else if (strlen($month) == 3)
            $this->sub($month, Zend_Date::MONTH_NAME, $locale, FALSE);
        else
            $this->sub($month, Zend_Date::MONTH, $locale, FALSE);
        return $this;
    }


    /**
     * Compares only the month part, returning the difference
     * Alias for compare($month,Zend_Date::MONTH);
     *
     * @param $month string/integer - OPTIONAL month to compare, when null the actual month is used for compare
     * @return string
     */
    public function compareMonth($month)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Compares only the month part, returning boolean true
     * Alias for compare($month,Zend_Date::MONTH);
     *
     * @param $month string/integer - OPTIONAL month to compare, when null the actual month is used for compare
     * @return string
     */
    public function isMonth($month)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Returns the day
     *
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return object
     */
    public function getDay($locale = false)
    {
        if (empty($locale))
            $locale = $this->_Locale;
        $day = $this->get(Zend_Date::DAY_SHORT, $locale, FALSE);
        return new Zend_Date($this->_Date->mktime(0, 0, 0, 1, $day, 0, $locale, false), $locale);
    }


    /**
     * Sets a new day
     * Alias for set($day, Zend_Date::DAY);
     *
     * @param $day string/integer - OPTIONAL day to set, when null the actual day is set
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return object
     */
    public function setDay($day = false, $locale = false)
    {
        if (empty($locale))
            $locale = $this->_Locale;
        if (empty($day))
            $day = $this->_Date->date('j');
        if (is_object($day))
            $day = $day->get(Zend_Date::DAY_SHORT, $locale, FALSE);
        // TODO: recognise localized day names
        $this->set($day, Zend_Date::DAY_SHORT, $locale, FALSE);
        return $this;
    }


    /**
     * Adds a day
     * Alias for add($day,Zend_Date::DAY);
     *
     * @param $day object     - OPTIONAL day to add, when null the actual day is add
     * @return object
     */
    public function addDay($day)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Substracts a day
     * Alias for sub($day,Zend_Date::DAY);
     *
     * @param $day object     - OPTIONAL day to sub, when null the actual day is sub
     * @return object
     */
    public function subDay($day)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Compares only the day part, returning the difference
     * Alias for compare($day,Zend_Date::DAY);
     *
     * @param $day string/integer - OPTIONAL day to compare, when null the actual day is used for compare
     * @return string
     */
    public function compareDay($day)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Compares only the day part, returning boolean true
     * Alias for compare($day,Zend_Date::DAY);
     *
     * @param $day string/integer - OPTIONAL day to compare, when null the actual day is used for compare
     * @return string
     */
    public function isDay($day)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Returns the weekday
     * Alias for clone(Zend_Date::WEEKDAY);toString();
     *
     * @return string
     */
    public function getWeekday()
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Sets a new weekday
     * Alias for set($weekday, Zend_Date::WEEKDAY);
     *
     * @param $weekday string/integer - OPTIONAL weekday to set, when null the actual weekday is set
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return object
     */
    public function setWeekday($weekday = false, $locale = false)
    {
        if (empty($locale))
            $locale = $this->_Locale;
        if (empty($weekday))
            $weekday = $this->_Date->date('w');
        if (is_object($weekday))
            $weekday = $month->get(Zend_Date::WEEKDAY_DIGIT, $locale, FALSE);
        if (is_numeric($weekday))
            $this->set($weekday, Zend_Date::WEEKDAY_DIGIT, $locale, FALSE);
        else if (strlen($weekday) == 1)
            $this->set($weekday, Zend_Date::WEEKDAY_NARROW, $locale, FALSE);
        else if (strlen($weekday) == 2)
            $this->set($weekday, Zend_Date::WEEKDAY_NAME, $locale, FALSE);
        else if (strlen($weekday) == 3)
            $this->set($weekday, Zend_Date::WEEKDAY_SHORT, $locale, FALSE);
        else
            $this->set($weekday, Zend_Date::WEEKDAY, $locale, FALSE);
        return $this;
    }


    /**
     * Adds a weekday
     * Alias for add($weekday,Zend_Date::WEEKDAY);
     *
     * @param $weekday object     - OPTIONAL weekday to add, when null the actual weekday is add
     * @return object
     */
    public function addWeekday($weekday)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Substracts a weekday
     * Alias for sub($weekday,Zend_Date::WEEKDAY);
     *
     * @param $weekday object     - OPTIONAL weekday to sub, when null the actual weekday is sub
     * @return object
     */
    public function subWeekday($weekday)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Compares only the weekday part, returning the difference
     * Alias for compare($weekday,Zend_Date::WEEKDAY);
     *
     * @param $weekday string/integer - OPTIONAL weekday to compare, when null the actual weekday is used for compare
     * @return string
     */
    public function compareWeekday($weekday)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Compares only the weekday part, returning boolean true
     * Alias for compare($day,Zend_Date::WEEKDAY);
     *
     * @param $weekday string/integer - OPTIONAL weekday to compare, when null the actual weekday is used for compare
     * @return string
     */
    public function isWeekday($weekday)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Returns the day of year
     *
     * @param $locale locale   - OPTIONAL locale for parsing
     * @return object
     */
    public function getDayOfYear($locale = false)
    {
        if (empty($locale))
            $locale = $this->_Locale;
        $day = $day->get(Zend_Date::DAY_OF_YEAR, $locale, FALSE);
        return new Zend_Date($this->_Date->mktime(0, 0, 0, 1, $day, 0, $locale, FALSE), $locale);
    }


    /**
     * Sets a new day of year
     * Alias for set($day, Zend_Date::DAY);
     *
     * @param $day string/integer - OPTIONAL day to set, when null the actual day is set
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return object
     */
    public function setDayOfYear($day = false, $locale = false)
    {
        if (empty($locale))
            $locale = $this->_Locale;
        if (empty($day))
            $day = $this->_Date->date('z');
        if (is_object($day))
            $day = $day->get(Zend_Date::DAY_OF_YEAR, $locale, FALSE);
        $this->set($day, Zend_Date::DAY_OF_YEAR, $locale, FALSE);
        return $this;
    }


    /**
     * Adds a day of year
     * Alias for add($day,Zend_Date::DAY);
     *
     * @param $day object     - OPTIONAL day to add, when null the actual day is add
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return object
     */
    public function addDayOfYear($day = false, $locale = false)
    {
        if (empty($locale))
            $locale = $this->_Locale;
        if (empty($day))
            $day = $this->_Date->date('z');
        if (is_object($day))
            $day = $day->get(Zend_Date::DAY_OF_YEAR, $locale, FALSE);
        $this->add($day, Zend_Date::DAY_OF_YEAR, $locale, FALSE);
        return $this;
    }


    /**
     * Substracts a day of year
     * Alias for sub($day,Zend_Date::DAY);
     *
     * @param $day object     - OPTIONAL day to sub, when null the actual day is sub
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return object
     */
    public function subDayOfYear($day = false, $locale = false)
    {
        if (empty($locale))
            $locale = $this->_Locale;
        if (empty($day))
            $day = $this->_Date->date('z');
        if (is_object($day))
            $day = $day->get(Zend_Date::DAY_OF_YEAR, $locale, FALSE);
        $this->sub($day, Zend_Date::DAY_OF_YEAR, $locale, FALSE);
        return $this;
    }


    /**
     * Compares only the day of year
     * Alias for compare($day,Zend_Date::DAY);
     *
     * @param $day string/integer - OPTIONAL day to compare, when null the actual day is used for compare
     * @return string
     */
    public function compareDayOfYear($day)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Compares only the day of year, returning boolean true
     * Alias for compare($day,Zend_Date::DAY);
     *
     * @param $day string/integer - OPTIONAL day to compare, when null the actual day is used for compare
     * @return string
     */
    public function isDayOfYear($day)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Returns the hour
     *
     * @param $locale locale   - OPTIONAL locale for parsing input
     * @return string
     */
    public function getHour($locale = false)
    {
        if (empty($locale))
            $locale = $this->_Locale;
        $hour = $hour->get(Zend_Date::HOUR_SHORT, $locale, FALSE);
        return new Zend_Date($this->_Date->mktime($hour, 0, 0, 1, 1, 0, $locale, FALSE), $locale);
    }


    /**
     * Sets a new hour
     *
     * @param $hour string/integer - OPTIONAL hour to set, when null the actual hour is set
     * @param $locale locale   - OPTIONAL locale for parsing input
     * @return object
     */
    public function setHour($hour = false, $locale = false)
    {
        if (empty($locale))
            $locale = $this->_Locale;
        if (empty($hour))
            $hour = $this->_Date->date('G');
        if (is_object($hour))
            $hour = $hour->get(Zend_Date::HOUR_SHORT, $locale, FALSE);
        $this->set($hour, Zend_Date::HOUR_SHORT, $locale, FALSE);
        return $this;
    }


    /**
     * Adds a hour
     *
     * @param $hour object     - OPTIONAL hour to add, when null the actual hour is add
     * @param $locale locale   - OPTIONAL locale for parsing input
     * @return object
     */
    public function addHour($hour = false, $locale = false)
    {
        if (empty($locale))
            $locale = $this->_Locale;
        if (empty($hour))
            $hour = $this->_Date->date('G');
        if (is_object($hour))
            $hour = $hour->get(Zend_Date::HOUR_SHORT, $locale, FALSE);
        $this->add($hour, Zend_Date::HOUR_SHORT, $locale, FALSE);
        return $this;
    }


    /**
     * Substracts a hour
     *
     * @param $hour object     - OPTIONAL hour to sub, when null the actual hour is sub
     * @param $locale locale   - OPTIONAL locale for parsing input
     * @return object
     */
    public function subHour($hour = false, $locale = false)
    {
        if (empty($locale))
            $locale = $this->_Locale;
        if (empty($hour))
            $hour = $this->_Date->date('G');
        if (is_object($hour))
            $hour = $hour->get(Zend_Date::HOUR_SHORT, $locale, FALSE);
        $this->sub($hour, Zend_Date::HOUR_SHORT, $locale, FALSE);
        return $this;
    }


    /**
     * Compares only the hour part, returning the difference
     * Alias for compare($hour,Zend_Date::HOUR);
     *
     * @param $hour string/integer - OPTIONAL hour to compare, when null the actual hour is used for compare
     * @return string
     */
    public function compareHour($hour)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Compares only the hour part, returning boolean true
     * Alias for compare($hour,Zend_Date::HOUR);
     *
     * @param $hour string/integer - OPTIONAL hour to compare, when null the actual hour is used for compare
     * @return string
     */
    public function isHour($hour)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Returns the minute
     *
     * @param $locale locale   - OPTIONAL locale for parsing input
     * @return string
     */
    public function getMinute($locale = false)
    {
        if (empty($locale))
            $locale = $this->_Locale;
        $minute = $minute->get(Zend_Date::MINUTE_SHORT, $locale, FALSE);
        return new Zend_Date($this->_Date->mktime(0, $minute, 0, 1, 1, 0, $locale, FALSE), $locale);
    }


    /**
     * Sets a new minute
     *
     * @param $minute string/integer - OPTIONAL minute to set, when null the actual minute is set
     * @param $locale locale   - OPTIONAL locale for parsing input
     * @return object
     */
    public function setMinute($minute = false, $locale = false)
    {
        if (empty($locale))
            $locale = $this->_Locale;
        if (empty($minute))
            $minute = $this->_Date->date('i');
        if (is_object($minute))
            $minute = $minute->get(Zend_Date::MINUTE_SHORT, $locale, FALSE);
        $this->set($minute, Zend_Date::MINUTE_SHORT, $locale, FALSE);
        return $this;
    }


    /**
     * Adds a minute
     *
     * @param $minute object   - OPTIONAL minute to add, when null the actual minute is add
     * @param $locale locale   - OPTIONAL locale for parsing input
     * @return object
     */
    public function addMinute($minute = false, $locale = false)
    {
        if (empty($locale))
            $locale = $this->_Locale;
        if (empty($minute))
            $minute = $this->_Date->date('i');
        if (is_object($minute))
            $minute = $minute->get(Zend_Date::MINUTE_SHORT, $locale, FALSE);
        $this->add($minute, Zend_Date::MINUTE_SHORT, $locale, FALSE);
        return $this;
    }


    /**
     * Substracts a minute
     *
     * @param $minute object   - OPTIONAL minute to sub, when null the actual minute is sub
     * @param $locale locale   - OPTIONAL locale for parsing input
     * @return object
     */
    public function subMinute($minute = false, $locale = false)
    {
        if (empty($locale))
            $locale = $this->_Locale;
        if (empty($minute))
            $minute = $this->_Date->date('i');
        if (is_object($minute))
            $minute = $minute->get(Zend_Date::MINUTE_SHORT, $locale, FALSE);
        $this->sub($minute, Zend_Date::MINUTE_SHORT, $locale, FALSE);
        return $this;
    }


    /**
     * Compares only the minute part, returning the difference
     * Alias for compare($minute,Zend_Date::MINUTE);
     *
     * @param $minute string/integer - OPTIONAL minute to compare, when null the actual minute is used for compare
     * @return string
     */
    public function compareMinute($minute)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Compares only the minute part, returning boolean true
     * Alias for compare($minute,Zend_Date::MINUTE);
     *
     * @param $minute string/integer - OPTIONAL minute to compare, when null the actual minute is used for compare
     * @return string
     */
    public function isMinute($minute)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Returns the second
     *
     * @param $locale locale   - OPTIONAL locale for parsing input
     * @return string
     */
    public function getSecond($locale = false)
    {
        if (empty($locale))
            $locale = $this->_Locale;
        $second = $second->get(Zend_Date::MINUTE_SHORT, $locale, FALSE);
        return new Zend_Date($this->_Date->mktime(0, 0, $second, 1, 1, 0, $locale, FALSE), $locale);
    }


    /**
     * Sets a new second
     *
     * @param $second string/integer - OPTIONAL second to set, when null the actual second is set
     * @param $locale locale   - OPTIONAL locale for parsing input
     * @return object
     */
    public function setSecond($second = false, $locale = false)
    {
        if (empty($locale))
            $locale = $this->_Locale;
        if (empty($second))
            $second = $this->_Date->date('s');
        if (is_object($second))
            $second = $second->get(Zend_Date::SECOND_SHORT, $locale, FALSE);
        $this->set($second, Zend_Date::SECOND_SHORT, $locale, FALSE);
        return $this;
    }


    /**
     * Adds a second
     *
     * @param $second object     - OPTIONAL second to add, when null the actual second is add
     * @return object
     */
    public function addSecond($second = false, $locale = false)
    {
        if (empty($locale))
            $locale = $this->_Locale;
        if (empty($second))
            $second = $this->_Date->date('s');
        if (is_object($second))
            $second = $second->get(Zend_Date::SECOND_SHORT, $locale, FALSE);
        $this->add($second, Zend_Date::SECOND_SHORT, $locale, FALSE);
        return $this;
    }


    /**
     * Substracts a second
     * Alias for sub($second,Zend_Date::SECOND);
     *
     * @param $second object     - OPTIONAL second to sub, when null the actual second is sub
     * @return object
     */
    public function subSecond($second = false, $locale = false)
    {
        if (empty($locale))
            $locale = $this->_Locale;
        if (empty($second))
            $second = $this->_Date->date('s');
        if (is_object($second))
            $second = $second->get(Zend_Date::SECOND_SHORT, $locale, FALSE);
        $this->sub($second, Zend_Date::SECOND_SHORT, $locale, FALSE);
        return $this;
    }


    /**
     * Compares only the second part, returning the difference
     * Alias for compare($second,Zend_Date::SECOND);
     *
     * @param $second string/integer - OPTIONAL second to compare, when null the actual second is used for compare
     * @return string
     */
    public function compareSecond($second)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Compares only the second part, returning boolean true
     * Alias for compare($second,Zend_Date::SECOND);
     *
     * @param $second string/integer - OPTIONAL second to compare, when null the actual second is used for compare
     * @return string
     */
    public function isSecond($second)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Returns the millisecond
     * Alias for clone(Zend_Date::MILLISECOND);toString();
     *
     * @return string
     */
    public function getMilliSecond()
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Sets a new millisecond
     * Alias for set($millisecond, Zend_Date::MILLISECOND);
     *
     * @param $millisecond string/integer - OPTIONAL millisecond to set, when null the actual millisecond is set
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return object
     */
    public function setMilliSecond($millisecond, $locale = false)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Adds a millisecond
     * Alias for add($millisecond,Zend_Date::MILLISECOND);
     *
     * @param $millisecond object     - OPTIONAL millisecond to add, when null the actual millisecond is add
     * @return object
     */
    public function addMilliSecond($millisecond)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Substracts a millisecond
     * Alias for sub($millisecond,Zend_Date::MILLISECOND);
     *
     * @param $millisecond object     - OPTIONAL millisecond to sub, when null the actual millisecond is sub
     * @return object
     */
    public function subMilliSecond($millisecond)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Compares only the millisecond part, returning the difference
     * Alias for compare($millisecond,Zend_Date::MILLISECOND);
     *
     * @param $millisecond string/integer - OPTIONAL millisecond to compare, when null the actual millisecond is used for compare
     * @return string
     */
    public function compareMilliSecond($millisecond)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Compares only the millisecond part, returning boolean true
     * Alias for compare($millisecond,Zend_Date::MILLISECOND);
     *
     * @param $millisecond string/integer - OPTIONAL millisecond to compare, when null the actual millisecond is used for compare
     * @return string
     */
    public function isMilliSecond($millisecond)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Returns the era
     * Alias for clone(Zend_Date::ERA);toString();
     *
     * @return string
     */
    public function getEra()
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Sets a new era
     * Alias for set($era, Zend_Date::ERA);
     *
     * @param $era string/integer - OPTIONAL era to set, when null the actual era is set
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return object
     */
    public function setEra($era, $locale)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Adds a era
     * Alias for add($era,Zend_Date::ERA);
     *
     * @param $era object     - OPTIONAL era to add, when null the actual era is add
     * @return object
     */
    public function addEra($era)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Substracts a era
     * Alias for sub($era,Zend_Date::ERA);
     *
     * @param $era object     - OPTIONAL era to sub, when null the actual era is sub
     * @return object
     */
    public function subEra($era)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Compares only the era part, returning the difference
     * Alias for compare($era,Zend_Date::ERA);
     *
     * @param $era string/integer - OPTIONAL era to compare, when null the actual era is used for compare
     * @return string
     */
    public function compareEra($era)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Compares only the era part, returning boolean true
     * Alias for compare($era,Zend_Date::ERA);
     *
     * @param $era string/integer - OPTIONAL era to compare, when null the actual era is used for compare
     * @return string
     */
    public function isEra($era)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Returns the week
     * Alias for clone(Zend_Date::WEEK);toString();
     *
     * @return string
     */
    public function getWeek()
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Sets a new week
     * Alias for set($week, Zend_Date::WEEK);
     *
     * @param $week string/integer - OPTIONAL week to set, when null the actual week is set
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return object
     */
    public function setWeek($week, $locale)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Adds a week
     * Alias for add($week,Zend_Date::WEEK);
     *
     * @param $week object     - OPTIONAL week to add, when null the actual week is add
     * @return object
     */
    public function addWeek($week)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Substracts a week
     * Alias for sub($week,Zend_Date::WEEK);
     *
     * @param $week object     - OPTIONAL week to sub, when null the actual week is sub
     * @return object
     */
    public function subWeek($week)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Compares only the week part, returning the difference
     * Alias for compare($week,Zend_Date::WEEK);
     *
     * @param $week string/integer - OPTIONAL week to compare, when null the actual week is used for compare
     * @return string
     */
    public function compareWeek($week)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Compares only the week part, returning boolean true
     * Alias for compare($week,Zend_Date::WEEK);
     *
     * @param $week string/integer - OPTIONAL week to compare, when null the actual week is used for compare
     * @return string
     */
    public function isWeek($week)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }
}