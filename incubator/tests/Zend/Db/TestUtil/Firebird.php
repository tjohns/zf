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
 * @version    $Id: Firebird.php 6847 2007-11-18 05:24:21Z peptolab $
 */


/**
 * @see Zend_Db_TestUtil_Common
 */
require_once 'Zend/Db/TestUtil/Common.php';


PHPUnit_Util_Filter::addFileToFilter(__FILE__);


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Table
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_TestUtil_Firebird extends Zend_Db_TestUtil_Common
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
        return $type;
    }

    protected function _getSqlCreateTable($tableName)
    {
        return 'CREATE TABLE ' . $this->_db->quoteIdentifier($tableName);
    }

    protected function _getSqlDropTable($tableName)
    {
        return 'DROP TABLE ' . $this->_db->quoteIdentifier($tableName);
    }

    protected function _rawQuery($sql)
    {
        $this->markTestSkipped('TODO');
    }
    
}
