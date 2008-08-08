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
 * @package    Zend_Paginator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_Paginator_ScrollingStyle_Sliding
 */
require_once 'Zend/Paginator/ScrollingStyle/Sliding.php';

/**
 * @see PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @see Zend_Paginator
 */
require_once 'Zend/Paginator.php';

/**
 * @category   Zend
 * @package    Zend_Paginator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Paginator_ScrollingStyle_SlidingTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_Paginator_ScrollingStyle_Sliding
     */
    private $_scrollingStyle;
    /**
     * @var Zend_Paginator
     */
    private $_paginator;
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_scrollingStyle = new Zend_Paginator_ScrollingStyle_Sliding();
        $this->_paginator = Zend_Paginator::factory(range(1, 101));
        $this->_paginator->setItemCountPerPage(10);
        $this->_paginator->setPageRange(5);
    }
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->_scrollingStyle = null;
        $this->_paginator = null;
        parent::tearDown();
    }
    
    public function testGetPagesInRangeForFirstPage()
    {
        $this->_paginator->setCurrentPageNumber(1);
        $actual = $this->_scrollingStyle->getPages($this->_paginator);
        $expected = array_combine(range(1, 5), range(1, 5));
        $this->assertEquals($expected, $actual);
    }
    
    public function testGetPagesInRangeForSecondPage()
    {
        $this->_paginator->setCurrentPageNumber(2);
        $actual = $this->_scrollingStyle->getPages($this->_paginator);
        $expected = array_combine(range(1, 5), range(1, 5));
        $this->assertEquals($expected, $actual);
    }
    
    public function testGetPagesInRangeForFifthPage()
    {
        $this->_paginator->setCurrentPageNumber(5);
        $actual = $this->_scrollingStyle->getPages($this->_paginator);
        $expected = array_combine(range(3, 7), range(3, 7));
        $this->assertEquals($expected, $actual);
    }
    
    public function testGetPagesInRangeForLastPage()
    {
        $this->_paginator->setCurrentPageNumber(11);
        $actual = $this->_scrollingStyle->getPages($this->_paginator);
        $expected = array_combine(range(7, 11), range(7, 11));
        $this->assertEquals($expected, $actual);
    }
    
    public function testGetNextAndPreviousPageForFirstPage()
    {
        $this->_paginator->setCurrentPageNumber(1);
        $pages = $this->_paginator->getPages('Sliding');
        
        $this->assertEquals(2, $pages->next);
    }
    
    public function testGetNextAndPreviousPageForSecondPage()
    {
        $this->_paginator->setCurrentPageNumber(2);
        $pages = $this->_paginator->getPages('Sliding');
        $this->assertEquals(1, $pages->previous);
        $this->assertEquals(3, $pages->next);
    }
    
    public function testGetNextAndPreviousPageForMiddlePage()
    {
        $this->_paginator->setCurrentPageNumber(6);
        $pages = $this->_paginator->getPages('Sliding');
        $this->assertEquals(5, $pages->previous);
        $this->assertEquals(7, $pages->next);
    }
    
    public function testGetNextAndPreviousPageForSecondLastPage()
    {
        $this->_paginator->setCurrentPageNumber(10);
        $pages = $this->_paginator->getPages('Sliding');
        $this->assertEquals(9, $pages->previous);
        $this->assertEquals(11, $pages->next);
    }
    
    public function testGetNextAndPreviousPageForLastPage()
    {
        $this->_paginator->setCurrentPageNumber(11);
        $pages = $this->_paginator->getPages('Sliding');
        $this->assertEquals(10, $pages->previous);
    }
    
    public function testPageRangeIsLargerThanPageCount()
    {
        $this->_paginator->setPageRange(100);
        $pages = $this->_paginator->getPages();
        $this->assertEquals(11, $pages->last);
    }
}