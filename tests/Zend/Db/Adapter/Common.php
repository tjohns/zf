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

        $this->assertContains('body', array_keys($desc));

        $this->assertContains('SCHEMA_NAME', array_keys($desc['body']));
        $this->assertContains('TABLE_NAME', array_keys($desc['body']));
        $this->assertContains('COLUMN_NAME', array_keys($desc['body']));
        $this->assertContains('DATA_TYPE', array_keys($desc['body']));
        $this->assertContains('DEFAULT', array_keys($desc['body']));
        $this->assertContains('NULLABLE', array_keys($desc['body']));
        $this->assertContains('LENGTH', array_keys($desc['body']));
        $this->assertContains('SCALE', array_keys($desc['body']));
        $this->assertContains('PRECISION', array_keys($desc['body']));
        $this->assertContains('PRIMARY', array_keys($desc['body']));

        $this->assertEquals(self::TABLE_NAME, $desc['body']['TABLE_NAME']);
        $this->assertEquals('body', $desc['body']['COLUMN_NAME']);
        $this->assertEquals('VARCHAR', $desc['body']['DATA_TYPE']);
        $this->assertEquals('', $desc['body']['DEFAULT']);
        $this->assertTrue($desc['body']['NULLABLE']);
        $this->assertEquals(100, $desc['body']['LENGTH']);
        $this->assertEquals(0, $desc['body']['SCALE']);
        $this->assertEquals(0, $desc['body']['PRECISION']);
        $this->assertEquals('', $desc['body']['PRIMARY']);
    }

    public function testFetchAll()
    {
        $result = $this->_db->query(
            'SELECT * FROM ' . self::TABLE_NAME . ' WHERE date_created > ?',
            array('2006-01-01')
        );

        $rows = $result->fetchAll();
        $this->assertEquals(2, count($rows));
        $this->assertEquals('1', $rows[0]['id']);
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
        $sql = $this->_db->limit('SELECT * FROM ' . self::TABLE_NAME, 1);

        $result = $this->_db->query($sql);
        $rows = $result->fetchAll();
        $this->assertEquals(1, count($rows));
        $this->assertEquals(5, count($rows[0]));
        $this->assertEquals(1, $rows[0]['id']);

        $sql = $this->_db->limit('SELECT * FROM ' . self::TABLE_NAME, 1, 1);

        $result = $this->_db->query($sql);
        $rows = $result->fetchAll();
        $this->assertEquals(1, count($rows));
        $this->assertEquals(5, count($rows[0]));
        $this->assertEquals(2, $rows[0]['id']);
    }

    public function testListTables()
    {
        $tables = $this->_db->listTables();
        $this->assertContains(self::TABLE_NAME, $tables);
    }

    public function testProfilerCreation()
    {
        $this->assertThat($this->_db->getProfiler(), $this->isInstanceOf('Zend_Db_Profiler'));
    }

    public function testSelect()
    {
        $select = $this->_db->select();
        $this->assertThat($select, $this->isInstanceOf('Zend_Db_Select'));

        $select->from(self::TABLE_NAME);
        $result = $this->_db->query($select);
        $row = $result->fetch();
        $this->assertEquals(5, count($row)); // correct number of fields
        $this->assertEquals('1', $row['id']); // correct data
    }

}
