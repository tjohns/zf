<?php
/**
 * @package 	Zend_Mime
 * @subpackage  UnitTests
 */


/**
 * Zend_Mime_Part
 */
require_once 'Zend/Mime/Part.php';

/**
 * PHPUnit2 test case
 */
require_once 'PHPUnit2/Framework/TestCase.php';


/**
 * @package 	Zend_Mime
 * @subpackage  UnitTests
 */
class Zend_Mime_PartTest extends PHPUnit2_Framework_TestCase
{
    /**
     * MIME part test object
     *
     * @var Zend_Mime_Part
     */
    protected $_part = null;
    protected $_testText;

    protected function setUp()
    {
        $this->_testText = 'safdsafsaölg öögdöö sdöjgösdjgöldögksdögjösdfgödsjögjsdögjödfsjgödsfjödjsög kjhdkj '
                       . 'fgaskjfdh gksjhgjkdh gjhfsdghdhgksdjhg';
        $this->part = new Zend_Mime_Part($this->_testText);
        $this->part->encoding = Zend_Mime::ENCODING_BASE64;
        $this->part->type = "text/plain";
        $this->part->fileName = 'test.txt';
        $this->part->disposition = 'attachment';
        $this->part->charset = 'iso8859-1';
        $this->part->id = '4711';
    }

    public function testHeaders()
    {
        $headers = $this->part->getHeaders();
        $this->assertFalse(false === strpos($headers, 'Content-Type: text/plain'));
        $this->assertFalse(false === strpos($headers, 'Content-Transfer-Encoding: ' . Zend_Mime::ENCODING_BASE64));
        $this->assertFalse(false === strpos($headers, 'Content-Disposition: attachment'));
        $this->assertFalse(false === strpos($headers, 'filename="test.txt"'));
        $this->assertFalse(false === strpos($headers, 'charset="iso8859-1"'));
        $this->assertFalse(false === strpos($headers, 'Content-ID: <4711>'));
    }

    public function testContentEncoding()
    {
        // Test with base64 encoding
        $content = $this->part->getContent();
        $this->assertEquals($this->_testText, base64_decode($content));
        // Test with quotedPrintable Encoding:
        $this->part->encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE;
        $content = $this->part->getContent();
        $this->assertEquals($this->_testText, quoted_printable_decode($content));
        // Test with 8Bit encoding
        $this->part->encoding = Zend_Mime::ENCODING_8BIT;
        $content = $this->part->getContent();
        $this->assertEquals($this->_testText, $content);
    }
}
