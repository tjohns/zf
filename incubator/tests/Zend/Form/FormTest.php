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
    
    public function testCanAddAndRetrieveSingleAttribs()
    {
        $this->markTestIncomplete();
    }

    public function testCanAddAndRetrieveMultipleAttribs()
    {
        $this->markTestIncomplete();
    }

    public function testSetAttribsOverwritesExistingAttribs()
    {
        $this->markTestIncomplete();
    }

    public function testCanRemoveSingleAttrib()
    {
        $this->markTestIncomplete();
    }

    public function testCanClearAllAttribs()
    {
        $this->markTestIncomplete();
    }

    // Elements:

    public function testCanAddAndRetrieveSingleElements()
    {
        $this->markTestIncomplete();
    }

    public function testCanAddAndRetrieveMultipleElements()
    {
        $this->markTestIncomplete();
    }

    public function testSetElementsOverwritesExistingElements()
    {
        $this->markTestIncomplete();
    }

    public function testCanRemoveSingleElement()
    {
        $this->markTestIncomplete();
    }

    public function testCanClearAllElements()
    {
        $this->markTestIncomplete();
    }

    public function testCanSetElementDefaultValues()
    {
        $this->markTestIncomplete();
    }

    public function testCanRetrieveSingleElementValue()
    {
        $this->markTestIncomplete();
    }

    public function testCanRetrieveAllElementValues()
    {
        $this->markTestIncomplete();
    }

    public function testCanRetrieveSingleUnfilteredElementValue()
    {
        $this->markTestIncomplete();
    }

    public function testCanRetrieveAllUnfilteredElementValues()
    {
        $this->markTestIncomplete();
    }

    public function testOverloadingRetrievesElements()
    {
        $this->markTestIncomplete();
    }

    // Element groups

    public function testCanAddAndRetrieveSingleGroups()
    {
        $this->markTestIncomplete();
    }

    public function testCanAddAndRetrieveMultipleGroups()
    {
        $this->markTestIncomplete();
    }

    public function testSetGroupsOverwritesExistingGroups()
    {
        $this->markTestIncomplete();
    }

    public function testCanRemoveSingleGroup()
    {
        $this->markTestIncomplete();
    }

    public function testCanClearAllGroups()
    {
        $this->markTestIncomplete();
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
