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
class Zend_Service_RememberTheMilk_TimezoneList implements IteratorAggregate
{
    /**
     * List of timezones
     *
     * @var array
     */
    protected $_timezones;

    /**
     * Constructor to initialize the object with data
     *
     * @param array $data Associative array containing data from an API
     *                    response
     * @return void
     */
    public function __construct($data)
    {
        $this->_timezones = array();

        foreach ($data['timezones'] as $timezone) {
            $this->_timezones[] = new Zend_Service_RememberTheMilk_Timezone($timezone);
        }
    }

    /**
     * Implementation of IteratorAggregate::getIterator()
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_timezones);
    }

    /**
     * Implementation of IteratorAggregate::getLength()
     *
     * @return int
     */
    public function getLength()
    {
        return count($this->_timezones);
    }
}
