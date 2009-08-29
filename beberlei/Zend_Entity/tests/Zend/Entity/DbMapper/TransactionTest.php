<?php

class Zend_Entity_DbMapper_TransactionTest extends Zend_Entity_Compliance_TransactionTestCase
{    
    public function createTransaction()
    {
        $dbTestAdapter = new Zend_Test_DbAdapter();
        return new Zend_Db_Mapper_Transaction($dbTestAdapter);
    }
}