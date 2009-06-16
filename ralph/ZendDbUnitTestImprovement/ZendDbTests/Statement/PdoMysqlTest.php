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

require_once 'Zend/Db/Statement/AbstractPdoTestCase.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

class Zend_Db_Statement_PdoMysqlTest extends Zend_Db_Statement_AbstractPdoTestCase
{

    public function testStatementNextRowset()
    {
        $select = $this->sharedFixture->dbAdapter->select()
            ->from('zf_products');
        $stmt = $this->sharedFixture->dbAdapter->prepare($select->__toString());
        try {
            $stmt->nextRowset();
        } catch (Zend_Db_Statement_Exception $e) {
            $this->assertType('Zend_Db_Statement_Exception', $e,
                'Expecting object of type Zend_Db_Statement_Exception, got '.get_class($e));
            $this->assertEquals('SQLSTATE[HYC00]: Optional feature not implemented', $e->getMessage());
        }
        $stmt->closeCursor();
    }

    /**
     * Ensures that the character sequence ":0'" is handled properly
     *
     * @link   http://framework.zend.com/issues/browse/ZF-2059
     * @return void
     */
    public function testZF2059()
    {
        $sql = "SELECT bug_id FROM zf_bugs WHERE bug_status != ':0'";
        $results = $this->sharedFixture->dbAdapter->fetchAll($sql);
        $this->assertEquals(4, count($results));

        $select = $this->sharedFixture->dbAdapter->select()->from('zf_bugs', 'bug_id')
                                      ->where('bug_status != ?', ':0');
        $results = $this->sharedFixture->dbAdapter->fetchAll($select);
        $this->assertEquals(4, count($results));
    }

    public function getDriver()
    {
        return 'Pdo_Mysql';
    }

}
