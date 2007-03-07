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
 * @package    Zend_Gdata
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Zend/Gdata/CodeSearch.php';
require_once 'Zend/Http/Client.php';
require_once 'Zend/Http/Client/Adapter/Test.php';

/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_CodeSearchTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $testAdapter = new Zend_Http_Client_Adapter_Test();
        $client = new Zend_Http_Client(null, array('adapter' => $testAdapter));
        $this->gdata = new Zend_Gdata_CodeSearch($client);
   }

    public function testMaxResultsParam()
    {
        $this->gdata->resetParameters();
        $query = 'malloc';
        $this->gdata->setQuery($query);
        $max = 3;
        $this->gdata->setMaxResults($max);
        $this->assertTrue(isset($this->gdata->maxResults));
        $this->assertEquals($max, $this->gdata->getMaxResults());
        unset($this->gdata->maxResults);
        $this->assertFalse(isset($this->gdata->maxResults));
    }

    public function testStartIndexParam()
    {
        $this->gdata->resetParameters();
        $query = 'malloc';
        $this->gdata->setQuery($query);
        $start = 3;
        $this->gdata->setStartIndex($start);
        $this->assertTrue(isset($this->gdata->startIndex));
        $this->assertEquals($start, $this->gdata->getStartIndex());
        unset($this->gdata->startIndex);
        $this->assertFalse(isset($this->gdata->startIndex));
    }

    public function testExceptionPostNotSupported()
    {
        $this->gdata->resetParameters();
        try {
            $this->gdata->post('dummy-data', 'dummy-uri');
            $this->fail('Expected to catch Zend_Gdata_BadMethodCallException');
        } catch (Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Gdata_BadMethodCallException'),
                'Expected Zend_Gdata_BadMethodCallException, got '.get_class($e));
            $this->assertEquals("There are no post operations for CodeSearch.", $e->getMessage());
        }
    }

    public function testExceptionUpdatedMinMaxParam()
    {
        $this->gdata->resetParameters();
        try {
            $feed = $this->gdata->updatedMin = 'string';
            $this->fail('Expected to catch Zend_Gdata_InvalidArgumentException');
        } catch (Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Gdata_InvalidArgumentException'),
                'Expected Zend_Gdata_InvalidArgumentException, got '.get_class($e));
            $this->assertEquals("Parameter 'updatedMin' is not currently supported in CodeSearch.", $e->getMessage());
        }

        $this->gdata->resetParameters();
        try {
            $feed = $this->gdata->updatedMax = 'string';
            $this->fail('Expected to catch Zend_Gdata_InvalidArgumentException');
        } catch (Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Gdata_InvalidArgumentException'),
                'Expected Zend_Gdata_InvalidArgumentException, got '.get_class($e));
            $this->assertEquals("Parameter 'updatedMax' is not currently supported in CodeSearch.", $e->getMessage());
        }
    }

}
