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
require_once 'PHPUnit/Util/Filter.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
 * @package    Zend_Db_Adapter_Pdo_Common
 * @subpackage UnitTests
 */
abstract class Zend_Db_Adapter_Common extends PHPUnit_Framework_TestCase
{
    const TABLE_NAME = 'zf_test_table';
    const TABLE_NAME_2 = 'zf_test_table2';

    protected $_resultSetUppercase = false;
    protected $_textDataType = 'text';

    abstract public function getDriver();
    abstract public function getParams();
    abstract public function getCreateTableSQL();
    abstract public function getCreateTableSQL2();
    abstract public function testExceptionInvalidLoginCredentials();

    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db;

    /**
     * @param string The name of the identifier, to be transformed.
     * @return string The name of a column or table, transformed for the
     * current adapter.
     */
    public function getIdentifier($name)
    {
        if ($this->_resultSetUppercase) {
            return strtoupper($name);
        } else {
            return $name;
        }
    }

    /**
     * @return string SQL statement for dropping the test table.
     */
    protected function getDropTableSQL()
    {
        $sql = 'DROP TABLE ' . self::TABLE_NAME;
        return $sql;
    }

    /**
     * @return string SQL statement for dropping the test table.
     */
    protected function getDropTableSQL2()
    {
        $sql = 'DROP TABLE ' . self::TABLE_NAME_2;
        return $sql;
    }

    /**
     * Create the test table and populate it with some rows of data.
     * @return void
     */
    protected function createTestTable()
    {
        $sql = $this->getCreateTableSQL();
        $this->_db->query($sql);

        $sql = 'INSERT INTO ' . self::TABLE_NAME . "
            (title, subTitle, body, date_created)
            VALUES ('News Item 1', 'Sub title 1', 'This is body 1', '2006-05-01 11:11:11')";
        $this->_db->query($sql);

        $sql = 'INSERT INTO ' . self::TABLE_NAME . "
            (title, subTitle, body, date_created)
            VALUES ('News Item 2', 'Sub title 2', 'This is body 2', '2006-05-02 12:12:12')";
        $this->_db->query($sql);
    }

    /**
     * Create a second test table that is used for joins.
     * @return void
     */
    protected function createTestTable2()
    {
        $sql = $this->getCreateTableSQL2();
        $this->_db->query($sql);

        $sql = 'INSERT INTO ' . self::TABLE_NAME_2 . "
            (news_id, user_id, commentTitle, commentBody, date_posted)
            VALUES (1, 101, 'I agree', 'This is comment 1', '2006-05-01 13:13:13')";
        $this->_db->query($sql);

        $sql = 'INSERT INTO ' . self::TABLE_NAME_2 . "
            (news_id, user_id, commentTitle, commentBody, date_posted)
            VALUES (1, 102, 'I disagree', 'This is comment 2', '2006-05-01 14:14:14')";
        $this->_db->query($sql);

        $sql = 'INSERT INTO ' . self::TABLE_NAME_2 . "
            (news_id, user_id, commentTitle, commentBody, date_posted)
            VALUES (1, 101, 'I still agree', 'This is comment 3', '2006-05-01 15:15:15')";
        $this->_db->query($sql);
    }

    /**
     * Skip test if driver is disabled in TestConfiguration.php.
     * Instantiate driver.  Set up database metadata.
     */
    public function setUp()
    {
        // check for driver test disabled
        $driver = $this->getDriver();
        $enabledConst = 'TESTS_ZEND_DB_ADAPTER_' . strtoupper($driver) . '_ENABLED';
        if (!(defined($enabledConst) && constant($enabledConst) == true)) {
            $this->markTestSkipped("Tests for Zend_Db adapter $driver are disabled in TestConfiguration.php");
            return;
        }
        
        // open a new connection
        $this->_db = Zend_Db::factory($this->getDriver(), $this->getParams());

        $this->setUpMetadata();
    }

    /**
     * Drop existing test table if such exists.
     * This is also where one would drop other metadata objects, like sequences.
     * Then create clean test table.
     */
    protected function setUpMetadata()
    {
        // create a test table and populate it
        $this->tearDownMetadata();
        $this->createTestTable();
    }

    /**
     * Drop test table and close connection.
     */
    public function tearDown()
    {
        $this->tearDownMetadata();

        // close the PDO connection
        $connection = $this->_db->getConnection();
        $connection = null;
        $this->_db = null;
    }

    /**
     * Drop test table.
     */
    protected function tearDownMetadata()
    {
        $sql = $this->getDropTableSQL();
        $this->_db->query($sql);
        $sql = $this->getDropTableSQL2();
        $this->_db->query($sql);
    }

    /**
     * Test Adapter's delete() method.
     * Delete one row from test table, and verify it was deleted.
     * Then try to delete a row that doesn't exist, and verify it had no effect.
     *
     * @todo: test that require delimited identifiers.
     */
    public function testDelete()
    {
        $id = $this->getIdentifier('id');

        $result = $this->_db->delete(self::TABLE_NAME, 'id = 2');
        $this->assertEquals(1, $result);

        $select = $this->_db->select()
            ->from(self::TABLE_NAME);
        $result = $this->_db->fetchAll($select);

        $this->assertEquals(1, count($result));
        $this->assertEquals(1, $result[0][$id]);

        $result = $this->_db->delete(self::TABLE_NAME, 'id = 327');
        $this->assertEquals(0, $result);
    }

    /**
     * Test Adapter's describeTable() method.
     * Retrieve the adapter's description of the test table and examine it.
     */
    public function testDescribeTable()
    {
        $desc = $this->_db->describeTable(self::TABLE_NAME);

        $bodyKey = $this->getIdentifier('body');
        $tableName = $this->getIdentifier(self::TABLE_NAME);

        $this->assertThat($desc, $this->arrayHasKey($bodyKey));

        $this->assertThat($desc[$bodyKey], $this->arrayHasKey('SCHEMA_NAME'));
        $this->assertThat($desc[$bodyKey], $this->arrayHasKey('TABLE_NAME'));
        $this->assertThat($desc[$bodyKey], $this->arrayHasKey('COLUMN_NAME'));
        $this->assertThat($desc[$bodyKey], $this->arrayHasKey('COLUMN_POSITION'));
        $this->assertThat($desc[$bodyKey], $this->arrayHasKey('DATA_TYPE'));
        $this->assertThat($desc[$bodyKey], $this->arrayHasKey('DEFAULT'));
        $this->assertThat($desc[$bodyKey], $this->arrayHasKey('NULLABLE'));
        $this->assertThat($desc[$bodyKey], $this->arrayHasKey('LENGTH'));
        $this->assertThat($desc[$bodyKey], $this->arrayHasKey('SCALE'));
        $this->assertThat($desc[$bodyKey], $this->arrayHasKey('PRECISION'));
        $this->assertThat($desc[$bodyKey], $this->arrayHasKey('UNSIGNED'));
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

    /**
     * Test the Adapter's fetchAll() method.
     */
    public function testAdapterFetchAll()
    {
        $id = $this->getIdentifier('id');
        $result = $this->_db->fetchAll(
            'SELECT * FROM ' . self::TABLE_NAME . ' WHERE date_created > ? ORDER BY id ASC',
            array('2006-01-01')
        );
        $this->assertEquals(2, count($result));
        $this->assertEquals('1', $result[0][$id]);
    }

    /**
     * Test the Adapter's fetchAssoc() method.
     */
    public function testAdapterFetchAssoc()
    {
        $id = $this->getIdentifier('id');
        $result = $this->_db->fetchAssoc(
            'SELECT * FROM ' . self::TABLE_NAME . ' WHERE date_created > ? ORDER BY id DESC',
            array('2006-01-01')
        );
        foreach ($result as $idKey => $row) {
            $this->assertEquals($idKey, $row[$id]);
        }
    }

    /**
     * Test the Adapter's fetchCol() method.
     */
    public function testAdapterFetchCol()
    {
        $id = $this->getIdentifier('id');
        $result = $this->_db->fetchCol(
            'SELECT * FROM ' . self::TABLE_NAME . ' WHERE date_created > ? ORDER BY id',
            array('2006-01-01')
        );
        $this->assertEquals(2, count($result)); // count rows
        $this->assertEquals(1, $result[0]);
        $this->assertEquals(2, $result[1]);
    }

    /**
     * Test the Adapter's fetchOne() method.
     */
    public function testAdapterFetchOne()
    {
        $title = 'News Item 1';
        $result = $this->_db->fetchOne(
            'SELECT title FROM ' . self::TABLE_NAME . ' WHERE date_created > ? ORDER BY id',
            array('2006-01-01')
        );
        $this->assertEquals($title, $result);
    }

    /**
     * Test the Adapter's fetchPairs() method.
     */
    public function testAdapterFetchPairs()
    {
        $title = 'News Item 1';
        $result = $this->_db->fetchPairs(
            'SELECT id, title FROM ' . self::TABLE_NAME . ' WHERE date_created > ? ORDER BY id',
            array('2006-01-01')
        );
        $this->assertEquals(2, count($result)); // count rows
        $this->assertEquals($title, $result[1]);
    }

    /**
     * Test the Adapter's fetchRow() method.
     */
    public function testAdapterFetchRow()
    {
        $id = $this->getIdentifier('id');

        $result = $this->_db->fetchRow(
            'SELECT * FROM ' . self::TABLE_NAME . ' WHERE date_created > ? ORDER BY id',
            array('2006-01-01')
        );
        $this->assertEquals(5, count($result)); // count columns
        $this->assertEquals(1, $result[$id]);
    }

    /**
     * Test the Statement's fetchAll() method.
     */
    public function testStatementFetchAll()
    {
        $id = $this->getIdentifier('id');
        $stmt = $this->_db->query(
            'SELECT * FROM ' . self::TABLE_NAME . " WHERE date_created > '2006-01-01' ORDER BY id"
        );
        $result = $stmt->fetchAll();
        $this->assertEquals(2, count($result));
        $this->assertEquals(5, count($result[0]));
        $this->assertEquals(1, $result[0][$id]);
    }

    /**
     * Test the Statement's fetchColumn() method.
     */
    public function testStatementFetchColumn()
    {
        $stmt = $this->_db->query(
            'SELECT * FROM ' . self::TABLE_NAME . " WHERE date_created > '2006-01-01' ORDER BY id"
        );
        $result = $stmt->fetchColumn();
        $this->assertEquals(1, $result);
        $result = $stmt->fetchColumn();
        $this->assertEquals(2, $result);
    }

    /**
     * Test the Statement's fetchObject() method.
     */
    public function testStatementFetchObject()
    {
        $title = 'News Item 1';
        $titleCol = $this->getIdentifier('title');
        $stmt = $this->_db->query(
            'SELECT * FROM ' . self::TABLE_NAME . " WHERE date_created > '2006-01-01' ORDER BY id"
        );
        $result = $stmt->fetchObject();
        $this->assertThat($result, $this->isInstanceOf('stdClass'));
        $this->assertEquals($title, $result->$titleCol);
    }

    /**
     * Test the Adapter's insert() method.
     * This requires providing an associative array of column=>value pairs.
     *
     * @todo: test that require delimited identifiers.
     */
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

    /**
     * Test the Adapter's limit() method.
     * Fetch 1 row.  Then fetch 1 row offset by 1 row.
     */
    public function testLimit()
    {
        $id = $this->getIdentifier('id');

        $sql = $this->_db->limit('SELECT * FROM ' . self::TABLE_NAME, 1);

        $result = $this->_db->query($sql);
        $result = $result->fetchAll();
        $this->assertEquals(1, count($result));
        $this->assertEquals(5, count($result[0]));
        $this->assertEquals(1, $result[0][$id]);

        $sql = $this->_db->limit('SELECT * FROM ' . self::TABLE_NAME, 1, 1);

        $result = $this->_db->query($sql);
        $result = $result->fetchAll();
        $this->assertEquals(1, count($result));
        $this->assertEquals(5, count($result[0]));
        $this->assertEquals(2, $result[0][$id]);
    }

    /**
     * Test the Adapter's listTables() method.
     * Fetch the list of tables and verify that the test table exists in
     * the list.
     */
    public function testListTables()
    {
        $tableName = $this->getIdentifier(self::TABLE_NAME);

        $tables = $this->_db->listTables();
        $this->assertContains($tableName, $tables);
    }

    /**
     * Test that the Adapter has an instance of a Zend_Db_Profiler object.
     */
    public function testProfilerCreation()
    {
        $this->assertThat($this->_db->getProfiler(), $this->isInstanceOf('Zend_Db_Profiler'));
    }

    /**
     * Test basic use of the Zend_Db_Select class.
     */
    public function testSelect()
    {
        $id = $this->getIdentifier('id');

        $select = $this->_db->select();
        $this->assertThat($select, $this->isInstanceOf('Zend_Db_Select'));

        $select->from(self::TABLE_NAME);
        $stmt = $this->_db->query($select);
        $row = $stmt->fetch();
        $this->assertEquals(5, count($row)); // correct number of fields
        $this->assertEquals('1', $row[$id]); // correct data
    }

    /**
     * Test basic use of the Zend_Db_Select class.
     */
    public function testSelectQuery()
    {
        $id = $this->getIdentifier('id');

        $select = $this->_db->select();
        $this->assertThat($select, $this->isInstanceOf('Zend_Db_Select'));

        $select->from(self::TABLE_NAME);
        $stmt = $select->query();
        $row = $stmt->fetch();
        $this->assertEquals(5, count($row)); // correct number of fields
        $this->assertEquals('1', $row[$id]); // correct data
    }

    /**
     * Test Zend_Db_Select specifying columns
     */
    public function testSelectColumns()
    {
        $title = $this->getIdentifier('title');
        $subtitle = $this->getIdentifier('subtitle');

        $select = $this->_db->select()
            ->from(self::TABLE_NAME, $title); // scalar
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, count($result[0]));
        $this->assertThat($result[0], $this->arrayHasKey($title));

        $select = $this->_db->select()
            ->from(self::TABLE_NAME, array($title, $subtitle)); // array
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(2, count($result[0]));
        $this->assertThat($result[0], $this->arrayHasKey($title));
        $this->assertThat($result[0], $this->arrayHasKey($subtitle));
    }

    /**
     * Test adding the FOR UPDATE query modifier to a Zend_Db_Select object.
     */
    public function testSelectDistinctModifier()
    {
        $id = $this->getIdentifier('id');

        $select = $this->_db->select()
            ->distinct()
            ->from(self::TABLE_NAME, array())
            ->from('', 327);
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, count($result));
    }

    /**
     * Test adding the FOR UPDATE query modifier to a Zend_Db_Select object.
     *
    public function testSelectForUpdateModifier()
    {
    }
     */

    /**
     * Test adding a JOIN to a Zend_Db_Select object.
     */
    public function testSelectJoinClause()
    {
        $this->createTestTable2();
        $select = $this->_db->select()
            ->from(self::TABLE_NAME)
            ->join(self::TABLE_NAME_2, 'id = news_id');
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result));
        $this->assertEquals(10, count($result[0]));
    }

    /**
     * Test adding an INNER JOIN to a Zend_Db_Select object.
     * This should be exactly the same as the plain JOIN clause.
     */
    public function testSelectJoinClauseWithCorrelationName()
    {
        $this->createTestTable2();
        $select = $this->_db->select()
            ->from( array(self::TABLE_NAME => 'xyz1') )
            ->join( array(self::TABLE_NAME_2 => 'xyz2'), 'xyz1.id = xyz2.news_id')
            ->where('xyz1.id = 1');
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result));
        $this->assertEquals(10, count($result[0]));
    }

    /**
     * Test adding an INNER JOIN to a Zend_Db_Select object.
     * This should be exactly the same as the plain JOIN clause.
     */
    public function testSelectJoinInnerClause()
    {
        $this->createTestTable2();
        $select = $this->_db->select()
            ->from(self::TABLE_NAME)
            ->joinInner(self::TABLE_NAME_2, 'id = news_id');
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result));
        $this->assertEquals(10, count($result[0]));
    }

    /**
     * Test adding an outer join to a Zend_Db_Select object.
     */
    public function testSelectJoinLeftClause()
    {
        $id = $this->getIdentifier('id');
        $newsId = $this->getIdentifier('news_id');

        $this->createTestTable2();
        $select = $this->_db->select()
            ->from(self::TABLE_NAME)
            ->joinLeft(self::TABLE_NAME_2, 'id = news_id');
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(4, count($result));
        $this->assertEquals(10, count($result[0]));
        $this->assertEquals(2, $result[3][$id]);
        $this->assertNull($result[3][$newsId]);
    }

    /**
     * Test adding an outer join to a Zend_Db_Select object.
     */
    public function testSelectJoinRightClause()
    {
        if ($this->getDriver() == 'pdo_Sqlite') {
            $this->markTestSkipped('SQLite does not support RIGHT OUTER JOIN');
            return;
        }

        $id = $this->getIdentifier('id');
        $newsId = $this->getIdentifier('news_id');

        $this->createTestTable2();
        $select = $this->_db->select()
            ->from(self::TABLE_NAME_2)
            ->joinRight(self::TABLE_NAME, 'id = news_id');
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(4, count($result));
        $this->assertEquals(10, count($result[0]));
        $this->assertEquals(2, $result[3][$id]);
        $this->assertNull($result[3][$newsId]);
    }

    /**
     * Test adding a cross join to a Zend_Db_Select object.
     */
    public function testSelectJoinCrossClause()
    {
        if ($this->getDriver() == 'Db2') {
            $this->markTestSkipped('DB2 does not support CROSS JOIN');
            return;
        }

        $id = $this->getIdentifier('id');
        $newsId = $this->getIdentifier('news_id');

        $this->createTestTable2();
        $select = $this->_db->select()
            ->from(self::TABLE_NAME)
            ->joinCross(self::TABLE_NAME_2);
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(6, count($result));
        $this->assertEquals(10, count($result[0]));
    }

    /**
     * Test adding a WHERE clause to a Zend_Db_Select object.
     * @todo: test where() with 2 args for quoteInto()
     */
    public function testSelectWhereClause()
    {
        $id = $this->getIdentifier('id');

        $select = $this->_db->select()
            ->from(self::TABLE_NAME)
            ->where('id = 2');
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, count($result));
        $this->assertEquals(2, $result[0][$id]);

        // test adding more WHERE conditions,
        // which should be combined with AND by default.
        $select = $this->_db->select()
            ->from(self::TABLE_NAME)
            ->where('id = 2')
            ->where('id = 1');
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(0, count($result));
    }

    /**
     * Test adding an OR WHERE clause to a Zend_Db_Select object.
     * @todo: test orWhere() with 2 args for quoteInto()
     */
    public function testSelectOrWhereClause()
    {
        $id = $this->getIdentifier('id');

        $select = $this->_db->select()
            ->from(self::TABLE_NAME)
            ->orWhere('id = 1')
            ->orWhere('id = 2');
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(2, count($result));
        $this->assertEquals(1, $result[0][$id]);
        $this->assertEquals(2, $result[1][$id]);
    }

    /**
     * Test adding a GROUP BY clause to a Zend_Db_Select object.
     */
    public function testSelectGroupByClause()
    {
        // @todo: fix ZF-884
        if ($this->getDriver() == 'pdo_Sqlite') {
            $this->markTestIncomplete('Pending fix for ZF-884');
            return;
        }

        $userId = $this->getIdentifier('user_id');
        $count = $this->getIdentifier('thecount');

        $this->createTestTable2();

        $select = $this->_db->select()
            ->from(self::TABLE_NAME_2, 'user_id')
            ->from('', 'count(*) as thecount')
            ->group('user_id')
            ->order('user_id');
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(2, count($result));
        $this->assertEquals(101, $result[0][$userId]);
        $this->assertEquals(2, $result[0][$count]);
        $this->assertEquals(102, $result[1][$userId]);
        $this->assertEquals(1, $result[1][$count]);

        $select = $this->_db->select()
            ->from(self::TABLE_NAME_2, array('user_id'))
            ->from('', 'count(*) as thecount')
            ->group(array('user_id', 'user_id'))
            ->order('user_id');
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(2, count($result));
        $this->assertEquals(101, $result[0][$userId]);
        $this->assertEquals(2, $result[0][$count]);
        $this->assertEquals(102, $result[1][$userId]);
        $this->assertEquals(1, $result[1][$count]);
    }

    /**
     * Test adding a HAVING clause to a Zend_Db_Select object.
     * @todo: test having() with 2 args for quoteInto()
     */
    public function testSelectHavingClause()
    {
        // @todo: fix ZF-884
        if ($this->getDriver() == 'pdo_Sqlite') {
            $this->markTestIncomplete('Pending fix for ZF-884');
            return;
        }

        $userId = $this->getIdentifier('user_id');
        $count = $this->getIdentifier('thecount');

        $this->createTestTable2();

        $select = $this->_db->select()
            ->from(self::TABLE_NAME_2, array('user_id'))
            ->from('', 'count(*) as thecount')
            ->group('user_id')
            ->having('count(*) > 1');
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, count($result));
        $this->assertEquals(101, $result[0][$userId]);
        $this->assertEquals(2, $result[0][$count]);

        $select = $this->_db->select()
            ->from(self::TABLE_NAME_2, array('user_id'))
            ->from('', 'count(*) as thecount')
            ->group('user_id')
            ->having('count(*) > 1')
            ->having('count(*) = 1');
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(0, count($result));
    }

    /**
     * Test adding a HAVING clause to a Zend_Db_Select object.
     * @todo: test orHaving() with 2 args for quoteInto()
     */
    public function testSelectOrHavingClause()
    {
        // @todo: fix ZF-884
        if ($this->getDriver() == 'pdo_Sqlite') {
            $this->markTestIncomplete('Pending fix for ZF-884');
            return;
        }

        $userId = $this->getIdentifier('user_id');
        $count = $this->getIdentifier('thecount');

        $this->createTestTable2();

        $select = $this->_db->select()
            ->from(self::TABLE_NAME_2, array('user_id'))
            ->from('', 'count(*) as thecount')
            ->group('user_id')
            ->orHaving('count(*) > 1')
            ->orHaving('count(*) = 1')
            ->order('user_id');
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(2, count($result));
        $this->assertEquals(101, $result[0][$userId]);
        $this->assertEquals(2, $result[0][$count]);
        $this->assertEquals(102, $result[1][$userId]);
        $this->assertEquals(1, $result[1][$count]);

        $select = $this->_db->select()
            ->from(self::TABLE_NAME_2, array('user_id'))
            ->from('', 'count(*) as thecount')
            ->group('user_id')
            ->orHaving('count(*) > 1')
            ->orHaving('count(*) = 1')
            ->order('user_id');
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(2, count($result));
        $this->assertEquals(101, $result[0][$userId]);
        $this->assertEquals(2, $result[0][$count]);
        $this->assertEquals(102, $result[1][$userId]);
        $this->assertEquals(1, $result[1][$count]);
    }

    /**
     * Test adding an ORDER BY clause to a Zend_Db_Select object.
     */
    public function testSelectOrderByClause()
    {
        $id = $this->getIdentifier('id');

        $select = $this->_db->select()
            ->from(self::TABLE_NAME)
            ->order($id);
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, $result[0][$id]);

        $select = $this->_db->select()
            ->from(self::TABLE_NAME)
            ->order(array($id, $id));
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, $result[0][$id]);

        $select = $this->_db->select()
            ->from(self::TABLE_NAME)
            ->order("$id ASC");
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, $result[0][$id]);

        $select = $this->_db->select()
            ->from(self::TABLE_NAME)
            ->order("$id DESC");
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(2, $result[0][$id]);
    }

    /**
     * Test adding a LIMIT clause to a Zend_Db_Select object.
     */
    public function testSelectLimitClause()
    {
        $id = $this->getIdentifier('id');

        $select = $this->_db->select()
            ->from(self::TABLE_NAME)
            ->limit(); // no limit
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(2, count($result));

        $select = $this->_db->select()
            ->from(self::TABLE_NAME)
            ->limit(1);
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, count($result));
        $this->assertEquals(1, $result[0][$id]);

        $select = $this->_db->select()
            ->from(self::TABLE_NAME)
            ->limit(1, 1);
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, count($result));
        $this->assertEquals(2, $result[0][$id]);
    }

    /**
     * Test the limitPage() method of a Zend_Db_Select object.
     */
    public function testSelectLimitPage()
    {
        $id = $this->getIdentifier('id');

        $select = $this->_db->select()
            ->from(self::TABLE_NAME)
            ->limitPage(1, 1); // first page, length 1
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, count($result));
        $this->assertEquals(1, $result[0][$id]);

        $select = $this->_db->select()
            ->from(self::TABLE_NAME)
            ->limitPage(2, 1); // second page, length 1
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, count($result));
        $this->assertEquals(2, $result[0][$id]);
    }

    /**
     * Test the getPart() and reset() methods of a Zend_Db_Select object.
     */
    public function testSelectGetPartAndReset()
    {
        $select = $this->_db->select()
            ->from(self::TABLE_NAME)
            ->limit(1);
        $count = $select->getPart(Zend_Db_Select::LIMIT_COUNT);
        $this->assertEquals(1, $count);

        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $count = $select->getPart(Zend_Db_Select::LIMIT_COUNT);
        $this->assertNull($count);

        $select->reset(); // reset the whole object
        $from = $select->getPart(Zend_Db_Select::FROM);
        $this->assertTrue(empty($from));
    }

    /**
     *
    public function testTransactionCommit()
    {
    }
     */

    /**
     *
    public function testTransactionRollback()
    {
    }
     */

    /**
     * Test the Adapter's update() method.
     * Update a single row and verify that the change was made.
     * Attempt to update a row that does not exist, and verify
     * that no change was made.
     *
     * @todo: test that requires delimited identifiers.
     */
    public function testUpdate()
    {
        $id = $this->getIdentifier('id');
        $title = $this->getIdentifier('title');
        $subtitle = $this->getIdentifier('subtitle');

        $newTitle = 'New News Item 2';
        $newSubTitle = 'New Sub title 2';

        // Test that we can change the values in
        // an existing row.
        $result = $this->_db->update(self::TABLE_NAME,
            array(
                'title'        => $newTitle,
                'subTitle'     => $newSubTitle
            ),
            'id = 2'
        );
        $this->assertEquals(1, $result);

        // Query the row to see if we have the new values.
        $select = $this->_db->select();
        $select->from(self::TABLE_NAME);
        $select->where('id = 2');
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();

        $this->assertEquals(2, $result[0][$id]);
        $this->assertEquals($newTitle, $result[0][$title]);
        $this->assertEquals($newSubTitle, $result[0][$subtitle]);

        // Test that update affects no rows if the WHERE
        // clause matches none.
        $result = $this->_db->update(self::TABLE_NAME,
            array(
                'title'        => $newTitle,
                'subTitle'     => $newSubTitle,
            ),
            'id = 327'
        );
        $this->assertEquals(0, $result);
    }

    public function testExceptionInvalidLimitArgument()
    {
        $exceptionSeen = false;
        try {
            $sql = $this->_db->limit('SELECT * FROM ' . self::TABLE_NAME, 0);
        } catch (Zend_Db_Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Db_Adapter_Exception'));
            $exceptionSeen = true;
        }
        $this->assertTrue($exceptionSeen);

        $exceptionSeen = false;
        try {
            $sql = $this->_db->limit('SELECT * FROM ' . self::TABLE_NAME, 1, -1);
        } catch (Zend_Db_Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Db_Adapter_Exception'));
            $exceptionSeen = true;
        }
        $this->assertTrue($exceptionSeen);
    }

}
