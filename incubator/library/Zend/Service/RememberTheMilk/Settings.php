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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage RememberTheMilk
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_RememberTheMilk_Settings
{
    /**
     * User's Olson timezone
     *
     * @var string
     */
    protected $_timezone;

    /**
     * Whether or not an American date format is being used
     *
     * @var bool
     */
    protected $_dateFormat;

    /**
     * Whether or not a 24 hour time format is being used
     *
     * @var bool
     */
    protected $_timeFormat;

    /**
     * Identifier for the user's default list
     *
     * @var int
     */
    protected $_defaultList;

    /**
     * User's language (ISO 639-1 code)
     *
     * @var string
     */
    protected $_language;

    /**
     * Constructor to initialize the object with data
     *
     * @param array $data Associative array containing data from an API
     *                    response
     * @return void
     */
    public function __construct($data)
    {
        $data = $data->settings;
        $this->_timezone = $data->timezone;
        $this->_dateFormat = $data->dateformat;
        $this->_timeFormat = $data->timeformat;
        $this->_defaultList = $data->defaultlist;
        $this->_language = $data->language;
    }

    /**
     * Returns the user's Olson timezone, or the empty string if the user has
     * not set a timezone. (Ex: Australia/Sydney)
     *
     * @return string
     */
    public function getTimezone()
    {
        return $this->_timezone;
    }

    /**
     * Returns whether or not an American date format is being used.
     *
     * @return bool TRUE for American (e.g. 02/14/06), FALSE for European
     *         (e.g. 14/02/06)
     */
    public function getDateFormat()
    {
        return $this->_dateFormat;
    }

    /**
     * Returns whether or not a 24 hour time format is being used.
     *
     * @return bool TRUE for 24 hour time (e.g. 17:00), FALSE for 12 hour time
     *              (e.g. 5pm)
     */
    public function getTimeFormat()
    {
        return $this->_timeFormat;
    }

    /**
     * Returns the identifier of the user's default list.
     *
     * @return int Identifier for the default list, or NULL if no default is
     *             set
     */
    public function getDefaultList()
    {
        return $this->_defaultList;
    }

    /**
     * Returns the user's language.
     *
     * @return string Language (ISO 639-1 code)
     */
    public function getLanguage()
    {
        return $this->_language;
    }
}
