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

    function getCreateTableSQL()
    {
        return 'CREATE TABLE  '. self::TABLE_NAME . " (
            id           int IDENTITY,
            title        varchar(100),
            subtitle     varchar (100),
            body         {$this->_textDataType},
            date_created datetime
        )";
    }

    function getCreateTableSQL2()
    {
        return 'CREATE TABLE  '. self::TABLE_NAME_2 . " (
            news_id       int not null,
            user_id       int not null,
            comment_title varchar (100),
            comment_body  {$this->_textDataType},
            date_posted   datetime
        )";
    }

    function getCreateTableSQLIntersection()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS '. self::TABLE_NAME_I . '(
            news_id     int not null,
            user_id     int not null,
            date_posted datetime,
            PRIMARY KEY (news_id, user_id, date_posted),
            FOREIGN KEY (news_id) REFERENCES ' . self::TABLE_NAME . '(news_id),
            FOREIGN KEY (user_id, date_posted) REFERENCES ' . self::TABLE_NAME_2 . '(user_id, date_posted)
        )';
        return $sql;
    }

    public function testQuote()
    {
        // test double quotes are fine
        $value = $this->_db->quote('St John"s Wort');
        $this->assertEquals("'St John\"s Wort'", $value);

        // test that single quotes are escaped with another single quote
        $value = $this->_db->quote('St John\'s Wort');
        $this->assertEquals("'St John''s Wort'", $value);

        // quote an array
        $value = $this->_db->quote(array("it's", 'all', 'right!'));
        $this->assertEquals("'it''s', 'all', 'right!'", $value);

        // test numeric
        $value = $this->_db->quote('1');
        $this->assertEquals("'1'", $value);

        $value = $this->_db->quote(1);
        $this->assertEquals("1", $value);

        $value = $this->_db->quote(array(1, '2', 3));
        $this->assertEquals("1, '2', 3", $value);
    }

    public function testQuoteInto()
    {
        // test double quotes are fine
        $value = $this->_db->quoteInto('id=?', 'St John"s Wort');
        $this->assertEquals("id='St John\"s Wort'", $value);

        // test that single quotes are escaped with another single quote
        $value = $this->_db->quoteInto('id = ?', 'St John\'s Wort');
        $this->assertEquals("id = 'St John''s Wort'", $value);
    }

    public function testQuoteIdentifier()
    {
        $value = $this->_db->quoteIdentifier('table_name');
        $this->assertEquals("[table_name]", $value);

        $value = $this->_db->quoteIdentifier('table_[]_name');
        $this->assertEquals("[table_[]]_name]", $value);
    }

    public function testExceptionInvalidLoginCredentials()
    {
        $params = $this->getParams();
        $params['password'] = 'xxxxxxxx'; // invalid password

        try {
            $db = new Zend_Db_Adapter_Pdo_Mssql($params);
            $db->getConnection(); // force connection
            $this->fail('Expected to catch Zend_Db_Adapter_Exception');
        } catch (Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Db_Adapter_Exception'), 'Expecting object of type Zend_Db_Adapter_Exception, got '.get_class($e));
        }
    }

}
