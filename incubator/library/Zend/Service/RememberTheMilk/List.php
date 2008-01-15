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
class Zend_Service_RememberTheMilk_List
{
    /**
     * Identifier for the list
     *
     * @var int
     */
    protected $_id;

    /**
     * Name of the list
     *
     * @var string
     */
    protected $_name;

    /**
     * Position of the list
     *
     * @var int
     */
    protected $_position;

    /**
     * Filter setting for the list
     *
     * @var string
     */
    protected $_filter;

    /**
     * Whether or not the list has been deleted
     *
     * @var bool
     */
    protected $_deleted;

    /**
     * Whether or not the list has been locked
     *
     * @var bool
     */
    protected $_locked;

    /**
     * Whether or not the list has been archived
     *
     * @var bool
     */
    protected $_archived;

    /**
     * Whether or not the list is a Smart List
     *
     * @var bool
     */
    protected $_smart;

    /**
     * Constructor to initialize the object with data
     *
     * @param array $data Associative array containing data from an API
     *                    response
     * @return void
     */
    public function __construct($data)
    {
        $this->_id = (int) $data->id;
        $this->_name = $data->name;
        $this->_position = (int) $data->position;
        $this->_deleted = ($data->deleted == '1') ? true : false;
        $this->_locked = ($data->locked == '1') ? true : false;
        $this->_archived = ($data->archived == '1') ? true : false;
        $this->_smart = ($data->smart == '1') ? true : false;
    }

    /**
     * Returns the identifier for the list.
     *
     * @return int
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Returns the name of the list.
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Returns the position of the list.
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->_position;
    }

    /**
     * Returns the filter setting for the list, or the empty string if the
     * list is not a Smart List.
     *
     * @return string
     */
    public function getFilter()
    {
        return $this->_filter;
    }

    /**
     * Returns whether or not the list has been deleted.
     *
     * @return bool TRUE if the list has been deleted, FALSE otherwise
     */
    public function isDeleted()
    {
        return $this->_deleted;
    }

    /**
     * Returns whether or not the list has been locked.
     *
     * @return bool TRUE if the list has been locked, FALSE otherwise
     */
    public function isLocked()
    {
        return $this->_locked;
    }

    /**
     * Returns whether or not the list has been archived.
     *
     * @return bool TRUE if the list has been archived, FALSE otherwise
     */
    public function isArchived()
    {
        return $this->_archived;
    }

    /**
     * Returns whether or not the list is a Smart List.
     *
     * @return bool TRUE if the list is a Smart List, FALSE otherwise
     */
    public function isSmart()
    {
        return $this->_smart;
    }
}
