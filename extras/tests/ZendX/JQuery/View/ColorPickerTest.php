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
 * @copyright   Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license     http://framework.zend.com/license/new-bsd     New BSD License
 * @version     $Id$
 */

require_once dirname(__FILE__)."/../../../TestHelper.php";

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'ZendX_JQuery_View_ColorPickerTest::main');
}

require_once "Zend/Registry.php";
require_once "Zend/View.php";
require_once "ZendX/JQuery.php";
require_once "ZendX/JQuery/View/Helper/JQuery.php";

require_once "ZendX/JQuery/View/Helper/ColorPicker.php";

class ZendX_JQuery_View_ColorPickerTest extends PHPUnit_Framework_TestCase
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
        $suite  = new PHPUnit_Framework_TestSuite("ZendX_JQuery_View_ColorPickerTest");
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
        $element = $this->view->colorPicker("element", "");

        $this->assertTrue($this->jquery->isEnabled());
        $this->assertTrue($this->jquery->uiIsEnabled());
    }

    public function testShouldAppendToJqueryHelper()
    {
        $element = $this->view->colorPicker("elem1", "Default", array('option' => 'true'));

        $jquery = $this->view->jQuery()->__toString();
        $this->assertContains('colorpicker(', $jquery);
        $this->assertContains('"option":"true"', $jquery);
    }

    public function testShouldCreateInputField()
    {
        $element = $this->view->colorPicker("elem1");

        $this->assertEquals(array('$("#elem1").colorpicker({});'), $this->view->jQuery()->getOnLoadActions());
        $this->assertContains('<input', $element);
        $this->assertContains('id="elem1"', $element);
    }

    public function testShouldDoSomeSemiChecksIfValueCanBeAppliedToColorOption()
    {
        $element = $this->view->colorPicker("elem1", "abc");
        $this->assertEquals(array('$("#elem1").colorpicker({});'), $this->view->jQuery()->getOnLoadActions());

        $element = $this->view->colorPicker("elem1", "#FFFFFF");
        $this->assertEquals(array('$("#elem1").colorpicker({});', '$("#elem1").colorpicker({"color":"#FFFFFF"});'), $this->view->jQuery()->getOnLoadActions());
    }
}

if (PHPUnit_MAIN_METHOD == 'ZendX_JQuery_View_ColorPickerTest::main') {
    ZendX_JQuery_View_ColorPickerTest::main();
}