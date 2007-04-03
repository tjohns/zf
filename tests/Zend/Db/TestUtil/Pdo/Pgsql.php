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

class Zend_Db_TestUtil_Pdo_Pgsql extends Zend_Db_TestUtil_Pdo_Common
{

    function getParams(array $constants = array())
    {
        $params = array (
            'host'     => TESTS_ZEND_DB_ADAPTER_PDO_PGSQL_HOSTNAME,
            'username' => TESTS_ZEND_DB_ADAPTER_PDO_PGSQL_USERNAME,
            'password' => TESTS_ZEND_DB_ADAPTER_PDO_PGSQL_PASSWORD,
            'dbname'   => TESTS_ZEND_DB_ADAPTER_PDO_PGSQL_DATABASE
        );
        return $params;
    }

    public function getSchema()
    {
        return 'public';
    }

    public function getSqlType($type)
    {
        if ($type == 'IDENTITY') {
            return 'INTEGER NOT NULL PRIMARY KEY';
        }
        if ($type == 'DATETIME') {
            return 'TIMESTAMP';
        }
        return $type;
    }

    public function _getSqlDropTable()
    {
        return 'DROP TABLE IF EXISTS';
    }

    protected function _getSqlCreateSequence()
    {
        return 'CREATE SEQUENCE';
    }

    protected function _getSqlDropSequence()
    {
        return 'DROP SEQUENCE IF EXISTS';
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
            $row['bug_id'] = new Zend_Db_Expr("NEXTVAL('bugs_seq')");
        }
        return $data;
    }

    protected function _getDataProducts()
    {
        $data = parent::_getDataProducts();
        foreach ($data as &$row) {
            $row['product_id'] = new Zend_Db_Expr("NEXTVAL('products_seq')");
        }
        return $data;
    }

}
