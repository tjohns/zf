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
 * @package    Zend_Entity
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Transactions are controlled through this interface which can be obtained from the Entity Manager.
 *
 * @category   Zend
 * @package    Zend_Entity
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Zend_Entity_Transaction
{
    /**
     * Begin a transaction
     *
     * @return void
     */
    public function begin();

    /**
     * Commit a transaction, possibly flushing all enqeued changes to the database
     *
     * @return void
     */
    public function commit();

    /**
     * Rollback the transaction
     *
     * @return void
     */
    public function rollback();

    /**
     * Enforce a rollback for this transaction, even if {@link commit()} is called.
     *
     * @return void
     */
    public function setRollbackOnly();

    /**
     * Get current status of the rollback only property of the transaction.
     *
     * @return boolean
     */
    public function getRollbackOnly();

    /**
     * Check wheater a transaction is currently active.
     *
     * @return boolean
     */
    public function isActive();
}