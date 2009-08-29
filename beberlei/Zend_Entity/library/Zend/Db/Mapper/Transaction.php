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
 * @package    Zend_Db
 * @subpackage Mapper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once "Zend/Entity/Transaction.php";

/**
 * Zend_Db_Mapper implementation for the Zend_Entity_Transaction interface
 *
 * @uses       Zend_Entity_Transaction
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Mapper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Mapper_Transaction implements Zend_Entity_Transaction
{
    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db = null;

    /**
     * @var boolean
     */
    protected $_rollbackOnly = false;

    /**
     * @var boolean
     */
    protected $_isActive = false;

    /**
     * @param Zend_Db_Adapter_Abstract $db
     */
    public function __construct(Zend_Db_Adapter_Abstract $db)
    {
        $this->_db = $db;
    }

    /**
     * Begin a transaction
     *
     * @return void
     */
    public function begin()
    {
        if($this->_isActive == true) {
            require_once "Zend/Entity/IllegalStateException.php";
            throw new Zend_Entity_IllegalStateException();
        }

        $this->_db->beginTransaction();
        $this->_isActive = true;
    }

    /**
     * Commit a transaction, possibly flushing all enqeued changes to the database
     *
     * @return void
     */
    public function commit()
    {
        if($this->_isActive == false) {
            require_once "Zend/Entity/IllegalStateException.php";
            throw new Zend_Entity_IllegalStateException();
        }

        $this->_db->commit();
        $this->_rollbackOnly = false;
        $this->_isActive = false;
    }

    /**
     * Rollback the transaction
     *
     * @return void
     */
    public function rollback()
    {
        if($this->_isActive == false) {
            require_once "Zend/Entity/IllegalStateException.php";
            throw new Zend_Entity_IllegalStateException();
        }

        $this->_db->rollBack();
        $this->_rollbackOnly = false;
        $this->_isActive = false;
    }

    /**
     * Enforce a rollback for this transaction, even if {@link commit()} is called.
     *
     * @return void
     */
    public function setRollbackOnly()
    {
        $this->_rollbackOnly = true;
    }

    /**
     * Get current status of the rollback only property of the transaction.
     *
     * @return boolean
     */
    public function getRollbackOnly()
    {
        if($this->_isActive == false) {
            require_once "Zend/Entity/IllegalStateException.php";
            throw new Zend_Entity_IllegalStateException();
        }

        return $this->_rollbackOnly;
    }

    /**
     * Check wheater a transaction is currently active.
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->_isActive;
    }
}