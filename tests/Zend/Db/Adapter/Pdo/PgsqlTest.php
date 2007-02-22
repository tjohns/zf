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

    /**
     * @param string The name of the identifier, to be transformed.
     * @return string The name of a column or table, transformed for the
     * current adapter.
     */
    public function getIdentifier($name)
    {
        return strtolower($name);
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
        $sql = 'CREATE TABLE  '. self::TABLE_NAME . " (
            id           SERIAL,
            title        VARCHAR(100),
            subtitle     VARCHAR(100),
            body         {$this->_textDataType},
            date_created TIMESTAMP,
            PRIMARY KEY (id)
        )";
        return $sql;
    }

    function getCreateTableSQL2()
    {
        $sql = 'CREATE TABLE  '. self::TABLE_NAME_2 . " (
            news_id       INTEGER,
            user_id       INTEGER,
            comment_title VARCHAR(100),
            comment_body  {$this->_textDataType},
            date_posted   TIMESTAMP
        )";
        return $sql;
    }

    public function getDropTableSQL()
    {
        $sql = 'DROP TABLE IF EXISTS ' . self::TABLE_NAME;
        return $sql;
    }

    public function getDropTableSQL2()
    {
        $sql = 'DROP TABLE IF EXISTS ' . self::TABLE_NAME_2;
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
        $sql = $this->getDropTableSQL();
        $this->_db->query($sql);
        $sql = $this->getDropTableSQL2();
        $this->_db->query($sql);
        $sql = $this->getDropSequenceSQL();
        $this->_db->query($sql);
    }

    protected function createTestTable()
    {
        $this->tearDownMetadata();
        $sql = $this->getCreateSequenceSQL();
        $this->_db->query($sql);
        $sql = $this->getCreateTableSQL();
        $this->_db->query($sql);
        $sql = 'INSERT INTO ' . self::TABLE_NAME . " (id, title, subtitle, body, date_created)
                VALUES (nextval('" . self::SEQUENCE_NAME . "'), 'News Item 1', 'Sub title 1', 'This is body 1', '2006-05-01 11:11:11')";
        $this->_db->query($sql);
        $sql = 'INSERT INTO ' . self::TABLE_NAME . " (id, title, subtitle, body, date_created)
                VALUES (nextval('" . self::SEQUENCE_NAME . "'), 'News Item 2', 'Sub title 2', 'This is body 2', '2006-05-02 12:12:12')";
        $this->_db->query($sql);
    }

    public function testInsert()
    {
        $nextId = $this->_db->fetchOne("SELECT nextval('" . self::SEQUENCE_NAME . "')");
        $row = array (
            'id'           => $nextId,
            'title'        => 'News Item 3',
            'subtitle'     => 'Sub title 3',
            'body'         => 'This is body 1',
            'date_created' => '2006-05-03 13:13:13'
        );
        $rows_affected = $this->_db->insert(self::TABLE_NAME, $row);
        $last_insert_id = $this->_db->lastInsertId(self::TABLE_NAME);
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
        $this->assertEquals("1", $value);

        $value = $this->_db->quote(array(1,'2',3));
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
        $this->assertEquals('"table_name"', $value);
        $value = $this->_db->quoteIdentifier('table_"_name');
        $this->assertEquals('"table_""_name"', $value);
    }

    public function testTableInsert()
    {
        Zend::loadClass('Zend_Db_Table_ZfTestTable');
        $table = $this->getIdentifier(self::TABLE_NAME);
        $id = $this->getIdentifier('id');

        $tab1 = new Zend_Db_Table_ZfTestTable(
            array(
                'db' => $this->_db,
                'name' => $table,
                'primary' => $id
            )
        );

        $nextId = $this->_db->fetchOne("SELECT nextval('" . self::SEQUENCE_NAME . "')");
        $row = array (
            'id'           => $nextId,
            'title'        => 'News Item 3',
            'subtitle'     => 'Sub title 3',
            'body'         => 'This is body 1',
            'date_created' => '2006-05-03 13:13:13'
        );
        $insertResult = $tab1->insert($row);
        $last_insert_id = $this->_db->lastInsertId($table);

        $this->assertEquals($insertResult, (string) $last_insert_id);
        $this->assertEquals(3, (string) $last_insert_id);
    }

    public function testExceptionInvalidLoginCredentials()
    {
        $params = $this->getParams();
        $params['password'] = 'xxxxxxxx'; // invalid password

        try {
            $db = new Zend_Db_Adapter_Pdo_Pgsql($params);
        } catch (Zend_Db_Adapter_Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Db_Adapter_Pdo_Exception'));
            echo $e->getMessage();
        }
    }

    public function testTableRowSaveInsert()
    {
        list ($dbTable, $table, $id) = $this->getInstanceOfDbTable();

        $this->markTestIncomplete('Need solution for Zend_Db_Table when inserting to PostgreSQL');
        return;
    }

}
