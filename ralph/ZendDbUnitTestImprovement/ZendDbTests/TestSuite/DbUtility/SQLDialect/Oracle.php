<?php

require_once 'Zend/Db/TestSuite/DbUtility/SQLDialect/Base.php';

class Zend_Db_TestSuite_DbUtility_SQLDialect_Oracle extends Zend_Db_TestSuite_DbUtility_SQLDialect_Base
{

    public function getSqlType($type)
    {
        if (preg_match('/VARCHAR(.*)/', $type, $matches)) {
            return 'VARCHAR2' . $matches[1];
        }
        if ($type == 'IDENTITY') {
            return 'NUMBER(11) PRIMARY KEY';
        }
        if ($type == 'INTEGER') {
            return 'NUMBER(11)';
        }
        if ($type == 'DATETIME') {
            return 'TIMESTAMP';
        }
        return $type;
    }

    protected function _getSqlCreateTable($tableName)
    {
        $tableList = $this->_db->fetchCol('SELECT UPPER(TABLE_NAME) FROM ALL_TABLES '
            . $this->_db->quoteInto(' WHERE UPPER(TABLE_NAME) = UPPER(?)', $tableName)
        );
        if (in_array(strtoupper($tableName), $tableList)) {
            return null;
        }
        return 'CREATE TABLE ' . $this->_db->quoteIdentifier($tableName, true);
    }

    protected function _getSqlDropTable($tableName)
    {
        $tableList = $this->_db->fetchCol('SELECT UPPER(TABLE_NAME) FROM ALL_TABLES '
            . $this->_db->quoteInto(' WHERE UPPER(TABLE_NAME) = UPPER(?)', $tableName)
        );
        if (in_array(strtoupper($tableName), $tableList)) {
            return 'DROP TABLE ' . $this->_db->quoteIdentifier($tableName, true);
        }
        return null;
    }

    protected function _getSqlCreateSequence($sequenceName)
    {
        $seqList = $this->_db->fetchCol('SELECT UPPER(SEQUENCE_NAME) FROM ALL_SEQUENCES '
            . $this->_db->quoteInto(' WHERE UPPER(SEQUENCE_NAME) = UPPER(?)', $sequenceName)
        );
        if (in_array(strtoupper($sequenceName), $seqList)) {
            return null;
        }
        return 'CREATE SEQUENCE ' . $this->_db->quoteIdentifier($sequenceName, true);
    }

    protected function _getSqlDropSequence($sequenceName)
    {
        $seqList = $this->_db->fetchCol('SELECT UPPER(SEQUENCE_NAME) FROM ALL_SEQUENCES '
            . $this->_db->quoteInto(' WHERE UPPER(SEQUENCE_NAME) = UPPER(?)', $sequenceName)
        );
        if (in_array(strtoupper($sequenceName), $seqList)) {
            return 'DROP SEQUENCE ' . $this->_db->quoteIdentifier($sequenceName, true);
        }
        return null;
    }

}
