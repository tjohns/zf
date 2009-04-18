<?php

class Zend_Test_PHPUnit_Database_DataSet_QueryTable extends PHPUnit_Extensions_Database_DataSet_QueryTable
{
    protected function loadData()
    {
        if($this->data === null) {
            $stmt = $this->databaseConnection->getConnection()->query($this->query);
            $this->data = $stmt->fetchAll(Zend_Db::FETCH_ASSOC);
        }
    }
}