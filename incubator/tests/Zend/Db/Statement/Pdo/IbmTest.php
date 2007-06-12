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

require_once 'Zend/Db/Statement/Pdo/TestCommon.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

class Zend_Db_Statement_Pdo_IbmTest extends Zend_Db_Statement_Pdo_TestCommon
{

    public function getDriver()
    {
        return 'Pdo_Ibm';
    }

    public function testStatementNextRowset()
    {
        $select = $this->_db->select()
        ->from('zfproducts');
        $stmt = $this->_db->prepare($select->__toString());

        $result = $stmt->nextRowset();

        // there is no next rowset so $result should be false
        $this->assertFalse($result);
        $stmt->closeCursor();
    }

    public function testStatementColumnCountForSelect()
    {
        $select = $this->_db->select()
        ->from('zfproducts');

        $stmt = $this->_db->prepare($select->__toString());

        $n = $stmt->columnCount();
        $this->assertEquals(2, $n);

        $stmt->execute();

        $n = $stmt->columnCount();
        $stmt->closeCursor();

        $this->assertType('integer', $n);
        $this->assertEquals(2, $n);
    }

    public function testStatementBindParamByPosition()
    {
        $this->markTestIncomplete($this->getDriver() . ' support for bindParam to be implemented soon.');
    }

    public function testStatementBindValueByPosition()
    {
        $this->markTestIncomplete($this->getDriver() . ' support for bindValue to be implemented soon.');
    }

    public function testStatementBindValueByName()
    {
        $this->markTestIncomplete($this->getDriver() . ' support for bindValue to be implemented soon.');
    }

    public function testStatementBindParamByName()
    {
        $this->markTestIncomplete($this->getDriver() . ' support for bindParam to be implemented soon.');
    }
}
