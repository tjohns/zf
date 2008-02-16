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
class Zend_Service_RememberTheMilk_Note
{
    /**
     * Identifier for the note
     *
     * @var int
     */
    protected $_id;

    /**
     * Creation date of the note
     *
     * @var Zend_Date
     */
    protected $_createdDate;

    /**
     * Modification date of the note
     *
     * @var Zend_Date
     */
    protected $_modifiedDate;

    /**
     * Title of the note
     *
     * @var string
     */
    protected $_title;

    /**
     * Text of the note
     *
     * @var string
     */
    protected $_text;

    /**
     * Constructor to initialize the object with data
     *
     * @todo Check parsing for Note::_text
     * @param array $data Associative array containing data from an API
     *                    response
     * @return void
     */
    public function __construct($data)
    {
        $data = $data->note;
        $this->_id = (int) $data->id;
        $this->_createdDate = new Zend_Date($data->created);
        $this->_modifiedDate = new Zend_Date($data->modified);
        $this->_title = $data->title;
        $this->_text = $data->note;
    }

    /**
     * Returns the identifier for the note.
     *
     * @return int
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Returns the creation date of the note.
     *
     * @return Zend_Date
     */
    public function getCreatedDate()
    {
        return $this->_createdDate;
    }

    /**
     * Returns the last modification date of the note.
     *
     * @return Zend_Date
     */
    public function getModifiedDate()
    {
        return $this->_modifiedDate;
    }

    /**
     * Returns the title of the note.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Returns the text of the note.
     *
     * @return string
     */
    public function getText()
    {
        return $this->_text;
    }
}