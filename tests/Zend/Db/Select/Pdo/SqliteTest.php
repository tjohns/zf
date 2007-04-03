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

require_once 'Zend/Db/Select/TestCommon.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

class Zend_Db_Select_Pdo_SqliteTest extends Zend_Db_Select_TestCommon
{

    public function testSelectFromQualified()
    {
        $this->markTestSkipped('SQLite does not support qualified table names');
    }

    public function testSelectJoinQualified()
    {
        $this->markTestSkipped('SQLite does not support qualified table names');
    }

    public function testSelectGroupBy()
    {
        $this->markTestIncomplete('Pending fix for ZF-884');
    }

    public function testSelectGroupByQualified()
    {
        $this->markTestIncomplete('Pending fix for ZF-884');
    }

    public function testSelectGroupByExpr()
    {
        $this->markTestIncomplete('Pending fix for ZF-884');
    }

    public function testSelectGroupByAutoExpr()
    {
        $this->markTestIncomplete('Pending fix for ZF-884');
    }

    public function testSelectHaving()
    {
        $this->markTestIncomplete('Pending fix for ZF-884');
    }

    public function testSelectHavingAnd()
    {
        $this->markTestIncomplete('Pending fix for ZF-884');
    }

    public function testSelectHavingWithParameter()
    {
        $this->markTestIncomplete('Pending fix for ZF-884');
    }

    public function testSelectHavingOr()
    {
        $this->markTestIncomplete('Pending fix for ZF-884');
    }

    public function testSelectHavingOrWithParameter()
    {
        $this->markTestIncomplete('Pending fix for ZF-884');
    }

    public function testSelectJoinRight()
    {
        $this->markTestSkipped('SQLite does not support RIGHT OUTER JOIN');
    }

    public function getDriver()
    {
        return 'Pdo_Sqlite';
    }

}
