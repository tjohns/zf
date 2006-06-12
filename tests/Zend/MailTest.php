<?php
/**
 * @package 	Zend_Mail
 * @subpackage  UnitTests
 */


/**
 * Zend_Mail
 */
require_once 'Zend/Mail.php';

/**
 * Zend_Mail_Transport_Abstract
 */
require_once 'Zend/Mail/Transport/Abstract.php';

/**
 * Zend_Mail_Transport_Sendmail
 */
require_once 'Zend/Mail/Transport/Sendmail.php';

/**
 * Zend_Mail_Transport_Smtp
 */
require_once 'Zend/Mail/Transport/Smtp.php';

/**
 * PHPUnit2 test case
 */
require_once 'PHPUnit2/Framework/TestCase.php';


/**
 * Mock mail transport class for testing purposes
 */
class Zend_Mail_Transport_Mock extends Zend_Mail_Transport_Abstract
{
    /**
     * @var Zend_Mail
     */
    public $mail    = null;
    public $subject = null;
    public $from    = null;
    public $called  = false;

    public function _sendMail()
    {
        $this->mail    = $this->_mail;
        $this->subject = $this->_mail->getSubject();
        $this->from    = $this->_mail->getFrom();
        $this->called  = true;
    }
}


/**
 * Mock mail transport class for testing Sendmail transport
 */
class Zend_Mail_Transport_Sendmail_Mock extends Zend_Mail_Transport_Sendmail
{
    /**
     * @var Zend_Mail
     */
    public $mail    = null;
    public $from    = null;
    public $subject = null;
    public $called  = false;

    public function _sendMail()
    {
        $this->mail    = $this->_mail;
        $this->from    = $this->_mail->getFrom();
        $this->subject = $this->_mail->getSubject();
        $this->called  = true;
    }
}


/**
 * @package 	Zend_Mail
 * @subpackage  UnitTests
 */
class Zend_MailTest extends PHPUnit2_Framework_TestCase
{

    /**
     * Test case for a simple email text message with
     * multiple recipients.
     *
     */
    public function testOnlyText()
    {
        $mail = new Zend_Mail();
        $res = $mail->setBodyText('This is a test.');
        $mail->setFrom('testmail@example.com', 'test Mail User');
        $mail->setSubject('My Subject');
        $mail->addTo('recipient1@example.com');
        $mail->addTo('recipient2@example.com');
        $mail->addBcc('recipient1_bcc@example.com');
        $mail->addBcc('recipient2_bcc@example.com');
        $mail->addCc('recipient1_cc@example.com', 'Example no. 1 for cc');
        $mail->addCc('recipient2_cc@example.com', 'Example no. 2 for cc');

        $mock = new Zend_Mail_Transport_Mock();
        $mail->send($mock);

        $this->assertTrue($mock->called);
        $this->assertEquals('My Subject', $mock->subject);
        $this->assertEquals('testmail@example.com', $mock->from);
        $this->assertContains('recipient1@example.com', $mock->recipients);
        $this->assertContains('recipient2@example.com', $mock->recipients);
        $this->assertContains('recipient1_bcc@example.com', $mock->recipients);
        $this->assertContains('recipient2_bcc@example.com', $mock->recipients);
        $this->assertContains('recipient1_cc@example.com', $mock->recipients);
        $this->assertContains('recipient2_cc@example.com', $mock->recipients);
        $this->assertContains('This is a test.', $mock->body);
        $this->assertContains('Content-Transfer-Encoding: quoted-printable', $mock->header);
        $this->assertContains('Content-Type: text/plain', $mock->header);
        $this->assertContains('From: "test Mail User" <testmail@example.com>', $mock->header);
        $this->assertContains('Subject: My Subject', $mock->header);
        $this->assertContains('To: <recipient1@example.com>', $mock->header);
        $this->assertContains('Cc: "Example no. 1 for cc" <recipient1_cc@example.com>', $mock->header);
    }

    /**
     * Check if Header Fields are encoded correctly and if
     * header injection is prevented.
     */
    public function testHeaderEncoding()
    {
        $mail = new Zend_Mail();
        $mail->setBodyText('My Nice Test Text');
        // try header injection:
        $mail->addTo("testmail@example.com\nCc:foobar@example.com");
        $mail->addHeader('X-MyTest', "Test\nCc:foobar2@example.com", true);
        // try special Chars in Header Fields:
        $mail->setFrom('mymail@example.com', 'äüößÄÖÜ');
        $mail->addTo('testmail2@example.com', 'äüößÄÖÜ');
        $mail->addCc('testmail3@example.com', 'äüößÄÖÜ');
        $mail->setSubject('äüößÄÖÜ');
        $mail->addHeader('X-MyTest', 'Test-äüößÄÖÜ', true);

        $mock = new Zend_Mail_Transport_Mock();
        $mail->send($mock);

        $this->assertTrue($mock->called);
        $this->assertContains('From: =?iso-8859-1?Q?"=E4=FC=F6=DF=C4=D6=DC"?=', $mock->header);
        $this->assertNotContains("\nCc:foobar@example.com", $mock->header);
        $this->assertContains('=?iso-8859-1?Q?"=E4=FC=F6=DF=C4=D6=DC"=20?=<testmail2@example.com>', $mock->header);
        $this->assertContains('Cc: =?iso-8859-1?Q?"=E4=FC=F6=DF=C4=D6=DC"=20?=<testmail3@example.com>', $mock->header);
        $this->assertContains('Subject: =?iso-8859-1?Q?=E4=FC=F6=DF=C4=D6=DC?=', $mock->header);
        $this->assertContains('X-MyTest:', $mock->header);
        $this->assertNotContains("\nCc:foobar2@example.com", $mock->header);
        $this->assertContains('=?iso-8859-1?Q?Test-=E4=FC=F6=DF=C4=D6=DC?=', $mock->header);
    }

    /**
     * Check if Header Fields are stripped accordingly in sendmail transport;
     * also check for header injection
     */
    public function testHeaderEncoding2()
    {
        $mail = new Zend_Mail();
        $mail->setBodyText('My Nice Test Text');
        // try header injection:
        $mail->addTo("testmail@example.com\nCc:foobar@example.com");
        $mail->addHeader('X-MyTest', "Test\nCc:foobar2@example.com", true);
        // try special Chars in Header Fields:
        $mail->setFrom('mymail@example.com', 'äüößÄÖÜ');
        $mail->addTo('testmail2@example.com', 'äüößÄÖÜ');
        $mail->addCc('testmail3@example.com', 'äüößÄÖÜ');
        $mail->setSubject('äüößÄÖÜ');
        $mail->addHeader('X-MyTest', 'Test-äüößÄÖÜ', true);

        $mock = new Zend_Mail_Transport_Sendmail_Mock();
        $mail->send($mock);

        $this->assertTrue($mock->called);
        $this->assertContains('From: =?iso-8859-1?Q?"=E4=FC=F6=DF=C4=D6=DC"?=', $mock->header);
        $this->assertNotContains("\nCc:foobar@example.com", $mock->header);
        $this->assertContains('Cc: =?iso-8859-1?Q?"=E4=FC=F6=DF=C4=D6=DC"=20?=<testmail3@example.com>', $mock->header);
        $this->assertContains('X-MyTest:', $mock->header);
        $this->assertNotContains("\nCc:foobar2@example.com", $mock->header);
        $this->assertContains('=?iso-8859-1?Q?Test-=E4=FC=F6=DF=C4=D6=DC?=', $mock->header);

        $this->assertNotContains('Subject: ', $mock->header);
        $this->assertContains('=?iso-8859-1?Q?=E4=FC=F6=DF=C4=D6=DC?=', $mock->subject);
        $this->assertContains('=?iso-8859-1?Q?"=E4=FC=F6=DF=C4=D6=DC"=20?=<testmail2@example.com>', $mock->recipients);
    }

    /**
     * Check if Mails with HTML and Text Body are generated correctly.
     *
     */
    public function testMultipartAlternative()
    {
        $mail = new Zend_Mail();
        $mail->setBodyText('My Nice Test Text');
        $mail->setBodyHtml('My Nice <b>Test</b> Text');
        $mail->addTo('testmail@example.com', 'Test Recipient');
        $mail->setFrom('mymail@example.com', 'Test Sender');
        $mail->setSubject('Test: Alternate Mail with Zend_Mail');

        $mock = new Zend_Mail_Transport_Mock();
        $mail->send($mock);

        // check headers
        $this->assertTrue($mock->called);
        $this->assertContains('multipart/alternative', $mock->header);
        $boundary = $mock->boundary;
        $this->assertContains('boundary="' . $boundary . '"', $mock->header);
        $this->assertContains('MIME-Version: 1.0', $mock->header);

        // check body
        // search for first boundary
        $p1 = strpos($mock->body, "--$boundary\n");
        $this->assertNotEquals(null, $p1);

        // cut out first (Text) part
        $start1 = $p1 + 3 + strlen($boundary);
        $p2 = strpos($mock->body, "--$boundary\n", $start1);
        $this->assertNotEquals(null, $p2);

        $partBody1 = substr($mock->body, $start1, ($p2 - $start1));
        $this->assertContains('Content-Type: text/plain', $partBody1);
        $this->assertContains('My Nice Test Text', $partBody1);

        // check second (HTML) part
        // search for end boundary
        $start2 = $p2 + 3 + strlen($boundary);
        $p3 = strpos($mock->body, "--$boundary--");
        $this->assertNotEquals(null, $p3);

        $partBody2 = substr($mock->body, $start2, ($p3 - $start2));
        $this->assertContains('Content-Type: text/html', $partBody2);
        $this->assertContains('My Nice <b>Test</b> Text', $partBody2);
    }

    /**
     * check if attachment handling works
     *
     */
    public function testAttachment()
    {
        $mail = new Zend_Mail();
        $mail->setBodyText('My Nice Test Text');
        $mail->addTo('testmail@example.com', 'Test Recipient');
        $mail->setFrom('mymail@example.com', 'Test Sender');
        $mail->setSubject('Test: Attachment Test with Zend_Mail');
        $at = $mail->addAttachment('abcdefghijklmnopqrstuvexyz');
        $at->type = 'image/gif';
        $at->id = 12;
        $at->filename = 'test.gif';
        $mock = new Zend_Mail_Transport_Mock();
        $mail->send($mock);

        // now check what was generated by Zend_Mail.
        // first the mail headers:
        $this->assertContains('Content-Type: multipart/mixed', $mock->header);
        $boundary = $mock->boundary;
        $this->assertContains('boundary="' . $boundary . '"', $mock->header);
        $this->assertContains('MIME-Version: 1.0', $mock->header);

        // check body
        // search for first boundary
        $p1 = strpos($mock->body, "--$boundary\n");
        $this->assertNotEquals(null, $p1);

        // cut out first (Text) part
        $start1 = $p1 + 3 + strlen($boundary);
        $p2 = strpos($mock->body, "--$boundary\n", $start1);
        $this->assertNotEquals(null, $p2);

        $partBody1 = substr($mock->body, $start1, ($p2 - $start1));
        $this->assertContains('Content-Type: text/plain', $partBody1);
        $this->assertContains('My Nice Test Text', $partBody1);

        // check second (HTML) part
        // search for end boundary
        $start2 = $p2 + 3 + strlen($boundary);
        $p3 = strpos($mock->body, "--$boundary--");
        $this->assertNotEquals(null, $p3);

        $partBody2 = substr($mock->body, $start2, ($p3 - $start2));
        $this->assertContains('Content-Type: image/gif', $partBody2);
        $this->assertContains('Content-Transfer-Encoding: base64', $partBody2);
        $this->assertContains('Content-ID: <12>', $partBody2);
    }

    /**
     * Check if Mails with HTML and Text Body are generated correctly.
     *
     */
    public function testMultipartAlternativePlusAttachment()
    {
        $mail = new Zend_Mail();
        $mail->setBodyText('My Nice Test Text');
        $mail->setBodyHtml('My Nice <b>Test</b> Text');
        $mail->addTo('testmail@example.com', 'Test Recipient');
        $mail->setFrom('mymail@example.com', 'Test Sender');
        $mail->setSubject('Test: Alternate Mail with Zend_Mail');

        $at = $mail->addAttachment('abcdefghijklmnopqrstuvexyz');
        $at->type = 'image/gif';
        $at->id = 12;
        $at->filename = 'test.gif';

        $mock = new Zend_Mail_Transport_Mock();
        $mail->send($mock);

        // check headers
        $this->assertTrue($mock->called);
        $this->assertContains('multipart/mixed', $mock->header);
        $boundary = $mock->boundary;
        $this->assertContains('boundary="' . $boundary . '"', $mock->header);
        $this->assertContains('MIME-Version: 1.0', $mock->header);

        // check body
        // search for first boundary
        $p1 = strpos($mock->body, "--$boundary\n");
        $this->assertNotEquals(null, $p1);

        // cut out first (multipart/alternative) part
        $start1 = $p1 + 3 + strlen($boundary);
        $p2 = strpos($mock->body, "--$boundary\n", $start1);
        $this->assertNotEquals(null, $p2);

        $partBody1 = substr($mock->body, $start1, ($p2 - $start1));
        $this->assertContains('Content-Type: multipart/alternative', $partBody1);
        $this->assertContains('Content-Type: text/plain', $partBody1);
        $this->assertContains('Content-Type: text/html', $partBody1);
        $this->assertContains('My Nice Test Text', $partBody1);
        $this->assertContains('My Nice <b>Test</b> Text', $partBody1);

        // check second (image) part
        // search for end boundary
        $start2 = $p2 + 3 + strlen($boundary);
        $p3 = strpos($mock->body, "--$boundary--");
        $this->assertNotEquals(null, $p3);

        $partBody2 = substr($mock->body, $start2, ($p3 - $start2));
        $this->assertContains('Content-Type: image/gif', $partBody2);
        $this->assertContains('Content-Transfer-Encoding: base64', $partBody2);
        $this->assertContains('Content-ID: <12>', $partBody2);
    }

}
