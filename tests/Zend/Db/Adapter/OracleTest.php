<?php
/**
 * @package    Zend_Db
 * @subpackage UnitTests
 */

/**
 * Common class is DB independant
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Common.php';


/**
 * @package    Zend_Db_Adapter_Pdo_MysqlTest
 * @subpackage UnitTests
 */
class Zend_Db_Adapter_OracleTest extends Zend_Db_Adapter_Common
{
    const TableName = 'ZF_TEST_TABLE';
    const SequenceName = 'ZF_TEST_TABLE_SEQ';

    public function getCreateTableSQL()
    {
        $sql = 'CREATE TABLE  '. self::TableName . '
        (id NUMBER(11) PRIMARY KEY, subTitle VARCHAR2(100), title VARCHAR2(100), body VARCHAR2(100), date_created VARCHAR2(100))';
        return $sql;
    }

    protected function getDropTableSQL()
    {
        $sql = 'DROP TABLE ' . self::TableName;
        return $sql;
    }

    protected function getCreateSequenceSQL()
    {
        $sql = 'CREATE SEQUENCE ' . self::SequenceName;
        return $sql;
    }

    protected function getDropSequenceSQL()
    {
        $sql = 'DROP SEQUENCE ' . self::SequenceName;
        return $sql;
    }

    public function getDriver()
    {
        return 'Oracle';
    }

    public function getParams()
    {
        $params = array (
            'host'     => TESTS_ZEND_DB_ADAPTER_ORACLE_HOSTNAME,
            'port'     => TESTS_ZEND_DB_ADAPTER_ORACLE_PORT,
            'username' => TESTS_ZEND_DB_ADAPTER_ORACLE_USERNAME,
            'password' => TESTS_ZEND_DB_ADAPTER_ORACLE_PASSWORD,
            'dbname'   => TESTS_ZEND_DB_ADAPTER_ORACLE_SID
        );

        return $params;
    }

    protected function tearDownMetadata()
    {
        $tableList = $this->_db->fetchCol('SELECT table_name FROM ALL_TABLES');
        if (in_array(self::TableName, $tableList['TABLE_NAME'])) {
            $this->_db->query($this->getDropTableSQL());
        }
        $seqList = $this->_db->fetchCol('SELECT sequence_name FROM ALL_SEQUENCES');
        if (in_array(self::SequenceName, $seqList['SEQUENCE_NAME'])) {
            $this->_db->query($this->getDropSequenceSQL());
        }
    }

    protected function createTestTable()
    {
        $this->tearDownMetadata();
        $this->_db->query($this->getCreateSequenceSQL());

        $this->_db->query($this->getCreateTableSQL());

        $sql = 'INSERT INTO ' . self::TableName . " (id, title, subTitle, body, date_created)
                VALUES (" . self::SequenceName . ".nextval, 'News Item 1', 'Sub title 1', 'This is body 1', '2006-05-01 11:11:11')";
        $this->_db->query($sql);

        $sql = 'INSERT INTO ' . self::TableName . " (id, title, subTitle, body, date_created)
                VALUES (" . self::SequenceName . ".nextval, 'News Item 2', 'Sub title 2', 'This is body 2', '2006-05-02 12:12:12')";
        $this->_db->query($sql);
    }

    public function testQuote()
    {
        // test double quotes are fine
        $value = $this->_db->quote('St John"s Wort');
        $this->assertEquals("St John\"s Wort", $value);

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
        $this->assertEquals("'table_name'", $value);
        $value = $this->_db->quoteIdentifier('table_`_name');
        $this->assertEquals("'table_`_name'", $value);
    }

    public function testInsert()
    {
        $row = array (
            'id'           => 3,
            'title'        => 'News Item 3',
            'subTitle'     => 'Sub title 3',
            'body'         => 'This is body 1',
            'date_created' => '2006-05-03 13:13:13'
        );
        $rows_affected = $this->_db->insert(self::TableName, $row);
        $last_insert_id = $this->_db->lastInsertId();
        $this->assertEquals('3', (string)$last_insert_id); // correct id has been set
    }

}
