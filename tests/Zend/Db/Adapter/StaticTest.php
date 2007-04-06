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

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'PHPUnit/Util/Filter.php';
PHPUnit_Util_Filter::addFileToFilter(__FILE__);

require_once 'Zend/Db.php';
require_once 'Zend/Db/Adapter/Static.php';

class Zend_Db_Adapter_StaticTest extends PHPUnit_Framework_TestCase
{

    function testFactory()
    {
        $db = Zend_Db::factory('Static', array('dbname' => 'dummy') );
        $this->assertThat($db, $this->isInstanceOf('Zend_Db_Adapter_Abstract'));
    }

    function testConstructorWithoutFactory()
    {
        $db = new Zend_Db_Adapter_Static( array('dbname' => 'dummy') );
        $this->assertThat($db, $this->isInstanceOf('Zend_Db_Adapter_Abstract'));
    }

    function testGetConnection()
    {
        $db = Zend_Db::factory('Static', array('dbname' => 'dummy'));

        $conn = $db->getConnection();
        $this->assertThat($conn, $this->isInstanceOf('Zend_Db_Adapter_Static'));
    }

    function testGetFetchMode()
    {
        $db = Zend_Db::factory('Static', array('dbname' => 'dummy'));
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
            $db = Zend_Db::factory('Static', 'scalar');
            $this->fail('Expected to catch Zend_Db_Exception');
        } catch (Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Db_Exception'));
            $this->assertEquals($e->getMessage(), 'Configuration must be an array.');
        }
    }

    function testExceptionInvalidOptionsArrayWithoutFactory()
    {
        try {
            $db = new Zend_Db_Adapter_Static('scalar');
            $this->fail('Expected to catch Zend_Db_Adapter_Exception');
        } catch (Exception $e) {
            $this->assertContains('Argument 1 passed to Zend_Db_Adapter_Abstract::__construct() must be an array, string given', $e->getMessage());
        }
    }

    function testExceptionNoConfig()
    {
        try {
            $db = Zend_Db::factory('Static', null);
            $this->fail('Expected to catch Zend_Db_Exception');
        } catch (Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Db_Exception'));
            $this->assertEquals($e->getMessage(), 'Configuration must be an array.');
        }
    }

    function testExceptionNoDatabaseName()
    {
        try {
            $db = Zend_Db::factory('Static', array());
            $this->fail('Expected to catch Zend_Db_Adapter_Exception');
        } catch (Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Db_Adapter_Exception'));
            $this->assertEquals($e->getMessage(), "Configuration must have a key for 'dbname' that names the database instance.");
        }
    }

    public function getDriver()
    {
        return 'Static';
    }

}
