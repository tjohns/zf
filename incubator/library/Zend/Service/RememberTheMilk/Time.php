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
 * @package    Zend_Service
 * @subpackage RememberTheMilk
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage RememberTheMilk
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_RememberTheMilk_Time
{
    /**
     * Date object instance representing the time
     *
     * @var Zend_Date
     */
    protected $_time;

    /**
     * Precision of the time
     *
     * @var string
     */
    protected $_precision;

    /**
     * Time zone to which the time is relative
     *
     * @var string
     */
    protected $_timezone;

    /**
     * Constructor to initialize the object with data
     *
     * @todo Check parsing for description in Argument::__construct()
     * @param array $data Associative array containing data from an API
     *                    response
     * @return void
     */
    public function __construct($data)
    {
        $this->_time = new Zend_Date($data['time']['time']);
        if (isset($data['precision'])) {
            $this->_precision = $data['precision'];
        }
        if (isset($data['timezone'])) {
            $this->_timezone = $data['timezone'];
        }
    }

    /**
     * Returns the time.
     *
     * @return Zend_Date
     */
    public function getDate()
    {
        return $this->_time;
    }

    /**
     * Returns the precision of the time.
     *
     * @return string
     */
    public function getPrecision()
    {
        return $this->_precision;
    }

    /**
     * Returns the time zone to which the time is relative.
     *
     * @return string
     */
    public function getTimezone()
    {
        return $this->_timezone;
    }
}