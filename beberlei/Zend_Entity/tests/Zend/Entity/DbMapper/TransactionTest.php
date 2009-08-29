<?php

class Zend_Entity_Mapper_TransactionTest extends Zend_Entity_Compliance_TransactionTestCase
{    
    public function createTransaction()
    {
        $dbTestAdapter = new Zend_Test_DbAdapter();
        return new Zend_Entity_Mapper_Transaction($dbTestAdapter);
    }
}