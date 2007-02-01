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
 * Zend_Db
 */
require_once 'Zend/Db.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @package    Zend_Db_Adapter_Pdo_Common
 * @subpackage UnitTests
 */
abstract class Zend_Db_Adapter_Common extends PHPUnit_Framework_TestCase
{
    const TABLE_NAME = 'zf_test_table';
    protected $_resultSetUppercase = false;
    protected $_textDataType = 'text';

    abstract public function getDriver();
    abstract public function getParams();
    abstract public function getCreateTableSQL();

    /**
     * @var Zend_Db_Adapter_Pdo_Abstract
     */
    protected $_db;

    protected function getDropTableSQL()
    {
        $sql = 'DROP TABLE ' . self::TABLE_NAME;
        return $sql;
    }

    protected function createTestTable()
    {
        $this->_db->query($this->getCreateTableSQL());

        $sql = 'INSERT INTO ' . self::TABLE_NAME . " (title, subTitle, body, date_created)
                VALUES ('News Item 1', 'Sub title 1', 'This is body 1', '2006-05-01 11:11:11')";
        $this->_db->query($sql);

        $sql = 'INSERT INTO ' . self::TABLE_NAME . " (title, subTitle, body, date_created)
                VALUES ('News Item 2', 'Sub title 2', 'This is body 2', '2006-05-02 12:12:12')";
        $this->_db->query($sql);
    }

    public function setUp()
    {
        // check for driver test disabled
        $driver = $this->getDriver();
        $enabledConst = 'TESTS_ZEND_DB_ADAPTER_' . strtoupper($driver) . '_ENABLED';
        if (!(defined($enabledConst) && constant($enabledConst) == true)) {
            $this->markTestSkipped($driver . " tests disabled in TestConfiguration.php");
        }
        
        // open a new connection
        $this->_db = Zend_Db::factory($this->getDriver(), $this->getParams());

        $this->setUpMetadata();
    }

    protected function setUpMetadata()
    {
        // create a test table and populate it
        $this->tearDownMetadata();
        $this->createTestTable();
    }

    public function tearDown()
    {
        $this->tearDownMetadata();

        // close the PDO connection
        $connection = $this->_db->getConnection();
        $connection = null;
        $this->_db = null;
    }

    protected function tearDownMetadata()
    {
        // drop test table
        $sql = $this->getDropTableSQL();
        $this->_db->query($sql);
    }

    public function testDescribeTable()
    {
        $desc = $this->_db->describeTable(self::TABLE_NAME);

        $bodyKey = 'body';
        $tableName = self::TABLE_NAME;
        if ($this->_resultSetUppercase) {
            $bodyKey = strtoupper($bodyKey);
            $tableName = strtoupper($tableName);
        }

        $this->assertThat($desc, $this->arrayHasKey($bodyKey));

        $this->assertThat($desc[$bodyKey], $this->arrayHasKey('SCHEMA_NAME'));
        $this->assertThat($desc[$bodyKey], $this->arrayHasKey('TABLE_NAME'));
        $this->assertThat($desc[$bodyKey], $this->arrayHasKey('COLUMN_NAME'));
        $this->assertThat($desc[$bodyKey], $this->arrayHasKey('DATA_TYPE'));
        $this->assertThat($desc[$bodyKey], $this->arrayHasKey('DEFAULT'));
        $this->assertThat($desc[$bodyKey], $this->arrayHasKey('NULLABLE'));
        $this->assertThat($desc[$bodyKey], $this->arrayHasKey('LENGTH'));
        $this->assertThat($desc[$bodyKey], $this->arrayHasKey('SCALE'));
        $this->assertThat($desc[$bodyKey], $this->arrayHasKey('PRECISION'));
        $this->assertThat($desc[$bodyKey], $this->arrayHasKey('PRIMARY'));

        $this->assertEquals($tableName, $desc[$bodyKey]['TABLE_NAME']);
        $this->assertEquals($bodyKey, $desc[$bodyKey]['COLUMN_NAME']);
        $this->assertEquals($this->_textDataType, $desc[$bodyKey]['DATA_TYPE']);
        $this->assertEquals('', $desc[$bodyKey]['DEFAULT']);
        $this->assertTrue($desc[$bodyKey]['NULLABLE']);
        $this->assertEquals(0, $desc[$bodyKey]['SCALE']);
        $this->assertEquals(0, $desc[$bodyKey]['PRECISION']);
        $this->assertEquals('', $desc[$bodyKey]['PRIMARY']);
    }

    public function testFetchAll()
    {
        $colName = 'id';
        if ($this->_resultSetUppercase) {
            $colName = strtoupper($colName);
        }

        $result = $this->_db->query(
            'SELECT * FROM ' . self::TABLE_NAME . ' WHERE date_created > ?',
            array('2006-01-01')
        );

        $rows = $result->fetchAll();
        $this->assertEquals(2, count($rows));
        $this->assertEquals('1', $rows[0][$colName]);
    }

    public function testInsert()
    {
        $row = array (
            'title'        => 'News Item 3',
            'subTitle'     => 'Sub title 3',
            'body'         => 'This is body 1',
            'date_created' => '2006-05-03 13:13:13'
            );
        $rows_affected = $this->_db->insert(self::TABLE_NAME, $row);
        $last_insert_id = $this->_db->lastInsertId();
        $this->assertEquals('3', (string) $last_insert_id); // correct id has been set
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
        $this->assertEquals(5, count($rows[0]));
        $this->assertEquals(1, $rows[0][$colName]);

        $sql = $this->_db->limit('SELECT * FROM ' . self::TABLE_NAME, 1, 1);

        $result = $this->_db->query($sql);
        $rows = $result->fetchAll();
        $this->assertEquals(1, count($rows));
        $this->assertEquals(5, count($rows[0]));
        $this->assertEquals(2, $rows[0][$colName]);
    }

    public function testListTables()
    {
        $tableName = self::TABLE_NAME;
        if ($this->_resultSetUppercase) {
            $tableName = strtoupper($tableName);
        }

        $tables = $this->_db->listTables();
        $this->assertContains($tableName, $tables);
    }

    public function testProfilerCreation()
    {
        $this->assertThat($this->_db->getProfiler(), $this->isInstanceOf('Zend_Db_Profiler'));
    }

    public function testSelect()
    {
        $colName = 'id';
        if ($this->_resultSetUppercase) {
            $colName = strtoupper($colName);
        }

        $select = $this->_db->select();
        $this->assertThat($select, $this->isInstanceOf('Zend_Db_Select'));

        $select->from(self::TABLE_NAME);
        $result = $this->_db->query($select);
        $row = $result->fetch();
        $this->assertEquals(5, count($row)); // correct number of fields
        $this->assertEquals('1', $row[$colName]); // correct data
    }

}
