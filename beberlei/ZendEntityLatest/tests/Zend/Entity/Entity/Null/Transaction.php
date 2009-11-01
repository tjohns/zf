<?php

class Zend_Entity_Null_Transaction implements Zend_Entity_Transaction
{
    /**
     * Begin a transaction
     *
     * @return void
     */
    public function begin()
    {

    }

    /**
     * Commit a transaction, possibly flushing all enqeued changes to the database
     *
     * @return void
     */
    public function commit()
    {

    }

    /**
     * Rollback the transaction
     *
     * @return void
     */
    public function rollback()
    {

    }

    /**
     * Enforce a rollback for this transaction, even if {@link commit()} is called.
     *
     * @return void
     */
    public function setRollbackOnly()
    {

    }

    /**
     * Get current status of the rollback only property of the transaction.
     *
     * @return boolean
     */
    public function getRollbackOnly()
    {

    }

    /**
     * Check wheater a transaction is currently active.
     *
     * @return boolean
     */
    public function isActive()
    {
        
    }
}