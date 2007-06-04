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
 * @version    $Id$
 */


/**
 * @see Zend_Db_Adapter_Pdo_TestCommon
 */
require_once 'Zend/Db/Adapter/Pdo/TestCommon.php';


/**
 * @see Zend_Db_Adapter_Pdo_Mysql
 */
require_once 'Zend/Db/Adapter/Pdo/Mysql.php';


PHPUnit_Util_Filter::addFileToFilter(__FILE__);


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Adapter_Pdo_MysqlTest extends Zend_Db_Adapter_Pdo_TestCommon
{
    public function getDriver()
    {
        return 'Pdo_Mysql';
    }

    public function testAdapterQuoteIdentifier()
    {
        $value = $this->_db->quoteIdentifier('table_name');
        $this->assertEquals('`table_name`', $value);
        $value = $this->_db->quoteIdentifier('table_`_name');
        $this->assertEquals('`table_``_name`', $value);
    }

    public function testAdapterExceptionInvalidLoginCredentials()
    {
        $params = $this->_util->getParams();
        $params['password'] = 'xxxxxxxx'; // invalid password

        try {
            $db = new Zend_Db_Adapter_Pdo_Mysql($params);
            $db->getConnection(); // force a connection
            $this->fail('Expected to catch Zend_Db_Adapter_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Adapter_Exception', $e,
                    'Expecting object of type Zend_Db_Adapter_Exception, got '.get_class($e));
        }
    }

    /**
     * Test AUTO_QUOTE_IDENTIFIERS option
     * Case: Zend_Db::AUTO_QUOTE_IDENTIFIERS = true
     *
     * MySQL actually allows delimited identifiers to remain
     * case-insensitive, so this test overrides its parent.
     */
    public function testAdapterAutoQuoteIdentifiersTrue()
    {
        $params = $this->_util->getParams();

        $params['options'] = array(
            Zend_Db::AUTO_QUOTE_IDENTIFIERS => true
        );
        $db = Zend_Db::factory($this->getDriver(), $params);
        $db->getConnection();

        $select = $this->_db->select();
        $select->from('zfproducts');
        $stmt = $this->_db->query($select);
        $result1 = $stmt->fetchAll();

        $this->assertEquals(1, $result1[0]['product_id']);

        $select = $this->_db->select();
        $select->from('ZFPRODUCTS');
        try {
            $stmt = $this->_db->query($select);
            $result2 = $stmt->fetchAll();
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Statement_Exception', $e,
                'Expecting object of type Zend_Db_Statement_Exception, got '.get_class($e));
            $this->fail('Unexpected exception '.get_class($e).' received: '.$e->getMessage());
        }

        $this->assertEquals($result1, $result2);
    }

    /**
     * Ensures that driver_options are properly passed along to PDO
     *
     * @see    http://framework.zend.com/issues/browse/ZF-285
     * @return void
     */
    public function testAdapterDriverOptions()
    {
        $params = $this->_util->getParams();

        $params['driver_options'] = array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true);

        $db = Zend_Db::factory($this->getDriver(), $params);

        $this->assertTrue((boolean) $db->getConnection()->getAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY));

        $params['driver_options'] = array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => false);

        $db = Zend_Db::factory($this->getDriver(), $params);

        $this->assertFalse((boolean) $db->getConnection()->getAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY));
    }

}
