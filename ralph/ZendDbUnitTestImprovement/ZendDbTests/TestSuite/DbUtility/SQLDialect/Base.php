<?php

class Zend_Db_TestSuite_DbUtility_SQLDialect_Base
{
    
    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_dbAdapter = null;
    
    protected $_lastCreateTableIdentityName;
    protected $_lastCreateTableSequenceName;
    
    public function __construct()
    {
    }

    public function setDbAdapter(Zend_Db_Adapter_Abstract $dbAdapter)
    {
        $this->_dbAdapter = $dbAdapter;
    }
    
    public function supportsIfNotExists()
    {
        return false;
    }
    
    public function getCreateTableSQL($tableName, Array $columns = array())
    {
        $this->_lastCreateTableIdentityName = null;
        $this->_lastCreateTableSequenceName = null;
        $sql = $this->_getCreateTableSQLTableName($tableName);
        
        $sql .= " (\n\t";

        $pKey = null;
        $pKeys = array();
        if (isset($columns['PRIMARY KEY'])) {
            $pKey = $columns['PRIMARY KEY'];
            unset($columns['PRIMARY KEY']);
            foreach (explode(',', $pKey) as $pKeyCol) {
                $pKeys[] = $this->_dbAdapter->quoteIdentifier($pKeyCol, true);
            }
            $pKey = implode(', ', $pKeys);
        }

        $col = array();
        
        foreach ($columns as $columnName => $type) {
            $col[] = $this->_dbAdapter->quoteIdentifier($columnName, true) . ' ' . $this->_getCreateTableSQLColumnType($type);
        }

        if ($pKey) {
            $col[] = "PRIMARY KEY ($pKey)";
        }

        $sql .= implode(",\n\t", $col);
        $sql .= "\n)" . $this->_getCreateTableSQLTableType();
        
        $this->_getCreateTableSQLPostProcess($tableName, $col, $sql);
        
        return $sql;
    }
    
    public function getLastCreateTableSequenceName()
    {
        return $this->_lastCreateTableSequenceName;
    }
    
    public function getLastCreateTableIdentityName()
    {
        return $this->_lastCreateTableIdentityName;
    }
    
    public function getHasTableSQL($tableName)
    {
        return null;
    }
    
    public function getDeleteFromTableSQL($tableName)
    {
        return 'DELETE FROM ' . $this->_dbAdapter->quoteIdentifier($tableName, true);
    }
    
    public function getResetIdentitySQL($tableName, $identityName)
    {
        return null;
    }
    
    public function getDropTableSQL($tableName)
    {
        return 'DROP TABLE ' . $this->_dbAdapter->quoteIdentifier($tableName, true);
    }
    
    public function getCreateProcedureSQL($procedureName, $body)
    {
        return null;
    }
    
    public function getDropProcedureSQL($procedureName)
    {
        return null;
    }
    
    public function getHasSequenceSQL($sequenceName)
    {
        return null;
    }
    
    public function getCreateSequenceSQL($sequenceName)
    {
        return null;
    }

    public function getResetSequenceSQL($sequenceName)
    {
        return null;
    }
    
    public function getDropSequenceSQL($sequenceName)
    {
        return null;
    }
    
    public function getCreateViewSQL($viewName, $asStatement = 'SELECT * FROM', $fromTableName = null)
    {
        $sql = $this->_getCreateViewSQLViewName($viewName)
             . ' AS ' . $asStatement;

        if ($fromTableName) {
            $sql .= ' ' . $this->_dbAdapter->quoteIdentifier($fromTableName, true);
        }

        return $sql;
    }
    
    public function getHasViewSQL($viewName)
    {
        return null;
    }

    public function getDropViewSQL($viewName)
    {
        return 'DROP VIEW ' . $this->_dbAdapter->quoteIdentifier($viewName, true);
    }
    
    
    
    /**
     * getCreateTableSQL() helpers
     */
    
    protected function _getCreateTableSQLTableName($tableName)
    {
        return 'CREATE TABLE ' . $this->_dbAdapter->quoteIdentifier($tableName, true);
    }
    
    protected function _getCreateTableSQLColumnType($type)
    {
        return $type;
    }
    
    protected function _getCreateTableSQLTableType()
    {
        return '';
    }
    
    protected function getSqlCreateTableType()
    {
        return null;
    }

    protected function _getCreateTableSQLPostProcess($tableName, $columns, $sql)
    {
        return null;
    }
    
    /**
     * getCreateViewSQL() & getDropView
     */

    protected function _getCreateViewSQLViewName($viewName)
    {
        return 'CREATE VIEW ' . $this->_dbAdapter->quoteIdentifier($viewName, true);
    }
    
}