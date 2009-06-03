<?php

require_once 'Zend/Db/TestSuite/DbUtility/SQLDialect/Base.php';

class Zend_Db_TestSuite_DbUtility_SQLDialect_Sqlite extends Zend_Db_TestSuite_DbUtility_SQLDialect_Base
{
    
    protected function _getCreateTableSQLTableName($tableName)
    {
        return 'CREATE TABLE IF NOT EXISTS ' . $this->_dbAdapter->quoteIdentifier($tableName);
    }

    public function getDropTableSQL($tableName)
    {
        return 'DROP TABLE IF EXISTS ' . $this->_dbAdapter->quoteIdentifier($tableName);
    }
    
    public function getDeleteFromTableSQL($tableName)
    {
        $sqls = array(
            'DELETE FROM ' . $this->_dbAdapter->quoteIdentifier($tableName, true),
            'DELETE FROM sqlite_sequence WHERE name = ' . $this->_dbAdapter->quoteIdentifier($tableName, true)
            );
        return $sqls;
    }
    
    protected function _getCreateTableSQLColumnType($type)
    {
        if ($type == 'IDENTITY') {
            return 'INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT';
        }
        return $type;
    }

    protected function _getCreateViewSQLViewName($viewName)
    {
        return 'CREATE VIEW IF NOT EXISTS ' . $this->_dbAdapter->quoteIdentifier($viewName, true);
    }

    public function getDropViewSQL($viewName)
    {
        return 'DROP VIEW IF EXISTS ' . $this->_dbAdapter->quoteIdentifier($viewName, true);
    }
    
}