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
     * @var int
     */
    protected $_nestingLevelCounter = 0;

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
        if($this->isActive() == false) {
            $this->_db->beginTransaction();
        }
        $this->_nestingLevelCounter++;
    }

    /**
     * Commit a transaction, possibly flushing all enqeued changes to the database
     *
     * @return void
     */
    public function commit()
    {
        if($this->isActive() == false) {
            require_once "Zend/Entity/IllegalStateException.php";
            throw new Zend_Entity_IllegalStateException();
        }

        if($this->_nestingLevelCounter == 1) {
            if($this->_rollbackOnly == true) {
                $this->_db->rollBack();
            } else {
                $this->_db->commit();
            }
            $this->_rollbackOnly = false;
        }
        $this->_nestingLevelCounter--;
    }

    /**
     * Rollback the transaction
     *
     * @return void
     */
    public function rollback()
    {
        if($this->isActive() == false) {
            require_once "Zend/Entity/IllegalStateException.php";
            throw new Zend_Entity_IllegalStateException();
        }

        if($this->_nestingLevelCounter == 1) {
            $this->_db->rollBack();
            $this->_rollbackOnly = false;
        } else {
            $this->_rollbackOnly = true;
        }
        $this->_nestingLevelCounter--;
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
        if($this->isActive() == false) {
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
        return ($this->_nestingLevelCounter>0);
    }
}