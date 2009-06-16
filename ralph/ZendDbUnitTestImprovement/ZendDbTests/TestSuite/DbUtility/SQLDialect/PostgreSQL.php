<?php

require_once 'Zend/Db/TestSuite/DbUtility/SQLDialect/Base.php';

class Zend_Db_TestSuite_DbUtility_SQLDialect_PostgreSQL extends Zend_Db_TestSuite_DbUtility_SQLDialect_Base
{

    

    public function supportsIfNotExists()
    {
        return false;
    }
    
    protected function _getCreateTableSQLColumnType($type)
    {
        if ($type == 'IDENTITY') {
            return 'SERIAL PRIMARY KEY';
        }
        if ($type == 'DATETIME') {
            return 'TIMESTAMP';
        }
        if ($type == 'CLOB') {
            return 'TEXT';
        }
        if ($type == 'BLOB') {
            return 'TEXT';
        }
        return $type;
    }
    
    protected function _getCreateTableSQLPostProcess($tableName, $columns, $sql)
    {
        foreach ($columns as $col) {
            if (strpos($col, 'SERIAL PRIMARY KEY')) {
                $target = str_replace(array('\'', '"', '`'), '', $col);
                $this->_lastCreateTableSequenceName = $tableName . '_' . substr($target, 0, strpos($target, ' ')) . '_seq';
                return;
            }
        }
    }
    
    public function getHasTableSQL($tableName)
    {
        $sql = $this->_dbAdapter->quoteInto(
            'SELECT ((SELECT relname FROM pg_class WHERE relkind = \'r\' AND relname = ?) IS NOT NULL) as table_exists',
            $tableName
            );
        return $sql;
    }
    
    public function getHasViewSQL($viewName)
    {
        $sql = $this->_dbAdapter->quoteInto(
            'SELECT ((SELECT relname FROM pg_class WHERE relkind = \'v\' AND relname = ?) IS NOT NULL) as view_exists',
            $viewName
            );
        return $sql;
    }
    
    public function getDropTableSQL($tableName)
    {
        return parent::getDropTableSQL($tableName) . ' CASCADE';
    }
    
    public function getHasSequenceSQL($sequenceName)
    {
        $sql = $this->_dbAdapter->quoteInto(
            'SELECT ((SELECT relname FROM pg_class WHERE relkind = \'S\' AND relname = ?) IS NOT NULL) as sequence_exists',
            $sequenceName
            );
        return $sql;
    }
    
    public function getResetSequenceSQL($sequenceName)
    {
        return 'ALTER SEQUENCE ' . $this->_dbAdapter->quoteIdentifier($sequenceName) . ' RESTART WITH 1';
    }
    

    public function getDeleteFromTableSQL($tableName)
    {
        return 'TRUNCATE TABLE ' . $this->_dbAdapter->quoteIdentifier($tableName, true) . ' CASCADE';
    }
    
    
    
    
    /*
    protected function _getCreateTableSQLTableName($tableName)
    {
        return 'CREATE TABLE IF NOT EXISTS ' . $this->_dbAdapter->quoteIdentifier($tableName);
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
    */

}
