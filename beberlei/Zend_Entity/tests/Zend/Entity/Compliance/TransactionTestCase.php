<?php

abstract class Zend_Entity_Compliance_TransactionTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @return Zend_Entity_Transaction
     */
    abstract public function createTransaction();

    public function testIsNotActiveByDefault()
    {
        $t = $this->createTransaction();
        $this->assertFalse($t->isActive());
    }

    public function testBeginTransactionSetsIsActive()
    {
        $t = $this->createTransaction();
        $t->begin();

        $this->assertTrue($t->isActive());
    }

    public function testCommitSetsInactive()
    {
        $t = $this->createTransaction();
        $t->begin();
        $t->commit();

        $this->assertFalse($t->isActive());
    }

    public function testRollbackSetsInactive()
    {
        $t = $this->createTransaction();
        $t->begin();
        $t->rollback();

        $this->assertFalse($t->isActive());
    }

    public function testCommit_IfInactive_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_IllegalStateException");

        $t = $this->createTransaction();
        $t->commit();
    }
    
    public function testRollback_IfInactive_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_IllegalStateException");

        $t = $this->createTransaction();
        $t->rollback();
    }

    public function testBeginTwice_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_IllegalStateException");

        $t = $this->createTransaction();
        $t->begin();
        $t->begin();
    }

    public function testIsNotRollback_ThrowsException_IfIsActiveIsFalse()
    {
        $this->setExpectedException("Zend_Entity_IllegalStateException");

        $t = $this->createTransaction();
        $t->getRollbackOnly();
    }

    public function testIsNotRollbackOnlyByDefault()
    {
        $t = $this->createTransaction();
        $t->begin();
        
        $this->assertFalse($t->getRollbackOnly());
    }

    public function testSetRollbackOnly()
    {
        $t = $this->createTransaction();
        $t->begin();
        $t->setRollbackOnly();
        
        $this->assertTrue($t->getRollbackOnly());
    }

    public function testCommitResetsRollbackOnly()
    {
        $t = $this->createTransaction();
        $t->begin();
        $t->setRollbackOnly();
        $t->commit();

        $t->begin();
        $this->assertFalse($t->getRollbackOnly());
    }

    public function testRollbackRestsRollbackOnly()
    {
        $t = $this->createTransaction();
        $t->begin();
        $t->setRollbackOnly();
        $t->rollBack();

        $t->begin();
        $this->assertFalse($t->getRollbackOnly());
    }
}