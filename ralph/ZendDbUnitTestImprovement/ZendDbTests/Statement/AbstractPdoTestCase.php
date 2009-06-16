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

require_once 'Zend/Db/Statement/AbstractTestCase.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

abstract class Zend_Db_Statement_AbstractPdoTestCase extends Zend_Db_Statement_AbstractTestCase
{

    public function testStatementConstruct()
    {
        $select = $this->sharedFixture->dbAdapter->select()
            ->from('zf_products');
        $sql = $select->__toString();
        $stmt = new Zend_Db_Statement_Pdo($this->sharedFixture->dbAdapter, $sql);
        $this->assertType('Zend_Db_Statement_Pdo', $stmt);
        $stmt->closeCursor();
    }

    public function testStatementConstructWithSelectObject()
    {
        $select = $this->sharedFixture->dbAdapter->select()
            ->from('zf_products');
        $stmt = new Zend_Db_Statement_Pdo($this->sharedFixture->dbAdapter, $select);
        $this->assertType('Zend_Db_Statement_Interface', $stmt);
        $stmt->closeCursor();
    }

    public function testStatementNextRowset()
    {
        $select = $this->sharedFixture->dbAdapter->select()
            ->from('zf_products');
        $stmt = $this->sharedFixture->dbAdapter->prepare($select->__toString());
        try {
            $stmt->nextRowset();
            $this->fail('Expected to catch Zend_Db_Statement_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Statement_Exception', $e,
                'Expecting object of type Zend_Db_Statement_Exception, got '.get_class($e));
            $this->assertEquals('SQLSTATE[IM001]: Driver does not support this function: driver does not support multiple rowsets', $e->getMessage());
        }
        $stmt->closeCursor();
    }

    /**
     * @group ZF-4486
     */
    public function testStatementIsIterableThroughtForeach()
    {
        $select = $this->sharedFixture->dbAdapter->select()->from('zf_products');
        $stmt = $this->sharedFixture->dbAdapter->query($select);
        $stmt->setFetchMode(Zend_Db::FETCH_OBJ);
        foreach ($stmt as $test) {
            $this->assertTrue($test instanceof stdClass);
        }
        $this->assertType('int', iterator_count($stmt));
    }
}
