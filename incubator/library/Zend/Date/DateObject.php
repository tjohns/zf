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
 * @category   Zend
 * @package    Zend_Date
 * @subpackage Zend_Date_DateObject
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Date_DateObject {


    /**
     * UNIX Timestamp
     */
    private $_unixtimestamp;


    /**
     * Table of Monthdays
     */
    private $_monthTable = array(31,28,31,30,31,30,31,31,30,31,30,31);


    /**
     * active timezone 
     */
    private $_timezone = false;


    /**
     * Generates the standard date object
     * must be given as Timestamp
     * This object simulates the PHP 5.2 Date Object
     *
     * @param $date mixed - timestamp number
     */
    public function __construct($date)
    {
        $this->setTimestamp($date);
    }


    /**
     * Sets a new timestamp
     * 
     * @param $date mixed - timestamp number
     */
    public function setTimestamp($date)
    {
        // no date value, take actual time
        if (empty($date))
        {
            $this->_unixtimestamp = time();
            return true;
        }

        if (is_numeric($date))
        {
          $this->_unixtimestamp = $date;
          return true;
        }  

        $this->throwException('"'.$date.'" is no valid date');
    }


    /**
     * Returns unix timestamp
     * 
     * @return  timestamp
     */
    public function getTimestamp()
    {
        return $this->_unixtimestamp;
    }


    /**
     * Internal mktime function
     * for handling 64bit timestamps
     * 
     * Returns a timestamp to a given GMT/UTC time
     * Summer/winter time is only supported for 32bit timestamps
     * Year has to be 4 digits otherwise it would be recognised as
     * year 70 AD instead of 1970 AD as expected !!
     * 
     * @param $hour   number  - hour
     * @param $minute number  - minute
     * @param $second number  - second
     * @param $month  number  - month
     * @param $day    number  - day
     * @param $year   number  - year
     * @param $dst    boolean - summer/wintertime
     * @param $gmt    boolean - timezone
     */
    public function mktime($hour, $minute, $second, $month = false, $day = false, $year = false, $dst= false, $gmt = false)
    {
        // only time - use PHP internal
        if ($month === false)
            return $gmt ? @gmmktime($hour, $minute, $second) : @mktime($hour, $minute, $second);
        
        // complete date but in 32bit timestamp - use PHP internal
        if ((1901 < $year) and ($year < 2038))
            return $gmt ? @gmmktime($hour, $minute, $second, $month, $day, $year, $dst) : @mktime($hour, $minute, $second, $month, $day, $year, $dst);
        
        //
        // after here we are handling 64bit timestamps
        //
        
        // get difference fronm local to gmt
        $difference = ($gmt) ? 0 : $this->_gmtDifference();
        
        // date to integer
        $day   = intval($day);
        $month = intval($month);
        $year  = intval($year);
        
        // correct months > 12 and months < 1
        if ($month > 12)
        {
            $overlap = floor($month / 12);
            $year   += $overlap;
            $month  -= $overlap*12;
        } else {
            $overlap = ceil((1-$month) / 12);
            $year   -= $overlap;
            $month  += $overlap*12;
        }
        
        $date = 0;
        if ($year >= 1970)
        {
            // Date is after UNIX epoch
            // go through leapyears
            // add months from letest given year
            for ($count = 1970; $count <= $year; $count++)
            {
                $leapyear = $this->isLeapYear($count);
                if ($count < $year)
                {
                    $date += 365;
                    if ($leapyear == true)
                        $date++;
                } else {
                    for ($mcount = 0; $mcount < ($month -1); $mcount++)
                    {
                        $date += $this->_monthTable[$mcount];
                        if (($leapyear == true) and ($month == 1))
                            $date++;
                    }
                }
            }
            
            $date += $day-1;
            
            return (($date * 86400) + ($hour * 3600) + ($minute * 60) + $second + $difference);
        } else {
            // Date is after UNIX epoch
            // go through leapyears
            // add months from letest given year
            for ($count = 1969; $count >= $year; $count--)
            {
                $leapyear = $this->isLeapYear($count);
                if ($count > $year)
                {
                    $date += 365;
                    if ($leapyear == true)
                        $date++;
                } else {
                    for ($mcount = 11; $mcount > ($month-1); $mcount--)
                    {
                        $date += $this->_monthTable[$mcount];
                        if (($leapYear == true) and ($month == 1))
                            $date++; 
                    }
                }
            }
            
            $date += ($this->_monthTable[$mcount] - $day);
            $date = -(($date*86400) + (86400 - (($hour * 3600) + ($minute * 60 + $second))) - $difference);

            // gregorian correction for 5.Oct.1582
            if ($date < -12220185600)
            {
                $date += 864000;
            } else if ($date < -12219321600) {
                $date  = -12219321600;
            }
            
            return $date;
        }
    }


    /**
     * Returns the difference from local time to GMT
     * 
     * @return  integer
     */
    private function _gmtDifference()
    {
        if ($this->_timezone !== false)
            return $this->_timezone;
        
        $this->_timezone = mktime(0,0,0,1,2,1970,0) - gmmktime(0,0,0,1,2,1970,0);
        return $this->_timezone;
    }


    /**
     * Returns true if given date is a leap year
     * 
     * @param $year  integer
     * @return boolean - true if year is leap year 
     */
    public function isLeapYear($year)
    {
        if (($year % 4) != 0)
            return false;

        if ($year % 400 == 0)
        {
            return true;
        } else if (($year > 1582) and ($year % 100 == 0)) {
            return false;
        }

        return true;
    }


    /**
     * Internal date function
     * for handling 64bit timestamps
     * 
     * Returns a formatted date for a timestamp
     * 
     * @param $format     string - format for output
     * @param $timestamp  mixed
     * @param $gmt        boolean - timezone
     * @return  string  
     */    
    public function date($format, $timestamp, $gmt)
    {
        // TODO: implement function
        $this->throwException('function yet not implemented');
    }    


    /**
     * Returns the day of week
     * 0 = sunday, 6 = saturday
     * 
     * @param $year  integer
     * @param $month integer
     * @param $day   integer
     * @return dayOfWeek 
     */
    public function dayOfWeek($year, $month, $day)
    {
        // gregorian correction
        $correction = 0;
        if (($year < 1582) or (($year == 1582) and (($month < 10) or (($month == 10) && ($day < 15)))))
            $correction = 3;

        if ($month > 2)
        {
            $month -= 2;
        } else {
            $month += 10;
            $year--;
        }

        $day = floor((13 * $month - 1) / 5) +
               $day + ($year % 100) + 
               floor(($year % 100) / 4) + 
               floor(($year / 100) / 4) - 2 *
               floor($year / 100) + 77 + $correction;

        return $day - 7 * floor($day / 7);
    }


    /**
     * Internal getDate function
     * for handling 64bit timestamps
     * 
     * Returns an array with date parts to a given GMT/UTC timestamp
     * 
     * $all defines is ALL date parts should be returned.
     * Default is false, so the function works faster
     * 
     * @param  $timestamp  mixed
     * @param  $all        boolean
     * @param  $gmt        boolean - timezone
     * @return array
     */
    public function getDate($timestamp = false, $all = false, $gmt = false)
    {
        // TODO: implement function
        $this->throwException('function yet not implemented');
    }


    /**
     * Throw an exception
     *
     * Note : for performance reasons, the "load" of Zend/Date/Exception is dynamic
     */
    public static function throwException($message)
    {
        require_once('Zend/Date/Exception.php');
        throw new Zend_Date_Exception($message);
    }
}