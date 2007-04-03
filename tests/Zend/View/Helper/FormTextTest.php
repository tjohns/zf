<?php
require_once 'Zend/View/Helper/FormText.php';
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Zend_View_Helper_FormTextTest 
 *
 * Tests formText helper, including some common functionality of all form helpers
 * 
 * @uses PHPUnit_Framework_TestCase
 * @version $Id$
 */
class Zend_View_Helper_FormTextTest extends PHPUnit_Framework_TestCase 
{
    public function testIdSetFromName()
    {
        $helper  = new Zend_View_Helper_FormText();
        $element = $helper->formText('foo');
        $this->assertContains('name="foo"', $element);
        $this->assertContains('id="foo"', $element);
    }

    public function testSetIdFromAttribs()
    {
        $helper  = new Zend_View_Helper_FormText();
        $element = $helper->formText('foo', null, array('id' => 'bar'));
        $this->assertContains('name="foo"', $element);
        $this->assertContains('id="bar"', $element);
    }

    public function testSetValue()
    {
        $helper  = new Zend_View_Helper_FormText();
        $element = $helper->formText('foo', 'bar');
        $this->assertContains('name="foo"', $element);
        $this->assertContains('value="bar"', $element);
    }

    public function testReadOnlyAttribute()
    {
        $helper  = new Zend_View_Helper_FormText();
        $element = $helper->formText('foo', null, array('readonly' => 'readonly'));
        $this->assertContains('readonly="readonly"', $element);
    }
}
