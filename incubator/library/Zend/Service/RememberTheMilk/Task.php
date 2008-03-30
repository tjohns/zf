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
class Zend_Service_RememberTheMilk_Task
{
    /**
     * Identifier for the task
     *
     * @var int
     */
    protected $_id;

    /**
     * Due date of the task
     *
     * @var Zend_Date
     */
    protected $_dueDate;

    /**
     * Whether or not a time is included in the task due date
     *
     * @var bool
     */
    protected $_hasDueTime;

    /**
     * Date when the task was created
     *
     * @var Zend_Date
     */
    protected $_addedDate;

    /**
     * Date when the task was marked as completed
     *
     * @var Zend_Date
     */
    protected $_completedDate;

    /**
     * Date when the task was deleted
     *
     * @var Zend_Date
     */
    protected $_deletedDate;

    /**
     * Priority of the task
     *
     * @var int
     */
    protected $_priority;

    /**
     * Whether or not the task has been postponed
     *
     * @var bool
     */
    protected $_isPostponed;

    /**
     * Estimate for the task
     *
     * @var string
     */
    protected $_estimate;

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
        if (!empty($data->due)) {
            $this->_dueDate = new Zend_Date($data->due);
        }
        $this->_hasDueTime = ($data->has_due_time == '1') ? true : false;
        if (!empty($data->added)) {
            $this->_addedDate = new Zend_Date($data->added);
        }
        $this->_completed = ($data->completed == '1') ? true : false;
        $this->_deleted = ($data->deleted == '1') ? true : false;
        $this->_priority = ($data->priority == 'N') ? null : (int) $priority;
        $this->_postponed = ($data->postponed == '1') ? true : false;
        $this->_estimate = $data->estimate;
    }

    /**
     * Returns the identifier for the task.
     *
     * @return int
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Returns the due date of the task.
     *
     * @return Zend_Date Due date of the task, or NULL if the task has no due
     *                   date
     */
    public function getDueDate()
    {
        return $this->_dueDate;
    }

    /**
     * Returns whether or not a time is included in the task due date.
     *
     * @return bool FALSE if the task has no due date or if its due date does
     *              not include a time, TRUE otherwise
     */
    public function hasDueTime()
    {
        return $this->_hasDueTime;
    }

    /**
     * Returns the date when the task was created.
     *
     * @return Zend_Date
     */
    public function getAddedDate()
    {
        return $this->_addedDate;
    }

    /**
     * Returns the date when the task was marked as completed.
     *
     * @return Zend_Date Date when the task was marked as completed, or NULL
     *                   if the task has not been marked as completed
     */
    public function getCompletedDate()
    {
        return $this->_completedDate;
    }

    /**
     * Returns the date when the task was deleted.
     *
     * @return Zend_Date Date when the task was deleted, or NULL if the task
     *                   has not been deleted
     */
    public function getDeletedDate()
    {
        return $this->_deletedDate;
    }

    /**
     * Returns the priority of the task.
     *
     * @return int Priority of the task, or NULL if the task has not been
     *             assigned a priority level
     */
    public function getPriority()
    {
        return $this->_priority;
    }

    /**
     * Returns whether or not the task has been postponed.
     *
     * @return bool TRUE if the task has been postponed, FALSE otherwise
     */
    public function isPostponed()
    {
        return $this->_isPostponed;
    }

    /**
     * Returns the estimate for the task.
     *
     * @return string
     */
    public function getEstimate()
    {
        return $this->_estimate;
    }
}
