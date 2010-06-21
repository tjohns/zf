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
 * @category    ZendX
 * @package     ZendX_JQuery
 * @subpackage  View
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license     http://framework.zend.com/license/new-bsd     New BSD License
 * @version     $Id$
 */

require_once dirname(__FILE__)."/../../../TestHelper.php";

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'ZendX_JQuery_View_TabContainerTest::main');
}

require_once "Zend/Registry.php";
require_once "Zend/View.php";
require_once "ZendX/JQuery.php";
require_once "ZendX/JQuery/View/Helper/JQuery.php";

require_once "ZendX/JQuery/View/Helper/TabContainer.php";

class ZendX_JQuery_View_TabContainerTest extends PHPUnit_Framework_TestCase
{
	private $view = null;
	private $jquery = null;
	private $helper = null;

    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("ZendX_JQuery_View_TabContainerTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

	public function setUp()
	{
        Zend_Registry::_unsetInstance();
        $this->view   = $this->getView();
        $this->jquery = new ZendX_JQuery_View_Helper_JQuery_Container();
        $this->jquery->setView($this->view);
        Zend_Registry::set('ZendX_JQuery_View_Helper_JQuery', $this->jquery);
	}

	public function tearDown()
	{
		ZendX_JQuery_View_Helper_JQuery::disableNoConflictMode();
	}

    public function getView()
    {
        require_once 'Zend/View.php';
        $view = new Zend_View();
        $view->addHelperPath('ZendX/JQuery/View/Helper/', 'ZendX_JQuery_View_Helper');
        return $view;
    }

    public function testCallingInViewEnablesJQueryHelper()
    {
        $element = $this->view->tabContainer();

        $this->assertTrue($this->jquery->isEnabled());
        $this->assertTrue($this->jquery->uiIsEnabled());
    }

    public function testShouldAppendToJqueryHelper()
    {
        $this->view->tabContainer()->addPane("elem1", "test1", "test1");
        $element = $this->view->tabContainer("elem1", array('option' => 'true'), array());

        $jquery = $this->view->jQuery()->__toString();
        $this->assertContains('tabs(', $jquery);
        $this->assertContains('"option":"true"', $jquery);
    }

    public function testShouldAllowAddingTabs()
    {
        $tabs = $this->view->tabContainer()->addPane("container1", "elem1", "Text1")
                        ->addPane("container1", "elem2", "Text2")
                        ->tabContainer("container1", array(), array());

        $this->assertEquals(array('$("#container1").tabs({});'), $this->jquery->getOnLoadActions());
        $this->assertContains("elem1", $tabs);
        $this->assertContains("Text1", $tabs);
        $this->assertContains("elem2", $tabs);
        $this->assertContains("Text2", $tabs);
        $this->assertContains('href="#container1-frag-1"', $tabs);
        $this->assertContains('href="#container1-frag-2"', $tabs);
    }

    public function testShoudAllowAddingTabsFromUrls()
    {
        $tabs = $this->view->tabContainer()->addPane("container1", "elem1", '', array('contentUrl' => 'blub.html'))
                        ->addPane("container1", "elem2", '', array('contentUrl' => 'cookie.html'))
                        ->tabContainer("container1", array(), array());

        $this->assertEquals(array('$("#container1").tabs({});'), $this->jquery->getOnLoadActions());
        $this->assertContains("elem1", $tabs);
        $this->assertContains("elem2", $tabs);
        $this->assertContains('href="blub.html"', $tabs);
        $this->assertContains('href="cookie.html"', $tabs);
    }

    public function testShouldAllowCaptureTabContent()
    {
        $this->view->tabPane()->captureStart("container1", "elem1");
        echo "Lorem Ipsum!";
        $this->view->tabPane()->captureEnd("container1");

        $this->view->tabPane()->captureStart("container1", "elem2", array('contentUrl' => 'foo.html'));
        echo "This is captured, but not displayed: contentUrl overrides this output.";
        $this->view->tabPane()->captureEnd("container1");

        $tabs = $this->view->tabContainer("container1", array(), array());

        $this->assertEquals(array('$("#container1").tabs({});'), $this->jquery->getOnLoadActions());
        $this->assertContains('elem1', $tabs);
        $this->assertContains('elem2', $tabs);
        $this->assertContains('Lorem Ipsum!', $tabs);
        $this->assertContains('href="foo.html"', $tabs);
        $this->assertNotContains('This is captured, but not displayed: contentUrl overrides this output.', $tabs);
    }

    public function testShouldAllowUsingTabPane()
    {
        $this->view->tabPane("container1", "Lorem Ipsum!", array('title' => 'elem1'));
        $this->view->tabPane("container1", '', array('title' => 'elem2', 'contentUrl' => 'foo.html'));

        $tabs = $this->view->tabContainer("container1", array(), array());

        $this->assertEquals(array('$("#container1").tabs({});'), $this->jquery->getOnLoadActions());
        $this->assertContains('elem1', $tabs);
        $this->assertContains('elem2', $tabs);
        $this->assertContains('Lorem Ipsum!', $tabs);
        $this->assertContains('href="foo.html"', $tabs);
        $this->assertNotContains('This is captured, but not displayed: contentUrl overrides this output.', $tabs);
    }

    public function testPaneCaptureLockExceptionNoNestingAllowed()
    {
        $this->view->tabPane()->captureStart('pane1', 'Label1');
        try {
            $this->view->tabPane()->captureStart('pane1', 'Label1');
            $this->fail();
        } catch(ZendX_JQuery_View_Exception $e) {

        }
    }

    public function testPaneCaptureLockExceptionNoEndWithoutStartPossible()
    {
        try {
            $this->view->tabPane()->captureEnd('pane3');
            $this->fail();
        } catch(ZendX_JQuery_View_Exception $e) {

        }
    }
}

if (PHPUnit_MAIN_METHOD == 'ZendX_JQuery_View_TabContainerTest::main') {
    ZendX_JQuery_View_TabContainerTest::main();
}