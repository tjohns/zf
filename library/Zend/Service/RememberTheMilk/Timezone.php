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
class Zend_Service_RememberTheMilk_Timezone
{
    /**
     * Identifier for the timezone
     *
     * @var int
     */
    protected $_id;

    /**
     * Name of the timezone
     *
     * @var string
     */
    protected $_name;

    /**
     * Whether or not the timezone is currently in Daylight Savings Time
     *
     * @var bool
     */
    protected $_dst;

    /**
     * Offset for the timezone
     *
     * @var int
     */
    protected $_offset;

    /**
     * Current offset for the timezone
     *
     * @var int
     */
    protected $_currentOffset;

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
        $data = $data['timezone'];
        $this->_id = $data['id'];
        $this->_name = $data['name'];
        $this->_dst = ($data['dst'] == '1');
        $this->_offset = $data['offset'];
        $this->_currentOffset = $data['current_offset'];
    }

    /**
     * Returns the identifier for the timezone.
     *
     * @return int
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Returns the name of the timezone.
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Returns whether or not the timezone is currently in Daylight Savings
     * Time.
     *
     * @return bool TRUE if the timezone is in Daylight Savings Time, FALSE
     *              otherwise
     */
    public function inDST()
    {
        return $this->_dst;
    }

    /**
     * Returns the offset for the timezone.
     *
     * @var int
     */
    public function getOffset()
    {
        return $this->_offset;
    }

    /**
     * Returns the current offset for the timezone.
     *
     * @var int
     */
    public function getCurrentOffset()
    {
        return $this->_currentOffset;
    }
}