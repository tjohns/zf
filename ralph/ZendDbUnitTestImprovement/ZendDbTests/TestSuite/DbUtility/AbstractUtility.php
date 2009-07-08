<?php

require_once 'SQLDialect/Base.php';

abstract class Zend_Db_TestSuite_DbUtility_AbstractUtility
{
    
    /**
     * @var Zend_Db_TestSuite_AbstractTestSuite
     */
    protected $_testSuite = null;
    
    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_dbAdapter = null;
    
    /**
     * @var bool
     */
    protected $_canManageResources = true;
    
    /**
     * @var Zend_Db_TestSuite_DbUtility_SQLDialect_Base
     */
    protected $_sqlDialect = null;
    
    /**
     * @var string
     */
    protected $_resourcePrefix = 'zf_';
    
    /**
     * @var array Array of resources that have been created by this utility
     */
    protected $_createdResources = array(
        'tables'     => array(),
        'identities' => array(),
        'procedures' => array(),
        'sequences'  => array(),
        'views'      => array()
        );
    
    /**
     * __construct()
     *
     * @param Zend_Db_TestSuite_AbstractTestSuite $testSuite
     */
    public function __construct(Zend_Db_TestSuite_AbstractTestSuite $testSuite)
    {
        $this->_testSuite = $testSuite;
        $this->_dbAdapter = $this->createDbAdapter();

        if (!$this->_dbAdapter instanceof Zend_Db_Adapter_Abstract) {
            throw new Exception('The Db Adapter selected for this utility class is not of type Zend_Db_Adapter_Abstract');
        }
        
        $this->_sqlDialect = $this->getSQLDialect();
        
        if (!$this->_sqlDialect instanceof Zend_Db_TestSuite_DbUtility_SQLDialect_Base) {
            throw new Exception('The SQL Dialect selected for this utility class is not of type Zend_Db_TestSuite_SQLDialect_Base');
        }
        
        $this->_sqlDialect->setDbAdapter($this->_dbAdapter);
    }
    
    /**
     * getDriverConfigurationAsParams()
     * 
     * @return array
     */
    abstract public function getDriverConfigurationAsParams();
    
    /**
     * _executeRawQuery() 
     * 
     * This method shall be defined by the implementing class, specific to the target vendor database
     *
     * @param string $sql
     */
    abstract protected function _executeRawQuery($sql);

    /**
     * Enter description here...
     *
     * @param string|array $sql A string or array of sql statements
     * @param string $caller
     * @return true
     */
    public function executeRawQuery($sql, $caller = null)
    {
        if (!is_array($sql)) {
            $sql = array($sql);
        }
        
        foreach ($sql as $s) {
            $result = $this->_executeRawQuery($s);
            if ($result === false) {
                $exceptionMessage = "Statement failed:\n$s\nError: " . $this->_dbAdapter->getConnection()->error;
                if ($caller) {
                    $exceptionMessage .= "\nFrom Caller: " . $caller; 
                }
                throw new Zend_Db_Exception($exceptionMessage);
            }
        }
        
        return true;
    }
    
    /**
     * createDbAdapter()
     *
     * @param array $params
     * @param bool $mergeSuppliedParams
     * @return unknown
     */
    public function createDbAdapter(array $params = array(), $mergeSuppliedParams = true)
    {
        $adapterParams = $this->getDriverConfigurationAsParams();
        
        if ($params) {
             $adapterParams = ($mergeSuppliedParams) ? array_merge($adapterParams, $params) : $params;
        }
        
        return Zend_Db::factory($this->_testSuite->getDriverName(), $adapterParams);
    }
    
    /**
     * getSQLDialect()
     *
     * @return Zend_Db_TestSuite_SQLDialect_Base
     */
    public function getSQLDialect()
    {
        return new Zend_Db_TestSuite_DbUtility_SQLDialect_Base();
    }
    
    /**
     * setDbAdapter()
     *
     * @param Zend_Db_Adapter_Abstract $dbAdapter
     */
    public function setDbAdapter(Zend_Db_Adapter_Abstract $dbAdapter)
    {
        $this->_dbAdapter = $dbAdapter;
        $this->_sqlDialect->setDbAdapter($this->_dbAdapter);
    }
    
    /**
     * getDbAdapter()
     *
     * @return Zend_Db_Adapter_Abstract
     */
    public function getDbAdapter()
    {
        return $this->_dbAdapter;
    }
    
    /**
     * getDriverName()
     *
     * @return string
     */
    public function getDriverName()
    {
        return $this->_testSuite->getDriverName();
    }
    
    /**
     * getSchema()
     *
     * @return string
     */
    public function getSchema()
    {
        $param = $this->getDriverConfigurationAsParams();

        if (isset($param['dbname']) && strpos($param['dbname'], ':') === false) {
            return $param['dbname'];
        }

        return null;
    }
    
    /**
     * createDefaultSchema()
     *
     * @return Zend_Db_TestSuite_DbUtility_AbstractUtility
     */
    public function createDefaultResources()
    {
        if ($this->_canManageResources === false) {
            throw new Exception('This utilty cannot create, delete or alter database resources.');
        }
        
        $this->createArbitraryUtilityResources();
        
        $defaultSchemaArray = $this->_getDefaultResourceArray();
        
        foreach ($defaultSchemaArray['tables'] as $tableInfo) {
            $this->createTable($tableInfo['tableId'], $tableInfo['tableName'], $tableInfo['columns'], true);
        }
        
        foreach ($defaultSchemaArray['procedures'] as $procedureInfo) {
            $this->createProcedure($procedureInfo['procedureName'], $procedureInfo['body'], true);
        }
        
        foreach ($defaultSchemaArray['views'] as $viewInfo) {
            $this->createView($viewInfo['viewName'], $viewInfo['asStatement'], $viewInfo['fromTableName'], true);
        }
        
//        foreach ($defaultSchemaArray['sequences'] as $sequenceInfo) {
//            $this->createSequence($sequenceInfo['name'], true);
//        }
        
        return $this;
    }
    
    /**
     * setCanManageResources()
     *
     * @param bool $canManageResources
     * @return Zend_Db_TestSuite_DbUtility_AbstractUtility
     */
    public function setCanManageResources($canManageResources)
    {
        $this->_canManageResources = (bool) $canManageResources;
        return $this;
    }
    
    /**
     * loadDefaultTableData()
     *
     * @return Zend_Db_TestSuite_DbUtility_AbstractUtility
     */
    public function loadDefaultTableData()
    {
        $defaultDataArray = $this->_getDefaultTableDataArray();
        foreach ($defaultDataArray as $tableId => $data) {
            $this->populateData($tableId, $data);
        }
        return $this;
    }
    
    public function deleteTableData($tableId = null)
    {
        if ($tableId === null) {
            foreach ($this->_createdResources['tables'] as $tableId => $tableName) {
                $this->deleteTableDataByTableName($tableName, false, false);
            }
        }
        
        if (!array_key_exists($tableId, $this->_createdResources['tables'])) {
            return false;
        }
        
        return $this->deleteTableDataByTableName($this->_createdResources['tables'][$tableId], false, false);
    }
    
    public function deleteTableDataByTableName($tableName, $forceIfUtilityUnknown = false, $includeResourcePrefix = true)
    {
        if ($includeResourcePrefix) {
            $tableName = $this->_resourcePrefix . $tableName;
        }
        
        if (!$forceIfUtilityUnknown && !array_search($tableName, $this->_createdResources['tables'])) {
            return;
        }
        
        $sql = $this->_sqlDialect->getDeleteFromTableSQL($tableName);
        $result = $this->executeRawQuery($sql, __METHOD__);
    }
    
    /**
     * resetState()
     *
     * @return Zend_Db_TestSuite_DbUtility_AbstractUtility
     */
    public function resetState()
    {
        $this->_dbAdapter->setFetchMode(Zend_Db::FETCH_ASSOC);
        $this->_dbAdapter->setProfiler(null);
        foreach ($this->_createdResources['tables'] as $tableId => $tableName) {
            $this->deleteTableData($tableId);
        }
        
        foreach ($this->_createdResources['identities'] as $tableName => $identityName) {
            $this->resetIdentity($tableName);
        }

        foreach ($this->_createdResources['sequences'] as $sequenceName) {
            $this->resetSequence($sequenceName, true, false);
        }

        return $this;
    }

    /**
     * cleanupResources()
     *
     * @return Zend_Db_TestSuite_DbUtility_AbstractUtility
     */
    public function cleanupResources()
    {
        if ($this->_canManageResources === false) {
            throw new Exception('This utilty cannot create, delete or alter database resources.');
        }
        
        foreach ($this->_createdResources['tables'] as $tableId => $tableName) {
            $this->dropTableByName($tableName, false, false);
        }
        
        foreach ($this->_createdResources['procedures'] as $procedureName) {
            $this->dropProcedure($procedureName, false, false);
        }
        
        foreach ($this->_createdResources['views'] as $viewName) {
            $this->dropView($viewName, false, false);
        }
        
        foreach ($this->_createdResources['sequences'] as $sequenceName) {
            $this->dropSequence($sequenceName);
        }
        
        $this->_createdResources = array(
            'tables'     => array(),
            'identities' => array(),
            'procedures' => array(),
            'views'      => array(),
            'sequences'  => array()
            );

        $this->dropArbitraryUtilityResources();
            
        return $this;
    }
    
    /**
     * getTableName()
     *
     * @param string $tableId
     * @return string|false
     */
    public function getTableName($tableId)
    {
        if (!isset($this->_createdResources['tables'][$tableId])) {
            return false;
        }
        
        return $this->_createdResources['tables'][$tableId];
    }
    
    /**
     * createArbitraryUtilityResources()
     * 
     * This should be overrridden by vendor specific implementations 
     *
     * @return null
     */
    public function createArbitraryUtilityResources()
    {
        return null;
    }
    
    /**
     * dropArbitraryUtilityResources()
     *
     * @return null
     */
    public function dropArbitraryUtilityResources()
    {
        return null;
    }
    
    /**
     * createTable()
     *
     * Column Format:
     * array(
     *   'product_id'    => 'INTEGER NOT NULL',
     *   'price_name'    => 'VARCHAR(100)',
     *   'price_total'   => 'DECIMAL(10,2) NOT NULL',
     *   'PRIMARY KEY'   => 'product_id'
     *   )
     * 
     * @param string $tableId
     * @param string $tableName Table name (might be subject to prefix)
     * @param array $columns (the columns in format above)
     * @param bool $includeResourcePrefix
     * @return string Actual table name
     */
    public function createTable($tableId, $tableName, array $columns, $includeResourcePrefix = true)
    {
        if ($this->_canManageResources === false) {
            throw new Exception('This utilty cannot create, delete or alter database resources.');
        }
        
        if ($tableName == null) {
            $tableName = $tableId;
        }
        
        if (is_array($tableName)) {
            throw new Exception('$tableName in createTable() should be a string');
        }
        
        if ($includeResourcePrefix) {
            $tableName = $this->_resourcePrefix . $tableName;
        }
        
        if (!$this->_sqlDialect->supportsIfNotExists() && $this->hasTableByName($tableName, false)) {
            $this->dropTableByName($tableName, true, false);
        }
        
        $sql = $this->_sqlDialect->getCreateTableSQL($tableName, $columns);
        
        if (($identityName = $this->_sqlDialect->getLastCreateTableIdentityName()) != '') {
            $this->_createdResources['identities'][$tableName] = $identityName;
        }
        
        if (($sequenceName = $this->_sqlDialect->getLastCreateTableSequenceName()) != '') {
            $this->_createdResources['sequences'][] = $sequenceName;
        }
        
        $result = $this->executeRawQuery($sql, __METHOD__);
        
        $this->_createdResources['tables'][$tableId] = $tableName;
        
        return $tableName;
    }

    public function hasTable($tableId)
    {
        $tableName = $this->getTableName($tableId);
        return $this->hasTableByName($tableName, false);
    }
    
    public function hasTableByName($tableName, $includeResourcePrefix = true)
    {
        
        if ($includeResourcePrefix) {
            $tableName = $this->_resourcePrefix . $tableName;
        }
        
        $sql = $this->_sqlDialect->getHasTableSQL($tableName);
        
        if (!$sql) {
            return null;
        }
        
        $result = $this->_dbAdapter->query($sql);
        return (bool) $result->fetchColumn();
    }
    
    public function resetIdentity($tableName)
    {
        if (!array_key_exists($tableName, $this->_createdResources['identities'])) {
            return;
        }
        
        $sql = $this->_sqlDialect->getResetIdentitySQL($tableName, $this->_createdResources['identities'][$tableName]);
        $result = $this->executeRawQuery($sql, __METHOD__);
        
        return $this;
    }
    
    /**
     * dropTable()
     *
     * @param string $tableId
     * @return bool
     */
    public function dropTable($tableId, $forceIfUtilityUnknown = false)
    {
        if ($this->_canManageResources === false) {
            throw new Exception('This utilty cannot create, delete or alter database resources.');
        }
        
        $tableName = $this->getTableName($tableId);

        if ($tableName === false && !$forceIfUtilityUnknown) {
            return false;
        }
        
        $this->dropTableByName($tableName, $forceIfUtilityUnknown);
        
        unset($this->_createdResources['tables'][$tableId]);
        return true;
    }
    
    /**
     * dropTableByName()
     *
     * @param string $tableName
     * @param bool $includeResourcePrefix
     */
    public function dropTableByName($tableName, $forceIfUtilityUnknown = false, $includeResourcePrefix = true)
    {
        if ($this->_canManageResources === false) {
            throw new Exception('This utilty cannot create, delete or alter database resources.');
        }
        
        if ($includeResourcePrefix) {
            $tableName = $this->_resourcePrefix . $tableName;
        }
        
        $tableIndex = array_search($tableName, $this->_createdResources['tables']);
        
        if ($tableIndex === false && !$forceIfUtilityUnknown) {
            return;
        }
        
        $sql = $this->_sqlDialect->getDropTableSQL($tableName);
        $result = $this->executeRawQuery($sql, __METHOD__);
        
        if ($tableIndex) {
            unset($this->_createdResources['tables'][$tableIndex]);
        }
        
    }
    
    /**
     * populateDataForTableName()
     *
     * @param string $tableName
     * @param array $data
     * @param bool $includeResourcePrefix
     */
    public function populateDataForTableName($tableName, array $data, $includeResourcePrefix = true)
    {
        if ($includeResourcePrefix) {
            $tableName = $this->_resourcePrefix . $tableName;
        }
        
        foreach ($data as $row) {
            $sql = 'INSERT INTO ' .  $this->_dbAdapter->quoteIdentifier($tableName, true);
            $cols = array();
            $vals = array();
            foreach ($row as $col => $val) {
                $cols[] = $this->_dbAdapter->quoteIdentifier($col, true);
                if ($val instanceof Zend_Db_Expr) {
                    $vals[] = $val->__toString();
                } else {
                    $vals[] = $this->_dbAdapter->quote($val);
                }
            }
            $sql .=        ' (' . implode(', ', $cols) . ')';
            $sql .= ' VALUES (' . implode(', ', $vals) . ')';
            $result = $this->executeRawQuery($sql, __METHOD__);
        }
        
        return true;
    }
    
    /**
     * populateData()
     *
     * @param string $tableId
     * @param array $data
     * @return bool
     */
    public function populateData($tableId, array $data)
    {
        $tableName = $this->getTableName($tableId);

        if ($tableName === false) {
            return false;
        }

        return $this->populateDataForTableName($tableName, $data, false);
    }
    
    /**
     * createProcedure()
     *
     * @param string $procedureName
     * @param string $body
     * @param bool $includeResourcePrefix
     * @return string
     */
    public function createProcedure($procedureName, $body, $includeResourcePrefix = true)
    {
        if ($this->_canManageResources === false) {
            throw new Exception('This utilty cannot create, delete or alter database resources.');
        }
        
        if ($includeResourcePrefix) {
            $procedureName = $this->_resourcePrefix . $procedureName;
        }
        
        $dropSQL = $this->_sqlDialect->getDropProcedureSQL($procedureName);
        
        if ($dropSQL != '') {
            $result = $this->executeRawQuery($dropSQL, __METHOD__);
        }
        
        $sql = $this->_sqlDialect->getCreateProcedureSQL($procedureName, $body);
        
        if ($sql == '') {
            return;
        }
        
        $result = $this->executeRawQuery($sql, __METHOD__);
        
        $this->_createdResources['procedures'][] = $procedureName;
        
        return $procedureName;
    }
    
    /**
     * dropProcedure()
     *
     * @param unknown_type $procedureName
     * @param unknown_type $forceIfUtilityUnknown
     * @param unknown_type $includeResourcePrefix
     */
    public function dropProcedure($procedureName, $forceIfUtilityUnknown = false, $includeResourcePrefix = true)
    {
        if ($this->_canManageResources === false) {
            throw new Exception('This utilty cannot create, delete or alter database resources.');
        }
        
        
        if ($includeResourcePrefix) {
            $procedureName = $this->_resourcePrefix . $procedureName;
        }
        
        if (!array_search($procedureName, $this->_createdResources['procedures']) && !$forceIfUtilityUnknown) {
            return;
        }
        
        $sql = $this->_sqlDialect->getDropProcedureSQL($procedureName);
        
        if ($sql == '') {
            return;
        }
        
        $result = $this->executeRawQuery($sql, __METHOD__);
        
    }
    
    /**
     * createView()
     *
     * @param string $viewName
     * @param string $asStatement
     * @param string $fromTableName
     * @param bool $includeResourcePrefix
     */
    public function createView($viewName, $asStatement = 'SELECT * FROM', $fromTableName = null, $includeResourcePrefix = true)
    {
        if ($this->_canManageResources === false) {
            throw new Exception('This utilty cannot create, delete or alter database resources.');
        }
        
        if ($includeResourcePrefix) {
            $viewName = $this->_resourcePrefix . $viewName;
        }
        
        if (!$this->_sqlDialect->supportsIfNotExists() && $this->hasView($viewName, false)) {
            $dropSQL = $this->_sqlDialect->getDropViewSQL($viewName);
            if ($dropSQL != '') {
                $result = $this->executeRawQuery($dropSQL, __METHOD__);
            }
        }
        

        if ($includeResourcePrefix) {
            $fromTableName = $this->_resourcePrefix . $fromTableName;
        }
        
        $sql = $this->_sqlDialect->getCreateViewSQL($viewName, $asStatement = 'SELECT * FROM', $fromTableName);
        
        if ($sql == '') {
            return;
        }
        
        $result = $this->executeRawQuery($sql, __METHOD__);
        
        $this->_createdResources['views'][] = $viewName;
    }
    
    public function hasView($viewName, $includeResourcePrefix = true)
    {
        if ($includeResourcePrefix) {
            $viewName = $this->_resourcePrefix . $viewName;
        }
        
        $sql = $this->_sqlDialect->getHasViewSQL($viewName);
        
        if (!$sql) {
            return null;
        }
        
        $result = $this->_dbAdapter->query($sql);
        return (bool) $result->fetchColumn();
    }
    
    /**
     * dropView();
     *
     * @param string $viewName
     * @param bool $forceIfUtilityUnknown
     * @param bool $includeResourcePrefix
     */
    public function dropView($viewName, $forceIfUtilityUnknown = false, $includeResourcePrefix = true)
    {
        if ($this->_canManageResources === false) {
            throw new Exception('This utilty cannot create, delete or alter database resources.');
        }

        $viewIndex = array_search($viewName, $this->_createdResources['views']);
        
        // if the utility doesnt know about it, and $forceIfUtilityUnknown is false, return
        if (($viewIndex === false) && !$forceIfUtilityUnknown) {
            return;
        }
        
        if (!$this->_sqlDialect->supportsIfNotExists() && !$this->hasView($viewName, false)) {
            return;
        }
        
        $sql = $this->_sqlDialect->getDropViewSQL($viewName);
        
        if ($sql == '') {
            return;
        }
        
        $result = $this->executeRawQuery($sql, __METHOD__);
        
        if ($viewIndex !== false) {
            unset($this->_createdResources['views'][$viewIndex]);    
        }
        
    }
    
    
    
    
    
    

    /**
     * createSequence()
     *
     * @param string $sequenceName
     * @param string $asStatement
     * @param string $fromTableName
     * @param bool $includeResourcePrefix
     */
    public function createSequence($sequenceName, $includeResourcePrefix = true)
    {
        $sequenceId = $sequenceName;
        
        if ($this->_canManageResources === false) {
            throw new Exception('This utilty cannot create, delete or alter database resources.');
        }
        
        if ($includeResourcePrefix) {
            $sequenceName = $this->_resourcePrefix . $sequenceName;
        }
        
        if (!$this->_sqlDialect->supportsIfNotExists() && $this->hasSequence($sequenceName, false)) {
            $dropSQL = $this->_sqlDialect->getDropSequenceSQL($sequenceName);
            if ($dropSQL != '') {
                $result = $this->executeRawQuery($dropSQL, __METHOD__);
            }
        }
        
        $sql = $this->_sqlDialect->getCreateSequenceSQL($sequenceName);
        
        if ($sql == '') {
            return;
        }
        
        $result = $this->executeRawQuery($sql, __METHOD__);
        
        $this->_createdResources['sequences'][$sequenceId] = $sequenceName;
    }
    
    public function getSequenceNameById($name)
    {
        if (isset($this->_createdResources['sequences'][$name])) {
            return $this->_createdResources['sequences'][$name];
        }
        
        return $name;
    }
    
    public function hasSequence($sequenceName, $includeResourcePrefix = true)
    {
        if ($includeResourcePrefix) {
            $sequenceName = $this->_resourcePrefix . $sequenceName;
        }
        
        $sql = $this->_sqlDialect->getHasSequenceSQL($sequenceName);
        
        if (!$sql) {
            return null;
        }
        
        $result = $this->_dbAdapter->query($sql);
        return (bool) $result->fetchColumn();
    }
    
    /**
     * resetSequence()
     *
     * @param string $sequenceName
     * @param bool $forceIfUtilityUnknown
     * @param bool $includeResourcePrefix
     * @return Zend_Db_TestSuite_DbUtility_AbstractUtility
     */
    public function resetSequence($sequenceName, $forceIfUtilityUnknown = false, $includeResourcePrefix = true)
    {
        if ($includeResourcePrefix) {
            $sequenceName = $this->_resourcePrefix . $sequenceName;
        }
        
        if (!$forceIfUtilityUnknown && !array_search($sequenceName, $this->_createdResources['sequences'])) {
            return;
        }
        
        $sql = $this->_sqlDialect->getResetSequenceSQL($sequenceName);
        
        if (!$sql) {
            return;
        }
        
        $result = $this->executeRawQuery($sql, __METHOD__);
        
        return $this;
    }
    
    /**
     * dropSequence();
     *
     * @param string $sequenceName
     * @param bool $forceIfUtilityUnknown
     * @param bool $includeResourcePrefix
     */
    public function dropSequence($sequenceName, $forceIfUtilityUnknown = false, $includeResourcePrefix = true)
    {
        if ($this->_canManageResources === false) {
            throw new Exception('This utilty cannot create, delete or alter database resources.');
        }

        $sequenceIndex = array_search($sequenceName, $this->_createdResources['sequences']);
        
        // if the utility doesnt know about it, and $forceIfUtilityUnknown is false, return
        if (($sequenceIndex === false) && !$forceIfUtilityUnknown) {
            return;
        }
        
        if (!$this->_sqlDialect->supportsIfNotExists() && !$this->hasSequence($sequenceName, false)) {
            return;
        }
        
        $sql = $this->_sqlDialect->getDropSequenceSQL($sequenceName);
        
        if ($sql == '') {
            return;
        }
        
        $result = $this->executeRawQuery($sql, __METHOD__);
        
        if ($sequenceIndex !== false) {
            unset($this->_createdResources['sequences'][$sequenceIndex]);    
        }
        
    }
    
    
    
    
    
    /**
     * Clone - when cloning, make sure to reset some properties:
     * 
     * - the _resourcePrefix should be different (Since this
     * utility will be managing different set of resrouces)
     * - reset the list of created resources, as this utility
     * object will only keep track of its own
     *
     */
    public function __clone()
    {
        // give resources a new prefix
        switch ($this->_resourcePrefix) {
            case 'zf_':  $this->_resourcePrefix = 'zf2_'; break;
            case 'zf2_': $this->_resourcePrefix = 'zf3_'; break;
            case 'zf3_': $this->_resourcePrefix = 'zf4_'; break;
            case 'zf4_': $this->_resourcePrefix = 'zf5_'; break;
        }
        
        $this->_createdResources = array(
            'tables'     => array(),
            'procedures' => array(),
            'views'      => array(),
            'sequences'  => array()
            );
            
        $this->_sqlDialect = clone $this->_sqlDialect;
            
        $this->_canManageResources = true;
    }

    /**
     * Destructor - make sure to destroy the dbAdapter
     *
     */
    public function __destruct()
    {
        // if this is left at PHP_SHUTDOWN time, a error without stack will be throw
        // this is WANTED behavior as any test creating resources should ultimately clean
        // them up.
        $this->_dbAdapter->closeConnection();
        $this->_dbAdapter = null;
    }
    
    /**
     * _getDefaultResourceArray()
     *
     * @return array
     */
    protected function _getDefaultResourceArray()
    {
        $schemaDefinition = array(
            'tables' => array(
                array(
                    'tableId'   => 'Accounts',
                    'tableName' => 'accounts',
                    'columns'   => array(
                        'account_name' => 'VARCHAR(100) NOT NULL',
                        'PRIMARY KEY'  => 'account_name'
                        )
                    ),
                array(
                    'tableId'   => 'Products',
                    'tableName' => 'products',
                    'columns'   => array(
                        'product_id'   => 'IDENTITY',
                        'product_name' => 'VARCHAR(100)'
                        )
                    ),    
                array(
                    'tableId'   => 'Bugs',
                    'tableName' => 'bugs',
                    'columns'   => array(
                        'bug_id'          => 'IDENTITY',
                        'bug_description' => 'VARCHAR(100)',
                        'bug_status'      => 'VARCHAR(20)',
                        'created_on'      => 'DATETIME',
                        'updated_on'      => 'DATETIME',
                        'reported_by'     => 'VARCHAR(100)',
                        'assigned_to'     => 'VARCHAR(100)',
                        'verified_by'     => 'VARCHAR(100)'
                        )
                    ),
                array(
                    'tableId'   => 'BugsProducts',
                    'tableName' => 'bugs_products',
                    'columns'   => array(
                        'bug_id'       => 'INTEGER NOT NULL',
                        'product_id'   => 'INTEGER NOT NULL',
                        'PRIMARY KEY'  => 'bug_id,product_id'
                        )
                    ),
                array(
                    'tableId'   => 'Documents',
                    'tableName' => 'documents',
                    'columns'   => array(
                        'doc_id'       => 'INTEGER NOT NULL',
                        'doc_clob'     => 'CLOB',
                        'doc_blob'     => 'BLOB',
                        'PRIMARY KEY'  => 'doc_id'
                        )
                    ),
                array(
                    'tableId'   => 'Prices',
                    'tableName' => 'prices',
                    'columns'   => array(
                        'product_id'    => 'INTEGER NOT NULL',
                        'price_name'    => 'VARCHAR(100)',
                        'price_total'   => 'DECIMAL(10,2) NOT NULL',
                        'PRIMARY KEY'   => 'product_id'
                        )
                    )
                ),
            'procedures' => array(
                array(
                    'procedureName' => 'get_product_procedure',
                    'body' => '(IN param1 INTEGER) BEGIN SELECT * FROM ' . $this->_resourcePrefix . 'products WHERE product_id = param1; END' 
                    )
                ),
            'views' => array(
                array(
                    'viewName' => 'bugs_view',
                    'asStatement' => 'SELECT * FROM',
                    'fromTableName' => 'bugs'
                    )
                )
            );

        return $schemaDefinition;
    }
    
    /**
     * _getDefaultDataArray()
     *
     * @return array
     */
    protected function _getDefaultTableDataArray()
    {
        $data = array(
            'Accounts' => array(
                array('account_name' => 'mmouse'),
                array('account_name' => 'dduck'),
                array('account_name' => 'goofy'),
                ),
            'Products' => array(
                array('product_name' => 'Windows'),
                array('product_name' => 'Linux'),
                array('product_name' => 'OS X'),
                ),
            'Bugs' => array(
                array(
                    'bug_description' => 'System needs electricity to run',
                    'bug_status'      => 'NEW',
                    'created_on'      => '2007-04-01',
                    'updated_on'      => '2007-04-01',
                    'reported_by'     => 'goofy',
                    'assigned_to'     => 'mmouse',
                    'verified_by'     => 'dduck'
                    ),
                array(
                    'bug_description' => 'Implement Do What I Mean function',
                    'bug_status'      => 'VERIFIED',
                    'created_on'      => '2007-04-02',
                    'updated_on'      => '2007-04-02',
                    'reported_by'     => 'goofy',
                    'assigned_to'     => 'mmouse',
                    'verified_by'     => 'dduck'
                    ),
                array(
                    'bug_description' => 'Where are my keys?',
                    'bug_status'      => 'FIXED',
                    'created_on'      => '2007-04-03',
                    'updated_on'      => '2007-04-03',
                    'reported_by'     => 'dduck',
                    'assigned_to'     => 'mmouse',
                    'verified_by'     => 'dduck'
                    ),
                array(
                    'bug_description' => 'Bug no product',
                    'bug_status'      => 'INCOMPLETE',
                    'created_on'      => '2007-04-04',
                    'updated_on'      => '2007-04-04',
                    'reported_by'     => 'mmouse',
                    'assigned_to'     => 'goofy',
                    'verified_by'     => 'dduck'
                    )
                ),
            'BugsProducts' => array(
                array(
                    'bug_id'       => 1,
                    'product_id'   => 1
                    ),
                array(
                    'bug_id'       => 1,
                    'product_id'   => 2
                    ),
                array(
                    'bug_id'       => 1,
                    'product_id'   => 3
                    ),
                array(
                    'bug_id'       => 2,
                    'product_id'   => 3
                    ),
                array(
                    'bug_id'       => 3,
                    'product_id'   => 2
                    ),
                array(
                    'bug_id'       => 3,
                    'product_id'   => 3
                    )
                ),
            'Documents' => array(
                array(
                    'doc_id'    => 1,
                    'doc_clob'  => 'this is the clob that never ends...'.
                                   'this is the clob that never ends...'.
                                   'this is the clob that never ends...',
                    'doc_blob'  => 'this is the blob that never ends...'.
                                   'this is the blob that never ends...'.
                                   'this is the blob that never ends...'
                    )
                ),
            'Prices' => array(
                array(
                    'product_id'   => 1,
                    'price_name'   => 'Price 1',
                    'price_total'  => 200.45
                    )
                )
            );

        return $data;
    }
    
    /**
     * _getConstantAsParams
     * 
     * Given a list of constants, return them as a parameter array
     *
     * @param array $constantToParamMap
     * @return array
     */
    protected function _getConstantAsParams(array $constantToParamMap = array())
    {
        $params = array();
        foreach ($constantToParamMap as $constant => $paramName) {
            if (defined($constant)) {
                $params[$paramName] = constant($constant);
            }
        }
        return $params;
    }
    
}