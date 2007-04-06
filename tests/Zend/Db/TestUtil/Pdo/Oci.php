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

require_once 'Zend/Db/TestUtil/Pdo/Common.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

class Zend_Db_TestUtil_Pdo_Oci extends Zend_Db_TestUtil_Pdo_Common
{

    public function setUp(Zend_Db_Adapter_Abstract $db)
    {
        $this->createSequence($db, 'bugs_seq');
        $this->createSequence($db, 'products_seq');
        parent::setUp($db);
    }

    public function getParams(array $constants = array())
    {
        $params = array (
            'host'     => TESTS_ZEND_DB_ADAPTER_ORACLE_HOSTNAME,
            'username' => TESTS_ZEND_DB_ADAPTER_ORACLE_USERNAME,
            'password' => TESTS_ZEND_DB_ADAPTER_ORACLE_PASSWORD,
            'dbname'   => TESTS_ZEND_DB_ADAPTER_ORACLE_SID
        );
        return $params;
    }

    public function getSqlType($type)
    {
        if (preg_match('/VARCHAR(.*)/', $type, $matches)) {
            return 'VARCHAR2' . $matches[1];
        }
        if ($type == 'IDENTITY') {
            return 'NUMBER(11)';
        }
        if ($type == 'INTEGER') {
            return 'NUMBER(11)';
        }
        if ($type == 'DATETIME') {
            return 'TIMESTAMP';
        }
        return $type;
    }

    protected function _getSqlCreateTable(Zend_Db_Adapter_Abstract $db, $tableName)
    {
        $tableList = $db->fetchCol('SELECT table_name FROM ALL_TABLES');
        if (in_array($tableName, $tableList)) {
            return null;
        }
        return 'CREATE TABLE ' . $db->quoteIdentifier($tableName);;
    }

    protected function _getSqlDropTable(Zend_Db_Adapter_Abstract $db, $tableName)
    {
        $tableList = $db->fetchCol('SELECT table_name FROM ALL_TABLES');
        if (in_array($tableName, $tableList)) {
            return 'DROP TABLE ' . $db->quoteIdentifier($tableName);
        }
        return null;
    }

    protected function _getSqlCreateSequence(Zend_Db_Adapter_Abstract $db, $sequenceName)
    {
        $seqList = $db->fetchCol('SELECT sequence_name FROM ALL_SEQUENCES');
        if (in_array($sequenceName, $seqList)) {
            return null;
        }
        return 'CREATE SEQUENCE ' . $db->quoteIdentifier($sequenceName);
    }

    protected function _getSqlDropSequence(Zend_Db_Adapter_Abstract $db, $sequenceName)
    {
        $seqList = $db->fetchCol('SELECT sequence_name FROM ALL_SEQUENCES');
        if (in_array($sequenceName, $seqList)) {
            return 'DROP SEQUENCE ' . $db->quoteIdentifier($sequenceName);
        }
        return null;
    }

    protected function _getDataBugs(Zend_Db_Adapter_Abstract $db)
    {
        $data = parent::_getDataBugs($db);
        foreach ($data as &$row) {
            $row['bug_id'] = new Zend_Db_Expr($db->quoteIdentifier('bugs_seq').'.NEXTVAL');
            $row['created_on'] = new Zend_Db_Expr($db->quoteInto('DATE ?', $row['created_on']));
            $row['updated_on'] = new Zend_Db_Expr($db->quoteInto('DATE ?', $row['updated_on']));
        }
        return $data;
    }

    protected function _getDataProducts(Zend_Db_Adapter_Abstract $db)
    {
        $data = parent::_getDataProducts($db);
        foreach ($data as &$row) {
            $row['product_id'] = new Zend_Db_Expr($db->quoteIdentifier('products_seq').'.NEXTVAL');
        }
        return $data;
    }

}
