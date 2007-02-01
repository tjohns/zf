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
class Zend_Db_Adapter_Db2Test extends Zend_Db_Adapter_Common
{
    protected $_resultSetUppercase = true;
    protected $_textDataType = 'VARCHAR';
    const TABLE_NAME = 'ZF_TEST_TABLE';
    const SEQUENCE_NAME = 'ZF_TEST_TABLE_SEQ';

    public function getDriver()
    {
        return 'Db2';
    }

    public function getParams()
    {
        $params = array (
            'host'     => TESTS_ZEND_DB_ADAPTER_DB2_HOSTNAME,
            'username' => TESTS_ZEND_DB_ADAPTER_DB2_USERNAME,
            'password' => TESTS_ZEND_DB_ADAPTER_DB2_PASSWORD,
            'dbname'   => TESTS_ZEND_DB_ADAPTER_DB2_DATABASE
        );
        return $params;
    }

    public function getCreateTableSQL()
    {
        $sql = 'CREATE TABLE  '. self::TABLE_NAME . " (
            id           INT NOT NULL PRIMARY KEY,
            subTitle     {$this->_textDataType}(100),
            title        {$this->_textDataType}(100),
            body         {$this->_textDataType}(100),
            date_created {$this->_textDataType}(100)
        )";
        return $sql;
    }

    protected function getDropTableSQL()
    {
        $sql = 'DROP TABLE ' . self::TABLE_NAME;
        return $sql;
    }

    protected function getCreateSequenceSQL()
    {
        $sql = 'CREATE SEQUENCE ' . self::SEQUENCE_NAME . ' AS INTEGER';
        return $sql;
    }

    protected function getDropSequenceSQL()
    {
        $sql = 'DROP SEQUENCE ' . self::SEQUENCE_NAME . ' RESTRICT';
        return $sql;
    }

    protected function tearDownMetadata()
    {
        $tableList = $this->_db->fetchCol('SELECT tabname FROM SYSCAT.TABLES');
        if (in_array(self::TABLE_NAME, $tableList)) {
            $this->_db->query($this->getDropTableSQL());
        }
        $seqList = $this->_db->fetchCol('SELECT seqname FROM SYSCAT.SEQUENCES');
        if (in_array(self::SEQUENCE_NAME, $seqList)) {
            $this->_db->query($this->getDropSequenceSQL());
        }
    }

    protected function createTestTable()
    {
        $this->tearDownMetadata();
        $this->_db->query($this->getCreateSequenceSQL());

        $this->_db->query($this->getCreateTableSQL());

        $sql = 'INSERT INTO ' . self::TABLE_NAME . " (id, title, subTitle, body, date_created)
                VALUES (NEXTVAL FOR " . self::SEQUENCE_NAME . ", 'News Item 1', 'Sub title 1', 'This is body 1', '2006-05-01 11:11:11')";
        $this->_db->query($sql);

        $sql = 'INSERT INTO ' . self::TABLE_NAME . " (id, title, subTitle, body, date_created)
                VALUES (NEXTVAL FOR " . self::SEQUENCE_NAME . ", 'News Item 2', 'Sub title 2', 'This is body 2', '2006-05-02 12:12:12')";
        $this->_db->query($sql);
    }

    public function testInsert()
    {
        // @todo get NEXTVAL FOR self::SEQUENCE_NAME
        $row = array (
            'id'           => 3,
            'title'        => 'News Item 3',
            'subTitle'     => 'Sub title 3',
            'body'         => 'This is body 1',
            'date_created' => '2006-05-03 13:13:13'
        );
        $rows_affected = $this->_db->insert(self::TABLE_NAME, $row);
        // @todo specify sequence name as argument
        $last_insert_id = $this->_db->lastInsertId();
        $this->assertEquals('3', (string)$last_insert_id); // correct id has been set
    }

    public function testLimit()
    {
        $colName = 'id';
        if ($this->_resultSetUppercase) {
            $colName = strtoupper($colName);
        }

        $sql = $this->_db->limit('SELECT * FROM ' . self::TABLE_NAME, 1);

        $result = $this->_db->query($sql);
        $rows = $result->fetchAll();

        $this->assertEquals(1, count($rows));
        $this->assertEquals(6, count($rows[0]));
        $this->assertEquals(1, $rows[0][$colName]);

        $sql = $this->_db->limit('SELECT * FROM ' . self::TABLE_NAME, 1, 1);

        $result = $this->_db->query($sql);
        $rows = $result->fetchAll();
        $this->assertEquals(1, count($rows));
        $this->assertEquals(6, count($rows[0]));
        $this->assertEquals(2, $rows[0][$colName]);
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
        $value = $this->_db->quoteInto('id = ?', "St John's Wort");
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
