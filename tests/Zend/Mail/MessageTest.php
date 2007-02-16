<?php

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 */


/**
 * Zend_Mail_Message
 */
require_once 'Zend/Mail/Message.php';

/**
 * Zend_Mime_Decode
 */
require_once 'Zend/Mime/Decode.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 */
class Zend_Mail_MessageTest extends PHPUnit_Framework_TestCase
{
    protected $_file;

    public function setUp()
    {
        $this->_file = dirname(__FILE__) . '/_files/mail.txt';
    }

    public function testInvalidFile()
    {
        try {
            $message = new Zend_Mail_Message(array('file' => '/this/file/does/not/exists'));
        } catch (Exception $e) {
            return; // ok
        }

        $this->fail('no exception raised while loading unknown file');
    }

    public function testIsMultipart()
    {
        $message = new Zend_Mail_Message(array('file' => $this->_file));

        $this->assertTrue($message->isMultipart());
    }

    public function testGetHeader()
    {
        $message = new Zend_Mail_Message(array('file' => $this->_file));

        $this->assertEquals($message->subject, 'multipart');
    }

    public function testGetDecodedHeader()
    {
        $message = new Zend_Mail_Message(array('file' => $this->_file));

        $this->assertEquals($message->from, iconv('UTF-8', iconv_get_encoding('internal_encoding'),
                                                                   '"Peter Müller" <peter-mueller@example.com>'));
    }

    public function testGetHeaderAsArray()
    {
        $message = new Zend_Mail_Message(array('file' => $this->_file));

        $this->assertEquals($message->getHeader('subject', 'array'), array('multipart'));
    }

    public function testGetHeaderFromOpenFile()
    {
        $fh = fopen($this->_file, 'r');
        $message = new Zend_Mail_Message(array('file' => $fh));

        $this->assertEquals($message->subject, 'multipart');
    }

    public function testGetFirstPart()
    {
        $message = new Zend_Mail_Message(array('file' => $this->_file));

        $this->assertEquals(substr($message->getPart(1)->getContent(), 0, 14), 'The first part');
    }

    public function testGetFirstPartTwice()
    {
        $message = new Zend_Mail_Message(array('file' => $this->_file));

        $message->getPart(1);
        $this->assertEquals(substr($message->getPart(1)->getContent(), 0, 14), 'The first part');
    }


    public function testGetWrongPart()
    {
        $message = new Zend_Mail_Message(array('file' => $this->_file));

        try {
            $message->getPart(-1);
        } catch (Exception $e) {
            return; // ok
        }

        $this->fail('no exception raised while fetching unknown part');
    }

    public function testNoHeaderMessage()
    {
        $message = new Zend_Mail_Message(array('file' => __FILE__));

        $this->assertEquals(substr($message->getContent(), 0, 5), '<?php');

        $raw = file_get_contents(__FILE__);
        $raw = "\t" . $raw;
        $message = new Zend_Mail_Message(array('raw' => $raw));

        $this->assertEquals(substr($message->getContent(), 0, 6), "\t<?php");
    }

    public function testMultipleHeader()
    {
        $raw = file_get_contents($this->_file);
        $raw = "sUBject: test\nSubJect: test2\n" . $raw;
        $message = new Zend_Mail_Message(array('raw' => $raw));

        $this->assertEquals($message->getHeader('subject', 'string'),  "test\r\ntest2\r\nmultipart");
        $this->assertEquals($message->getHeader('subject'),  array('test', 'test2', 'multipart'));
    }

    public function testContentTypeDecode()
    {
        $message = new Zend_Mail_Message(array('file' => $this->_file));

        $this->assertEquals(Zend_Mime_Decode::splitContentType($message->ContentType),
                            array('type' => 'multipart/alternative', 'boundary' => 'crazy-multipart'));
    }

    public function testSplitEmptyMessage()
    {
        $this->assertEquals(Zend_Mime_Decode::splitMessageStruct('', 'xxx'), null);
    }

    public function testSplitInvalidMessage()
    {
        try {
            Zend_Mime_Decode::splitMessageStruct("--xxx\n", 'xxx');
        } catch (Zend_Exception $e) {
            return; // ok
        }

        $this->fail('no exception raised while decoding invalid message');
    }

    public function testInvalidMailHandler()
    {
        try {
            $message = new Zend_Mail_Message(array('handler' => 1));
        } catch (Zend_Exception $e) {
            return; // ok
        }

        $this->fail('no exception raised while using invalid mail handler');

    }

    public function testMissingId()
    {
        $mail = new Zend_Mail_Storage_Mbox(array('filename' => dirname(__FILE__) . '/_files/test.mbox'));

        try {
            $message = new Zend_Mail_Message(array('handler' => $mail));
        } catch (Zend_Exception $e) {
            return; // ok
        }

        $this->fail('no exception raised while mail handler without id');

    }

    public function testIterator()
    {
        $message = new Zend_Mail_Message(array('file' => $this->_file));
        foreach (new RecursiveIteratorIterator($message) as $num => $part) {
            if ($num == 1) {
            	// explicit call of __toString() needed for PHP < 5.2
                $this->assertEquals(substr($part->__toString(), 0, 14), 'The first part');
            }
        }
        $this->assertEquals($part->contentType, 'text/x-vertical');
    }
}
