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
class Zend_Service_RememberTheMilk_ListList implements IteratorAggregate
{
    /**
     * List of lists by identifier
     *
     * @var array
     */
    protected $_listsById;

    /**
     * List of lists by name
     *
     * @var array
     */
    protected $_listsByName;

    /**
     * Constructor to initialize the object with data.
     *
     * @param array $data Associative array containing data from an API
     *                    response
     * @return void
     */
    public function __construct($data)
    {
        $this->_listsById = array();
        $this->_listsByName = array();

        foreach ($data->lists->list as $list) {
            $list = new Zend_Service_RememberTheMilk_List($list);
            $this->_listsById[$list->getId()] = $list;
            $this->_listsByName[$list->getName()] = $list;
        }
    }

    /**
     * Implementation of IteratorAggregate::getIterator().
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_listsById);
    }

    /**
     * Implementation of IteratorAggregate::getLength().
     *
     * @return int
     */
    public function getLength()
    {
        return count($this->_listsById);
    }

    /**
     * Returns the list instance with the specified identifier.
     *
     * @param int $id Identifier for the list
     * @return Zend_Service_RememberTheMilk_List
     */
    public function getListById($id)
    {
        if (isset($this->_listsById[$id])) {
            return $this->_listsById[$id];
        }
        return null;
    }

    /**
     * Returns the list instance with the specified name.
     *
     * @param string $name Name of the list
     * @return Zend_Service_RememberTheMilk_List
     */
    public function getListByName($name)
    {
        if (isset($this->_listsByName[$name])) {
            return $this->_listsByName[$name];
        }
        return null;
    }
}
