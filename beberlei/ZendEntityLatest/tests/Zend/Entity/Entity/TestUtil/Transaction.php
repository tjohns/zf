<?php

class Zend_Entity_TestUtil_Transaction implements Zend_Entity_Transaction
{
    public $beginCalled = 0;
    public $commitCalled = 0;
    public $rollbackCalled = 0;
    public $rollbackOnly = false;

    /**
     * Begin a transaction
     *
     * @return void
     */
    public function begin()
    {
        $this->beginCalled++;
    }

    /**
     * Commit a transaction, possibly flushing all enqeued changes to the database
     *
     * @return void
     */
    public function commit()
    {
        $this->commitCalled++;
    }

    /**
     * Rollback the transaction
     *
     * @return void
     */
    public function rollback()
    {
        $this->rollbackCalled++;
    }

    /**
     * Enforce a rollback for this transaction, even if {@link commit()} is called.
     *
     * @return void
     */
    public function setRollbackOnly()
    {
        $this->rollbackOnly = true;
    }

    /**
     * Get current status of the rollback only property of the transaction.
     *
     * @return boolean
     */
    public function getRollbackOnly()
    {
        return $this->rollbackOnly;
    }

    /**
     * Check wheater a transaction is currently active.
     *
     * @return boolean
     */
    public function isActive()
    {
        return ($this->beginCalled>$this->commitCalled);
    }
}