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
 * @see Zend_Db_Adapter_TestCommon
 */
require_once 'Zend/Db/Adapter/TestCommon.php';

/**
 * @see Zend_Db_Adapter_Db2
 */
require_once 'Zend/Db/Adapter/Db2.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

class Zend_Db_Adapter_Db2Test extends Zend_Db_Adapter_TestCommon
{

    public function testDbAdapterExceptionInvalidLoginCredentials()
    {
        $params = $this->getParams();

        try {
            $db = new Zend_Db_Adapter_Db2('scalar');
            $db->getConnection(); // force a connection
            $this->fail('Expected to catch Zend_Db_Adapter_Db2_Exception');
        } catch (Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Db_Adapter_Db2_Exception'), 'Expected to catch Zend_Db_Adapter_Db2_Exception, got '.get_class($e));
            $this->assertEquals("Configuration must be an array.", $e->getMessage());
        }

        try {
            $p = $params;
            unset($p['password']);
            $db = new Zend_Db_Adapter_Db2($p);
            $db->getConnection(); // force a connection
            $this->fail('Expected to catch Zend_Db_Adapter_Db2_Exception');
        } catch (Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Db_Adapter_Db2_Exception'), 'Expected to catch Zend_Db_Adapter_Db2_Exception, got '.get_class($e));
            $this->assertEquals("Configuration array must have a key for 'password' for login credentials.", $e->getMessage());
        }

        try {
            $p = $params;
            unset($p['username']);
            $db = new Zend_Db_Adapter_Db2($p);
            $db->getConnection(); // force a connection
            $this->fail('Expected to catch Zend_Db_Adapter_Db2_Exception');
        } catch (Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Db_Adapter_Db2_Exception'), 'Expected to catch Zend_Db_Adapter_Db2_Exception, got '.get_class($e));
            $this->assertEquals("Configuration array must have a key for 'username' for login credentials.", $e->getMessage());
        }

        try {
            $p = $params;
            unset($p['dbname']);
            $db = new Zend_Db_Adapter_Db2($p);
            $db->getConnection(); // force a connection
            $this->fail('Expected to catch Zend_Db_Adapter_Db2_Exception');
        } catch (Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Db_Adapter_Db2_Exception'), 'Expected to catch Zend_Db_Adapter_Db2_Exception, got '.get_class($e));
            $this->assertEquals("Configuration array must have a key for 'dbname' that names the database instance.", $e->getMessage());
        }

    }

    public function getDriver()
    {
        return 'Db2';
    }

}
