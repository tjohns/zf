<?php
/**
 * @package    Zend_Db
 * @subpackage UnitTests
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


    abstract function getCreatTableSQL();
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
        $this->_db->query($this->getCreatTableSQL());

        $sql = 'INSERT INTO ' . self::TableName . " (title, subTitle, body, date_created)
                VALUES ('News Item 1', 'Sub title 1', 'This is body 1', '2006-05-01 11:11:11')";
        $this->_db->query($sql);

        $sql = 'INSERT INTO ' . self::TableName . " (title, subTitle, body, date_created)
                VALUES ('News Item 2', 'Sub title 2', 'This is body 2', '2006-05-02 12:12:12')";
        $this->_db->query($sql);
    }

    function setUp()
    {
        // open a new connection
        $this->_db = Zend_Db::factory($this->getDriver(), $this->getParams());

        // create a test table and populate it
        $this->createTestTable();
    }

    function tearDown()
    {
        // drop test table
        $this->_db->query($this->getDropTableSQL());

        // close the PDO connection
        $connection = $this->_db->getConnection();
        $connection = null;
        $this->_db = null;
    }


    function testFetchAll()
    {
        $result = $this->_db->query('SELECT * FROM ' . self::TableName . ' WHERE date_created > :placeholder',
                        array('placeholder' => '2006-01-01')
                        );

        $rows = $result->fetchAll();
        $this->assertEquals(2, count($rows));
        $this->assertEquals('1', $rows[0]['id']);
    }

    function testFieldNamesAreLowercase()
    {
        $result = $this->_db->query('SELECT * FROM ' . self::TableName . ' WHERE date_created > :placeholder',
                        array('placeholder' => '2006-01-01')
                        );

        // use the PDOStatement $result to fetch all rows as an array
        $row = $result->fetch();

        $this->assertEquals(5, count($row)); // correct number of fields
        $this->assertEquals('1', $row['id']); // correct data
        $this->assertTrue(array_key_exists('subtitle', $row)); // "subTitle" is now "subtitle"
        $this->assertFalse(array_key_exists('subTitle', $row)); // "subTitle" is not a key

    }

    function testInsert()
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

}
