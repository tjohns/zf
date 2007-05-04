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

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
 * Common class is DB independant
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Common.php';


/**
 * @package    Zend_Db_Adapter_Pdo_MssqlTest
 * @subpackage UnitTests
 */
class Zend_Db_Adapter_Pdo_MssqlTest extends Zend_Db_Adapter_Pdo_Common
{

    function getDriver()
    {
        return 'pdo_Mssql';
    }

    function getParams()
    {
        $params = array (
            'host'     => TESTS_ZEND_DB_ADAPTER_PDO_MSSQL_HOSTNAME,
            'username' => TESTS_ZEND_DB_ADAPTER_PDO_MSSQL_USERNAME,
            'password' => TESTS_ZEND_DB_ADAPTER_PDO_MSSQL_PASSWORD,
            'dbname'   => TESTS_ZEND_DB_ADAPTER_PDO_MSSQL_DATABASE
        );
        if (defined('TESTS_ZEND_DB_ADAPTER_PDO_MSSQL_PORT')) {
            $params['port'] = constant('TESTS_ZEND_DB_ADAPTER_PDO_MSSQL_PORT');
        }

        return $params;
    }

    public function testExceptionInvalidLoginCredentials()
    {
        $params = $this->getParams();
        $params['password'] = 'xxxxxxxx'; // invalid password

        try {
            $db = new Zend_Db_Adapter_Pdo_Mssql($params);
            $db->getConnection(); // force connection
            $this->fail('Expected to catch Zend_Db_Adapter_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Adapter_Exception', $e,
                'Expecting object of type Zend_Db_Adapter_Exception, got '.get_class($e));
        }
    }

}
