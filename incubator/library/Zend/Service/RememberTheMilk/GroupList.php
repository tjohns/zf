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
class Zend_Service_RememberTheMilk_GroupList implements IteratorAggregate
{
    /**
     * List of groups by identifier
     *
     * @var array
     */
    protected $_groupsById;

    /**
     * List of groups by name
     *
     * @var array
     */
    protected $_groupsByName;

    /**
     * Constructor to initialize the object with data.
     *
     * @param array $data Associative array containing data from an API
     *                    response
     * @return void
     */
    public function __construct($data)
    {
        $this->_groupsById = array();
        $this->_groupsByName = array();

        foreach ($data->groups as $group) {
            $group = new Zend_Service_RememberTheMilk_Group($group);
            $this->_groupsById[$group->getId()] = $group;
            $this->_groupsByName[$group->getName()] = $group;
        }
    }

    /**
     * Implementation of IteratorAggregate::getIterator().
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_groupsById);
    }

    /**
     * Implementation of IteratorAggregate::getLength().
     *
     * @return int
     */
    public function getLength()
    {
        return count($this->_groupsById);
    }

    /**
     * Returns the group instance with the specified identifier.
     *
     * @param int $id Identifier for the group
     * @return Zend_Service_RememberTheMilk_Group
     */
    public function getGroupById($id)
    {
        if (isset($this->_groupsById[$id])) {
            return $this->_groupsById[$id];
        }
        return null;
    }

    /**
     * Returns the group instance with the specified name.
     *
     * @param string $name Name of the group
     * @return Zend_Service_RememberTheMilk_Group
     */
    public function getGroupByName($name)
    {
        if (isset($this->_groupsByName[$name])) {
            return $this->_groupsByName[$name];
        }
        return null;
    }
}
