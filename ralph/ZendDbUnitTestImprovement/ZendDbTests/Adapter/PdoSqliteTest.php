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
 * @see Zend_Db_Adapter_Pdo_AbstractTestCase
 */
require_once 'Zend/Db/Adapter/AbstractPdoTestCase.php';


PHPUnit_Util_Filter::addFileToFilter(__FILE__);


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Adapter_PdoSqliteTest extends Zend_Db_Adapter_AbstractPdoTestCase
{

    protected $_numericDataTypes = array(
        Zend_Db::INT_TYPE    => Zend_Db::INT_TYPE,
        Zend_Db::BIGINT_TYPE => Zend_Db::BIGINT_TYPE,
        Zend_Db::FLOAT_TYPE  => Zend_Db::FLOAT_TYPE,
        'INTEGER'            => Zend_Db::BIGINT_TYPE,
        'REAL'               => Zend_Db::FLOAT_TYPE
    );

    /**
     * Test AUTO_QUOTE_IDENTIFIERS option
     * Case: Zend_Db::AUTO_QUOTE_IDENTIFIERS = true
     *
     * SQLite actually allows delimited identifiers to remain
     * case-insensitive, so this test overrides its parent.
     */
    public function testAdapterAutoQuoteIdentifiersTrue()
    {
        $this->markTestSkipped('This test makes no sense, it never consults the created adapter.');
        return; 
        
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

        $this->assertEquals(1, $result1[0]['product_id']);

        $select = $this->sharedFixture->dbAdapter->select();
        $select->from('ZFPRODUCTS');
        try {
            $stmt = $this->sharedFixture->dbAdapter->query($select);
            $result2 = $stmt->fetchAll();
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Statement_Exception', $e,
                'Expecting object of type Zend_Db_Statement_Exception, got '.get_class($e));
            $this->fail('Unexpected exception '.get_class($e).' received: '.$e->getMessage());
        }

        $this->assertEquals($result1, $result2);
    }


    public function testAdapterConstructInvalidParamDbnameException()
    {
        $this->markTestSkipped($this->sharedFixture->dbUtility->getDriverName() . ' does not throw exception on missing dbname');
    }

    public function testAdapterConstructInvalidParamUsernameException()
    {
        $this->markTestSkipped($this->sharedFixture->dbUtility->getDriverName() . ' does not support login credentials');
    }

    public function testAdapterConstructInvalidParamPasswordException()
    {
        $this->markTestSkipped($this->sharedFixture->dbUtility->getDriverName() . ' does not support login credentials');
    }

    public function testAdapterInsertSequence()
    {
        $this->markTestSkipped($this->sharedFixture->dbUtility->getDriverName() . ' does not support sequences');
    }

    /**
     * Used by:
     * - testAdapterOptionCaseFoldingNatural()
     * - testAdapterOptionCaseFoldingUpper()
     * - testAdapterOptionCaseFoldingLower()
     */
    
//    protected function _testAdapterOptionCaseFoldingSetup(Zend_Db_Adapter_Abstract $db)
//    {
//        $db->getConnection();
//        $this->sharedFixture->dbUtility->setUp($db);
//    }

    /**
     * Test that quote() takes an array and returns
     * an imploded string of comma-separated, quoted elements.
     */
    public function testAdapterQuoteArray()
    {
        $array = array("it's", 'all', 'right!');
        $value = $this->sharedFixture->dbAdapter->quote($array);
        $this->assertEquals("'it''s', 'all', 'right!'", $value);
    }

    /**
     * test that quote() escapes a double-quote
     * character in a string.
     */
    public function testAdapterQuoteDoubleQuote()
    {
        $value = $this->sharedFixture->dbAdapter->quote('St John"s Wort');
        $this->assertEquals("'St John\"s Wort'", $value);
    }

    /**
     * test that quote() escapes a single-quote
     * character in a string.
     */
    public function testAdapterQuoteSingleQuote()
    {
        $string = "St John's Wort";
        $value = $this->sharedFixture->dbAdapter->quote($string);
        $this->assertEquals("'St John''s Wort'", $value);
    }

    /**
     * test that quoteInto() escapes a double-quote
     * character in a string.
     */
    public function testAdapterQuoteIntoDoubleQuote()
    {
        $value = $this->sharedFixture->dbAdapter->quoteInto('id=?', 'St John"s Wort');
        $this->assertEquals("id='St John\"s Wort'", $value);
    }

    /**
     * test that quoteInto() escapes a single-quote
     * character in a string.
     */
    public function testAdapterQuoteIntoSingleQuote()
    {
        $value = $this->sharedFixture->dbAdapter->quoteInto('id = ?', 'St John\'s Wort');
        $this->assertEquals("id = 'St John''s Wort'", $value);
    }

    public function testAdapterTransactionAutoCommit()
    {
        $this->markTestSkipped($this->sharedFixture->dbUtility->getDriverName() . ' does not support transactions or concurrency');
    }

    public function testAdapterTransactionCommit()
    {
        $this->markTestSkipped($this->sharedFixture->dbUtility->getDriverName() . ' does not support transactions or concurrency');
    }

    public function testAdapterTransactionRollback()
    {
        $this->markTestSkipped($this->sharedFixture->dbUtility->getDriverName() . ' does not support transactions or concurrency');
    }

    /**
     * @return void
     * @see    http://framework.zend.com/issues/browse/ZF-2293
     */
    public function testAdapterSupportsLengthInTableMetadataForVarcharFields()
    {
        $metadata = $this->sharedFixture->dbAdapter->describeTable('zf_bugs');
        $this->assertEquals(100, $metadata['bug_description']['LENGTH']);
        $this->assertEquals(20, $metadata['bug_status']['LENGTH']);
    }

    public function getDriver()
    {
        return 'Pdo_Sqlite';
    }

}
