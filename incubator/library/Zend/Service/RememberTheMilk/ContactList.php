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
class Zend_Service_RememberTheMilk_ContactList implements IteratorAggregate
{
    /**
     * List of contacts by identifier
     *
     * @var array
     */
    protected $_contactsById;

    /**
     * List of contacts by username
     *
     * @var array
     */
    protected $_contactsByUsername;

    /**
     * Constructor to initialize the object with data.
     *
     * @param array $data Associative array containing data from an API
     *                    response
     * @return void
     */
    public function __construct($data)
    {
        $this->_contactsById = array();
        $this->_contactsByUsername = array();

        foreach ($data->contacts as $contact) {
            $contact = new Zend_Service_RememberTheMilk_Contact($contact);
            $this->_contactsById[$contact->getId()] = $contact;
            $this->_contactsByUsername[$contact->getUsername()] = $contact;
        }
    }

    /**
     * Implementation of IteratorAggregate::getIterator().
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_contactsById);
    }

    /**
     * Implementation of IteratorAggregate::getLength().
     *
     * @return int
     */
    public function getLength()
    {
        return count($this->_contactsById);
    }

    /**
     * Returns the contact instance with the specified identifier.
     *
     * @param int $id Identifier for the contact
     * @return Zend_Service_RememberTheMilk_Contact
     */
    public function getContactById($id)
    {
        if (isset($this->_contactsById[$id])) {
            return $this->_contactsById[$id];
        }
        return null;
    }

    /**
     * Returns the contact instance with the specified username.
     *
     * @param string $username Username for the contact
     * @return Zend_Service_RememberTheMilk_Contact
     */
    public function getContactByUsername($username)
    {
        if (isset($this->_contactsByUsername[$username])) {
            return $this->_contactsByUsername[$username];
        }
        return null;
    }
}
