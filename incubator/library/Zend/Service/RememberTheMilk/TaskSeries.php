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
class Zend_Service_RememberTheMilk_TaskSeries implements IteratorAggregate
{
    /**
     * Identifier for the task series
     *
     * @var int
     */
    protected $_id;

    /**
     * Name of the task series
     *
     * @var string
     */
    protected $_name;

    /**
     * Identifier for the list containing the task series
     *
     * @var int
     */
    protected $_listId;

    /**
     * Date the task series was created
     *
     * @var Zend_Date
     */
    protected $_createdDate;

    /**
     * Date the task series was last modified
     *
     * @var Zend_Date
     */
    protected $_modifiedDate;

    /**
     * Source responsible for creating the event
     *
     * @var string
     */
    protected $_source;

    /**
     * Identifier for the location associated with the task series
     *
     * @var int
     */
    protected $_locationId;

    /**
     * List of tags applied to the task series
     *
     * @var array
     */
    protected $_tags;

    /**
     * List of identifiers for participants in the task series
     *
     * @var array
     */
    protected $_participants;

    /**
     * List of identifiers for notes associated with the task series
     *
     * @var array
     */
    protected $_notes;

    /**
     * List of tasks associated with the task series
     *
     * @var Zend_Service_RememberTheMilk_TaskList
     */
    protected $_tasks;

    /**
     * Constructor to initialize the object with data
     *
     * @todo Add parsing for tags, participants, and notes in TaskSeries
     * @param array $data Associative array containing data from an API
     *                    response
     * @return void
     */
    public function __construct($data)
    {
        $this->_listId = $data->list->id;
        $data = $data->list->taskseries;
        $this->_id = (int) $data->id;
        $this->_createdDate = new Zend_Date($data->created);
        $this->_modifiedDate = new Zend_Date($data->modified);
        $this->_name = $data->name;
        $this->_source = $data->source;
        $this->_url = $data->url;
        $this->_locationId = $data->location_id;
        $this->_tags = $data->tags;
        $this->_participants = $data->participants;
        $this->_notes = new Zend_Service_RememberTheMilk_NoteList($data);
        $this->_tasks = new Zend_Service_RememberTheMilk_TaskList($data);
    }

    /**
     * Returns the identifier for the task series.
     *
     * @return int
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Returns the name of the task series.
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Returns the identifier for the list containing the task series.
     *
     * @return int
     */
    public function getListId()
    {
        return $this->_listId;
    }

    /**
     * Returns the creation date of the task series.
     *
     * @return Zend_Date
     */
    public function getCreatedDate()
    {
        return $this->_createdDate;
    }

    /**
     * Returns the last modification date of the task series.
     *
     * @return Zend_Date
     */
    public function getModifiedDate()
    {
        return $this->_modifiedDate;
    }

    /**
     * Returns the source responsible for creating the task series.
     *
     * @return string
     */
    public function getSource()
    {
        return $this->_source;
    }

    /**
     * Returns the identifier for the location associated with the task
     * series.
     *
     * @return int
     */
    protected function getLocationId()
    {
        return $this->_locationId;
    }

    /**
     * Returns a list of tags applied to the task series.
     *
     * @return array
     */
    public function getTags()
    {
        return $this->_tags;
    }

    /**
     * Returns a list of identifiers for participants in the task series.
     *
     * @return array
     */
    public function getParticipants()
    {
        return $this->_participants;
    }

    /**
     * Returns a list of notes associated with the task series.
     *
     * @return Zend_Service_RememberTheMilk_NoteList
     */
    public function getNoteList()
    {
        return $this->_notes;
    }

    /**
     * Returns a list of tasks associated with the task series
     *
     * @return Zend_Service_RememberTheMilk_TaskList
     */
    public function getTaskList()
    {
        return $this->_tasks;
    }

    /**
     * Implementation of IteratorAggregate::getIterator()
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return $this->_tasks->getIterator();
    }

    /**
     * Implementation of IteratorAggregate::getLength()
     *
     * @return int
     */
    public function getLength()
    {
        return $this->_tasks->getLength();
    }
}
