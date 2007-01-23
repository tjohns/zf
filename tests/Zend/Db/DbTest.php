<?php
/**
 * @package    Zend_Db
 * @subpackage UnitTests
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
        $db = Zend_Db::factory('pdo_sqlite', array('dbname' => TESTS_ZEND_DB_ADAPTER_PDO_SQLITE_DATABASE));
        $this->assertThat($db, $this->isInstanceOf('Zend_Db_Adapter_Abstract'));
    }

    function testFactoryOption()
    {
        $db = Zend_Db::factory('pdo_sqlite', array('dbname' => TESTS_ZEND_DB_ADAPTER_PDO_SQLITE_DATABASE));
        $this->assertThat($db, $this->isInstanceOf('Zend_Db_Adapter_Abstract'));
    }

    function testExceptionInvalidDriverName()
    {
        try {
            $db = Zend_Db::factory(null);
        } catch (Zend_Db_Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Db_Exception'));
            $this->assertEquals($e->getMessage(), 'Adapter name must be specified in a string');
        }
    }

    function testExceptionInvalidOptionsArray()
    {
        try {
            $db = Zend_Db::factory('pdo_sqlite', 'scalar');
        } catch (Zend_Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Db_Exception'));
            $this->assertEquals($e->getMessage(), 'Configuration must be an array');
        }
    }

}
