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

require_once 'Zend/Db/TestUtil/Common.php';
require_once 'Zend/Db/Expr.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

class Zend_Db_TestUtil_Oracle extends Zend_Db_TestUtil_Common
{

    public function getParams(array $constants = array())
    {
        $constants = array(
            'host'     => 'TESTS_ZEND_DB_ADAPTER_ORACLE_HOSTNAME',
            'username' => 'TESTS_ZEND_DB_ADAPTER_ORACLE_USERNAME',
            'password' => 'TESTS_ZEND_DB_ADAPTER_ORACLE_PASSWORD',
            'dbname'   => 'TESTS_ZEND_DB_ADAPTER_ORACLE_DATABASE',
        );
        return parent::getParams($constants);
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

    protected function _getSqlCreateSequence(Zend_Db_Adapter_Abstract $db, $sequenceName)
    {
        $seqList = $db->fetchCol('SELECT sequence_name FROM ALL_SEQUENCES');
        if (in_array($sequenceName, $seqList)) {
            return null;
        }
        return 'CREATE SEQUENCE';
    }

    protected function _getSqlDropSequence(Zend_Db_Adapter_Abstract $db, $sequenceName)
    {
        $seqList = $db->fetchCol('SELECT sequence_name FROM ALL_SEQUENCES');
        if (in_array($sequenceName, $seqList)) {
            return 'DROP SEQUENCE';
        }
        return null;
    }

    public function setUp(Zend_Db_Adapter_Abstract $db)
    {
        $this->createSequence($db, 'bugs_seq');
        $this->createSequence($db, 'products_seq');
        parent::setUp($db);
    }

    protected function _getDataBugs()
    {
        $data = parent::_getDataBugs();
        foreach ($data as &$row) {
            $row['bug_id'] = new Zend_Db_Expr('bug_seq.NEXTVAL');
        }
        return $data;
    }

    protected function _getDataProducts()
    {
        $data = parent::_getDataProducts();
        foreach ($data as &$row) {
            $row['product_id'] = new Zend_Db_Expr('products_seq.NEXTVAL');
        }
        return $data;
    }

}
