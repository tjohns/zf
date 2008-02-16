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
class Zend_Service_RememberTheMilk_TaskList implements IteratorAggregate
{
    /**
     * List of tasks by identifier
     *
     * @var array
     */
    protected $_tasksById;

    /**
     * Constructor to initialize the object with data.
     *
     * @param array $data Associative array containing data from an API
     *                    response
     * @return void
     */
    public function __construct($data)
    {
        $this->_tasks = array();

        if (is_array($data->task)) {
            foreach ($data->task as $task) {
                $task = new Zend_Service_RememberTheMilk_Task($task);
                $this->_tasksById[$task->getId()] = $task;
            }
        } else {
            $task = new Zend_Service_RememberTheMilk_Task($data->task);
            $this->_tasksById[$task->getId()] = $task;
        }
    }

    /**
     * Implementation of IteratorAggregate::getIterator().
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_tasksById);
    }

    /**
     * Implementation of IteratorAggregate::getLength().
     *
     * @return int
     */
    public function getLength()
    {
        return count($this->_tasksById);
    }

    /**
     * Returns the task instance with the specified identifier.
     *
     * @param int $id Task identifier
     */
    public function getTaskById($id)
    {
        return $this->_tasksById[$id];
    }
}
