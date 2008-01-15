<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Form_DisplayGroupTest::main');
}

require_once dirname(__FILE__) . '/../../TestHelper.php';
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

// error_reporting(E_ALL);

require_once 'Zend/Form/DisplayGroup.php';

require_once 'Zend/Config.php';
require_once 'Zend/Controller/Action/HelperBroker.php';
require_once 'Zend/Form/Decorator/Fieldset.php';
require_once 'Zend/Form/Element.php';
require_once 'Zend/Form/Element/Text.php';
require_once 'Zend/Loader/PluginLoader.php';
require_once 'Zend/Translate/Adapter/Array.php';
require_once 'Zend/View.php';

class Zend_Form_DisplayGroupTest extends PHPUnit_Framework_TestCase
{
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";
        $suite  = new PHPUnit_Framework_TestSuite('Zend_Form_DisplayGroupTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        Zend_Controller_Action_HelperBroker::resetHelpers();
        $this->loader = new Zend_Loader_PluginLoader(
            array('Zend_Form_Decorator' => 'Zend/Form/Decorator')
        );
        $this->group = new Zend_Form_DisplayGroup(
            'test',
            $this->loader
        );
    }

    public function tearDown()
    {
    }

    public function getView()
    {
        $view = new Zend_View();
        $libPath = dirname(__FILE__) . '/../../../library';
        $view->addHelperPath($libPath . '/Zend/View/Helper');
        return $view;
    }

    // General
    public function testConstructorRequiresNameAndPluginLoader()
    {
        $this->assertEquals('test', $this->group->getName());
        $this->assertSame($this->loader, $this->group->getPluginLoader());
    }

    public function testOrderNullByDefault()
    {
        $this->assertNull($this->group->getOrder());
    }

    public function testCanSetOrder()
    {
        $this->testOrderNullByDefault();
        $this->group->setOrder(50);
        $this->assertEquals(50, $this->group->getOrder());
    }


    // Elements

    public function testCanAddElements()
    {
        $foo = new Zend_Form_Element('foo');
        $this->group->addElement($foo);
        $element = $this->group->getElement('foo');
        $this->assertSame($foo, $element);
    }

    public function testCanAddMultipleElements()
    {
        $foo = new Zend_Form_Element('foo');
        $bar = new Zend_Form_Element('bar');
        $this->group->addElements(array($foo, $bar));
        $elements = $this->group->getElements();
        $this->assertEquals(array('foo' => $foo, 'bar' => $bar), $elements);
    }

    public function testSetElementsOverWritesExistingElements()
    {
        $this->testCanAddMultipleElements();
        $baz = new Zend_Form_Element('baz');
        $this->group->setElements(array($baz));
        $elements = $this->group->getElements();
        $this->assertEquals(array('baz' => $baz), $elements);
    }

    public function testCanRemoveSingleElements()
    {
        $this->testCanAddMultipleElements();
        $this->group->removeElement('bar');
        $this->assertNull($this->group->getElement('bar'));
    }

    public function testRemoveElementReturnsFalseIfElementNotRegistered()
    {
        $this->assertFalse($this->group->removeElement('bar'));
    }

    public function testCanRemoveAllElements()
    {
        $this->testCanAddMultipleElements();
        $this->group->clearElements();
        $elements = $this->group->getElements();
        $this->assertTrue(is_array($elements));
        $this->assertTrue(empty($elements));
    }

    // Plugin loader

    public function testCanSetPluginLoader()
    {
        $loader = new Zend_Loader_PluginLoader();
        $this->group->setPluginLoader($loader);
        $this->assertSame($loader, $this->group->getPluginLoader());
    }

    // Decorators

    public function testFormDecoratorRegisteredByDefault()
    {
        $decorator = $this->group->getDecorator('form');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_Form);
        $options = $decorator->getOptions();
        $this->assertEquals('fieldset', $options['helper']);
    }

    public function testCanAddSingleDecoratorAsString()
    {
        $this->group->clearDecorators();
        $this->assertFalse($this->group->getDecorator('form'));

        $this->group->addDecorator('viewHelper');
        $decorator = $this->group->getDecorator('viewHelper');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_ViewHelper);
    }

    public function testCanRetrieveSingleDecoratorRegisteredAsStringUsingClassName()
    {
        $decorator = $this->group->getDecorator('Zend_Form_Decorator_Form');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_Form);
    }

    public function testCanAddSingleDecoratorAsDecoratorObject()
    {
        $this->group->clearDecorators();
        $this->assertFalse($this->group->getDecorator('form'));

        $decorator = new Zend_Form_Decorator_ViewHelper;
        $this->group->addDecorator($decorator);
        $test = $this->group->getDecorator('Zend_Form_Decorator_ViewHelper');
        $this->assertSame($decorator, $test);
    }

    public function testCanRetrieveSingleDecoratorRegisteredAsDecoratorObjectUsingShortName()
    {
        $this->group->clearDecorators();
        $this->assertFalse($this->group->getDecorator('form'));

        $decorator = new Zend_Form_Decorator_Fieldset;
        $this->group->addDecorator($decorator);
        $test = $this->group->getDecorator('fieldset');
        $this->assertSame($decorator, $test);
    }

    public function testCanAddMultipleDecorators()
    {
        $this->group->clearDecorators();
        $this->assertFalse($this->group->getDecorator('form'));

        $testDecorator = new Zend_Form_Decorator_HtmlTag;
        $this->group->addDecorators(array(
            'ViewHelper',
            $testDecorator
        ));

        $viewHelper = $this->group->getDecorator('viewHelper');
        $this->assertTrue($viewHelper instanceof Zend_Form_Decorator_ViewHelper);
        $decorator = $this->group->getDecorator('HtmlTag');
        $this->assertSame($testDecorator, $decorator);
    }

    public function testCanRemoveDecorator()
    {
        $this->testFormDecoratorRegisteredByDefault();
        $this->group->removeDecorator('fieldset');
        $this->assertFalse($this->group->getDecorator('fieldset'));
    }

    public function testCanClearAllDecorators()
    {
        $this->testCanAddMultipleDecorators();
        $this->group->clearDecorators();
        $this->assertFalse($this->group->getDecorator('viewHelper'));
        $this->assertFalse($this->group->getDecorator('HtmlTag'));
    }

    public function testRenderingRendersAllElementsWithinFieldsetByDefault()
    {
        $foo  = new Zend_Form_Element_Text('foo');
        $bar  = new Zend_Form_Element_Text('bar');

        $this->group->addElements(array($foo, $bar));
        $html = $this->group->render($this->getView());
        $this->assertRegexp('#^<fieldset.*?</fieldset>$#s', $html, $html);
        $this->assertContains('<input', $html);
        $this->assertContains('"foo"', $html);
        $this->assertContains('"bar"', $html);
    }

    public function testNoTranslatorByDefault()
    {
        $this->assertNull($this->group->getTranslator());
    }

    public function testTranslatorAccessorsWorks()
    {
        $translator = new Zend_Translate_Adapter_Array(array());
        $this->group->setTranslator($translator);
        $received = $this->group->getTranslator($translator);
        $this->assertSame($translator, $received);
    }

    // Iteration

    public function setupIteratorElements()
    {
        $foo = new Zend_Form_Element('foo');
        $bar = new Zend_Form_Element('bar');
        $baz = new Zend_Form_Element('baz');
        $this->group->addElements(array($foo, $bar, $baz));
    }
    
    public function testDisplayGroupIsIterableAndIteratesElements()
    {
        $this->setupIteratorElements();
        $expected = array('foo', 'bar', 'baz');
        $received = array();
        foreach ($this->group as $key => $element) {
            $received[] = $key;
            $this->assertTrue($element instanceof Zend_Form_Element);
        }
        $this->assertSame($expected, $received);
    }

    public function testDisplayGroupIteratesElementsInExpectedOrder()
    {
        $this->setupIteratorElements();
        $test = new Zend_Form_Element('checkorder', array('order' => 1));
        $this->group->addElement($test);
        $expected = array('foo', 'checkorder', 'bar', 'baz');
        $received = array();
        foreach ($this->group as $key => $element) {
            $received[] = $key;
        }
        $this->assertSame($expected, $received);
    }

    // Countable

    public function testCanCountDisplayGroup()
    {
        $this->setupIteratorElements();
        $this->assertEquals(3, count($this->group));
    }

    // Configuration

    public function getOptions()
    {
        $options = array(
            'name'   => 'foo',
            'legend' => 'Display Group',
            'order'  => 20,
            'class'  => 'foobar'
        );
        return $options;
    }

    public function testCanSetObjectStateViaSetOptions()
    {
        $this->group->setOptions($this->getOptions());
        $this->assertEquals('foo', $this->group->getName());
        $this->assertEquals('Display Group', $this->group->getLegend());
        $this->assertEquals(20, $this->group->getOrder());
        $this->assertEquals('foobar', $this->group->getAttrib('class'));
    }

    public function testSetOptionsOmitsAccessorsRequiringObjectsOrMultipleParams()
    {
        $options = $this->getOptions();
        $options['config']       = new Zend_Config($options);
        $options['options']      = $options;
        $options['pluginLoader'] = true;
        $options['view']         = true;
        $options['translator']   = true;
        $options['attrib']       = true;
        $this->group->setOptions($options);
    }

    public function testSetOptionsSetsArrayOfStringDecorators()
    {
        $options = $this->getOptions();
        $options['decorators'] = array('label', 'fieldset');
        $this->group->setOptions($options);
        $this->assertFalse($this->group->getDecorator('group'));

        $decorator = $this->group->getDecorator('label');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_Label);
        $decorator = $this->group->getDecorator('fieldset');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_Fieldset);
    }

    public function testSetOptionsSetsArrayOfArrayDecorators()
    {
        $options = $this->getOptions();
        $options['decorators'] = array(
            array('label', array('id' => 'mylabel')),
            array('fieldset', array('id' => 'fieldset')),
        );
        $this->group->setOptions($options);
        $this->assertFalse($this->group->getDecorator('group'));

        $decorator = $this->group->getDecorator('label');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_Label);
        $options = $decorator->getOptions();
        $this->assertEquals('mylabel', $options['id']);

        $decorator = $this->group->getDecorator('fieldset');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_Fieldset);
        $options = $decorator->getOptions();
        $this->assertEquals('fieldset', $options['id']);
    }

    public function testSetOptionsSetsArrayOfAssocArrayDecorators()
    {
        $options = $this->getOptions();
        $options['decorators'] = array(
            array(
                'options'   => array('id' => 'mylabel'),
                'decorator' => 'label', 
            ),
            array(
                'options'   => array('id' => 'fieldset'),
                'decorator' => 'fieldset', 
            ),
        );
        $this->group->setOptions($options);
        $this->assertFalse($this->group->getDecorator('group'));

        $decorator = $this->group->getDecorator('label');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_Label);
        $options = $decorator->getOptions();
        $this->assertEquals('mylabel', $options['id']);

        $decorator = $this->group->getDecorator('fieldset');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_Fieldset);
        $options = $decorator->getOptions();
        $this->assertEquals('fieldset', $options['id']);
    }

    public function testCanSetObjectStateViaSetConfig()
    {
        $config = new Zend_Config($this->getOptions());
        $this->group->setConfig($config);
        $this->assertEquals('foo', $this->group->getName());
        $this->assertEquals('Display Group', $this->group->getLegend());
        $this->assertEquals(20, $this->group->getOrder());
        $this->assertEquals('foobar', $this->group->getAttrib('class'));
    }

}

if (PHPUnit_MAIN_METHOD == 'Zend_Form_DisplayGroupTest::main') {
    Zend_Form_DisplayGroupTest::main();
}
