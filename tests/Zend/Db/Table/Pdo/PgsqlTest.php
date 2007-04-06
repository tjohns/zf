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

require_once 'Zend/Db/Table/TestCommon.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

class Zend_Db_Table_Pdo_PgsqlTest extends Zend_Db_Table_TestCommon
{

    public function testTableInsertAutoIncrement()
    {
        $driver = $this->getDriver();
        $this->markTestSkipped("$driver does not support auto-increment.");
    }

    public function testTableInsertSequence()
    {
        $this->markTestIncomplete('Pending solution for ZF-1140');
        return;

        $table = $this->_table['bugs'];
        $row = array (
            'bug_id'          => new Zend_Db_Expr("NEXTVAL('bugs_seq')"),
            'bug_description' => 'New bug',
            'bug_status'      => 'NEW',
            'created_on'      => '2007-04-02',
            'updated_on'      => '2007-04-02',
            'reported_by'     => 'micky',
            'assigned_to'     => 'goofy'
        );
        $insertResult         = $table->insert($row);
        $lastInsertId         = $this->_db->lastInsertId('bugs');
        $lastSequenceId       = $this->_db->lastSequenceId('bugs_seq');

        $this->assertEquals($insertResult, $lastInsertId);
        $this->assertEquals($insertResult, $lastSequenceId);
        $this->assertEquals(5, $insertResult);
    }

    public function getDriver()
    {
        return 'Pdo_Pgsql';
    }

}
