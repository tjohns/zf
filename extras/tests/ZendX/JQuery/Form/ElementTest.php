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
 * @version     $Id: AllTests.php 11232 2008-09-05 08:16:33Z beberlei $
 */

require_once dirname(__FILE__)."/../../../TestHelper.php";

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'ZendX_JQuery_Form_ElementTest::main');
}

require_once "Zend/Registry.php";
require_once "Zend/Form/Element.php";
require_once "ZendX/JQuery.php";
require_once "ZendX/JQuery/View/Helper/JQuery.php";

require_once "ZendX/JQuery/Form/Element/Spinner.php";
require_once "ZendX/JQuery/Form/Element/Slider.php";
require_once "ZendX/JQuery/Form/Element/ColorPicker.php";
require_once "ZendX/JQuery/Form/Element/DatePicker.php";
require_once "ZendX/JQuery/Form/Element/AutoComplete.php";

require_once "Zend/Form/Decorator/ViewHelper.php";
require_once "ZendX/JQuery/Form/Decorator/UiWidgetElement.php";


class ZendX_JQuery_Form_ElementTest extends PHPUnit_Framework_TestCase
{
    public function testElementSetGetJQueryParam()
    {
        $spinner = new ZendX_JQuery_Form_Element_Spinner('spinnerElem');
        $spinner->setJQueryParam("foo", "baz");
        $this->assertEquals("baz", $spinner->getJQueryParam("foo"));

        $spinner->setJQueryParam("foo", "bar");
        $spinner->setJQueryParam("bar", array());
        $this->assertEquals("bar", $spinner->getJQueryParam("foo"));
        $this->assertEquals(array(), $spinner->getJQueryParam("bar"));
    }

    public function testElementSetGetMassJQueryParams()
    {
        $spinner = new ZendX_JQuery_Form_Element_Spinner('spinnerElem');

        $spinner->setJQueryParams(array("foo" => "baz", "bar" => "baz"));
        $this->assertEquals(array("foo" => "baz", "bar" => "baz"), $spinner->getJQueryParams());

        $spinner->setJQueryParams(array("foo" => "bar"));
        $this->assertEquals(array("foo" => "bar", "bar" => "baz"), $spinner->getJQueryParams());
    }

    public function testElementsHaveUiWidgetDecorator()
    {
        $spinner = new ZendX_JQuery_Form_Element_Spinner('spinnerElem');
        $this->assertTrue($spinner->getDecorator('UiWidgetElement') !== false);

        $slider = new ZendX_JQuery_Form_Element_Slider('sliderElem');
        $this->assertTrue($slider->getDecorator('UiWidgetElement') !== false);

        $cp = new ZendX_JQuery_Form_Element_ColorPicker('cpElem');
        $this->assertTrue($cp->getDecorator('UiWidgetElement') !== false);

        $dp = new ZendX_JQuery_Form_Element_DatePicker('dpElem');
        $this->assertTrue($dp->getDecorator('UiWidgetElement') !== false);

        $ac = new ZendX_JQuery_Form_Element_AutoComplete('acElem');
        $this->assertTrue($ac->getDecorator('UiWidgetElement') !== false);
    }

    public function testElementsEnableJQueryViewPath()
    {
        $view = new Zend_View();
        $spinner = new ZendX_JQuery_Form_Element_Spinner("spinner1");

        $this->assertFalse( false !== $view->getPluginLoader('helper')->getPaths('ZendX_JQuery_View_Helper'));
        $spinner->setView($view);
        $this->assertTrue( false !== $view->getPluginLoader('helper')->getPaths('ZendX_JQuery_View_Helper'));
    }
}

if (PHPUnit_MAIN_METHOD == 'ZendX_JQuery_Form_ElementTest::main') {
    ZendX_JQuery_Form_ElementTest::main();
}