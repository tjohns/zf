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
 */

require_once 'Zend/Db/Table/TestCommon.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

class Zend_Db_Table_Pdo_OciTest extends Zend_Db_Table_TestCommon
{

    public function testTableInsert()
    {
        $this->markTestSkipped($this->sharedFixture->dbUtility->getDriverName().' does not support auto-increment keys.');
    }

    public function testIsIdentity()
    {
        $this->markTestSkipped($this->sharedFixture->dbUtility->getDriverName().' does not support auto-increment columns.');
    }

    /**
     * ZF-4330: Oracle needs sequence
     */
    public function testTableInsertWithSchema()
    {
        $schemaName = $this->sharedFixture->dbUtility->getSchema();
        $tableName = 'zf_bugs';
        $identifier = join('.', array_filter(array($schemaName, $tableName)));
        $table = $this->sharedFixture->tableUtility->getTable('My_ZendDbTable_TableSpecial',
            array('name' => $tableName, 'schema' => $schemaName,Zend_Db_Table_Abstract::SEQUENCE => 'zf_bugs_seq')
        );

        $row = array (
            'bug_description' => 'New bug',
            'bug_status'      => 'NEW',
            'created_on'      => '2007-04-02',
            'updated_on'      => '2007-04-02',
            'reported_by'     => 'micky',
            'assigned_to'     => 'goofy',
            'verified_by'     => 'dduck'
        );

        $profilerEnabled = $this->sharedFixture->dbAdapter->getProfiler()->getEnabled();
        $this->sharedFixture->dbAdapter->getProfiler()->setEnabled(true);
        $insertResult = $table->insert($row);
        $this->sharedFixture->dbAdapter->getProfiler()->setEnabled($profilerEnabled);

        $qp = $this->sharedFixture->dbAdapter->getProfiler()->getLastQueryProfile();
        $tableSpec = $this->sharedFixture->dbAdapter->quoteIdentifier($identifier, true);
        $this->assertContains("INSERT INTO $tableSpec ", $qp->getQuery());
    }

    public function testTableInsertSequence()
    {
        $table = $this->sharedFixture->tableUtility->getTable('My_ZendDbTable_TableBugs',
            array(Zend_Db_Table_Abstract::SEQUENCE => 'zf_bugs_seq'));
        $row = array (
            'bug_description' => 'New bug',
            'bug_status'      => 'NEW',
            'created_on'      => new Zend_Db_Expr(
                $this->sharedFixture->dbAdapter->quoteInto('DATE ?', '2007-04-02')),
            'updated_on'      => new Zend_Db_Expr(
                $this->sharedFixture->dbAdapter->quoteInto('DATE ?', '2007-04-02')),
            'reported_by'     => 'micky',
            'assigned_to'     => 'goofy'
        );
        $insertResult         = $table->insert($row);
        $lastInsertId         = $this->sharedFixture->dbAdapter->lastInsertId('zf_bugs');
        $lastSequenceId       = $this->sharedFixture->dbAdapter->lastSequenceId('zf_bugs_seq');
        $this->assertEquals($insertResult, $lastInsertId);
        $this->assertEquals($insertResult, $lastSequenceId);
        $this->assertEquals(5, $insertResult);
    }

    public function getDriver()
    {
        return 'Pdo_Oci';
    }

}
