<?php
require_once 'Zend/View/Helper/FormCheckbox.php';
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Zend_View_Helper_FormCheckboxTest 
 *
 * Tests formCheckbox helper
 * 
 * @uses PHPUnit_Framework_TestCase
 * @version $Id$
 */
class Zend_View_Helper_FormCheckboxTest extends PHPUnit_Framework_TestCase 
{
    public function testIdSetFromName()
    {
        $helper  = new Zend_View_Helper_FormCheckbox();
        $element = $helper->formCheckbox('foo');
        $this->assertContains('name="foo"', $element);
        $this->assertContains('id="foo"', $element);
    }

    public function testSetIdFromAttribs()
    {
        $helper  = new Zend_View_Helper_FormCheckbox();
        $element = $helper->formCheckbox('foo', null, array('id' => 'bar'));
        $this->assertContains('name="foo"', $element);
        $this->assertContains('id="bar"', $element);
    }
}
