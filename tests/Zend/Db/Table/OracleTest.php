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
 * @version    $Id$
 */


/**
 * @see Zend_Db_Table_TestCommon
 */
require_once 'Zend/Db/Table/TestCommon.php';


PHPUnit_Util_Filter::addFileToFilter(__FILE__);


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Table_OracleTest extends Zend_Db_Table_TestCommon
{

    public function testTableInsert()
    {
        $this->markTestSkipped($this->getDriver().' does not support auto-increment columns.');
    }

    public function testTableInsertSequence()
    {
        $table = $this->_getTable('Zend_Db_Table_TableBugs',
            array(Zend_Db_Table_Abstract::SEQUENCE => 'zfbugs_seq'));
        $row = array (
            'bug_description' => 'New bug',
            'bug_status'      => 'NEW',
            'created_on'      => new Zend_Db_Expr(
                $this->_db->quoteInto('DATE ?', '2007-04-02')),
            'updated_on'      => new Zend_Db_Expr(
                $this->_db->quoteInto('DATE ?', '2007-04-02')),
            'reported_by'     => 'micky',
            'assigned_to'     => 'goofy'
        );
        $insertResult         = $table->insert($row);
        $lastInsertId         = $this->_db->lastInsertId('zfbugs');
        $lastSequenceId       = $this->_db->lastSequenceId('zfbugs_seq');
        $this->assertEquals($insertResult, $lastInsertId);
        $this->assertEquals($insertResult, $lastSequenceId);
        $this->assertEquals(5, $insertResult);
    }

    /**
     * Ensures that the fetchAll() method works properly with a view object
     *
     * @return void
     * @see    http://framework.zend.com/issues/browse/ZF-1269
     */
    public function testViewFetchAll()
    {
        $this->markTestIncomplete('View tests are not implemented yet');
    }

    public function getDriver()
    {
        return 'Oracle';
    }

}
