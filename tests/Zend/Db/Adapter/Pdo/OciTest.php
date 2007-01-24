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
class Zend_Db_Adapter_Pdo_OciTest extends Zend_Db_Adapter_Pdo_Common
{

    public function getCreateTableSQL()
    {
        return 'CREATE TABLE  '. self::TableName . '
        (id NUMBER(11) PRIMARY KEY, subTitle CLOB, title CLOB, body CLOB, date_created CLOB)';
    }

    public function getDriver()
    {
        return 'pdo_OCI';
    }

    public function getParams()
    {
        $params = array (
            'host'     => TESTS_ZEND_DB_ADAPTER_PDO_OCI_HOSTNAME,
            'port'     => TESTS_ZEND_DB_ADAPTER_PDO_OCI_PORT,
            'username' => TESTS_ZEND_DB_ADAPTER_PDO_OCI_USERNAME,
            'password' => TESTS_ZEND_DB_ADAPTER_PDO_OCI_PASSWORD,
            'dbname'   => TESTS_ZEND_DB_ADAPTER_PDO_OCI_SID
        );

        return $params;
    }

    protected function createTestTable()
    {
        $this->_db->query('CREATE SEQUENCE '.self::TableName.'_seq');

        $this->_db->query($this->getCreateTableSQL());

        $sql = 'INSERT INTO ' . self::TableName . " (id, title, subTitle, body, date_created)
                VALUES (".self::TableName."_seq.nextval, 'News Item 1', 'Sub title 1', 'This is body 1', '2006-05-01 11:11:11')";
        $this->_db->query($sql);

        $sql = 'INSERT INTO ' . self::TableName . " (id, title, subTitle, body, date_created)
                VALUES (".self::TableName."_seq.nextval, 'News Item 2', 'Sub title 2', 'This is body 2', '2006-05-02 12:12:12')";
        $this->_db->query($sql);
    }

    protected function tearDownMetadata()
    {
        // drop test table
        $this->_db->query($this->getDropTableSQL());
        $this->_db->query('DROP SEQUENCE '.self::TableName.'_seq');
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
        $this->assertEquals("'table_name'", $value);
        $value = $this->_db->quoteIdentifier('table_`_name');
        $this->assertEquals("'table_`_name'", $value);
    }

}
