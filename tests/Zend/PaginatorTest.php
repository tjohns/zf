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
 * Test helper
 */
require_once dirname(__FILE__) . '/../TestHelper.php';

/**
 * @see Zend_Paginator
 */
require_once 'Zend/Paginator.php';

/**
 * @see PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @see Zend_Config_Xml
 */
require_once 'Zend/Config/Xml.php';

/**
 * @see Zend_Db_Adapter_Pdo_Sqlite
 */
require_once 'Zend/Db/Adapter/Pdo/Sqlite.php';

/**
 * @see Zend_View
 */
require_once 'Zend/View.php';

/**
 * @see Zend_Controller_Action_HelperBroker
 */
require_once 'Zend/Controller/Action/HelperBroker.php';

/**
 * @see Zend_View_Helper_PaginationControl
 */
require_once 'Zend/View/Helper/PaginationControl.php';

/**
 * @category   Zend
 * @package    Zend_Paginator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_PaginatorTest extends PHPUnit_Framework_TestCase  
{
    /**
     * Paginator instance
     *
     * @var Zend_Paginator
     */
    protected $_paginator = null; 
    
    protected $_testCollection = null;
    
    protected $_query = null;
    
    protected $_config = null;
    
    protected function setUp()
    {
        $db = new Zend_Db_Adapter_Pdo_Sqlite(array(
            'dbname' => dirname(__FILE__) . '/Paginator/_files/test.sqlite'
        ));
        
        $this->_query = $db->select()->from('test');
        
        $this->_testCollection = range(1, 101);
        $this->_paginator = Zend_Paginator::factory($this->_testCollection);
        
        $this->_config = new Zend_Config_Xml(dirname(__FILE__) . '/Paginator/_files/config.xml');
        // get a fresh new copy of ViewRenderer in each tests
        Zend_Controller_Action_HelperBroker::resetHelpers();
        
        $this->_restorePaginatorDefaults();
    }
    
    protected function tearDown()
    {
        $this->_dbConn = null;
        $this->_testCollection = null;
        $this->_paginator = null;
    }
    
    protected function _restorePaginatorDefaults()
    {
        $this->_paginator->setItemCountPerPage(10);
        $this->_paginator->setCurrentPageNumber(1);
        $this->_paginator->setPageRange(10);
        $this->_paginator->setView();
        
        Zend_Paginator::setDefaultScrollingStyle();
        Zend_View_Helper_PaginationControl::setDefaultViewPartial(null);
        
        Zend_Paginator::setConfig($this->_config->default);
        
        $loader = Zend_Paginator::getScrollingStyleLoader();
        $loader->clearPaths();
        $loader->addPrefixPath('Zend_Paginator_ScrollingStyle', 'Zend/Paginator/ScrollingStyle');
    }
    
    public function testFactoryReturnsArrayAdapter()
    {
        $paginator = Zend_Paginator::factory($this->_testCollection);
        $this->assertType('Zend_Paginator_Adapter_Array', $paginator->getAdapter());
    }

    public function testFactoryReturnsDbSelectAdapter()
    {
        $paginator = Zend_Paginator::factory($this->_query);
        
        $this->assertType('Zend_Paginator_Adapter_DbSelect', $paginator->getAdapter());
    }

    public function testFactoryReturnsIteratorAdapter()
    {
        $paginator = Zend_Paginator::factory(new ArrayIterator($this->_testCollection));
        $this->assertType('Zend_Paginator_Adapter_Iterator', $paginator->getAdapter());
    }
    
    public function testFactoryReturnsNullAdapter()
    {
        $paginator = Zend_Paginator::factory(101);
        $this->assertType('Zend_Paginator_Adapter_Null', $paginator->getAdapter());
    }
    
    public function testFactoryThrowsInvalidClassExceptionAdapter()
    {
        try {
            $paginator = Zend_Paginator::factory(new stdClass());
        } catch (Exception $e) {
            $this->assertType('Zend_Paginator_Exception', $e);
            $this->assertContains('stdClass', $e->getMessage());
        }
    }
    
    public function testFactoryThrowsInvalidTypeExceptionAdapter()
    {
        try {
            $paginator = Zend_Paginator::factory('invalid argument');
        } catch (Exception $e) {
            $this->assertType('Zend_Paginator_Exception', $e);
            $this->assertContains('string', $e->getMessage());
        }
    }
    
    public function testAddsSingleScrollingStylePrefixPath()
    {
        Zend_Paginator::addScrollingStylePrefixPath('prefix1', 'path1');
        $loader = Zend_Paginator::getScrollingStyleLoader();
        $paths = $loader->getPaths();
        
        $this->assertArrayHasKey('prefix1_', $paths);
        $this->assertEquals($paths['prefix1_'], array('path1/'));
    }
    
    public function testAddsSingleScrollingStylePrefixPathWithArray()
    {
        Zend_Paginator::addScrollingStylePrefixPaths(array('prefix' => 'prefix2',
                                                           'path'   => 'path2'));
        $loader = Zend_Paginator::getScrollingStyleLoader();
        $paths = $loader->getPaths();
        
        $this->assertArrayHasKey('prefix2_', $paths);
        $this->assertEquals($paths['prefix2_'], array('path2/'));
    }
    
    public function testAddsMultipleScrollingStylePrefixPaths()
    {
        $paths = array('prefix3' => 'path3',
                       'prefix4' => 'path4',
                       'prefix5' => 'path5');
        
        Zend_Paginator::addScrollingStylePrefixPaths($paths);
        $loader = Zend_Paginator::getScrollingStyleLoader();
        $paths = $loader->getPaths();
        
        for ($i = 3; $i <= 5; $i++) {
            $prefix = 'prefix' . $i . '_';
            $this->assertArrayHasKey($prefix, $paths);
            $this->assertEquals($paths[$prefix], array('path' . $i . '/'));
        }
        
        $loader->clearPaths();
    }
    
    public function testGetsAndSetsDefaultScrollingStyle()
    {
        $this->assertEquals(Zend_Paginator::getDefaultScrollingStyle(), 'Sliding');
        Zend_Paginator::setDefaultScrollingStyle('Scrolling');
        $this->assertEquals(Zend_Paginator::getDefaultScrollingStyle(), 'Scrolling');
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
    }
    
    public function testHasCorrectCountAfterInit()
    {
        $paginator = Zend_Paginator::factory(range(1, 101));
        $this->assertEquals(11, $paginator->count());
    }
    
    public function testLoadsFromConfig()
    {
        Zend_Paginator::setConfig($this->_config->testing);
        $this->assertEquals('Scrolling', Zend_Paginator::getDefaultScrollingStyle());
        
        $paths = array(
            'prefix6' => 'path6',
            'prefix7' => 'path7',
            'prefix8' => 'path8'
        );
        
        $loader = Zend_Paginator::getScrollingStyleLoader();
        $paths = $loader->getPaths();
        
        for ($i = 6; $i <= 8; $i++) {
            $prefix = 'prefix' . $i . '_';
            $this->assertArrayHasKey($prefix, $paths);
            $this->assertEquals($paths[$prefix], array('path' . $i . '/'));
        }
        
        $paginator = Zend_Paginator::factory(range(1, 101));
        $this->assertEquals(3, $paginator->getItemCountPerPage());
        $this->assertEquals(7, $paginator->getPageRange());
    }
    
    public function testGetsPagesForPageOne()
    {
        $expected = new stdClass();
        $expected->pageCount        = 11;
        $expected->itemCountPerPage = 10;
        $expected->first            = 1;
        $expected->current          = 1;
        $expected->last             = 11;
        $expected->next             = 2;
        $expected->pagesInRange     = array_combine(range(1, 10), range(1, 10));
        $expected->firstPageInRange = 1;
        $expected->lastPageInRange  = 10;
        $expected->currentItemCount = 10;
        $expected->totalItemCount   = 101;
        $expected->firstItemNumber  = 1;
        $expected->lastItemNumber   = 10;
        
        $actual = $this->_paginator->getPages();
        
        $this->assertEquals($expected, $actual);
    }
    
    public function testGetsPagesForPageTwo()
    {
        $expected = new stdClass();
        $expected->pageCount        = 11;
        $expected->itemCountPerPage = 10;
        $expected->first            = 1;
        $expected->current          = 2;
        $expected->last             = 11;
        $expected->previous         = 1;
        $expected->next             = 3;
        $expected->pagesInRange     = array_combine(range(1, 10), range(1, 10));
        $expected->firstPageInRange = 1;
        $expected->lastPageInRange  = 10;
        $expected->currentItemCount = 10;
        $expected->totalItemCount   = 101;
        $expected->firstItemNumber  = 11;
        $expected->lastItemNumber   = 20;
        
        $this->_paginator->setCurrentPageNumber(2);
        $actual = $this->_paginator->getPages();
        
        $this->assertEquals($expected, $actual);
    }
    
    public function testRendersWithoutPartial()
    {
        $this->_paginator->setView(new Zend_View());
        $string = @$this->_paginator->__toString();
        $this->assertEquals('', $string);
    }
    
    public function testRendersWithPartial()
    {
        $view = new Zend_View();
        $view->addBasePath(dirname(__FILE__) . '/Paginator/_files');
        $view->addHelperPath(dirname(__FILE__) . '/../../../trunk/library/Zend/View/Helper', 'Zend_View_Helper');
        
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('partial.phtml');
        
        $this->_paginator->setView($view);
        
        $string = $this->_paginator->__toString();
        $this->assertEquals('partial rendered successfully', $string);
    }
    
    public function testGetsPageCount()
    {
        $this->assertEquals(11, $this->_paginator->count());
    }
    
    public function testGetsAndSetsItemCountPerPage()
    {
        $this->assertEquals(10, $this->_paginator->getItemCountPerPage());
        $this->_paginator->setItemCountPerPage(15);
        $this->assertEquals(15, $this->_paginator->getItemCountPerPage());
        $this->_paginator->setItemCountPerPage(10);
    }
    
    public function testGetsCurrentItemCount()
    {
        $this->_paginator->setItemCountPerPage(10);
        $this->_paginator->setPageRange(10);
        
        $this->assertEquals(10, $this->_paginator->getCurrentItemCount());
        
        $this->_paginator->setCurrentPageNumber(11);
        
        $this->assertEquals(1, $this->_paginator->getCurrentItemCount());
        
        $this->_paginator->setCurrentPageNumber(1);
    }
    
    public function testGetsCurrentItems()
    {
        $items = $this->_paginator->getCurrentItems();
        $this->assertType('ArrayIterator', $items);
        
        $count = 0;
        
        foreach ($items as $item) {
        	$count++;
        }
        
        $this->assertEquals(10, $count);
    }
    
    public function testGetsIterator()
    {
        $items = $this->_paginator->getIterator();
        $this->assertType('ArrayIterator', $items);
        
        $count = 0;
        
        foreach ($items as $item) {
            $count++;
        }
        
        $this->assertEquals(10, $count);
    }
    
    public function testGetsAndSetsCurrentPageNumber()
    {
        $this->assertEquals(1, $this->_paginator->getCurrentPageNumber());
        $this->_paginator->setCurrentPageNumber(-1);
        $this->assertEquals(1, $this->_paginator->getCurrentPageNumber());
        $this->_paginator->setCurrentPageNumber(11);
        $this->assertEquals(11, $this->_paginator->getCurrentPageNumber());
        $this->_paginator->setCurrentPageNumber(111);
        $this->assertEquals(11, $this->_paginator->getCurrentPageNumber());
        $this->_paginator->setCurrentPageNumber(1);
        $this->assertEquals(1, $this->_paginator->getCurrentPageNumber());
    }
    
    public function testGetsAbsoluteItemNumber()
    {
        $this->assertEquals(1, $this->_paginator->getAbsoluteItemNumber(1));
        $this->assertEquals(11, $this->_paginator->getAbsoluteItemNumber(1, 2));
        $this->assertEquals(24, $this->_paginator->getAbsoluteItemNumber(4, 3));
    }
    
    public function testGetsItem()
    {
        $this->assertEquals(1, $this->_paginator->getItem(1));
        $this->assertEquals(11, $this->_paginator->getItem(1, 2));
        $this->assertEquals(24, $this->_paginator->getItem(4, 3));
    }
    
    public function testThrowsExceptionWhenCollectionIsEmpty()
    {
        $paginator = Zend_Paginator::factory(array());
        
        try {
            $paginator->getItem(1);
        } catch (Exception $e) {
            $this->assertType('Zend_Paginator_Exception', $e);
            $this->assertContains('Page 1 does not exist', $e->getMessage());
        }
    }
    
    public function testThrowsExceptionWhenRetrievingNonexistentItemFromLastPage()
    {
        try {
            $this->_paginator->getItem(10, 11);
        } catch (Exception $e) {
            $this->assertType('Zend_Paginator_Exception', $e);
            $this->assertContains('Page 11 does not contain item number 10', $e->getMessage());
        }
    }
    
    public function testNormalizesPageNumber()
    {
        $this->assertEquals(1, $this->_paginator->normalizePageNumber(0));
        $this->assertEquals(1, $this->_paginator->normalizePageNumber(1));
        $this->assertEquals(2, $this->_paginator->normalizePageNumber(2));
        $this->assertEquals(5, $this->_paginator->normalizePageNumber(5));
        $this->assertEquals(10, $this->_paginator->normalizePageNumber(10));
        $this->assertEquals(11, $this->_paginator->normalizePageNumber(11));
        $this->assertEquals(11, $this->_paginator->normalizePageNumber(12));
    }
    
    public function testNormalizesItemNumber()
    {
        $this->assertEquals(1, $this->_paginator->normalizeItemNumber(0));
        $this->assertEquals(1, $this->_paginator->normalizeItemNumber(1));
        $this->assertEquals(2, $this->_paginator->normalizeItemNumber(2));
        $this->assertEquals(5, $this->_paginator->normalizeItemNumber(5));
        $this->assertEquals(9, $this->_paginator->normalizeItemNumber(9));
        $this->assertEquals(10, $this->_paginator->normalizeItemNumber(10));
        $this->assertEquals(10, $this->_paginator->normalizeItemNumber(11));
    }
    
    public function testGetsPagesInSubsetRange()
    {
        $actual = $this->_paginator->getPagesInRange(3, 8);
        $this->assertEquals(array_combine(range(3, 8), range(3, 8)), $actual);
    }
    
    public function testGetsPagesInOutOfBoundsRange()
    {
        $actual = $this->_paginator->getPagesInRange(-1, 12);
        $this->assertEquals(array_combine(range(1, 11), range(1, 11)), $actual);
    }
    
    public function testGetsItemsByPage()
    {
        $expected = new ArrayIterator(range(1, 10));
        
        $page1 = $this->_paginator->getItemsByPage(1);
        
        $this->assertEquals($page1, $expected);
        $this->assertEquals($page1, $this->_paginator->getItemsByPage(1));
    }
    
    public function testGetsItemCount()
    {
        $this->assertEquals(101, $this->_paginator->getItemCount(range(1, 101)));
        
        $limitIterator = new LimitIterator(new ArrayIterator(range(1, 101)));
        $this->assertEquals(101, $this->_paginator->getItemCount($limitIterator));
    }
    
    public function testGetsViewFromViewRenderer()
    {
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $viewRenderer->setView(new Zend_View());
                
        $this->assertType('Zend_View_Interface', $this->_paginator->getView());
    }
    
    public function testGeneratesViewIfNonexistent()
    {
        $this->assertType('Zend_View_Interface', $this->_paginator->getView());
    }
    
    public function testGetsAndSetsView()
    {
        $this->_paginator->setView(new Zend_View());
        $this->assertType('Zend_View_Interface', $this->_paginator->getView());
    }
    
    public function testRenders()
    {
        try {
            $this->_paginator->render(new Zend_View());
        } catch (Exception $e) {
            $this->assertType('Zend_View_Exception', $e);
            $this->assertEquals('No view partial provided and no default set', $e->getMessage());
        }
    }
    
    public function testGetsAndSetsPageRange()
    {
        $this->assertEquals(10, $this->_paginator->getPageRange());
        $this->_paginator->setPageRange(15);
        $this->assertEquals(15, $this->_paginator->getPageRange());
    }
    
    /**
     * Test for ZF-3720
     */
    public function testGivesCorrectItemCount()
    {
        $paginator = Zend_Paginator::factory(range(1, 101));
        $paginator->setCurrentPageNumber(5)
                  ->setItemCountPerPage(5);
        $expected = new ArrayIterator(range(21, 25));
        
        $this->assertEquals($expected, $paginator->getCurrentItems());
    }
    
    /**
     * Test for ZF-3737
     */
    public function testKeepsCurrentPageNumberAfterItemCountPerPageSet()
    {
        $paginator = Zend_Paginator::factory(array('item1', 'item2'));
        $paginator->setCurrentPageNumber(2)
                  ->setItemCountPerPage(1);

        $items = $paginator->getCurrentItems();

        $this->assertEquals('item2', $items[0]);
    }
}