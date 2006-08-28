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
 * @category   Zend
 * @package    Zend_Date
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Date {


    // Class wide Date Constants
    const YEAR           = 'Zend_Date::YEAR';           // 4 digit year
    const YEAR_SHORT     = 'Zend_Date::YEAR_SHORT';     // 2 digit year, leading zeros 10 = 2010, 01 = 2001
    const MONTH          = 'Zend_Date::MONTH';          // full month name - locale aware - February, March 
    const MONTH_DIGIT    = 'Zend_Date::MONTH_DIGIT';    // 1 digit month, no leading zeros, 1 = jan, 10 = Oct
    const MONTH_NARROW   = 'Zend_Date::MONTH_NARROW';   // 1 letter month name - locale aware - J, F, M, A, J, J, 
    const MONTH_SHORT    = 'Zend_Date::MONTH_SHORT';    // 2 digit month, leading zeros 01 = Jan, 10 = Oct
    const MONTH_NAME     = 'Zend_Date::MONTH_NAME';     // 3 letter monthname - locale aware - Jan, Feb, Mar
    const WEEKDAY        = 'Zend_Date::WEEKDAY';        // full day name - locale aware - Monday, Friday
    const WEEKDAY_DIGIT  = 'Zend_Date::WEEKDAY_DIGIT';  // 1 digit day, no leading zeros
    const WEEKDAY_NARROW = 'Zend_Date::WEEKDAY_NARROW'; // 1 letter day name - locale aware - M, T, W
    const WEEKDAY_NAME   = 'Zend_Date::WEEKDAY_NAME';   // 2 letter day name - locale aware - Mo, Tu, We
    const DAY            = 'Zend_Date::DAY';            // 2 digit day of month - 18, 05, 31
    const DAY_SHORT      = 'Zend_Date::DAY_SHORT';      // 1,2 digit day of month - 18, 5, 31
    const HOUR           = 'Zend_Date::HOUR';           // 2 digit hour, leading zeros 01, 10
    const HOUR_SHORT     = 'Zend_Date::HOUR_SHORT';     // 1 digit hour, no leading zero
    const HOUR_AM        = 'Zend_Date::HOUR_AM';        // 2 digit hour, leading zeros 01, 10 0-11am/pm
    const HOUR_SHORT_AM  = 'Zend_Date::HOUR_SHORT_AM';  // 1 digit hour, no leading zero, 0-11 am/pm
    const MERIDIEM       = 'Zend_Date::HOUR';           // 2 digit hour, leading zeros 01, 10
    const MINUTE         = 'Zend_Date::MINUTE';         // 2 digit minute, leading zeros
    const MINUTE_SHORT   = 'Zend_Date::MINUTE_SHORT';   // 1 digit minute, no leading zero
    const SECOND         = 'Zend_Date::SECOND';         // 2 digit second, leading zeros
    const SECOND_SHORT   = 'Zend_Date::SECOND_SHORT';   // 1 digit second, no leading zero
    const MSECOND        = 'Zend_Date::MSECOND';        // Milliseconds
    const ERA            = 'Zend_Date::ERA';            // Era name
    const ERA_SHORT      = 'Zend_Date::ERA_SHORT';      // Era short name

    private $_Const = array(
        'Zend_Date::YEAR'           => 'yyyy',
        'Zend_Date::YEAR_SHORT'     => 'yy',
        'Zend_Date::MONTH'          => 'MMMM',
        'Zend_Date::MONTH_DIGIT'    => 'Mn',
        'Zend_Date::MONTH_NARROW'   => 'M',
        'Zend_Date::MONTH_SHORT'    => 'MM',
        'Zend_Date::MONTH_NAME'     => 'MMM',
        'Zend_Date::WEEKDAY'        => 'wwww',
        'Zend_Date::WEEKDAY_DIGIT'  => 'wn',
        'Zend_Date::WEEKDAY_NARROW' => 'w',
        'Zend_Date::WEEKDAY_NAME'   => 'ww',
        'Zend_Date::DAY'            => 'dd',
        'Zend_Date::DAY_SHORT'      => 'd',
        'Zend_Date::HOUR'           => 'HH',
        'Zend_Date::HOUR_SHORT'     => 'H',
        'Zend_Date::HOUR_AM'        => 'hh',
        'Zend_Date::HOUR_SHORT_AM'  => 'h',
        'Zend_Date::MERIDIEM'       => 'z',
        'Zend_Date::MINUTE'         => 'mm',
        'Zend_Date::MINUTE_SHORT'   => 'm',
        'Zend_Date::SECOND'         => 'ss',
        'Zend_Date::SECOND_SHORT'   => 's',
        'Zend_Date::MSECOND'        => 'µ',
        'Zend_Date::ERA'            => 'EEEE',
        'Zend_Date::ERA_SHORT'      => 'E'
    );

    // Predefined Date formats
    const TIME  = 'TIME';
    const DATE  = 'DATE';
    const TIMESTAMP = 'TIMESTAMP';


    /**
     * Date Object
     */
    private $_Date;


    /**
     * Generates the standard date object
     * could be
     *   - Unix timestamp
     *   - ISO
     *   - Locale
     *
     * @param $date string - OPTIONAL date string depending on $parameter
     * @param $locale string   - OPTIONAL locale for parsing input
     * @param $parameter mixed - OPTIONAL defines the input format of $date
     * @return object
     */
    public function __construct($date, $locale, $parameter)
    {
        // TODO: implement function
        // TODO: Is String
        // TODO: Lets Parse String Locale-Aware or Format-Parameter aware
        // TODO: New Zend_Locale_Format function getDate
        
        $this->_Date = new Zend_Date_DateObject($date);
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
     *
     * @param $locale string   - OPTIONAL locale for parsing input
     * @param $format - OPTIONAL an rule for formatting the output
     * @return string
     */
    public function toString($locale, $format)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }
    
    
    /**
     * Returns a string representation of the object
     * Alias for toString
     *
     * @param $locale string   - OPTIONAL locale for parsing input
     * @param $format - OPTIONAL an rule for formatting the output
     * @return string
     */
    public function __toString($locale, $format)
    {
        return $this->toString($locale, $format);
    }


    /**
     * Returns a integer representation of the object
     *
     * @param  $part   part of date to return as integer
     * @return integer
     */
    public function toValue($part)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Returns a timestamp or a part of a date 
     * 
     * @param  $part - datepart, if empty the timestamp will be returned
     * @return mixed   timestamp or datepart 
     */
    public function get($part)
    {
        switch($part)
        {
            case Zend_Date::YEAR :
                return $this->_Date->date('Y',$this->_Date->getTimestamp(), true);
                break;
            case Zend_Date::YEAR_SHORT :
                return $this->_Date->date('y',$this->_Date->getTimestamp(), true);
                break;
            case Zend_Date::MONTH :
                // TODO: locale aware full monthname : january
                break;
            case Zend_Date::MONTH_DIGIT :
                return $this->_Date->date('n',$this->_Date->getTimestamp(), true);
                break;
            case Zend_Date::MONTH_NARROW :
                // TODO: locale aware month letter : j
                break;
            case Zend_Date::MONTH_SHORT :
                return $this->_Date->date('m',$this->_Date->getTimestamp(), true);
                break;
            case Zend_Date::MONTH_NAME :
                // TODO: locale aware short monthname : jan
                break;
            case Zend_Date::WEEKDAY :
                // TODO: locale aware full weekday name : monday
                break;
            case Zend_Date::WEEKDAY_DIGIT :
                return $this->_Date->date('w',$this->_Date->getTimestamp(), true);
                break;
            case Zend_Date::WEEKDAY_NARROW :
                // TODO: locale aware weekday letter : m
                break;
            case Zend_Date::WEEKDAY_NAME :
                // TODO: locale aware short weekday name : Mo
                break;
            case Zend_Date::DAY :
                return $this->_Date->date('d',$this->_Date->getTimestamp(), true);
                break;
            case Zend_Date::DAY_SHORT :
                return $this->_Date->date('j',$this->_Date->getTimestamp(), true);
                break;
            case Zend_Date::HOUR :
                return $this->_Date->date('H',$this->_Date->getTimestamp(), true);
                break;
            case Zend_Date::HOUR_SHORT :
                return $this->_Date->date('G',$this->_Date->getTimestamp(), true);
                break;
            case Zend_Date::HOUR_AM :
                return $this->_Date->date('h',$this->_Date->getTimestamp(), true);
                break;
            case Zend_Date::HOUR_SHORT_AM :
                return $this->_Date->date('g',$this->_Date->getTimestamp(), true);
                break;
            case Zend_Date::MERIDIEM :
                return $this->_Date->date('a',$this->_Date->getTimestamp(), true);
                break;
            case Zend_Date::MINUTE :
                return $this->_Date->date('i',$this->_Date->getTimestamp(), true);
                break;
            case Zend_Date::MINUTE_SHORT :
                return $this->_Date->date('I',$this->_Date->getTimestamp(), true);
                break;
            case Zend_Date::SECOND :
                return $this->_Date->date('s',$this->_Date->getTimestamp(), true);
                break;
            case Zend_Date::SECOND_SHORT :
                return $this->_Date->date('C',$this->_Date->getTimestamp(), true);
                break;
            case Zend_Date::MSECOND :
                break;
            case Zend_Date::ERA :
                // TODO: locale aware era name - beyond christus
                break;
            case Zend_Date::ERA_SHORT :
                // TODO: locale aware short era name - BC
                break;
            default :
                return $this->_Date->getTimestamp();
                break;
        }        
        // TODO: 
        // Swatch time
        // ISO 8601
        // Timedifference GMT
        // RFC 2822
        // English Attachments th, rd, nd
        // Count days of month
        // actual timezonesettings 
        // number of week ISO 8601
        // day of year
    }


    /**
     * Sets the given date as new date
     *
     * @param $date string   - date which shall be our new date object
     * @param $part datepart - OPTIONAL datepart to set
     * @return object
     */
    public function set($date, $part)
    {
        switch($part)
        {
            case 'Zend_Date::YEAR_2' :
                // convert 2 digit to 4 digit
                $date += 1900;
                if (intval($date) < 30)
                    $date += 100;
            case 'Zend_Date::YEAR' :
                $timestamp = $this->_Date->mktime(0,0,0,1,1, intval($date));
                return $timestamp;
                break;
            case MONTH :
                $timestamp = $this->_Date->mktime(0,0,0,$date);
                break;
            case MONTH_0 :
                break;
            case MONTH_1 :
                break;
            case MONTH_2 :
                break;
            case MONTH_3 :
                break;
            case DAY :
                break;
            case DAY_0 :
                break;
            case DAY_1 :
                break;
            case DAY_2 :
                break;
            case HOUR :
                break;
            case HOUR_0 :
                break;
            case MINUTE :
                break;
            case MINUTE_0 :
                break;
            case SECOND :
                break;
            case SECOND_0 :
                break;
            case MSECOND :
                break;
            case ERA :
                break;
            default :
                return $this->_Date->setTimestamp();
                break;
        }        
        // TODO: return dateparts
    }


    /**
     * Adds a date to another date. Could add f.e. minutes, hours, days, months to a date object
     *
     * @param $date object     - date which shall be added to our actual date object
     * @param $part datepart   - OPTIONAL datepart to add
     * @return object
     */
    public function add($date, $part)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Substracts a date from another date. Could sub f.e. minutes, hours, days from a date object
     *
     * @param $date object     - date which shall be substracted to our actual date object
     * @param $part datepart   - OPTIONAL datepart to substract
     * @return object
     */
    public function sub($date, $part)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
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
        return new Zend_Date($this->_Date->getTimestamp());
    }


    /**
     * Returns true when both date objects have equal dates set
     *
     * @param $date object
     * @return boolean
     */
    public function equals($date)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
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
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
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
     * Returns if the date is befor this date
     *
     * @param $date object     - date to compare
     * @return boolean
     */
    public function isBefor($date)
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
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Returns the year
     * Alias for clone(Zend_Date::YEAR);toString();
     *
     * @return string
     */
    public function getYear()
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Sets a new year
     * Alias for set($year, Zend_Date::YEAR);
     *
     * @param $year string/integer - OPTIONAL year to set, when null the actual year is set
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return object
     */
    public function setYear($year, $locale)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Adds a year
     * Alias for add($year,Zend_Date::YEAR);
     *
     * @param $year object     - OPTIONAL year to add, when null the actual year is add
     * @return object
     */
    public function addYear($year)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Substracts a year
     * Alias for sub($year,Zend_Date::YEAR);
     *
     * @param $year object     - OPTIONAL year to sub, when null the actual year is sub
     * @return object
     */
    public function subYear($year)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
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
     * Alias for clone(Zend_Date::MONTH);toString();
     *
     * @return string
     */
    public function getMonth()
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Sets a new month
     * Alias for set($month, Zend_Date::MONTH);
     *
     * @param $month string/integer - OPTIONAL month to set, when null the actual month is set
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return object
     */
    public function setMonth($month, $locale)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Adds a month
     * Alias for add($month,Zend_Date::MONTH);
     *
     * @param $month object     - OPTIONAL month to add, when null the actual month is add
     * @return object
     */
    public function addMonth($month)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Substracts a month
     * Alias for sub($month,Zend_Date::MONTH);
     *
     * @param $month object     - OPTIONAL month to sub, when null the actual month is sub
     * @return object
     */
    public function subMonth($month)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
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
     * Alias for clone(Zend_Date::DAY);toString();
     *
     * @return string
     */
    public function getDay()
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Sets a new day
     * Alias for set($day, Zend_Date::DAY);
     *
     * @param $day string/integer - OPTIONAL day to set, when null the actual day is set
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return object
     */
    public function setDay($day, $locale)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
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
     * Returns the hour
     * Alias for clone(Zend_Date::HOUR);toString();
     *
     * @return string
     */
    public function getHour()
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Sets a new hour
     * Alias for set($hour, Zend_Date::HOUR);
     *
     * @param $hour string/integer - OPTIONAL hour to set, when null the actual hour is set
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return object
     */
    public function setHour($hour, $locale)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Adds a hour
     * Alias for add($hour,Zend_Date::HOUR);
     *
     * @param $hour object     - OPTIONAL hour to add, when null the actual hour is add
     * @return object
     */
    public function addHour($hour)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Substracts a hour
     * Alias for sub($hour,Zend_Date::HOUR);
     *
     * @param $hour object     - OPTIONAL hour to sub, when null the actual hour is sub
     * @return object
     */
    public function subHour($hour)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
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
     * Alias for clone(Zend_Date::MINUTE);toString();
     *
     * @return string
     */
    public function getMinute()
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Sets a new minute
     * Alias for set($minute, Zend_Date::MINUTE);
     *
     * @param $minute string/integer - OPTIONAL minute to set, when null the actual minute is set
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return object
     */
    public function setMinute($minute, $locale)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Adds a minute
     * Alias for add($minute,Zend_Date::MINUTE);
     *
     * @param $minute object     - OPTIONAL minute to add, when null the actual minute is add
     * @return object
     */
    public function addMinute($minute)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Substracts a minute
     * Alias for sub($minute,Zend_Date::MINUTE);
     *
     * @param $minute object     - OPTIONAL minute to sub, when null the actual minute is sub
     * @return object
     */
    public function subMinute($minute)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
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
     * Alias for clone(Zend_Date::SECOND);toString();
     *
     * @return string
     */
    public function getSecond()
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Sets a new second
     * Alias for set($second, Zend_Date::SECOND);
     *
     * @param $second string/integer - OPTIONAL second to set, when null the actual second is set
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return object
     */
    public function setSecond($second, $locale)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Adds a second
     * Alias for add($second,Zend_Date::SECOND);
     *
     * @param $second object     - OPTIONAL second to add, when null the actual second is add
     * @return object
     */
    public function addSecond($second)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Substracts a second
     * Alias for sub($second,Zend_Date::SECOND);
     *
     * @param $second object     - OPTIONAL second to sub, when null the actual second is sub
     * @return object
     */
    public function subSecond($second)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
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
    public function setMilliSecond($millisecond, $locale)
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


    /**
     * Returns the day of week of our Date Object
     *
     * @param $format string - OPTIONAL defines the format in which the day of the week should be outputted (1/sunday/...)
     * @param $locale string - OPTIONAL defines the locale in which the day of the week should be outputted
     * @return string
     */
    public function getDayOfWeek($locale, $format)
    {
        // TODO: implement function
        $this->_Date->throwException('function yet not implemented');
    }


    /**
     * Returns the day of year of our Date Object
     *
     * @return integer
     */
    public function getDayOfYear()
    {
        return $this->_Date->date('z',$this->_Date->getTimestamp(), true);
    }
}