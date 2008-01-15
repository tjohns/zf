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
class Zend_Service_RememberTheMilk_TaskSeriesList implements IteratorAggregate
{
    /**
     * List of task series by identifier
     *
     * @var array
     */
    protected $_seriesById;

    /**
     * List of task series by name
     *
     * @var array
     */
    protected $_seriesByName;

    /**
     * Constructor to initialize the object with data
     *
     * @param array $data Associative array containing data from an API
     *                    response
     * @return void
     */
    public function __construct($data)
    {
        $this->_seriesById = array();
        $this->_seriesByName = array();

        foreach ($data as $series) {
            $series = new Zend_Service_RememberTheMilk_TaskSeries($series);
            $this->_seriesById[$series->getId()] = $series;
            $this->_seriesByName[$series->getName()] = $series;
        }
    }

    /**
     * Implementation of IteratorAggregate::getIterator()
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_series);
    }

    /**
     * Implementation of IteratorAggregate::getLength()
     *
     * @return int
     */
    public function getLength()
    {
        return count($this->_series);
    }

    /**
     * Returns a task series instance with the specified identifier.
     *
     * @param int $id Task series identifier
     * @return Zend_Service_RememberTheMilk_TaskSeries
     */
    public function getSeriesById($id)
    {
        if (isset($this->_seriesById[$id])) {
            return $this->_seriesById[$id];
        }
        return null;
    }

    /**
     * Returns a task series instance with the specified name.
     *
     * @param string $name Task series name
     * @return Zend_Service_RememberTheMilk_TaskSeries
     */
    public function getSeriesByName($name)
    {
        if (isset($this->_seriesByName[$name])) {
            return $this->_seriesByName[$name];
        }
        return null;
    }
}
