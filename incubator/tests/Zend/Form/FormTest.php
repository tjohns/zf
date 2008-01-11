<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Form_FormTest::main');
}

require_once dirname(__FILE__) . '/../../TestHelper.php';
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

// error_reporting(E_ALL);

require_once 'Zend/Form.php';

require_once 'Zend/Controller/Action/HelperBroker.php';
require_once 'Zend/Form/Element.php';
require_once 'Zend/Loader/PluginLoader.php';

class Zend_Form_FormTest extends PHPUnit_Framework_TestCase
{
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";
        $suite  = new PHPUnit_Framework_TestSuite('Zend_Form_FormTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        Zend_Controller_Action_HelperBroker::resetHelpers();
        $this->form = new Zend_Form();
    }

    public function tearDown()
    {
    }

    // Configuration

    public function testSetOptionsSetsObjectState()
    {
        $this->markTestIncomplete();
    }

    public function testSetConfigSetsObjectState()
    {
        $this->markTestIncomplete();
    }

    // Attribs:

    public function testAttribsArrayInitiallyEmpty()
    {
        $attribs = $this->form->getAttribs();
        $this->assertTrue(is_array($attribs));
        $this->assertTrue(empty($attribs));
    }

    public function testRetrievingUndefinedAttribReturnsNull()
    {
        $this->assertNull($this->form->getAttrib('foo'));
    }
    
    public function testCanAddAndRetrieveSingleAttribs()
    {
        $this->testRetrievingUndefinedAttribReturnsNull();
        $this->form->setAttrib('foo', 'bar');
        $this->assertEquals('bar', $this->form->getAttrib('foo'));
    }

    public function testCanAddAndRetrieveMultipleAttribs()
    {
        $this->form->setAttrib('foo', 'bar');
        $this->assertEquals('bar', $this->form->getAttrib('foo'));
        $this->form->addAttribs(array(
            'bar' => 'baz',
            'baz' => 'bat',
            'bat' => 'foo'
        ));
        $test = $this->form->getAttribs();
        $attribs = array(
            'foo' => 'bar',
            'bar' => 'baz',
            'baz' => 'bat',
            'bat' => 'foo'
        );
        $this->assertSame($attribs, $test);
    }

    public function testSetAttribsOverwritesExistingAttribs()
    {
        $this->testCanAddAndRetrieveMultipleAttribs();
        $array = array('bogus' => 'value', 'not' => 'real');
        $this->form->setAttribs($array);
        $this->assertSame($array, $this->form->getAttribs());
    }

    public function testCanRemoveSingleAttrib()
    {
        $this->testCanAddAndRetrieveSingleAttribs();
        $this->assertTrue($this->form->removeAttrib('foo'));
        $this->assertNull($this->form->getAttrib('foo'));
    }

    public function testRemoveAttribReturnsFalseIfAttribDoesNotExist()
    {
        $this->assertFalse($this->form->removeAttrib('foo'));
    }

    public function testCanClearAllAttribs()
    {
        $this->testCanAddAndRetrieveMultipleAttribs();
        $this->form->clearAttribs();
        $attribs = $this->form->getAttribs();
        $this->assertTrue(is_array($attribs));
        $this->assertTrue(empty($attribs));
    }

    // Plugin loaders

    public function testGetPluginLoaderRetrievesDefaultDecoratorPluginLoader()
    {
        $loader = $this->form->getPluginLoader('decorator');
        $this->assertTrue($loader instanceof Zend_Loader_PluginLoader);
        $paths = $loader->getPaths('Zend_Form_Decorator');
        $this->assertTrue(is_array($paths), var_export($loader, 1));
        $this->assertTrue(0 < count($paths));
        $this->assertContains('Form', $paths[0]);
        $this->assertContains('Decorator', $paths[0]);
    }

    public function testCanSetCustomDecoratorPluginLoader()
    {
        $loader = new Zend_Loader_PluginLoader();
        $this->form->setPluginLoader($loader, 'decorator');
        $test = $this->form->getPluginLoader('decorator');
        $this->assertSame($loader, $test);
    }

    public function testCanAddDecoratorPluginLoaderPrefixPath()
    {
        $loader = $this->form->getPluginLoader('decorator');
        $this->form->addPrefixPath('Zend_Foo', 'Zend/Foo/', 'decorator');
        $paths = $loader->getPaths('Zend_Foo');
        $this->assertTrue(is_array($paths));
        $this->assertContains('Foo', $paths[0]);
    }

    public function testAddDecoratorPluginLoaderPrefixPathUpdatesElementDecoratorLoaders()
    {
        $this->markTestIncomplete();
    }

    public function testGetPluginLoaderRetrievesDefaultElementPluginLoader()
    {
        $loader = $this->form->getPluginLoader('element');
        $this->assertTrue($loader instanceof Zend_Loader_PluginLoader);
        $paths = $loader->getPaths('Zend_Form_Element');
        $this->assertTrue(is_array($paths), var_export($loader, 1));
        $this->assertTrue(0 < count($paths));
        $this->assertContains('Form', $paths[0]);
        $this->assertContains('Element', $paths[0]);
    }

    public function testCanSetCustomDecoratorElementLoader()
    {
        $loader = new Zend_Loader_PluginLoader();
        $this->form->setPluginLoader($loader, 'element');
        $test = $this->form->getPluginLoader('element');
        $this->assertSame($loader, $test);
    }

    public function testCanAddElementPluginLoaderPrefixPath()
    {
        $loader = $this->form->getPluginLoader('element');
        $this->form->addPrefixPath('Zend_Foo', 'Zend/Foo/', 'element');
        $paths = $loader->getPaths('Zend_Foo');
        $this->assertTrue(is_array($paths));
        $this->assertContains('Foo', $paths[0]);
    }

    public function testAddAllPluginLoaderPrefixPathsSimultaneously()
    {
        $decoratorLoader = new Zend_Loader_PluginLoader();
        $elementLoader   = new Zend_Loader_PluginLoader();
        $this->form->setPluginLoader($decoratorLoader, 'decorator')
                   ->setPluginLoader($elementLoader, 'element')
                   ->addPrefixPath('Zend', 'Zend/');

        $paths = $decoratorLoader->getPaths('Zend_Decorator');
        $this->assertTrue(is_array($paths), var_export($paths, 1));
        $this->assertContains('Decorator', $paths[0]);

        $paths = $elementLoader->getPaths('Zend_Element');
        $this->assertTrue(is_array($paths), var_export($paths, 1));
        $this->assertContains('Element', $paths[0]);
    }

    public function testAddingGlobalPrefixPathUpdatesAllElementPluginLoaders()
    {
        $this->markTestIncomplete();
    }

    // Elements:

    public function testCanAddAndRetrieveSingleElements()
    {
        $element = new Zend_Form_Element('foo');
        $this->form->addElement($element);
        $this->assertSame($element, $this->form->getElement('foo'));
    }

    public function testGetElementReturnsNullForUnregisteredElement()
    {
        $this->assertNull($this->form->getElement('foo'));
    }

    public function testCanAddAndRetrieveSingleElementsByStringType()
    {
        $this->form->addElement('text', 'foo');
        $element = $this->form->getElement('foo');
        $this->assertTrue($element instanceof Zend_Form_Element);
        $this->assertTrue($element instanceof Zend_Form_Element_Text);
        $this->assertEquals('foo', $element->getName());
    }

    public function testAddElementAsStringElementThrowsExceptionWhenNoNameProvided()
    {
        try {
            $this->form->addElement('text');
            $this->fail('Should not be able to specify string element type without name');
        } catch (Zend_Form_Exception $e) {
            $this->assertContains('must have', $e->getMessage());
        }
    }

    public function testCanAddAndRetrieveMultipleElements()
    {
        $this->form->addElements(array(
            'foo' => 'text',
            array('text', 'bar'),
            array('text', 'baz', array('foo' => 'bar')),
            new Zend_Form_Element_Text('bat'),
        ));
        $elements = $this->form->getElements();
        $names = array('foo', 'bar', 'baz', 'bat');
        $this->assertEquals($names, array_keys($elements));
        $foo = $elements['foo'];
        $this->assertTrue($foo instanceof Zend_Form_Element_Text);
        $bar = $elements['bar'];
        $this->assertTrue($bar instanceof Zend_Form_Element_Text);
        $baz = $elements['baz'];
        $this->assertTrue($baz instanceof Zend_Form_Element_Text);
        $this->assertEquals('bar', $baz->foo, var_export($baz->getAttribs(), 1));
        $bat = $elements['bat'];
        $this->assertTrue($bat instanceof Zend_Form_Element_Text);
    }

    public function testSetElementsOverwritesExistingElements()
    {
        $this->testCanAddAndRetrieveMultipleElements();
        $this->form->setElements(array(
            'bogus' => 'text'
        ));
        $elements = $this->form->getElements();
        $names = array('bogus');
        $this->assertEquals($names, array_keys($elements));
    }

    public function testCanRemoveSingleElement()
    {
        $this->testCanAddAndRetrieveMultipleElements();
        $this->assertTrue($this->form->removeElement('bar'));
        $this->assertNull($this->form->getElement('bar'));
    }

    public function testRemoveElementReturnsFalseWhenElementNotRegistered()
    {
        $this->assertFalse($this->form->removeElement('bogus'));
    }

    public function testCanClearAllElements()
    {
        $this->testCanAddAndRetrieveMultipleElements();
        $this->form->clearElements();
        $elements = $this->form->getElements();
        $this->assertTrue(is_array($elements));
        $this->assertTrue(empty($elements));
    }

    public function testCanSetElementDefaultValues()
    {
        $this->testCanAddAndRetrieveMultipleElements();
        $values = array(
            'foo' => 'foovalue',
            'bar' => 'barvalue',
            'baz' => 'bazvalue',
            'bat' => 'batvalue'
        );
        $this->form->setDefaults($values);
        $elements = $this->form->getElements();
        foreach (array_keys($values) as $name) {
            $this->assertEquals($name . 'value', $elements[$name]->getValue(), var_export($elements[$name], 1));
        }
    }

    public function testCanRetrieveSingleElementValue()
    {
        $this->form->addElement('text', 'foo', array('value' => 'foovalue'));
        $this->assertEquals('foovalue', $this->form->getValue('foo'));
    }

    public function testCanRetrieveAllElementValues()
    {
        $this->testCanAddAndRetrieveMultipleElements();
        $values = array(
            'foo' => 'foovalue',
            'bar' => 'barvalue',
            'baz' => 'bazvalue',
            'bat' => 'batvalue'
        );
        $this->form->setDefaults($values);
        $test     = $this->form->getValues();
        $elements = $this->form->getElements();
        foreach (array_keys($values) as $name) {
            $this->assertEquals($values[$name], $test[$name]);
        }
    }

    public function testCanRetrieveSingleUnfilteredElementValue()
    {
        $foo = new Zend_Form_Element_Text('foo');
        $foo->addFilter('StringToUpper')
            ->setValue('foovalue');
        $this->form->addElement($foo);
        $this->assertEquals('FOOVALUE', $this->form->getValue('foo'));
        $this->assertEquals('foovalue', $this->form->getUnfilteredValue('foo'));
    }

    public function testCanRetrieveAllUnfilteredElementValues()
    {
        $foo = new Zend_Form_Element_Text('foo');
        $foo->addFilter('StringToUpper')
            ->setValue('foovalue');
        $bar = new Zend_Form_Element_Text('bar');
        $bar->addFilter('StringToUpper')
            ->setValue('barvalue');
        $this->form->addElements(array($foo, $bar));
        $values     = $this->form->getValues();
        $unfiltered = $this->form->getUnfilteredValues();
        foreach (array('foo', 'bar') as $key) {
            $value = $key . 'value';
            $this->assertEquals(strtoupper($value), $values[$key]);
            $this->assertEquals($value, $unfiltered[$key]);
        }
    }

    public function testOverloadingRetrievesElements()
    {
        $this->form->addElement('text', 'foo');
        $element = $this->form->foo;
        $this->assertTrue($element instanceof Zend_Form_Element);
    }

    // Element groups

    public function testCanAddAndRetrieveSingleGroups()
    {
        $subForm = new Zend_Form;
        $subForm->addElements(array('foo' => 'text', 'bar' => 'text'));
        $this->form->addGroup($subForm, 'page1');
        $test = $this->form->getGroup('page1');
        $this->assertSame($subForm, $test);
    }

    public function testGetGroupReturnsNullForUnregisteredGroup()
    {
        $this->assertNull($this->form->getGroup('foo'));
    }

    public function testCanAddAndRetrieveMultipleGroups()
    {
        $page1 = new Zend_Form();
        $page2 = new Zend_Form();
        $page3 = new Zend_Form();
        $this->form->addGroups(array(
            'page1' => $page1,
            array($page2, 'page2'),
            array($page3, 'page3', 3)
        ));
        $groups = $this->form->getGroups();
        $keys = array('page1', 'page2', 'page3');
        $this->assertEquals($keys, array_keys($groups));
        $this->assertSame($page1, $groups['page1']);
        $this->assertSame($page2, $groups['page2']);
        $this->assertSame($page3, $groups['page3']);
    }

    public function testSetGroupsOverwritesExistingGroups()
    {
        $this->testCanAddAndRetrieveMultipleGroups();
        $foo = new Zend_Form();
        $this->form->setGroups(array('foo' => $foo));
        $groups = $this->form->getGroups();
        $keys = array('foo');
        $this->assertEquals($keys, array_keys($groups));
        $this->assertSame($foo, $groups['foo']);
    }

    public function testCanRemoveSingleGroup()
    {
        $this->testCanAddAndRetrieveMultipleGroups();
        $this->assertTrue($this->form->removeGroup('page2'));
        $this->assertNull($this->form->getGroup('page2'));
    }

    public function testRemoveGroupReturnsFalseForNonexistantGroup()
    {
        $this->assertFalse($this->form->removeGroup('foo'));
    }

    public function testCanClearAllGroups()
    {
        $this->markTestIncomplete();
        $this->testCanAddAndRetrieveMultipleGroups();
        $this->form->clearGroups();
        $groups = $this->form->getGroups();
        $this->assertTrue(is_array($groups));
        $this->assertTrue(empty($groups));
    }

    // Display groups

    public function testCanAddAndRetrieveSingleDisplayGroups()
    {
        $this->markTestIncomplete();
    }

    public function testCanAddAndRetrieveMultipleDisplayGroups()
    {
        $this->markTestIncomplete();
    }

    public function testSetDisplayGroupsOverwritesExistingDisplayGroups()
    {
        $this->markTestIncomplete();
    }

    public function testCanRemoveSingleDisplayGroup()
    {
        $this->markTestIncomplete();
    }

    public function testCanClearAllDisplayGroups()
    {
        $this->markTestIncomplete();
    }

    // Processing

    public function testPopulateProxiesToSetDefaults()
    {
        $this->markTestIncomplete();
    }

    public function testCanValidateFullForm()
    {
        $this->markTestIncomplete();
    }

    public function testCanValidatePartialForm()
    {
        $this->markTestIncomplete();
    }

    public function testProcessAjaxReturnsJson()
    {
        $this->markTestIncomplete();
    }

    public function testProcessAjaxCanProcessPartialForm()
    {
        $this->markTestIncomplete();
    }

    public function testPersistDataStoresDataInSession()
    {
        $this->markTestIncomplete();
    }
    
    public function testCanRetrieveErrorCodesFromAllElementsAfterFailedValidation()
    {
        $this->markTestIncomplete();
    }
    
    public function testCanRetrieveErrorMessagesFromAllElementsAfterFailedValidation()
    {
        $this->markTestIncomplete();
    }

    // Rendering
    public function testGetViewRetrievesFromViewRendererByDefault()
    {
        $this->markTestIncomplete();
    }

    public function testGetViewReturnsNullWhenNoViewRegisteredWithViewRenderer()
    {
        $this->markTestIncomplete();
    }

    public function testCanSetViewWithCustomViewObject()
    {
        $this->markTestIncomplete();
    }

    public function testCanAddAndRetrieveSingleDecorators()
    {
        $this->markTestIncomplete();
    }

    public function testCanAddAndRetrieveMultipleDecorators()
    {
        $this->markTestIncomplete();
    }

    public function testSetDecoratorsOverwritesExistingDecorators()
    {
        $this->markTestIncomplete();
    }

    public function testCanRemoveSingleDecorator()
    {
        $this->markTestIncomplete();
    }

    public function testCanClearAllDecorators()
    {
        $this->markTestIncomplete();
    }

    public function testRenderReturnsMarkup()
    {
        $this->markTestIncomplete();
    }

    public function testRenderReturnsMarkupRepresentingAllElements()
    {
        $this->markTestIncomplete();
    }

    public function testToStringProxiesToRender()
    {
        $this->markTestIncomplete();
    }

    // Localization

    public function testTranslatorIsNullByDefault()
    {
        $this->markTestIncomplete();
    }

    public function testCanSetTranslator()
    {
        $this->markTestIncomplete();
    }

    // Iteration
    public function testFormObjectIsIterableAndIteratesElements()
    {
        $this->markTestIncomplete();
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Form_FormTest::main') {
    Zend_Form_FormTest::main();
}
