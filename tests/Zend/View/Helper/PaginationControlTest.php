<?php
// Call Zend_View_Helper_PaginationControlTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_View_Helper_PaginationControlTest::main");
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';

require_once 'Zend/View.php';
require_once 'Zend/Paginator.php';
require_once 'Zend/View/Helper/PaginationControl.php';

class Zend_View_Helper_PaginationControlTest extends PHPUnit_Framework_TestCase 
{
    /**
     * @var Zend_View_Helper_PaginationControl
     */
    private $_viewHelper;

    private $_paginator;
    
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite = new PHPUnit_Framework_TestSuite("Zend_View_Helper_PaginationControlTest");
        PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $view = new Zend_View();
        $view->addBasePath(dirname(__FILE__) . '/_files');
        $view->addHelperPath('Zend/View/Helper/', 'Zend_View_Helper');
        
        $this->_viewHelper = new Zend_View_Helper_PaginationControl();
        $this->_viewHelper->setView($view);
        $this->_paginator = Zend_Paginator::factory(range(1, 101));
    }

    public function tearDown()
    {
        unset($this->_viewHelper);
        unset($this->_paginator);
    }
    
    public function testGetsAndSetsView()
    {
        $view = new Zend_View();
        $helper = new Zend_View_Helper_PaginationControl();
        $this->assertNull($helper->view);
        $helper->setView($view);
        $this->assertType('Zend_View_Interface', $helper->view);
    }
    
    public function testGetsAndSetsDefaultViewPartial()
    {
        $this->assertNull(Zend_View_Helper_PaginationControl::getDefaultViewPartial());
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('partial');
        $this->assertEquals('partial', Zend_View_Helper_PaginationControl::getDefaultViewPartial());
        Zend_View_Helper_PaginationControl::setDefaultViewPartial(null);
    }

    public function testUsesDefaultScrollingStyleIfNoneSupplied()
    {
        // First we'll make sure the base case works
        $output = $this->_viewHelper->paginationControl($this->_paginator, 'All', 'testPagination.phtml');
        $this->assertContains('page count (11) equals page range (11)', $output, $output);

        Zend_Paginator::setDefaultScrollingStyle('All');
        $output = $this->_viewHelper->paginationControl($this->_paginator, null, 'testPagination.phtml');        
        $this->assertContains('page count (11) equals page range (11)', $output, $output);
        
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('testPagination.phtml');
        $output = $this->_viewHelper->paginationControl($this->_paginator);        
        $this->assertContains('page count (11) equals page range (11)', $output, $output);
    }

    public function testUsesDefaultViewPartialIfNoneSupplied()
    {
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('testPagination.phtml');
        $output = $this->_viewHelper->paginationControl($this->_paginator);
        $this->assertContains('pagination control', $output, $output);
        Zend_View_Helper_PaginationControl::setDefaultViewPartial(null);
    }

    public function testThrowsExceptionIfNoViewPartialFound()
    {
        try {
            $this->_viewHelper->paginationControl($this->_paginator);
        } catch (Exception $e) {
            $this->assertType('Zend_View_Exception', $e);
            $this->assertEquals('No view partial provided and no default set', $e->getMessage());
        }
    }
}

// Call Zend_View_Helper_PaginationControlTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_PaginationControlTest::main") {
    Zend_View_Helper_PaginationControlTest::main();
}
