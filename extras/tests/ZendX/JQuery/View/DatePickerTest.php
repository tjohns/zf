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
    define('PHPUnit_MAIN_METHOD', 'ZendX_JQuery_View_DatePickerTest::main');
}

require_once "Zend/Registry.php";
require_once "Zend/View.php";
require_once "Zend/Locale.php";
require_once "ZendX/JQuery.php";
require_once "ZendX/JQuery/View/Helper/JQuery.php";

require_once "ZendX/JQuery/View/Helper/DatePicker.php";

class ZendX_JQuery_View_DatePickerTest extends PHPUnit_Framework_TestCase
{
    private $view = null;
    private $jquery = null;

    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("ZendX_JQuery_View_DatePickerTest");
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

    /**
     * Get jQuery View
     *
     * @return Zend_View
     */
    public function getView()
    {
        require_once 'Zend/View.php';
        $view = new Zend_View();
        $view->addHelperPath('ZendX/JQuery/View/Helper/', 'ZendX_JQuery_View_Helper');
        return $view;
    }

    public function testCallingInViewEnablesJQueryHelper()
    {
        $element = $this->view->datePicker("element", "");

        $this->assertTrue($this->jquery->isEnabled());
        $this->assertTrue($this->jquery->uiIsEnabled());
    }

    public function testShouldAppendToJqueryHelper()
    {
        $element = $this->view->datePicker("elem1", "", array("option" => "true"));

        $jquery = $this->view->jQuery()->__toString();
        $this->assertContains('datepicker(', $jquery);
        $this->assertContains('"option":"true"', $jquery);
    }

    public function testShouldCreateInputField()
    {
        $element = $this->view->datePicker("elem1", "01.01.2007");

        $this->assertEquals(array('$("#elem1").datepicker({});'), $this->view->jQuery()->getOnLoadActions());
        $this->assertContains("<input", $element);
        $this->assertContains('id="elem1"', $element);
        $this->assertContains('value="01.01.2007"', $element);
    }

    public function testDatePickerSupportsLocaleDe()
    {
        $view = $this->getView();
        $locale = new Zend_Locale('de');
        Zend_Registry::set('Zend_Locale', $locale);
        $view->datePicker("dp1");

        $this->assertEquals(array(
            '$("#dp1").datepicker({"dateFormat":"dd.mm.yy"});',
        ), $view->jQuery()->getOnLoadActions());
    }

    public function testDatePickerSupportsLocaleEn()
    {
        $view = $this->getView();

        $locale = new Zend_Locale('en');
        Zend_Registry::set('Zend_Locale', $locale);
        $view->datePicker("dp2");

        $this->assertEquals(array(
            '$("#dp2").datepicker({"dateFormat":"M d, yy"});',
        ), $view->jQuery()->getOnLoadActions());
    }

    public function testDatePickerSupportsLocaleFr()
    {
        $view = $this->getView();

        $locale = new Zend_Locale('fr');
        Zend_Registry::set('Zend_Locale', $locale);
        $view->datePicker("dp3");

        $this->assertEquals(array(
            '$("#dp3").datepicker({"dateFormat":"d M yy"});',
        ), $view->jQuery()->getOnLoadActions());
    }

    /**
     * @group ZF-5615
     */
    public function testDatePickerLocalization()
    {
        $dpFormat = ZendX_JQuery_View_Helper_DatePicker::resolveZendLocaleToDatePickerFormat("MMM d, yyyy");
        $this->assertEquals("M d, yy", $dpFormat, "'MMM d, yyyy' has to be converted to 'M d, yy'.");
    }
}

if (PHPUnit_MAIN_METHOD == 'ZendX_JQuery_View_DatePickerTest::main') {
    ZendX_JQuery_View_DatePickerTest::main();
}