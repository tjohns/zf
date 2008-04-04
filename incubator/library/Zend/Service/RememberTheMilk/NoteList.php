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
 * @version    $Id: $
 */


/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage RememberTheMilk
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_RememberTheMilk_NoteList implements IteratorAggregate
{
    /**
     * List of notes by identifier
     *
     * @var array
     */
    protected $_notesById;

    /**
     * List of notes by title
     *
     * @var array
     */
    protected $_notesByTitle;

    /**
     * Constructor to initialize the object with data.
     *
     * @param array $data Associative array containing data from an API
     *                    response
     * @return void
     */
    public function __construct($data)
    {
        $this->_notesById = array();
        $this->_notesByTitle = array();

        foreach ($data->notes as $note) {
            $note = new Zend_Service_RememberTheMilk_Note($note);
            $this->_notesById[$note->getId()] = $note;
            $this->_notesByTitle[$note->getTitle()] = $note;
        }
    }

    /**
     * Implementation of IteratorAggregate::getIterator().
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_notesById);
    }

    /**
     * Implementation of IteratorAggregate::getLength().
     *
     * @return int
     */
    public function getLength()
    {
        return count($this->_notesById);
    }

    /**
     * Returns the note instance with the specified identifier.
     *
     * @param int $id Note identifier
     * @return Zend_Service_RememberTheMilk_Note
     */
    public function getNoteById($id)
    {
        return $this->_notesById[$id];
    }

    /**
     * Returns the note instance with the specified title.
     *
     * @param string $title Note title
     * @return Zend_Service_RememberTheMilk_Note
     */
    public function getNoteByTitle($title)
    {
        return $this->_notesByTitle[$title];
    }
}
