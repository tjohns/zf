<?php

require_once 'Zend/Db/TestSuite/DbUtility/SQLDialect/Base.php';

class Zend_Db_TestSuite_DbUtility_SQLDialect_MySQL extends Zend_Db_TestSuite_DbUtility_SQLDialect_Base
{

    public function supportsIfNotExists()
    {
        return true;
    }
    
    protected function _getCreateTableSQLTableName($tableName)
    {
        return 'CREATE TABLE IF NOT EXISTS ' . $this->_dbAdapter->quoteIdentifier($tableName);
    }

    protected function _getCreateTableSQLColumnType($type)
    {
        if ($type == 'IDENTITY') {
            return 'INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT';
        }
        if ($type == 'CLOB') {
            return 'TEXT';
        }
        return $type;
    }
    
    protected function _getCreateTableSQLTableType()
    {
        return ' ENGINE=InnoDB';
    }

    public function getDeleteFromTableSQL($tableName)
    {
        return 'TRUNCATE TABLE ' . $this->_dbAdapter->quoteIdentifier($tableName, true);
    }
    
    public function getDropTableSQL($tableName)
    {
        return 'DROP TABLE IF EXISTS ' . $this->_dbAdapter->quoteIdentifier($tableName);
    }
    
    public function getCreateProcedureSQL($procedureName, $body)
    {
        $sql = 'CREATE PROCEDURE ' . $procedureName . ' ' . $body;
        return $sql;
    }
    
    public function getDropProcedureSQL($procedureName)
    {
        return 'DROP PROCEDURE IF EXISTS ' . $this->_dbAdapter->quoteIdentifier($procedureName);
    }
    
    protected function _getCreateViewSQLViewName($viewName)
    {
        return 'CREATE OR REPLACE VIEW ' . $this->_dbAdapter->quoteIdentifier($viewName, true);
    }

    public function getDropViewSQL($viewName)
    {
        return 'DROP VIEW IF EXISTS ' . $this->_dbAdapter->quoteIdentifier($viewName, true);
    }

}
