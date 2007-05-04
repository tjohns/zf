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
require_once 'Zend/Db/Adapter/Pdo/Mysql.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

class Zend_Db_Adapter_Pdo_MysqlTest extends Zend_Db_Adapter_Pdo_TestCommon
{

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

    function getDriver()
    {
        return 'pdo_Mysql';
    }

}
