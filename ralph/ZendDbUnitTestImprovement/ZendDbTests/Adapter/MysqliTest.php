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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * @see Zend_Db_Adapter_AbstractTestCase
 */
require_once 'Zend/Db/Adapter/AbstractTestCase.php';


PHPUnit_Util_Filter::addFileToFilter(__FILE__);


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Adapter_MysqliTest extends Zend_Db_Adapter_AbstractTestCase 
{

    protected $_numericDataTypes = array(
        Zend_Db::INT_TYPE    => Zend_Db::INT_TYPE,
        Zend_Db::BIGINT_TYPE => Zend_Db::BIGINT_TYPE,
        Zend_Db::FLOAT_TYPE  => Zend_Db::FLOAT_TYPE,
        'INT'                => Zend_Db::INT_TYPE,
        'INTEGER'            => Zend_Db::INT_TYPE,
        'MEDIUMINT'          => Zend_Db::INT_TYPE,
        'SMALLINT'           => Zend_Db::INT_TYPE,
        'TINYINT'            => Zend_Db::INT_TYPE,
        'BIGINT'             => Zend_Db::BIGINT_TYPE,
        'SERIAL'             => Zend_Db::BIGINT_TYPE,
        'DEC'                => Zend_Db::FLOAT_TYPE,
        'DECIMAL'            => Zend_Db::FLOAT_TYPE,
        'DOUBLE'             => Zend_Db::FLOAT_TYPE,
        'DOUBLE PRECISION'   => Zend_Db::FLOAT_TYPE,
        'FIXED'              => Zend_Db::FLOAT_TYPE,
        'FLOAT'              => Zend_Db::FLOAT_TYPE
    );

    /**
     * Test AUTO_QUOTE_IDENTIFIERS option
     * Case: Zend_Db::AUTO_QUOTE_IDENTIFIERS = true
     *
     * MySQL actually allows delimited identifiers to remain
     * case-insensitive, so this test overrides its parent.
     */
    public function testAdapterAutoQuoteIdentifiersTrue()
    {
        $params = $this->sharedFixture->dbUtility->getDriverConfigurationAsParams();

        $params['options'] = array(
            Zend_Db::AUTO_QUOTE_IDENTIFIERS => true
        );
        $db = Zend_Db::factory($this->sharedFixture->dbUtility->getDriverName(), $params);
        $db->getConnection();

        $select = $this->sharedFixture->dbAdapter->select();
        $select->from('zf_products');
        $stmt = $this->sharedFixture->dbAdapter->query($select);
        $result1 = $stmt->fetchAll();
        $this->assertEquals(3, count($result1), 'Expected 3 rows in first query result');

        $this->assertEquals(1, $result1[0]['product_id']);

        $select = $this->sharedFixture->dbAdapter->select();
        $select->from('zf_products');
        try {
            $stmt = $this->sharedFixture->dbAdapter->query($select);
            $result2 = $stmt->fetchAll();
            $this->assertEquals(3, count($result2), 'Expected 3 rows in second query result');
            $this->assertEquals($result1, $result2);
        } catch (Zend_Exception $e) {
            $this->fail('exception caught where none was expected.');
        }
    }

    public function testAdapterInsertSequence()
    {
        $this->markTestSkipped($this->sharedFixture->dbUtility->getDriverName() . ' does not support sequences');
    }

    /**
     * test that quoteColumnAs() accepts a string
     * and an alias, and returns each as delimited
     * identifiers, with 'AS' in between.
     */
    public function testAdapterQuoteColumnAs()
    {
        $string = "foo";
        $alias = "bar";
        $value = $this->sharedFixture->dbAdapter->quoteColumnAs($string, $alias);
        $this->assertEquals('`foo` AS `bar`', $value);
    }

    /**
     * test that quoteColumnAs() accepts a string
     * and an alias, but ignores the alias if it is
     * the same as the base identifier in the string.
     */
    public function testAdapterQuoteColumnAsSameString()
    {
        $string = 'foo.bar';
        $alias = 'bar';
        $value = $this->sharedFixture->dbAdapter->quoteColumnAs($string, $alias);
        $this->assertEquals('`foo`.`bar`', $value);
    }

    /**
     * test that quoteIdentifier() accepts a string
     * and returns a delimited identifier.
     */
    public function testAdapterQuoteIdentifier()
    {
        $value = $this->sharedFixture->dbAdapter->quoteIdentifier('table_name');
        $this->assertEquals('`table_name`', $value);
    }

    /**
     * test that quoteIdentifier() accepts an array
     * and returns a qualified delimited identifier.
     */
    public function testAdapterQuoteIdentifierArray()
    {
        $array = array('foo', 'bar');
        $value = $this->sharedFixture->dbAdapter->quoteIdentifier($array);
        $this->assertEquals('`foo`.`bar`', $value);
    }

    /**
     * test that quoteIdentifier() accepts an array
     * containing a Zend_Db_Expr, and returns strings
     * as delimited identifiers, and Exprs as unquoted.
     */
    public function testAdapterQuoteIdentifierArrayDbExpr()
    {
        $expr = new Zend_Db_Expr('*');
        $array = array('foo', $expr);
        $value = $this->sharedFixture->dbAdapter->quoteIdentifier($array);
        $this->assertEquals('`foo`.*', $value);
    }

    /**
     * test that quoteIdentifer() escapes a double-quote
     * character in a string.
     */
    public function testAdapterQuoteIdentifierDoubleQuote()
    {
        $string = 'table_"_name';
        $value = $this->sharedFixture->dbAdapter->quoteIdentifier($string);
        $this->assertEquals('`table_"_name`', $value);
    }

    /**
     * test that quoteIdentifer() accepts an integer
     * and returns a delimited identifier as with a string.
     */
    public function testAdapterQuoteIdentifierInteger()
    {
        $int = 123;
        $value = $this->sharedFixture->dbAdapter->quoteIdentifier($int);
        $this->assertEquals('`123`', $value);
    }

    /**
     * test that quoteIdentifier() accepts a string
     * containing a dot (".") character, splits the
     * string, quotes each segment individually as
     * delimited identifers, and returns the imploded
     * string.
     */
    public function testAdapterQuoteIdentifierQualified()
    {
        $string = 'table.column';
        $value = $this->sharedFixture->dbAdapter->quoteIdentifier($string);
        $this->assertEquals('`table`.`column`', $value);
    }

    /**
     * test that quoteIdentifer() escapes a single-quote
     * character in a string.
     */
    public function testAdapterQuoteIdentifierSingleQuote()
    {
        $string = "table_'_name";
        $value = $this->sharedFixture->dbAdapter->quoteIdentifier($string);
        $this->assertEquals('`table_\'_name`', $value);
    }

    /**
     * test that quoteTableAs() accepts a string and an alias,
     * and returns each as delimited identifiers.
     * Most RDBMS want an 'AS' in between.
     */
    public function testAdapterQuoteTableAs()
    {
        $string = "foo";
        $alias = "bar";
        $value = $this->sharedFixture->dbAdapter->quoteTableAs($string, $alias);
        $this->assertEquals('`foo` AS `bar`', $value);
    }

    /**
     * test that describeTable() returns correct types
     * @group ZF-3624
     *
     */
    public function testAdapterDescribeTableAttributeColumnFloat()
    {
        $tableName = $this->_getClonedUtility(false)->createTable(
            'AltPrices',
            'prices_alt',
            array(
                'product_id'    => 'INTEGER NOT NULL',
                'price_name'    => 'VARCHAR(100)',
                'price'         => 'FLOAT(10,8)',
                'price_total'   => 'DECIMAL(10,2) NOT NULL',
                'PRIMARY KEY'   => 'product_id'
                )
            );        
        $desc = $this->sharedFixture->dbAdapter->describeTable($tableName);
        $this->assertEquals($tableName,  $desc['price']['TABLE_NAME']);
        $this->assertRegExp('/float/i', $desc['price']['DATA_TYPE']);
    }

    /**
     * Ensures that the PDO Buffered Query does not throw the error
     * 2014 General error
     *
     * @group ZF-2101
     * @link   http://framework.zend.com/issues/browse/ZF-2101
     */
    public function testAdapterCanCalledStoredProcedure()
    {
        $params = $this->sharedFixture->dbUtility->getDriverConfigurationAsParams();
        $db = Zend_Db::factory($this->sharedFixture->dbUtility->getDriverName(), $params);

        // Set default bound value
        $customerId = 1;

        // Stored procedure returns a single row
        $stmt = $db->prepare('CALL zf_get_product_procedure(?)');
        $stmt->bindParam(1, $customerId);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $this->assertEquals(1, $result[0]['product_id']);

        // Reset statement
        $stmt->closeCursor();

        // Stored procedure returns a single row
        $stmt = $db->prepare('CALL zf_get_product_procedure(?)');
        $stmt->bindParam(1, $customerId);
        $stmt->execute();
        $this->assertEquals(1, $result[0]['product_id']);
    }

    public function testAdapterAlternateStatement()
    {
        $this->_testAdapterAlternateStatement('Test_MysqliStatement');
    }

    public function testMySqliInitCommand()
    {
        $params = $this->sharedFixture->dbUtility->getDriverConfigurationAsParams();
        $params['driver_options'] = array(
            'mysqli_init_command' => 'SET AUTOCOMMIT=0;'
        );
        $db = Zend_Db::factory($this->sharedFixture->dbUtility->getDriverName(), $params);

        $sql = 'SELECT @@AUTOCOMMIT as autocommit';

        $row = $db->fetchRow($sql);

        $this->assertEquals(0, $row['autocommit']);
    }

    public function getDriver()
    {
        return 'Mysqli';
    }

}
