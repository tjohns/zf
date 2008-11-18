<?php
/**
 * @package    Zend_Pdf
 * @subpackage UnitTests
 */


/** Zend_Pdf */
require_once 'Zend/Pdf.php';

/** Zend_Pdf_Page */
require_once 'Zend/Pdf/Page.php';

/** PHPUnit Test Case */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @package    Zend_Pdf
 * @subpackage UnitTests
 */
class Zend_Pdf_FactoryTest extends PHPUnit_Framework_TestCase
{
    public function testNewCustomPageCreator()
    {
        $pdf = new Zend_Pdf();
        $page = $pdf->newCustomSizePage(400, 400);

        $this->assertTrue($page instanceof Zend_Pdf_Page);
    }

    public function testNewPageCreator()
    {
        $pdf = new Zend_Pdf();
        $page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);

        $this->assertTrue($page instanceof Zend_Pdf_Page);
    }
}