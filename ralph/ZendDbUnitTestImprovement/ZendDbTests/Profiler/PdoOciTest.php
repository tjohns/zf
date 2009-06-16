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
 * @see Zend_Db_Profiler_AbstractTestCase
 */
require_once 'Zend/Db/Profiler/AbstractTestCase.php';


PHPUnit_Util_Filter::addFileToFilter(__FILE__);


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Profiler_PdoOciTest extends Zend_Db_Profiler_AbstractTestCase
{

    /**
     * Ensures that setFilterQueryType() actually filters
     *
     * @return void
     */
    protected function _testProfilerSetFilterQueryTypeCommon($queryType)
    {
        $bugs = $this->sharedFixture->dbAdapter->quoteIdentifier('zf_bugs', true);
        $bug_id = $this->sharedFixture->dbAdapter->quoteIdentifier('bug_id', true);
        $bug_status = $this->sharedFixture->dbAdapter->quoteIdentifier('bug_status', true);

        $prof = $this->sharedFixture->dbAdapter->getProfiler();
        $prof->setEnabled(true);

        $this->assertSame($prof->setFilterQueryType($queryType), $prof);
        $this->assertEquals($queryType, $prof->getFilterQueryType());

        $this->sharedFixture->dbAdapter->query("SELECT * FROM $bugs");
        $this->sharedFixture->dbAdapter->query("INSERT INTO $bugs ($bug_id, $bug_status) VALUES (:id, :status)", array(':id' => 100,':status' => 'NEW'));
        $this->sharedFixture->dbAdapter->query("DELETE FROM $bugs");
        $this->sharedFixture->dbAdapter->query("UPDATE $bugs SET $bug_status = :status", array(':status'=>'FIXED'));

        $qps = $prof->getQueryProfiles();
        $this->assertType('array', $qps, 'Expecting some query profiles, got none');
        foreach ($qps as $qp) {
            $qtype = $qp->getQueryType();
            $this->assertEquals($queryType, $qtype,
                "Found query type $qtype, which should have been filtered out");
        }

        $prof->setEnabled(false);
    }

    public function getDriver()
    {
        return 'Pdo_Oci';
    }
}
