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
 * @package    Zend_Db
 * @subpackage UnitTests
 */
class Zend_Db_Adapter_Pdo_OciTest extends Zend_Db_Adapter_Pdo_Common
{
    const TableName    = 'ZF_TEST_TABLE';
    const SequenceName = 'ZF_TEST_TABLE_SEQ';

    public function getDriver()
    {
        return 'pdo_OCI';
    }

    public function getCreateTableSQL()
    {
        $sql = 'CREATE TABLE  '. self::TableName . '
        (id NUMBER(11) PRIMARY KEY, subTitle VARCHAR2(100), title VARCHAR2(100), body VARCHAR2(100), date_created VARCHAR2(100))';
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

    public function getParams()
    {
        $params = array (
            'host'     => TESTS_ZEND_DB_ADAPTER_ORACLE_HOSTNAME,
            'username' => TESTS_ZEND_DB_ADAPTER_ORACLE_USERNAME,
            'password' => TESTS_ZEND_DB_ADAPTER_ORACLE_PASSWORD,
            'dbname'   => TESTS_ZEND_DB_ADAPTER_ORACLE_SID
        );

        return $params;
    }

    protected function tearDownMetadata()
    {
        $tableList = $this->_db->fetchCol('SELECT table_name FROM ALL_TABLES');
        if (in_array(self::TableName, $tableList)) {
            $this->_db->query($this->getDropTableSQL());
        }
        $seqList = $this->_db->fetchCol('SELECT sequence_name FROM ALL_SEQUENCES');
        if (in_array(self::SequenceName, $seqList)) {
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

    public function testDescribeTable()
    {
        $desc = $this->_db->describeTable(self::TableName);

        $this->assertContains('BODY', array_keys($desc));

        $this->assertContains('SCHEMA_NAME', array_keys($desc['BODY']));
        $this->assertContains('TABLE_NAME', array_keys($desc['BODY']));
        $this->assertContains('COLUMN_NAME', array_keys($desc['BODY']));
        $this->assertContains('DATA_TYPE', array_keys($desc['BODY']));
        $this->assertContains('DEFAULT', array_keys($desc['BODY']));
        $this->assertContains('NULLABLE', array_keys($desc['BODY']));
        $this->assertContains('LENGTH', array_keys($desc['BODY']));
        $this->assertContains('SCALE', array_keys($desc['BODY']));
        $this->assertContains('PRECISION', array_keys($desc['BODY']));
        $this->assertContains('PRIMARY', array_keys($desc['BODY']));

        $this->assertEquals(strtoupper(self::TableName), $desc['BODY']['TABLE_NAME']);
        $this->assertEquals('BODY', $desc['BODY']['COLUMN_NAME']);
        $this->assertEquals('VARCHAR2', $desc['BODY']['DATA_TYPE']);
        $this->assertEquals('', $desc['BODY']['DEFAULT']);
        $this->assertTrue($desc['BODY']['NULLABLE']);
        $this->assertEquals(100, $desc['BODY']['LENGTH']);
        $this->assertEquals(0, $desc['BODY']['SCALE']);
        $this->assertEquals(0, $desc['BODY']['PRECISION']);
        $this->assertEquals('', $desc['BODY']['PRIMARY']);
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
        $value = $this->_db->quoteIdentifier('table_`_name');
        $this->assertEquals('"table_`_name"', $value);
    }

    public function testLimit()
    {
        $sql = 'SELECT * FROM ' . self::TableName . ' ORDER BY ID';
        $sql = $this->_db->limit($sql, 1);
        $result = $this->_db->query($sql);
        $rows = $result->fetchAll();
        $this->assertEquals(1, count($rows));
        $this->assertEquals(6, count($rows[0]));
        $this->assertEquals(1, $rows[0]['id']);

        $sql = 'SELECT * FROM ' . self::TableName . ' ORDER BY ID';
        $sql = $this->_db->limit($sql, 1, 1);
        $result = $this->_db->query($sql);
        $rows = $result->fetchAll();
        $this->assertEquals(1, count($rows));
        $this->assertEquals(6, count($rows[0]));
        $this->assertEquals(2, $rows[0]['id']);
    }

    public function testInsert()
    {
        $nextId = $this->_db->fetchOne('SELECT ' . self::SequenceName . '.NEXTVAL FROM DUAL');
        $row = array (
            'id'           => $nextId,
            'title'        => 'News Item 3',
            'subTitle'     => 'Sub title 3',
            'body'         => 'This is body 1',
            'date_created' => '2006-05-03 13:13:13'
        );
        $rows_affected = $this->_db->insert(self::TableName, $row);
        $last_insert_id = $this->_db->lastInsertId(self::SequenceName);
        $this->assertEquals(3, $last_insert_id); // correct id has been set
    }

    public function testSelect()
    {
        $select = $this->_db->select();
        $this->assertThat($select, $this->isInstanceOf('Zend_Db_Select'));

        $select->from(self::TableName);
        $result = $this->_db->query($select);
        $row = $result->fetch();
        $this->assertEquals(5, count($row)); // correct number of fields
        $this->assertEquals('1', $row['id']); // correct data
    }

}
