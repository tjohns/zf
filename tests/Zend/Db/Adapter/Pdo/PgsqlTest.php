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

require_once 'Zend/Db/Adapter/Pdo/TestCommon.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

class Zend_Db_Adapter_Pdo_PgsqlTest extends Zend_Db_Adapter_Pdo_TestCommon
{

    public function testAdapterDescribeTablePrimaryAuto()
    {
        $desc = $this->_db->describeTable('zfbugs');

        $this->assertTrue($desc['bug_id']['PRIMARY']);
        $this->assertEquals(1, $desc['bug_id']['PRIMARY_POSITION']);
        $this->assertTrue($desc['bug_id']['IDENTITY']);
    }

    /**
     * Test the Adapter's insert() method.
     * This requires providing an associative array of column=>value pairs.
     */
    public function testAdapterInsert()
    {
        $row = array (
            'bug_description' => 'New bug',
            'bug_status'      => 'NEW',
            'created_on'      => '2007-04-02',
            'updated_on'      => '2007-04-02',
            'reported_by'     => 'micky',
            'assigned_to'     => 'goofy'
        );
        $rowsAffected = $this->_db->insert('zfbugs', $row);
        $this->assertEquals(1, $rowsAffected);
        $lastInsertId = $this->_db->lastInsertId('zfbugs', 'bug_id');
        $lastSequenceId = $this->_db->lastSequenceId('zfbugs_bug_id_seq');
        $this->assertEquals((string) $lastInsertId, (string) $lastSequenceId,
            'Expected last insert id to be equal to last sequence id');
        $this->assertEquals('5', (string) $lastInsertId,
            'Expected new id to be 5');
    }

    public function testAdapterInsertSequence()
    {
        $row = array (
            'product_id' => $this->_db->nextSequenceId('zfproducts_seq'),
            'product_name' => 'Solaris',
        );
        $rowsAffected = $this->_db->insert('zfproducts', $row);
        $this->assertEquals(1, $rowsAffected);
        $lastInsertId = $this->_db->lastInsertId('zfproducts');
        $lastSequenceId = $this->_db->lastSequenceId('zfproducts_seq');
        $this->assertEquals((string) $lastInsertId, (string) $lastSequenceId,
            'Expected last insert id to be equal to last sequence id');
        $this->assertEquals('4', (string) $lastInsertId,
            'Expected new id to be 4');
    }

    /**
     * Test that quote() takes an array and returns
     * an imploded string of comma-separated, quoted elements.
     */
    public function testAdapterQuoteArray()
    {
        $array = array("it's", 'all', 'right!');
        $value = $this->_db->quote($array);
        $this->assertEquals("'it''s', 'all', 'right!'", $value);
    }

    /**
     * test that quote() escapes a double-quote
     * character in a string.
     */
    public function testAdapterQuoteDoubleQuote()
    {
        $value = $this->_db->quote('St John"s Wort');
        $this->assertEquals("'St John\"s Wort'", $value);
    }

    /**
     * test that quote() escapes a single-quote
     * character in a string.
     */
    public function testAdapterQuoteSingleQuote()
    {
        $string = "St John's Wort";
        $value = $this->_db->quote($string);
        $this->assertEquals("'St John''s Wort'", $value);
    }

    /**
     * test that quoteInto() escapes a double-quote
     * character in a string.
     */
    public function testAdapterQuoteIntoDoubleQuote()
    {
        $value = $this->_db->quoteInto('id=?', 'St John"s Wort');
        $this->assertEquals("id='St John\"s Wort'", $value);
    }

    /**
     * test that quoteInto() escapes a single-quote
     * character in a string.
     */
    public function testAdapterQuoteIntoSingleQuote()
    {
        $value = $this->_db->quoteInto('id = ?', 'St John\'s Wort');
        $this->assertEquals("id = 'St John''s Wort'", $value);
    }

    function getDriver()
    {
        return 'Pdo_Pgsql';
    }

}
