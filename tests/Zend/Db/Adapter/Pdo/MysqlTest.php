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
 * Common class is DB independant
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Common.php';

/**
 * @package    Zend_Db_Adapter_Pdo_MysqlTest
 * @subpackage UnitTests
 */
class Zend_Db_Adapter_Pdo_MysqlTest extends Zend_Db_Adapter_Pdo_Common
{

    function getDriver()
    {
        return 'pdo_Mysql';
    }

    function getParams()
    {
        $params = array (
            'host'     => TESTS_ZEND_DB_ADAPTER_MYSQL_HOSTNAME,
            'username' => TESTS_ZEND_DB_ADAPTER_MYSQL_USERNAME,
            'password' => TESTS_ZEND_DB_ADAPTER_MYSQL_PASSWORD,
            'dbname'   => TESTS_ZEND_DB_ADAPTER_MYSQL_DATABASE
        );
        return $params;
    }

    function getCreateTableSQL()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS '. self::TABLE_NAME . "(
            id           INT NOT NULL AUTO_INCREMENT,
            title        VARCHAR(100),
            subtitle     VARCHAR(100),
            body         {$this->_textDataType},
            date_created DATETIME,
            PRIMARY KEY (id)
        )";
        return $sql;
    }

    function getCreateTableSQL2()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS '. self::TABLE_NAME_2 . "(
            news_id       INT NOT NULL,
            user_id       INT NOT NULL,
            comment_title VARCHAR(100),
            comment_body  {$this->_textDataType},
            date_posted   DATETIME
        )";
        return $sql;
    }

    protected function getDropTableSQL()
    {
        $sql = 'DROP TABLE IF EXISTS ' . self::TABLE_NAME;
        return $sql;
    }

    protected function getDropTableSQL2()
    {
        $sql = 'DROP TABLE IF EXISTS ' . self::TABLE_NAME_2;
        return $sql;
    }

    public function testQuote()
    {
        // test double quotes are fine
        $value = $this->_db->quote('St John"s Wort');
        $this->assertEquals("'St John\\\"s Wort'", $value);

        // test that single quotes are escaped with another single quote
        $value = $this->_db->quote("St John's Wort");
        $this->assertEquals("'St John\\'s Wort'", $value);

        // quote an array
        $value = $this->_db->quote(array("it's", 'all', 'right!'));
        $this->assertEquals("'it\\'s', 'all', 'right!'", $value);

        // test numeric
        $value = $this->_db->quote('1');
        $this->assertEquals("'1'", $value);

        $value = $this->_db->quote(1);
        $this->assertEquals("1", $value);

        $value = $this->_db->quote(array(1,'2',3));
        $this->assertEquals("1, '2', 3", $value);
    }

    public function testQuoteInto()
    {
        // test double quotes are fine
        $value = $this->_db->quoteInto('id=?', 'St John"s Wort');
        $this->assertEquals("id='St John\\\"s Wort'", $value);

        // test that single quotes are escaped with another single quote
        $value = $this->_db->quoteInto('id = ?', 'St John\'s Wort');
        $this->assertEquals("id = 'St John\\'s Wort'", $value);
    }

    public function testQuoteIdentifier()
    {
        $value = $this->_db->quoteIdentifier('table_name');
        $this->assertEquals("`table_name`", $value);
        $value = $this->_db->quoteIdentifier('table_`_name');
        $this->assertEquals("`table_``_name`", $value);
    }

    public function testExceptionInvalidLoginCredentials()
    {
        $params = $this->getParams();
        $params['password'] = 'xxxxxxxx'; // invalid password

        try {
            $db = new Zend_Db_Adapter_Pdo_Mysql($params);
        } catch (Zend_Db_Adapter_Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Db_Adapter_Pdo_Exception'));
            echo $e->getMessage();
        }
    }

}
