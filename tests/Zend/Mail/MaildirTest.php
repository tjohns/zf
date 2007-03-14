<?php

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 */


/**
 * Zend_Mail_Storage_Maildir
 */
require_once 'Zend/Mail/Storage/Maildir.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 */
class Zend_Mail_MaildirTest extends PHPUnit_Framework_TestCase
{
    protected $_maildir;

    public function setUp()
    {
        $this->_maildir = dirname(__FILE__) . '/_files/test.maildir/';
        if (!is_dir($this->_maildir . '/cur/')) {
            $this->markTestSkipped('You have to unpack maildir.tar in Zend/Mail/_files/test.maildir/ '
                                 . 'directory before enabling the maildir tests');
        }
    }

    public function testLoadOk()
    {
        try {
            $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));
        } catch (Exception $e) {
            $this->fail('exception raised while loading maildir');
        }
    }

    public function testLoadFailure()
    {
        try {
            $mail = new Zend_Mail_Storage_Maildir(array('dirname' => '/This/Dir/Does/Not/Exist'));
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception raised while loading unknown dir');
    }

    public function testLoadInvalid()
    {
        try {
            $mail = new Zend_Mail_Storage_Maildir(array('dirname' => dirname(__FILE__)));
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception while loading invalid dir');
    }

    public function testClose()
    {
        $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));

        try {
            $mail->close();
        } catch (Exception $e) {
            $this->fail('exception raised while closing maildir');
        }
    }

    public function testHasTop()
    {
        $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));

        $this->assertTrue($mail->hasTop);
    }

    public function testHasCreate()
    {
        $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));

        $this->assertFalse($mail->hasCreate);
    }

    public function testNoop()
    {
        $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));

        try {
            $mail->noop();
        } catch (Exception $e) {
            $this->fail('exception raised while doing nothing (noop)');
        }
    }

    public function testCount()
    {
        $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));

        $count = $mail->countMessages();
        $this->assertEquals(5, $count);
    }

    public function testSize()
    {
        $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));
        $shouldSizes = array(1 => 397, 89, 694, 452, 497);


        $sizes = $mail->getSize();
        $this->assertEquals($shouldSizes, $sizes);
    }

    public function testSingleSize()
    {
        $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));

        $size = $mail->getSize(2);
        $this->assertEquals(89, $size);
    }

    public function testFetchHeader()
    {
        $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));

        $subject = $mail->getMessage(1)->subject;
        $this->assertEquals('Simple Message', $subject);
    }

/*
    public function testFetchTopBody()
    {
        $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));

        $content = $mail->getHeader(3, 1)->getContent();
        $this->assertEquals('Fair river! in thy bright, clear flow', trim($content));
    }
*/
    public function testFetchMessageHeader()
    {
        $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));

        $subject = $mail->getMessage(1)->subject;
        $this->assertEquals('Simple Message', $subject);
    }

    public function testFetchMessageBody()
    {
        $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));

        $content = $mail->getMessage(3)->getContent();
        list($content, ) = explode("\n", $content, 2);
        $this->assertEquals('Fair river! in thy bright, clear flow', trim($content));
    }

    public function testFetchWrongSize()
    {
        $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));

        try {
            $mail->getSize(0);
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception raised while getting size for message 0');
    }

    public function testFetchWrongMessageBody()
    {
        $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));

        try {
            $mail->getMessage(0);
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception raised while fetching message 0');
    }

    public function testFailedRemove()
    {
        $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));

        try {
            $mail->removeMessage(1);
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception raised while deleting message (maildir is read-only)');
    }

    public function testHasFlag()
    {
        $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));

        $this->assertFalse($mail->getMessage(5)->hasFlag(Zend_Mail_Storage::FLAG_SEEN));
        $this->assertTrue($mail->getMessage(5)->hasFlag(Zend_Mail_Storage::FLAG_RECENT));
        $this->assertTrue($mail->getMessage(2)->hasFlag(Zend_Mail_Storage::FLAG_FLAGGED));
        $this->assertFalse($mail->getMessage(2)->hasFlag(Zend_Mail_Storage::FLAG_ANSWERED));
    }

    public function testGetFlags()
    {
        $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));

        $flags = $mail->getMessage(1)->getFlags();
        $this->assertTrue(isset($flags[Zend_Mail_Storage::FLAG_SEEN]));
        $this->assertTrue(in_array(Zend_Mail_Storage::FLAG_SEEN, $flags));
    }

    public function testUniqueId()
    {
        $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));

        $this->assertTrue($mail->hasUniqueId);
        $this->assertEquals(1, $mail->getNumberByUniqueId($mail->getUniqueId(1)));

        $ids = $mail->getUniqueId();
        $should_ids = array(1 => '1000000000.P1.example.org', '1000000001.P1.example.org', '1000000002.P1.example.org',
                            '1000000003.P1.example.org', '1000000004.P1.example.org');
        foreach ($ids as $num => $id) {
            $this->assertEquals($id, $should_ids[$num]);

            if ($mail->getNumberByUniqueId($id) != $num) {
                    $this->fail('reverse lookup failed');
            }
        }
    }

    public function testWrongUniqueId()
    {
        $mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->_maildir));
        try {
            $mail->getNumberByUniqueId('this_is_an_invalid_id');
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception while getting number for invalid id');
    }
}
