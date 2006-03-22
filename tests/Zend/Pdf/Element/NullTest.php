<?php
/**
 * @package    Zend_Pdf
 * @subpackage UnitTests
 */


/**
 * Zend_Pdf_Element_Null
 */
require_once 'Zend/Pdf/Element/Null.php';

/**
 * PHPUnit2 Test Case
 */
require_once 'PHPUnit2/Framework/TestCase.php';


/**
 * @package    Zend_Pdf
 * @subpackage UnitTests
 */
class Zend_Pdf_Element_NullTest extends PHPUnit2_Framework_TestCase
{
    public function testPDFNull()
    {
        $nullObj = new Zend_Pdf_Element_Null();
        $this->assertTrue($nullObj instanceof Zend_Pdf_Element_Null);
    }

    public function testGetType()
    {
        $nullObj = new Zend_Pdf_Element_Null();
        $this->assertEquals($nullObj->getType(), Zend_Pdf_Element::TYPE_NULL);
    }

    public function testToString()
    {
        $nullObj = new Zend_Pdf_Element_Null();
        $this->assertEquals($nullObj->toString(), 'null');
    }
}
