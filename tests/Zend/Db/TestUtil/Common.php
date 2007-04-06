<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Zend/Db/Expr.php';
require_once 'PHPUnit/Util/Filter.php';
PHPUnit_Util_Filter::addFileToFilter(__FILE__);

abstract class Zend_Db_TestUtil_Common
{
    protected $_tables = array();
    protected $_sequences = array();

    protected function _getSqlCreateTable(Zend_Db_Adapter_Abstract $db, $tableName)
    {
        return 'CREATE TABLE ' . $db->quoteIdentifier($tableName);
    }

    protected function _getSqlDropTable(Zend_Db_Adapter_Abstract $db, $tableName)
    {
        return 'DROP TABLE ' . $db->quoteIdentifier($tableName);
    }

    public function getSqlType($type)
    {
        return $type;
    }

    public function createTable($db, $tableId, array $columns = array())
    {
        if (!$columns) {
            $columns = $this->{'_getColumns'.$tableId}();
        }
        $tableName = $this->getTableName($tableId);
        $this->dropTable($db, $tableName);

        if (isset($this->_tables[$tableName])) {
            return;
        }
        $sql = $this->_getSqlCreateTable($db, $tableName);
        if (!$sql) {
            return;
        }
        $sql .= " (\n\t";

        $pKey = null;
        $pKeys = array();
        if (isset($columns['PRIMARY KEY'])) {
            $pKey = $columns['PRIMARY KEY'];
            unset($columns['PRIMARY KEY']);
            foreach (explode(',', $pKey) as $pKeyCol) {
                $pKeys[] = $db->quoteIdentifier($pKeyCol);
            }
            $pKey = implode(', ', $pKeys);
        }

        foreach ($columns as $columnName => $type) {
            $col[] = $db->quoteIdentifier($columnName) . ' ' . $this->getSqlType($type);
        }

        if ($pKey) {
            $col[] = "PRIMARY KEY ($pKey)";
        }

        $sql .= implode(",\n\t", $col);
        $sql .= "\n)";
        $result = $db->getConnection()->query($sql);
        if ($result === false) {
            throw new Zend_Db_Exception("Statement failed:\n$sql\nError: " . $db->getConnection()->error);
        }
        $this->_tables[$tableName] = true;
    }

    public function dropTable($db, $tableName = null)
    {
        if (!$tableName) {
            foreach (array_keys($this->_tables) as $tab) {
                $this->dropTable($db, $tab);
            }
            return;
        }

        $sql = $this->_getSqlDropTable($db, $tableName);
        if (!$sql) {
            return;
        }
        $result = $db->getConnection()->query($sql);
        if ($result === false) {
            throw new Zend_Db_Exception("DROP TABLE statement failed:\n$sql\nError: " . $db->getConnection()->error);
        }
        unset($this->_tables[$tableName]);
    }

    protected function _getSqlCreateSequence(Zend_Db_Adapter_Abstract $db, $sequenceName)
    {
        return null;
    }

    protected function _getSqlDropSequence(Zend_Db_Adapter_Abstract $db, $sequenceName)
    {
        return null;
    }

    public function createSequence($db, $sequenceName)
    {
        $this->dropSequence($db, $sequenceName);
        if (isset($this->_sequences[$sequenceName])) {
            return;
        }
        $sql = $this->_getSqlCreateSequence($db, $sequenceName);
        if (!$sql) {
            return;
        }
        $result = $db->getConnection()->query($sql);
        if ($result === false) {
            throw new Zend_Db_Exception("CREATE SEQUENCE statement failed:\n$sql\nError: " . $db->getConnection()->error);
        }
        $this->_sequences[$sequenceName] = true;
    }

    public function dropSequence($db, $sequenceName = null)
    {
        if (!$sequenceName) {
            foreach (array_keys($this->_sequences) as $seq) {
                $this->dropSequence($db, $seq);
            }
            return;
        }

        $sql = $this->_getSqlDropSequence($db, $sequenceName);
        if (!$sql) {
            return;
        }
        $result = $db->getConnection()->query($sql);
        if ($result === false) {
            throw new Zend_Db_Exception("DROP SEQUENCE statement failed:\n$sql\nError: " . $db->getConnection()->error);
        }
        unset($this->_sequences[$sequenceName]);
    }

    public function getParams(array $constants = array())
    {
        $params = array();
        foreach ($constants as $key => $constant) {
            if (defined($constant)) {
                $params[$key] = constant($constant);
            }
        }
        return $params;
    }

    public function getSchema()
    {
        $param = $this->getParams();
        return isset($param['dbname']) ? $param['dbname'] : null;
    }

    protected $_tableName = array(
        'Accounts'      => 'accounts',
        'Products'      => 'products',
        'Bugs'          => 'bugs',
        'BugsProducts'  => 'bugs_products',
        'special'       => 'My Table',
    );

    public function getTableName($tableId)
    {
        if (!isset($this->_tableName)) {
            throw new Exception("Invalid table id '$tableId'");
        }
        return $this->_tableName[$tableId];
    }

    protected function _getColumnsBugs()
    {
        return array(
            'bug_id'          => 'IDENTITY',
            'bug_description' => 'VARCHAR(100)',
            'bug_status'      => 'VARCHAR(20)',
            'created_on'      => 'DATETIME',
            'updated_on'      => 'DATETIME',
            'reported_by'     => 'VARCHAR(100)',
            'assigned_to'     => 'VARCHAR(100)',
            'verified_by'     => 'VARCHAR(100)'
        );
    }

    protected function _getColumnsAccounts()
    {
        return array(
            'account_name' => 'VARCHAR(100)',
            'PRIMARY KEY'  => 'account_name'
        );
    }

    protected function _getColumnsProducts()
    {
        return array(
            'product_id'   => 'IDENTITY',
            'product_name' => 'VARCHAR(100)'
        );
    }

    protected function _getColumnsBugsProducts()
    {
        return array(
            'bug_id'       => 'INTEGER',
            'product_id'   => 'INTEGER',
            'PRIMARY KEY'  => 'bug_id,product_id'
        );
    }

    protected function _getDataAccounts(Zend_Db_Adapter_Abstract $db)
    {
        return array(
            array('account_name' => 'mmouse'),
            array('account_name' => 'dduck'),
            array('account_name' => 'goofy'),
        );
    }

    protected function _getDataBugs(Zend_Db_Adapter_Abstract $db)
    {
        return array(
            array(
                'bug_description' => 'System needs electricity to run',
                'bug_status'      => 'NEW',
                'created_on'      => '2007-04-01',
                'updated_on'      => '2007-04-01',
                'reported_by'     => 'goofy',
                'assigned_to'     => 'mmouse',
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
                'reported_by'     => 'mmouse'
            )
        );
    }

    protected function _getDataProducts(Zend_Db_Adapter_Abstract $db)
    {
        return array(
            array('product_name' => 'Windows'),
            array('product_name' => 'Linux'),
            array('product_name' => 'OS X'),
        );
    }

    protected function _getDataBugsProducts(Zend_Db_Adapter_Abstract $db)
    {
        return array(
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
            ),
        );
    }

    public function populateTable(Zend_Db_Adapter_Abstract $db, $tableId)
    {
        $tableName = $this->getTableName($tableId);
        $data = $this->{'_getData'.$tableId}($db);
        foreach ($data as $row) {
            $sql = 'INSERT INTO ' .  $db->quoteIdentifier($tableName);
            $cols = array();
            $vals = array();
            foreach ($row as $col => $val) {
                $cols[] = $db->quoteIdentifier($col);
                if ($val instanceof Zend_Db_Expr) {
                    $vals[] = $val->__toString();
                } else {
                    $vals[] = $db->quote($val);
                }
            }
            $sql .=        ' (' . implode(', ', $cols) . ')';
            $sql .= ' VALUES (' . implode(', ', $vals) . ')';
            $result = $db->getConnection()->query($sql);
            if ($result === false) {
                throw new Zend_Db_Exception("Statement failed:\n$sql\nError: " . $db->getConnection()->error);
            }
        }
    }

    public function setUp(Zend_Db_Adapter_Abstract $db)
    {
        $this->createTable($db, 'Accounts');
        $this->populateTable($db, 'Accounts');

        $this->createTable($db, 'Products');
        $this->populateTable($db, 'Products');

        $this->createTable($db, 'Bugs');
        $this->populateTable($db, 'Bugs');

        $this->createTable($db, 'BugsProducts');
        $this->populateTable($db, 'BugsProducts');
    }

    public function tearDown(Zend_Db_Adapter_Abstract $db)
    {
        $this->dropTable($db);
        $this->dropSequence($db);
        $db->closeConnection();
    }

}
