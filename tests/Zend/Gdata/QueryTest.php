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

require_once 'Zend/Http/Client.php';
require_once 'Zend/Gdata/Query.php';

/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_QueryTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    public function testSetAndGetAlt()
    {
        $query = new Zend_Gdata_Query();
        $query->setAlt('rss');
        $this->assertEquals('rss', $query->alt);
        $this->assertTrue(strpos($query->getQueryUrl(), 'alt=rss') 
                !== false); 
    }

    public function testSetAndGetUpdatedMax()
    {
        $query = new Zend_Gdata_Query();
        $query->setUpdatedMax('2007-01-01');
        $this->assertEquals('2007-01-01', $query->getUpdatedMax());
        $this->assertTrue(strpos($query->getQueryUrl(), 
                'updated-max=2007-01-01') !== false); 
    }

    public function testSetAndGetUpdatedMin()
    {
        $query = new Zend_Gdata_Query();
        $query->setUpdatedMin('2007-01-01');
        $this->assertEquals('2007-01-01', $query->getUpdatedMin());
        $this->assertTrue(strpos($query->getQueryUrl(), 
                'updated-min=2007-01-01') !== false); 
    }

    public function testSetAndGetMaxResults()
    {
        $query = new Zend_Gdata_Query();
        $query->setMaxResults('300');
        $this->assertEquals('300', $query->getMaxResults());
        $this->assertTrue(strpos($query->getQueryUrl(), 
                'max-results=300') !== false); 
    }

    public function testSetAndGetGenericParam()
    {
        $query = new Zend_Gdata_Query();
        $query->setParam('fw', 'zend');
        $this->assertEquals('zend', $query->getParam('fw'));
        $this->assertTrue(strpos($query->getQueryUrl(), 
                'fw=zend') !== false); 
    }

    public function testSetAndGetFullTextQuery()
    {
        $query = new Zend_Gdata_Query();
        $query->setQuery('geek events');
        $this->assertEquals('geek events', $query->getQuery());
        $this->assertTrue(strpos($query->getQueryUrl(), 
                'q=geek+events') !== false); 
    }

    public function testSetAndGetStartIndex()
    {
        $query = new Zend_Gdata_Query();
        $query->setStartIndex(12);
        $this->assertEquals(12, $query->getStartIndex());
        $this->assertTrue(strpos($query->getQueryUrl(), 
                'start-index=12') !== false); 
    }

}
