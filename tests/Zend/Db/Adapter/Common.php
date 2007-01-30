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
    const TableName = 'zf_test_table';

    abstract function getCreateTableSQL();
    abstract function getParams();
    abstract function getDriver();

    /**
     * @var Zend_Db_Adapter_Pdo_Abstract
     */
    protected $_db;

    protected function getDropTableSQL()
    {
        return 'DROP TABLE ' . self::TableName;
    }

    protected function createTestTable()
    {
        $this->_db->query($this->getCreateTableSQL());

        $sql = 'INSERT INTO ' . self::TableName . " (title, subTitle, body, date_created)
                VALUES ('News Item 1', 'Sub title 1', 'This is body 1', '2006-05-01 11:11:11')";
        $this->_db->query($sql);

        $sql = 'INSERT INTO ' . self::TableName . " (title, subTitle, body, date_created)
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
        $this->_db->query($this->getDropTableSQL());
    }

    public function testListTables()
    {
        $tables = $this->_db->listTables();
        $this->assertContains(strtoupper(self::TableName), array_values($tables));
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
        $this->assertEquals('VARCHAR', $desc['BODY']['DATA_TYPE']);
        $this->assertEquals('', $desc['BODY']['DEFAULT']);
        $this->assertTrue($desc['BODY']['NULLABLE']);
        $this->assertEquals(100, $desc['BODY']['LENGTH']);
        $this->assertEquals(0, $desc['BODY']['SCALE']);
        $this->assertEquals(0, $desc['BODY']['PRECISION']);
        $this->assertEquals('', $desc['BODY']['PRIMARY']);
    }

    public function testFetchAll()
    {
        $result = $this->_db->query(
            'SELECT * FROM ' . self::TableName . ' WHERE date_created > ?',
            array('2006-01-01')
        );

        $rows = $result->fetchAll();
        $this->assertEquals(2, count($rows));
        $this->assertEquals('1', $rows[0]['ID']);
    }

    public function testInsert()
    {
        $row = array (
            'title' => 'News Item 3',
            'subTitle' => 'Sub title 3',
            'body' => 'This is body 1',
            'date_created' => '2006-05-03 13:13:13'
            );
        $rows_affected = $this->_db->insert(self::TableName, $row);
        $last_insert_id = $this->_db->lastInsertId();
        $this->assertEquals('3', (string)$last_insert_id); // correct id has been set
    }

    public function testLimit()
    {
        $sql = $this->_db->limit('SELECT * FROM ' . self::TableName, 1);

        $result = $this->_db->query($sql);
        $rows = $result->fetchAll();
        $this->assertEquals(1, count($rows));
        $this->assertEquals(5, count($rows[0]));
        $this->assertEquals(1, $rows[0]['ID']);

        $sql = $this->_db->limit('SELECT * FROM ' . self::TableName, 1, 1);

        $result = $this->_db->query($sql);
        $rows = $result->fetchAll();
        $this->assertEquals(1, count($rows));
        $this->assertEquals(5, count($rows[0]));
        $this->assertEquals(2, $rows[0]['ID']);
    }

    public function testProfilerCreation()
    {
        $this->assertThat($this->_db->getProfiler(), $this->isInstanceOf('Zend_Db_Profiler'));
    }

    public function testSelect()
    {
        $select = $this->_db->select();
        $this->assertThat($select, $this->isInstanceOf('Zend_Db_Select'));

        $select->from(self::TableName);
        $result = $this->_db->query($select);
        $row = $result->fetch();
        $this->assertEquals(5, count($row)); // correct number of fields
        $this->assertEquals('1', $row['ID']); // correct data
    }

}
