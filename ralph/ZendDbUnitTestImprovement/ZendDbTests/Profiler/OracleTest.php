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
class Zend_Db_Profiler_OracleTest extends Zend_Db_Profiler_AbstractTestCase
{

    public function testProfilerPreparedStatementWithParams()
    {
        $bug_id = $this->sharedFixture->dbAdapter->quoteIdentifier('bug_id', true);

        // prepare a query
        $select = $this->sharedFixture->dbAdapter->select()
            ->from('zf_bugs')
            ->where("$bug_id = :bug_id");
        $stmt = $this->sharedFixture->dbAdapter->prepare($select->__toString());

        // execute query a first time
        $stmt->execute(array(':bug_id' => 2));
        $results = $stmt->fetchAll();
        $this->assertType('array', $results);
        $this->assertEquals(2, $results[0]['bug_id']);

        // analyze query profiles
        $profiles = $this->sharedFixture->dbAdapter->getProfiler()->getQueryProfiles(null, true);
        $this->assertType('array', $profiles, 'Expected array, got '.gettype($profiles));
        $this->assertEquals(1, count($profiles), 'Expected to find 1 profile');
        $qp = $profiles[0];
        $this->assertType('Zend_Db_Profiler_Query', $qp);

        // analyze query in the profile
        $sql = $qp->getQuery();
        $this->assertContains(" = :bug_id", $sql);
        $params = $qp->getQueryParams();
        $this->assertType('array', $params);
        $this->assertEquals(array(':bug_id' => 2), $params);

        // execute query a second time
        $stmt->execute(array(':bug_id' => 3));
        $results = $stmt->fetchAll();
        $this->assertType('array', $results);
        $this->assertEquals(3, $results[0]['bug_id']);

        // analyze query profiles
        $profiles = $this->sharedFixture->dbAdapter->getProfiler()->getQueryProfiles(null, true);
        $this->assertType('array', $profiles, 'Expected array, got '.gettype($profiles));
        $this->assertEquals(2, count($profiles), 'Expected to find 2 profiles');
        $qp = $profiles[1];
        $this->assertType('Zend_Db_Profiler_Query', $qp);

        // analyze query in the profile
        $sql = $qp->getQuery();
        $this->assertContains(" = :bug_id", $sql);
        $params = $qp->getQueryParams();
        $this->assertType('array', $params);
        $this->assertEquals(array(':bug_id' => 3), $params);
    }

    public function testProfilerPreparedStatementWithBoundParams()
    {
        $bug_id = $this->sharedFixture->dbAdapter->quoteIdentifier('bug_id', true);

        // prepare a query
        $select = $this->sharedFixture->dbAdapter->select()
            ->from('zf_bugs')
            ->where("$bug_id = :bug_id");
        $stmt = $this->sharedFixture->dbAdapter->prepare($select->__toString());

        // execute query a first time
        $id = 1;
        $this->assertTrue($stmt->bindParam(':bug_id', $id));
        $id = 2;
        $stmt->execute();
        $results = $stmt->fetchAll();
        $this->assertType('array', $results);
        $this->assertEquals(2, $results[0]['bug_id']);

        // analyze query profiles
        $profiles = $this->sharedFixture->dbAdapter->getProfiler()->getQueryProfiles(null, true);
        $this->assertType('array', $profiles);
        $this->assertEquals(1, count($profiles), 'Expected to find 1 profile');
        $qp = $profiles[0];
        $this->assertType('Zend_Db_Profiler_Query', $qp);

        // analyze query in the profile
        $sql = $qp->getQuery();
        $this->assertContains(" = :bug_id", $sql);
        $params = $qp->getQueryParams();
        $this->assertType('array', $params);
        $this->assertEquals(array(':bug_id' => 2), $params);

        // execute query a first time
        $id = 3;
        $stmt->execute();
        $results = $stmt->fetchAll();
        $this->assertType('array', $results);
        $this->assertEquals(3, $results[0]['bug_id']);

        // analyze query profiles
        $profiles = $this->sharedFixture->dbAdapter->getProfiler()->getQueryProfiles(null, true);
        $this->assertType('array', $profiles);
        $this->assertEquals(2, count($profiles), 'Expected to find 2 profiles');
        $qp = $profiles[1];
        $this->assertType('Zend_Db_Profiler_Query', $qp);

        // analyze query in the profile
        $sql = $qp->getQuery();
        $this->assertContains(" = :bug_id", $sql);
        $params = $qp->getQueryParams();
        $this->assertType('array', $params);
        $this->assertEquals(array(':bug_id' => 3), $params);
    }

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
        return 'Oracle';
    }
}
