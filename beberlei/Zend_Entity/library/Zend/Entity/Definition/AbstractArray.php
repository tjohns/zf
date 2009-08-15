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
 * @package    Entity
 * @subpackage Definition
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Abstract array property, representing for example an array of elements or objects.
 *
 * @uses       Zend_Entity_Definition_Property
 * @category   Zend
 * @package    Entity
 * @subpackage Definition
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Entity_Definition_AbstractArray extends Zend_Entity_Definition_Property
{
    /**
     * @var string
     */
    public $table;

    /**
     * @var string
     */
    public $key;

    /**
     * @var string
     */
    public $fetch = self::FETCH_LAZY;

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param string $collectionTable
     */
    public function setTable($collectionTable)
    {
        $this->table = $collectionTable;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getFetch()
    {
        return $this->fetch;
    }

    /**
     *
     * @param string $fetch
     */
    public function setFetch($fetch)
    {
        $this->fetch = $fetch;
        return $this;
    }
}