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
 * @see Zend_Db_Adapter_Db2Test
 */
require_once 'Zend/Db/Adapter/Db2Test.php';


PHPUnit_Util_Filter::addFileToFilter(__FILE__);


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Adapter_PdoIbmTest extends Zend_Db_Adapter_Db2Test
{

    public function testAdapterTransactionCommit()
    {
        $server = $this->sharedFixture->dbUtility->getServer();

        if ($server == 'IDS') {
            $this->markTestIncomplete('IDS needs special consideration for transactions');
        } else {
            parent::testAdapterTransactionCommit();
        }
    }

    public function testAdapterTransactionRollback()
    {
        $server = $this->sharedFixture->dbUtility->getServer();

        if ($server == 'IDS') {
            $this->markTestIncomplete('IDS needs special consideration for transactions');
        } else {
            parent::testAdapterTransactionCommit();
        }
    }

    public function testAdapterLimitInvalidArgumentException()
    {
        $products = $this->sharedFixture->dbAdapter->quoteIdentifier('zf_products');
        $sql = $this->sharedFixture->dbAdapter->limit("SELECT * FROM $products", 0);

        $stmt = $this->sharedFixture->dbAdapter->query($sql);
        $result = $stmt->fetchAll();

        $this->assertEquals(0, count($result), 'Expecting to see 0 rows returned');

        try {
            $sql = $this->sharedFixture->dbAdapter->limit("SELECT * FROM $products", 1, -1);
            $this->fail('Expected to catch Zend_Db_Adapter_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Adapter_Exception', $e,
                'Expecting object of type Zend_Db_Adapter_Exception, got '.get_class($e));
        }
    }

    /**
     * Used by _testAdapterOptionCaseFoldingNatural()
     * DB2 returns identifiers in uppercase naturally,
     * while IDS does not
     */
    protected function _testAdapterOptionCaseFoldingNaturalIdentifier()
    {
        $server = $this->sharedFixture->dbUtility->getServer();

        if ($server == 'DB2') {
            return 'CASE_FOLDED_IDENTIFIER';
        }
        return 'case_folded_identifier';
    }
}
