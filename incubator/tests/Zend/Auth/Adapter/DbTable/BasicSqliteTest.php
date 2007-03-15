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
 * @package    Zend_Auth
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: AuthTest.php 3874 2007-03-12 19:50:38Z darby $
 */


/**
 * PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @see Zend_Auth_Adapter_Http
 */
require_once 'Zend/Auth/Adapter/Http.php';



/**
 * @see 
 */
require_once 'Zend/Db.php';

/**
 * @see 
 */
require_once 'Zend/Db/Adapter/Abstract.php';

require_once 'Zend/Auth.php';
require_once 'Zend/Auth/Result.php';
require_once 'Zend/Auth/Adapter/DbTable.php';



/**
 * @category   Zend
 * @package    Zend_Auth
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Auth_Adapter_DbTable_BasicSqliteTest extends PHPUnit_Framework_TestCase
{

    protected $_db = null;
    
    /**
     * Enter description here...
     *
     * @var Zend_Auth_Adapter_DbTable
     */
    protected $_adapter = null;
    
    /**
     * Set up test configuration
     *
     * @return void
     */
    public function __construct()
    {
        $this->_db = Zend_Db::factory('pdo_sqlite', array('dbname' => TESTS_ZEND_DB_ADAPTER_PDO_SQLITE_DATABASE));
        
        $sql_create = 'CREATE TABLE [users] ( [id] INTEGER  NOT NULL PRIMARY KEY, [username] VARCHAR(50)  UNIQUE NOT NULL, [password] VARCHAR(32)  NULL, [real_name] VARCHAR(150)  NULL)';
        $sql_insert = 'INSERT INTO users (username, password, real_name) VALUES ("my_username", "my_password", "My Real Name")';
        $this->_db->query($sql_create);
        $this->_db->query($sql_insert);
    }

    public function setUp()
    {
        $this->_adapter = new Zend_Auth_Adapter_DbTable($this->_db, 'users', 'username', 'password');
    }
    
    public function testSuccessAuthenticate()
    {
        $this->_adapter->setIdentity('my_username');
        $this->_adapter->setCredential('my_password');
        $result = $this->_adapter->authenticate();
        $this->assertEquals($result->getCode(), Zend_Auth_Result::SUCCESS);
        
    }

    public function testFailureAuthenticate()
    {
        $this->_adapter->setIdentity('my_username');
        $this->_adapter->setCredential('my_password_bad');
        $result = $this->_adapter->authenticate();
        print_r($result);
        
        $this->assertEquals($result->isValid(), false);
        $this->assertEquals($result->getCode(), Zend_Auth_Result::FAILURE_INVALID_CREDENTIAL);

               
    }
    
    public function testExceptions()
    {
        /*
        try {
            $result = $this->_adapter->authenticate();
            $this->assertFail('Exception should have been thrown');
        } catch (Zend_Auth_Exception $e) {
            $this->assertEquals(1, 1);
        }

        try {
            $result = $this->_adapter->authenticate();
            $this->assertFail('Exception should have been thrown');
        } catch (Zend_Auth_Exception $e) {
            $this->assertEquals(1, 1);
        }
        */
    }
    
    public function testResultRow()
    {
        $this->_adapter->setIdentity('my_username');
        $this->_adapter->setCredential('my_password');
        $result = $this->_adapter->authenticate();

        $result_row = $this->_adapter->getResultRow();
        $this->assertEquals($result_row['username'], 'my_username');
               
    }
    
    
}

class Zend_Auth_Adapter_DbTable_BasicSqliteTest_Skip extends Zend_Auth_Adapter_DbTable_BasicSqliteTest 
{
    public function __construct()
    {
        /* */
    }
    
    public function setUp()
    {
        $this->markTestSkipped('Sqlite is not enabled in TestConfiguration.php.');
    }
}
