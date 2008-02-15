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
 * @package    Zend_Calendar
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Implement date class
 */
require_once 'Zend/Date.php';
require_once 'Zend/Date/Exception.php';


/**
 * @category   Zend
 * @package    Zend_Calendar
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Calendar extends Zend_Date {

    /**
     * Generates the standard calendar object
     * Extends from Zend_Date and makes use of it's functions'
     *
     * @todo implement function
     * @param $date string     - OPTIONAL date object depending on $parameter
     * @param $locale string   - OPTIONAL locale for parsing input
     * @param $parameter mixed - OPTIONAL defines the input format of $date
     * @return object
     */
    public function __construct($date, $locale, $parameter)
    {
        throw new Zend_Date_Exception('Calendar class yet not implemented... will be done when Zend_Date is ready!');
    }

    /**
     * Serialization Interface
     * 
     * @todo implement function
     */
    public function serialize() {}

    /**
     * Returns a string representation of the object
     *
     * @todo implement function
     * @param $locale string   - OPTIONAL locale for parsing input
     * @param $format - OPTIONAL an rule for formatting the output
     * @return string
     */
    public function toString($locale, $format) {}
    public function __toString($locale, $format) {}
}
