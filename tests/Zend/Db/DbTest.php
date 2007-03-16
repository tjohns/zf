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

/**
 * Zend_Db
 */
require_once 'Zend/Db.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @package    Zend_Db
 * @subpackage UnitTests
 */
class Zend_Db_DbTest extends PHPUnit_Framework_TestCase
{
    function testFactory()
    {
        $db = Zend_Db::factory('pdo_sqlite',
            array(
                'dbname' => TESTS_ZEND_DB_ADAPTER_PDO_SQLITE_DATABASE
            )
        );
        $this->assertThat($db, $this->isInstanceOf('Zend_Db_Adapter_Abstract'));
    }

    function testConstructorWithoutFactory()
    {
        $db = new Zend_Db_Adapter_Pdo_Sqlite(
            array(
                'dbname' => TESTS_ZEND_DB_ADAPTER_PDO_SQLITE_DATABASE
            )
        );
        $this->assertThat($db, $this->isInstanceOf('Zend_Db_Adapter_Abstract'));
    }

    function testGetConnection()
    {
        $db = Zend_Db::factory('pdo_sqlite',
            array(
                'dbname' => TESTS_ZEND_DB_ADAPTER_PDO_SQLITE_DATABASE
            )
        );

        try {
            $conn = $db->getConnection();
            $this->assertThat($conn, $this->isInstanceOf('PDO'));
            $conn = null; // close the connection
        } catch (Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Db_Adapter_Exception'));
            $this->assertThat(
                $e->getMessage(),
                $this->logicalOr(
                    $this->equalTo('The PDO extension is required for this adapter but not loaded'),
                    $this->equalTo('The sqlite driver is not currently installed')
                )
            );
            $this->markTestSkipped($e->getMessage());
        }
    }

    function testGetFetchMode()
    {
        $db = Zend_Db::factory('pdo_sqlite',
            array(
                'dbname' => TESTS_ZEND_DB_ADAPTER_PDO_SQLITE_DATABASE
            )
        );
        $mode = $db->getFetchMode();
        $this->assertType('integer', $mode);
    }

    function testExceptionInvalidDriverName()
    {
        try {
            $db = Zend_Db::factory(null);
            $this->fail('Expected to catch Zend_Db_Exception');
        } catch (Zend_Db_Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Db_Exception'));
            $this->assertEquals($e->getMessage(), 'Adapter name must be specified in a string.');
        }
    }

    function testExceptionInvalidOptionsArray()
    {
        try {
            $db = Zend_Db::factory('pdo_sqlite', 'scalar');
            $this->fail('Expected to catch Zend_Db_Exception');
        } catch (Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Db_Exception'));
            $this->assertEquals($e->getMessage(), 'Configuration must be an array.');
        }
    }

    function testExceptionInvalidOptionsArrayWithoutFactory()
    {
        try {
            $db = new Zend_Db_Adapter_Pdo_Sqlite('scalar');
            $this->fail('Expected to catch Zend_Db_Adapter_Exception');
        } catch (Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Db_Adapter_Exception'));
            $this->assertEquals($e->getMessage(), 'Configuration must be an array.');
        }
    }

    function testExceptionNoConfig()
    {
        try {
            $db = Zend_Db::factory('pdo_sqlite', null);
            $this->fail('Expected to catch Zend_Db_Exception');
        } catch (Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Db_Exception'));
            $this->assertEquals($e->getMessage(), 'Configuration must be an array.');
        }
    }

    function testExceptionNoDatabaseName()
    {
        try {
            $db = Zend_Db::factory('pdo_sqlite', array());
            $this->fail('Expected to catch Zend_Db_Adapter_Exception');
        } catch (Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Db_Adapter_Exception'));
            $this->assertEquals($e->getMessage(), "Configuration must have a key for 'dbname' that names the database instance.");
        }
    }

}
