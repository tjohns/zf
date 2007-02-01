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
 * @package    Zend_Db_Adapter_Pdo_PgsqlTest
 * @subpackage UnitTests
 */
class Zend_Db_Adapter_Pdo_PgsqlTest extends Zend_Db_Adapter_Pdo_Common
{
    const SEQUENCE_NAME = 'zf_test_table_seq';

    function getDriver()
    {
        return 'pdo_Pgsql';
    }

    function getParams()
    {
        $params = array (
            'host'     => TESTS_ZEND_DB_ADAPTER_PDO_PGSQL_HOSTNAME,
            'username' => TESTS_ZEND_DB_ADAPTER_PDO_PGSQL_USERNAME,
            'password' => TESTS_ZEND_DB_ADAPTER_PDO_PGSQL_PASSWORD,
            'dbname'   => TESTS_ZEND_DB_ADAPTER_PDO_PGSQL_DATABASE
        );
        return $params;
    }

    function getCreateTableSQL()
    {
        return 'CREATE TABLE  '. self::TABLE_NAME . " (
            id           SERIAL,
            title        VARCHAR(100),
            subTitle     VARCHAR(100),
            body         {$this->_textDataType},
            date_created TIMESTAMP,
            PRIMARY KEY (id)
        )";
    }

    public function getDropTableSQL()
    {
        $sql = 'DROP TABLE IF EXISTS ' . self::TABLE_NAME;
        return $sql;
    }

    protected function getCreateSequenceSQL()
    {
        $sql = 'CREATE SEQUENCE ' . self::SEQUENCE_NAME;
        return $sql;
    }

    protected function getDropSequenceSQL()
    {
        $sql = 'DROP SEQUENCE IF EXISTS ' . self::SEQUENCE_NAME;
        return $sql;
    }

    protected function tearDownMetadata()
    {
        $this->_db->query($this->getDropTableSQL());
        $this->_db->query($this->getDropSequenceSQL());
    }

    protected function createTestTable()
    {
        $this->tearDownMetadata();
        $this->_db->query($this->getCreateSequenceSQL());
        $this->_db->query($this->getCreateTableSQL());
        $sql = 'INSERT INTO ' . self::TABLE_NAME . " (id, title, subTitle, body, date_created)
                VALUES (nextval('" . self::SEQUENCE_NAME . "'), 'News Item 1', 'Sub title 1', 'This is body 1', '2006-05-01 11:11:11')";
        $this->_db->query($sql);
        $sql = 'INSERT INTO ' . self::TABLE_NAME . " (id, title, subTitle, body, date_created)
                VALUES (nextval('" . self::SEQUENCE_NAME . "'), 'News Item 2', 'Sub title 2', 'This is body 2', '2006-05-02 12:12:12')";
        $this->_db->query($sql);
    }

    public function testInsert()
    {
        $nextId = $this->_db->fetchOne("SELECT nextval('" . self::SEQUENCE_NAME . "')");
        $row = array (
            'id'           => $nextId,
            'title'        => 'News Item 3',
            'subTitle'     => 'Sub title 3',
            'body'         => 'This is body 1',
            'date_created' => '2006-05-03 13:13:13'
        );
        $rows_affected = $this->_db->insert(self::TABLE_NAME, $row);
        $last_insert_id = $this->_db->lastInsertId(self::SEQUENCE_NAME);
        $this->assertEquals(3, $last_insert_id); // correct id has been set
    }

    public function testQuote()
    {
        // test double quotes are fine
        $value = $this->_db->quote('St John"s Wort');
        $this->assertEquals("'St John\"s Wort'", $value);

        // test that single quotes are escaped with another single quote
        $value = $this->_db->quote("St John's Wort");
        $this->assertEquals("'St John''s Wort'", $value);

        // quote an array
        $value = $this->_db->quote(array("it's", 'all', 'right!'));
        $this->assertEquals("'it''s', 'all', 'right!'", $value);

        // test numeric
        $value = $this->_db->quote('1');
        $this->assertEquals("'1'", $value);

        $value = $this->_db->quote(1);
        $this->assertEquals("'1'", $value);

        $value = $this->_db->quote(array(1,'2',3));
        $this->assertEquals("'1', '2', '3'", $value);
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
        $this->assertEquals('"table_name"', $value);
        $value = $this->_db->quoteIdentifier('table_"_name');
        $this->assertEquals('"table_""_name"', $value);
    }

}
