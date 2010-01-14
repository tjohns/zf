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
    define('PHPUnit_MAIN_METHOD', 'ZendX_JQuery_View_jQueryTest::main');
}

require_once "Zend/Registry.php";
require_once "Zend/View.php";
require_once "ZendX/JQuery.php";
require_once "ZendX/JQuery/View/Helper/JQuery.php";

class ZendX_JQuery_View_jQueryTest extends PHPUnit_Framework_TestCase
{
	private $view = null;
	private $helper = null;

    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("ZendX_JQuery_jQueryTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

	public function setUp()
	{
        Zend_Registry::_unsetInstance();
        $this->view   = $this->getView();
        $this->helper = new ZendX_JQuery_View_Helper_JQuery_Container();
        $this->helper->setView($this->view);
        Zend_Registry::set('ZendX_JQuery_View_Helper_JQuery', $this->helper);
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

    public function testHelperSuccessfulCallForward()
    {
    	$jquery = new ZendX_JQuery_View_Helper_JQuery;
    	$jquery->addJavascript('alert();');
    }

    /**
     * @expectedException Zend_View_Exception
     */
    public function testHelperFailingCallForward()
    {
    	$jquery = new ZendX_JQuery_View_Helper_JQuery();
    	$jquery->addAsdf();
    }

	public function testShouldCallHelperWithoutExceptions()
	{
		$jquery = $this->view->jQuery();
		$this->assertTrue($jquery instanceof ZendX_JQuery_View_Helper_JQuery_Container);
	}

    public function testShouldAllowSpecifyingJQueryVersion()
    {
        $this->helper->setVersion('1.2.3');
        $this->assertEquals('1.2.3', $this->helper->getVersion());
    }

    public function testShouldUseDefaultSupportedVersionWhenNotSpecifiedOtherwise()
    {
    	$this->assertEquals(ZendX_JQuery::DEFAULT_JQUERY_VERSION, $this->helper->getVersion());
    	$this->assertEquals(ZendX_JQuery::DEFAULT_JQUERY_VERSION, $this->helper->getCdnVersion());
    }

    /**
     * Behaviour changed in 1.8
     *
     * @group ZF-5667
     */
    public function testUsingCdnShouldNotEnableHelperAnymore()
    {
    	$this->helper->setCdnVersion();
    	$this->assertFalse($this->helper->isEnabled());
    }

    public function testShouldBeNotEnabledByDefault()
    {
    	$this->assertFalse($this->helper->isEnabled());
    }

    public function testUsingLocalPath()
    {
    	$this->helper->setLocalPath("/js/jquery.min.js");
    	$this->assertFalse($this->helper->useCDN());
    	$this->assertFalse($this->helper->isEnabled());
    	$this->assertTrue($this->helper->useLocalPath());
    	$this->assertContains("/js/jquery.min.js", $this->helper->getLocalPath());

    	$render = $this->helper->__toString();
    	$this->assertNotContains("/js/jquery.min.js", $render);
    }

    public function testUiDisabledDefault()
    {
         $this->assertFalse($this->helper->uiIsEnabled());
    }

    public function testUiUsingCdnByDefault()
    {
         $this->assertFalse($this->helper->useUiLocal());
         $this->assertTrue($this->helper->useUiCdn());
         $this->assertNull($this->helper->getUiPath());
    }

    public function testGetUiVersionReturnsDefaultSupportedVersionIfNotSpecifiedOtherwise()
    {
        $this->assertEquals(ZendX_JQuery::DEFAULT_UI_VERSION, $this->helper->getUiVersion());
        $this->assertEquals(ZendX_JQuery::DEFAULT_UI_VERSION, $this->helper->getUiCdnVersion());
    }

    public function testShouldAllowEnableUi()
    {
         $this->helper->uiEnable();

         $render = $this->helper->__toString();
         $this->assertContains("jquery-ui", $render);
         $this->assertContains($this->helper->getUiCdnVersion(), $render);
    }

    public function testShouldAllowSetUiVersion()
    {
         $this->helper->setUiVersion("1.5.1");
         $this->assertContains("1.5.1", $this->helper->getUiVersion());
    }

    public function testShouldAllowSetLocalUiPath()
    {
         $this->helper->setUiLocalPath("/js/jquery-ui.min.js");

         $this->assertTrue($this->helper->useUiLocal());
         $this->assertFalse($this->helper->useUiCdn());
         $this->assertContains("/js/jquery-ui.min.js", $this->helper->getUiPath());
    }

    public function testNoConflictShouldBeDisabledDefault()
    {
    	$this->assertFalse(ZendX_JQuery_View_Helper_JQuery::getNoConflictMode());
    }

    public function testUsingNoConflictMode()
    {
    	ZendX_JQuery_View_Helper_JQuery::enableNoConflictMode();
    	$this->helper->setCDNVersion("1.2.6");
        $this->helper->enable();
    	$render = $this->helper->__toString();

    	$this->assertContains('var $j = jQuery.noConflict();', $render);
    }

    public function testDefaultRenderModeShouldIncludeAllBlocks()
    {
    	$this->assertEquals(ZendX_JQuery::RENDER_ALL, $this->helper->getRenderMode());
    }

    public function testShouldAllowSettingRenderMode()
    {
    	$this->helper->setRenderMode(1);
    	$this->assertEquals(1, $this->helper->getRenderMode());
    	$this->helper->setRenderMode(2);
    	$this->assertEquals(2, $this->helper->getRenderMode());
    	$this->helper->setRenderMode(4);
    	$this->assertEquals(4, $this->helper->getRenderMode());
    }

    public function testShouldAllowUsingAddOnLoadStack()
    {
    	$this->helper->addOnLoad('$(document).alert();');
    	$this->assertEquals(array('$(document).alert();'), $this->helper->getOnLoadActions());
    }

    public function testShouldAllowStackingMultipleOnLoad()
    {
    	$this->helper->addOnLoad("1");
    	$this->helper->addOnLoad("2");
    	$this->assertEquals(2, count($this->helper->getOnLoadActions()));
    }

    public function testShouldAllowCaptureOnLoad()
    {
    	$this->helper->onLoadCaptureStart();
    	echo '$(document).alert();';
    	$this->helper->onLoadCaptureEnd();
    	$this->assertEquals(array('$(document).alert();'), $this->helper->getOnLoadActions());
    }

    public function testShouldAllowCaptureJavascript()
    {
    	$this->helper->javascriptCaptureStart();
    	echo '$(document).alert();';
    	$this->helper->javascriptCaptureEnd();
    	$this->assertEquals(array('$(document).alert();'), $this->helper->getJavascript());

    	$this->helper->clearJavascript();
    	$this->assertEquals(array(), $this->helper->getJavascript());
    }

    /**
     * @expectedException Zend_Exception
     */
    public function testShouldDisallowNestingCapturesWithException()
    {
    	$this->helper->javascriptCaptureStart();
    	$this->helper->javascriptCaptureStart();
    }

    /**
     * @expectedException Zend_Exception
     */
    public function testShouldDisallowNestingCapturesWithException2()
    {
    	$this->helper->onLoadCaptureStart();
    	$this->helper->onLoadCaptureStart();

    	$this->setExpectedException('Zend_Exception');
    }

    public function testAddJavascriptFiles()
    {
    	$this->helper->addJavascriptFile('/js/test.js');
    	$this->helper->addJavascriptFile('/js/test2.js');
    	$this->helper->addJavascriptFile('http://example.com/test3.js');

    	$this->assertEquals(array('/js/test.js', '/js/test2.js', 'http://example.com/test3.js'), $this->helper->getJavascriptFiles());
    }

    public function testAddedJavascriptFilesCanBeCleared()
    {
    	$this->helper->addJavascriptFile('/js/test.js');
    	$this->helper->addJavascriptFile('/js/test2.js');
    	$this->helper->addJavascriptFile('http://example.com/test3.js');

    	$this->helper->clearJavascriptFiles();
    	$this->assertEquals(array(), $this->helper->getJavascriptFiles());
    }

    public function testAddedJavascriptFilesRender()
    {
    	$this->helper->addJavascriptFile('/js/test.js');
    	$this->helper->addJavascriptFile('/js/test2.js');
    	$this->helper->addJavascriptFile('http://example.com/test3.js');

        $this->helper->enable();

    	$render = $this->helper->__toString();
    	$this->assertContains('src="/js/test.js"', $render);
    	$this->assertContains('src="/js/test2.js"', $render);
    	$this->assertContains('src="http://example.com/test3.js', $render);
    }

    public function testAddStylesheet()
    {
    	$this->helper->addStylesheet('test.css');
    	$this->helper->addStylesheet('test2.css');

    	$this->assertEquals(array('test.css', 'test2.css'), $this->helper->getStylesheets());
    }

    public function testShouldAddJavascriptOnlyOnce()
    {
    	$this->helper->addJavascript("alert();");
    	$this->helper->addJavascript("alert();");

    	$this->assertEquals(1, count($this->helper->getJavascript()));
    }

    public function testShouldAddDelimWhenNoneGiven()
    {
    	$this->helper->addJavascript("alert()");

    	$this->assertEquals(array('alert();'), $this->helper->getJavascript());
    }

    public function testShouldRenderNothingOnDisable()
    {
    	$this->helper->setCDNVersion("1.2.6");
    	$this->helper->addJavascriptFile("test.js");
    	$this->helper->disable();
    	$this->assertEquals(strlen(''), strlen($this->helper->__toString()));
    }

    public function testShouldAllowBasicSetupWithCDN()
    {
        $this->helper->enable();
    	$this->helper->setCDNVersion("1.2.3");
    	$this->helper->addJavascriptFile("test.js");

    	$render = $this->helper->__toString();

    	$this->assertTrue($this->helper->useCDN());
    	$this->assertContains('jquery.min.js', $render);
    	$this->assertContains('1.2.3', $render);
    	$this->assertContains('test.js', $render);
    	$this->assertContains('<script type="text/javascript"', $render);
    }

    public function testShouldAllowUseRenderMode()
    {
        $this->helper->enable();
    	$this->helper->setCDNVersion("1.2.3");
    	$this->helper->addJavascriptFile("test.js");
    	$this->helper->addJavascript("helloWorld();");
    	$this->helper->addStylesheet("test.css");
    	$this->helper->addOnLoad("alert();");

    	// CHeck CDN Usage
    	$this->assertTrue($this->helper->useCDN());

    	// Test with Render No Parts
    	$this->helper->setRenderMode(0);
    	$this->assertEquals(strlen(''), strlen(trim($this->helper->__toString())));

    	// Test Render Only Library
    	$this->helper->setRenderMode(ZendX_JQuery::RENDER_LIBRARY);
    	$render = $this->helper->__toString();
    	$this->assertContains("1.2.3/jquery.min.js", $render);
    	$this->assertNotContains("test.css", $render);
    	$this->assertNotContains("test.js", $render);
    	$this->assertNotContains("alert();", $render);
    	$this->assertNotContains("helloWorld();", $render);

    	// Test Render Only AddOnLoad
    	$this->helper->setRenderMode(ZendX_JQuery::RENDER_JQUERY_ON_LOAD);
    	$render = $this->helper->__toString();
    	$this->assertNotContains("1.2.3/jquery.min.js", $render);
    	$this->assertNotContains("test.css", $render);
    	$this->assertNotContains("test.js", $render);
    	$this->assertContains("alert();", $render);
    	$this->assertNotContains("helloWorld();", $render);

    	// Test Render Only Javascript
    	$this->helper->setRenderMode(ZendX_JQuery::RENDER_SOURCES);
    	$render = $this->helper->__toString();
    	$this->assertNotContains("1.2.3/jquery.min.js", $render);
    	$this->assertNotContains("test.css", $render);
    	$this->assertContains("test.js", $render);
    	$this->assertNotContains("alert();", $render);
    	$this->assertNotContains("helloWorld();", $render);

    	// Test Render Only Javascript
    	$this->helper->setRenderMode(ZendX_JQuery::RENDER_STYLESHEETS);
    	$render = $this->helper->__toString();
    	$this->assertNotContains("1.2.3/jquery.min.js", $render);
    	$this->assertContains("test.css", $render);
    	$this->assertNotContains("test.js", $render);
    	$this->assertNotContains("alert();", $render);
    	$this->assertNotContains("helloWorld();", $render);

		// Test Render Library and AddOnLoad
    	$this->helper->setRenderMode(ZendX_JQuery::RENDER_LIBRARY | ZendX_JQuery::RENDER_JQUERY_ON_LOAD);
    	$render = $this->helper->__toString();
    	$this->assertContains("1.2.3/jquery.min.js", $render);
    	$this->assertNotContains("test.css", $render);
    	$this->assertNotContains("test.js", $render);
    	$this->assertContains("alert();", $render);
    	$this->assertNotContains("helloWorld();", $render);

    	// Test Render All
    	$this->helper->setRenderMode(ZendX_JQuery::RENDER_ALL);
    	$render = $this->helper->__toString();
    	$this->assertContains("1.2.3/jquery.min.js", $render);
    	$this->assertContains("test.css", $render);
    	$this->assertContains("test.js", $render);
    	$this->assertContains("alert();", $render);
    	$this->assertContains("helloWorld();", $render);
    }

    /**
     * @group ZF-5185
     */
    public function testClearAddOnLoadStack()
    {
        $this->helper->addOnLoad("foo");
        $this->helper->addOnLoad("bar");
        $this->helper->addOnLoad("baz");

        $this->assertEquals(array("foo", "bar", "baz"), $this->helper->getOnLoadActions());

        $this->helper->clearOnLoadActions();
        $this->assertEquals(array(), $this->helper->getOnLoadActions());
    }

    /**
     * @group ZF-5344
     */
    public function testNoConflictModeIsRecognizedInRenderingOnLoadStackEvent()
    {
        ZendX_JQuery_View_Helper_JQuery::enableNoConflictMode();
        $this->helper->addOnLoad("foo");
        $this->helper->addOnLoad("bar");
        $this->helper->enable();

        $jQueryStack = $this->helper->__toString();
        $this->assertContains('$j(document).ready(function()', $jQueryStack);

        ZendX_JQuery_View_Helper_JQuery::disableNoConflictMode();

        $jQueryStack =  $this->helper->__toString();
        $this->assertNotContains('$j(document).ready(function()', $jQueryStack);
    }

    /**
     * @group ZF-5839
     */
    public function testStylesheetShouldRenderCorrectClosingBracketBasedOnHtmlDoctypeDefinition()
    {
        $this->helper->addStylesheet("test.css");
        $this->view->doctype("HTML4_STRICT");

        $assert = '<link rel="stylesheet" href="test.css" type="text/css" media="screen">';
        $this->helper->enable();
        $this->assertContains($assert, $this->helper->__toString());

    }

    /**
     * @group ZF-5839
     */
    public function testStylesheetShouldRenderCorrectClosingBracketBasedOnXHtmlDoctypeDefinition()
    {
        $this->helper->addStylesheet("test.css");
        $this->view->doctype("XHTML1_STRICT");

        $assert = '<link rel="stylesheet" href="test.css" type="text/css" media="screen" />';
        $this->helper->enable();
        $this->assertContains($assert, $this->helper->__toString());
    }

    /**
     * @group ZF-6078
     */
    public function testIncludeJQueryLibraryFromSslPath()
    {
        $this->helper->setCdnSsl(true);
        $this->helper->enable();

        $this->assertContains(ZendX_JQuery::CDN_BASE_GOOGLE_SSL, $this->helper->__toString());
    }

    /**
     * @group ZF-6594
     */
    public function testJQueryGoogleCdnPathIsBuiltCorrectly()
    {
        $jQueryCdnPath = "http://ajax.googleapis.com/ajax/libs/jquery/1.3.1/jquery.min.js";
        $this->helper->setVersion("1.3.1");
        $this->helper->enable();

        $this->assertContains($jQueryCdnPath, $this->helper->__toString());
    }

    /**
     * @group ZF-6594
     */
    public function testJQueryUiGoogleCdnPathIsBuiltCorrectly()
    {
        $jQueryCdnPath = "http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.1/jquery-ui.min.js";
        $this->helper->setVersion("1.3.1");
        $this->helper->enable();
        $this->helper->setUiVersion("1.7.1");
        $this->helper->uiEnable();

        $this->assertContains($jQueryCdnPath, $this->helper->__toString());
    }

    /**
     * @group ZF-6594
     */
    public function testJQueryGoogleCdnSslPathIsBuiltCorrectly()
    {
        $jQueryCdnPath = "https://ajax.googleapis.com/ajax/libs/jquery/1.3.1/jquery.min.js";
        $this->helper->setCdnSsl(true);
        $this->helper->setVersion("1.3.1");
        $this->helper->enable();

        $this->assertContains($jQueryCdnPath, $this->helper->__toString());
    }

    /**
     * @group ZF-6594
     */
    public function testJQueryUiGoogleCdnSslPathIsBuiltCorrectly()
    {
        $jQueryCdnPath = "https://ajax.googleapis.com/ajax/libs/jqueryui/1.7.1/jquery-ui.min.js";
        $this->helper->setCdnSsl(true);
        $this->helper->setVersion("1.3.1");
        $this->helper->enable();
        $this->helper->setUiVersion("1.7.1");
        $this->helper->uiEnable();

        $this->assertContains($jQueryCdnPath, $this->helper->__toString());
    }
}