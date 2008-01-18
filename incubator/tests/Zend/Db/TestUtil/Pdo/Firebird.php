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
 * @version    $Id: Oci.php 6294 2007-09-11 18:02:19Z bkarwin $
 */


/**
 * @see Zend_Db_TestUtil_Pdo_Common
 */
require_once 'Zend/Db/TestUtil/Pdo/Common.php';


PHPUnit_Util_Filter::addFileToFilter(__FILE__);


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_TestUtil_Pdo_Firebird extends Zend_Db_TestUtil_Pdo_Common
{

    public function setUp(Zend_Db_Adapter_Abstract $db)
    {
        $this->_db = $db;
        $this->createSequence('zfbugs_seq');
        $this->createSequence('zfproducts_seq');
        parent::setUp($db);
    }

    public function getParams(array $constants = array())
    {
        $constants = array(
            'host'     => 'TESTS_ZEND_DB_ADAPTER_FIREBIRD_HOSTNAME',
            'username' => 'TESTS_ZEND_DB_ADAPTER_FIREBIRD_USERNAME',
            'password' => 'TESTS_ZEND_DB_ADAPTER_FIREBIRD_PASSWORD',
            'dbname'   => 'TESTS_ZEND_DB_ADAPTER_FIREBIRD_DATABASE'
        );
        return parent::getParams($constants);
    }

    public function getSqlType($type)
    {
        if ($type == 'IDENTITY') {
            return 'INTEGER NOT NULL PRIMARY KEY';
        }
        if ($type == 'INTEGER') {
            return 'INTEGER';
        }
        if ($type == 'DATETIME') {
            return 'TIMESTAMP';
        }
        return $type;
    }

    protected function _getSqlCreateTable($tableName)
    {
        return 'CREATE TABLE ' . $this->_db->quoteIdentifier($tableName, true);
    }

    protected function _getSqlDropTable($tableName)
    {
        return 'DROP TABLE ' . $this->_db->quoteIdentifier($tableName, true);
    }

    protected function _getSqlCreateSequence($sequenceName)
    {
        return 'CREATE GENERATOR ' . $this->_db->quoteIdentifier($sequenceName, true);
    }

    protected function _getSqlDropSequence($sequenceName)
    {
        return 'DROP GENERATOR ' . $this->_db->quoteIdentifier($sequenceName, true);
    }

    protected function _getDataBugs()
    {
        $data = parent::_getDataBugs();
        foreach ($data as &$row) {
            $row['bug_id'] = new Zend_Db_Expr($this->_db->quoteIdentifier('GEN_ID(zfbugs_seq, 1)'));
            $row['created_on'] = new Zend_Db_Expr($this->_db->quoteInto('DATE ?', $row['created_on']));
            $row['updated_on'] = new Zend_Db_Expr($this->_db->quoteInto('DATE ?', $row['updated_on']));
        }
        return $data;
    }


    protected function _getDataProducts()
    {
        $data = parent::_getDataProducts();
        foreach ($data as &$row) {
            $row['product_id'] = new Zend_Db_Expr('GEN_ID(zfbugs_seq, 1)');
        }
        return $data;
    }

}
