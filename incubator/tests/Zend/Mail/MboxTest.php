<?php

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 */


/**
 * Zend_Mail_Mbox
 */
require_once 'Zend/Mail/Mbox.php';

/**
 * PHPUnit2 test case
 */
require_once 'PHPUnit2/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 */
class Zend_Mail_MboxTest extends PHPUnit2_Framework_TestCase
{
    protected $_mboxFile;

    public function setUp()
    {
        $this->_mboxFile = dirname(__FILE__) . '/_files/test.mbox';
    }

    public function testLoadOk()
    {
        try {
            $mail = new Zend_Mail_Mbox(array('filename' => $this->_mboxFile));
        } catch (Exception $e) {
            $this->fail('exception raised while loading mbox file');
        }
    }
    
    public function testLoadFailure()
    {
        try {
            $mail = new Zend_Mail_Mbox(array('filename' => 'ThisFileDoesNotExist'));
        } catch (Exception $e) {
            return; // test ok
        }
        
        $this->fail('no exception raised while loading unknown file');
    }

    public function testLoadInvalid()
    {
        try {
            $mail = new Zend_Mail_Mbox(array('filename' => __FILE__));
        } catch (Exception $e) {
            return; // test ok
        }
        
        $this->fail('no exception while loading invalid file');
    }
    
    public function testClose()
    {
        $mail = new Zend_Mail_Mbox(array('filename' => $this->_mboxFile));

        try {
            $mail->close();
        } catch (Exception $e) {
            $this->fail('exception raised while closing mbox file');
        }
    }

    public function testHasTop()
    {
        $mail = new Zend_Mail_Mbox(array('filename' => $this->_mboxFile));

        $this->assertTrue($mail->hasTop);
    }

    public function testHasCreate()
    {
        $mail = new Zend_Mail_Mbox(array('filename' => $this->_mboxFile));

        $this->assertFalse($mail->hasCreate);
    }

    public function testNoop()
    {
        $mail = new Zend_Mail_Mbox(array('filename' => $this->_mboxFile));

        try {
            $mail->noop();
        } catch (Exception $e) {
            $this->fail('exception raised while doing nothing (noop)');
        }
    }

    public function testCount()
    {
        $mail = new Zend_Mail_Mbox(array('filename' => $this->_mboxFile));

        $count = $mail->countMessages();
        $this->assertEquals(5, $count);
    }
    
    public function testSize()
    {
        $mail = new Zend_Mail_Mbox(array('filename' => $this->_mboxFile));
        $shouldSizes = array(1 => 397, 89, 694, 452, 497);


        $sizes = $mail->getSize();
        $this->assertEquals($shouldSizes, $sizes);
    }
    
    public function testSingleSize()
    {
        $mail = new Zend_Mail_Mbox(array('filename' => $this->_mboxFile));

        $size = $mail->getSize(2);
        $this->assertEquals(89, $size);
    }

    public function testFetchHeader()
    {
        $mail = new Zend_Mail_Mbox(array('filename' => $this->_mboxFile));
        
        $subject = $mail->getHeader(1)->subject;
        $this->assertEquals('Simple Message', $subject);
    }

    public function testFetchTopBody()
    {
        $mail = new Zend_Mail_Mbox(array('filename' => $this->_mboxFile));
        
        $content = $mail->getHeader(3, 1)->getContent();
        $this->assertEquals('Fair river! in thy bright, clear flow', trim($content));
    }

    public function testFetchMessageHeader()
    {
        $mail = new Zend_Mail_Mbox(array('filename' => $this->_mboxFile));
        
        $subject = $mail->getMessage(1)->subject;
        $this->assertEquals('Simple Message', $subject);
    }

    public function testFetchMessageBody()
    {
        $mail = new Zend_Mail_Mbox(array('filename' => $this->_mboxFile));
        
        $content = $mail->getMessage(3)->getContent();
        list($content, ) = explode("\n", $content, 2);
        $this->assertEquals('Fair river! in thy bright, clear flow', trim($content));
    }
    
    public function testFailedRemove()
    {
        $mail = new Zend_Mail_Mbox(array('filename' => $this->_mboxFile));

        try {
            $mail->removeMessage(1);
        } catch (Exception $e) {
            return; // test ok
        }
        
        $this->fail('no exception raised while deleting message (mbox is read-only)');
    }
}
