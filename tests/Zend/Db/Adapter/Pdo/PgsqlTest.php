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

require_once 'Zend/Db/Adapter/Pdo/TestCommon.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

class Zend_Db_Adapter_Pdo_PgsqlTest extends Zend_Db_Adapter_Pdo_TestCommon
{

    public function testDbAdapterInsert()
    {
        $row = array (
            'product_id'   => new Zend_Db_Expr("NEXTVAL('products_seq')"),
            'product_name' => 'Solaris',
        );
        $rowsAffected = $this->_db->insert('products', $row);
        $this->assertEquals(1, $rowsAffected);
        $lastInsertId = $this->_db->lastInsertId('products', null); // implies 'products_seq'
        $lastSequenceId = $this->_db->lastSequenceId('products_seq');
        $this->assertEquals('4', (string) $lastInsertId, 'Expected new id to be 4');
        $this->assertEquals('4', (string) $lastSequenceId, 'Expected new id to be 4');
    }

    public function testDbAdapterExceptionInvalidLoginCredentials()
    {
        $params = $this->_util->getParams();
        $params['password'] = 'xxxxxxxx'; // invalid password

        try {
            $db = new Zend_Db_Adapter_Pdo_Pgsql($params);
            $db->getConnection(); // force connection
            $this->fail('Expected to catch Zend_Db_Adapter_Exception');
        } catch (Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Db_Adapter_Exception'),
                'Expecting object of type Zend_Db_Adapter_Exception, got '.get_class($e));
        }
    }

    function getDriver()
    {
        return 'Pdo_Pgsql';
    }

}
