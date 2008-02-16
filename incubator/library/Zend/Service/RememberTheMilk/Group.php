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
class Zend_Service_RememberTheMilk_Group
{
    /**
     * Identifier for the group
     *
     * @var int
     */
    protected $_id;

    /**
     * Name of the group
     *
     * @var string
     */
    protected $_name;

    /**
     * List of identifiers for contacts in the group
     *
     * @var array
     */
    protected $_contacts;

    /**
     * Constructor to initialize the object with data.
     *
     * @param array $data Associative array containing data from an API
     *                    response
     * @return void
     */
    public function __construct($data)
    {
        $this->_id = (int) $data->id;
        $this->_name = $data->name;

        $this->_contacts = array();
        foreach ($data->contacts as $contact) {
            $this->_contacts[] = $contact->id;
        }
    }

    /**
     * Returns the identifier for the group.
     *
     * @return int
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Returns the name of the group.
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Returns a list of identifiers for contacts in the group.
     *
     * @return array
     */
    public function getContacts()
    {
        return $this->_contacts;
    }

    /**
     * Returns whether or not the group contains a specified contact.
     *
     * @param int $contact Identifier for the contact
     * @return bool TRUE if the group contains the contact, FALSE otherwise
     */
    public function hasContact($contact)
    {
        return in_array($contact, $this->_contacts);
    }
}
