<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to version 1.0 of the Zend Framework
 * license, that is bundled with this package in the file LICENSE, and
 * is available through the world-wide-web at the following URL:
 * http://www.zend.com/license/framework/1_0.txt. If you did not receive
 * a copy of the Zend Framework license and are unable to obtain it
 * through the world-wide-web, please send a note to license@zend.com
 * so we can mail you a copy immediately.
 *
 * @package    Zend_Mail
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */

/**
 * Zend_Mail_Exception
 */
require_once 'Zend/Mail/Exception.php';

/**
 * Zend_Mail_Transport_Sendmail
 */
require_once 'Zend/Mail/Transport/Sendmail.php';

/**
 * Zend_Mime
 */
require_once 'Zend/Mime.php';

/**
 * Zend_Mime_Message
 */
require_once 'Zend/Mime/Message.php';

/**
 * Zend_Mime_Part
 */
require_once 'Zend/Mime/Part.php';


/**
 * Class for sending an email.
 *
 * @package    Zend_Mail
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class Zend_Mail extends Zend_Mime_Message
{

    /**
     * @var Zend_Mail_Transport_Interface
     */
    static protected $_defaultTransport = null;
    protected $_headers = array();
    protected $_recipients = array();
    protected $_charset = null;
    protected $_from = null;
    protected $_subject = null;
    protected $_hasTextBody = false;
    protected $_hasHtmlBody = false;
    protected $_hasAttachments = false;
    protected $_mimeBoundary = null;


    /**
     * sets the default Zend_Mail_Transport_Interface for all following
     * uses of Zend_Mail::send();
     *
     * @param  Zend_Mail_Transport_Interface $transport
     */
    static public function setDefaultTransport(Zend_Mail_Transport_Interface $transport)
    {
        self::$_defaultTransport = $transport;
    }

    /**
     * Public constructor
     *
     * @param string $charset
     */
    public function __construct($charset='iso8859-1')
    {
        $this->_charset = $charset;
    }

    /**
     * Set an arbitrary mime boundary for this mail object.
     * If not set, Zend_Mime will generate one.
     *
     * @param String $boundary
     */
    public function setMimeBoundary($boundary)
    {
      $this->_mimeBoundary = $boundary;
    }

    /**
     * returns the boundary string used for this
     * email.
     *
     * @return string
     */
    public function getMimeBoundary()
    {
        return $this->_mimeBoundary;
    }


    /**
     * Sets the Text body for this message.
     *
     * @param String $txt
     * @param String $charset
     * @return Zend_Mime_Part
    */
    public function setBodyText($txt, $charset=null)
    {
        if ($charset === null) {
            $charset = $this->_charset;
        }

        $mp = new Zend_Mime_Part($txt);
        $mp->encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE;
        $mp->type = Zend_Mime::TYPE_TEXT;
        $mp->disposition = Zend_Mime::DISPOSITION_INLINE;
        $mp->charset = $charset;

        $this->addPart($mp);
        $this->_hasTextBody = true;
        return $mp;
    }


    /**
     * Sets the HTML Body for this eMail
     *
     * @param String $html
     * @param String $charset
     * @return Zend_Mime_Part
     */
    public function setBodyHtml($html, $charset=null)
    {
        if ($charset === null) {
            $charset = $this->_charset;
        }

        $mp = new Zend_Mime_Part($html);
        $mp->encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE;
        $mp->type = Zend_Mime::TYPE_HTML;
        $mp->disposition = Zend_Mime::DISPOSITION_INLINE;
        $mp->charset = $charset;

        $this->addPart($mp);
        $this->_hasHtmlBody = true;
        return $mp;
    }


    /**
     * Adds an attachment to this eMail
     *
     * @param String $body
     * @param String $mimeType
     * @param String $disposition
     * @param String $encoding
     * @return Zend_Mime_Part Created Part Object for advanced settings
     */
    public function addAttachment($body,
                                  $mimeType    = Zend_Mime::TYPE_OCTETSTREAM,
                                  $disposition = Zend_Mime::DISPOSITION_ATTACHMENT,
                                  $encoding    = Zend_Mime::ENCODING_BASE64)
    {

        $mp = new Zend_Mime_Part($body);
        $mp->encoding = $encoding;
        $mp->type = $mimeType;
        $mp->disposition = $disposition;

        $this->addPart($mp);
        $this->_hasAttachments = true;

        return $mp;
    }


    /**
     * Encode header fields according to RFC1522 if it contains
     * non-printable characters
     *
     * @param String $value
     * @return String
     */
    protected function _encodeHeader($value)
    {
      if (Zend_Mime::isPrintable($value)) {
          return $value;
      } else {
          $quotedValue = Zend_Mime::encodeQuotedPrintable($value);
          $quotedValue = str_replace('?', '=3F', $quotedValue);
          return '=?' . $this->_charset . '?Q?' . $quotedValue . '?=';
      }
    }


    /**
     * Adds another custom header to this eMail
     * if append is true and the header does already
     * exist, append the given string to the existing
     * header.
     *
     * @param String $headerName
     * @param String $value
     * @param Boolean $append
     */
    protected function _storeHeader($headerName, $value, $append=false)
    {
        $value = strtr($value,"\r\n\t",'???');
        if ($append) {
            // append value if a header with this name already exists
            if (array_key_exists($headerName, $this->_headers) ) {
                $this->_headers[$headerName][1] .= ',' .Zend_Mime::LINEEND. "\t" . $value;
            } else {
                $this->_headers[$headerName] = array($headerName, $value);
            }
        } else {
            $this->_headers[] = array($headerName, $value);
        }
    }


    /**
     * Add a recipient
     *
     * @param string $email
     */
    protected function _addRecipient($email)
    {
        // prevent duplicates
        $this->_recipients[$email] = 1;
    }


    /**
     * Helper function for adding a Recipient and the
     * according header
     *
     * @param String $headerName
     * @param String $name
     * @param String $email
     */
    protected function _addRecipientAndHeader($headerName, $name, $email)
    {
        $email = strtr($email,"\r\n\t",'???');
        $this->_addRecipient($email);
        if ($name != '') {
            $name = $this->_encodeHeader('"' .$name. '" ');
        }

        $this->_storeHeader($headerName, $name .'<'. $email . '>', true);
    }


    /**
     * Adds to-header and recipient
     *
     * @param String $name
     * @param String $email
     */
    public function addTo($email, $name='')
    {
        $this->_addRecipientAndHeader('To', $name, $email);
    }


    /**
     * Adds Cc-header and recipient
     *
     * @param String $name
     * @param String $email
     */
    public function addCc($email, $name='')
    {
        $this->_addRecipientAndHeader('Cc', $name, $email);
    }


    /**
     * Adds Bcc recipient
     *
     * @param String $email
     */
    public function addBcc($email)
    {
        $email = strtr($email,"\r\n\t",'???');
        $this->_addRecipient($email);
    }

    /**
     * Return list of recipient email addresses
     *
     * @return Array (of strings)
     */
    public function getRecipients()
    {
        return array_keys($this->_recipients);
    }

    /**
     * Sets From Header and sender of the eMail
     *
     * @param String $email
     * @param String $name
     */
    public function setFrom($email, $name)
    {
        if ($this->_from === null) {
            $email = strtr($email,"\r\n\t",'???');
            $this->_from = $email;
            $this->_storeHeader('From', $this->_encodeHeader('"'.$name.'"').' <'.$email.'>', true);
        } else {
            throw new Zend_Mail_Exception('From Header set twice');
        }
    }


    /**
     * Sets the subject of the eMail
     *
     * @param String $subject
     */
    public function setSubject($subject)
    {
        if ($this->_subject === null) {
            $subject = strtr($subject,"\r\n\t",'???');
            $this->_subject = $subject;
            $this->_storeHeader('Subject', $this->_encodeHeader($subject));
        } else {
            throw new Zend_Mail_Exception('Subject set twice');
        }
    }

    /**
     * returns the subject of the mail
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->_subject;
    }

    /**
     * Add a custom header to this eMail
     *
     * @param String $name
     * @param String $value
     * @param Boolean $append
     */
    public function addHeader($name, $value, $append=false)
    {
        if (in_array(strtolower($name), array('to', 'cc', 'bcc', 'from', 'subject'))) {
            throw new Zend_Mail_Exception('Cannot set standardheader here');
        }

        $value = strtr($value,"\r\n\t",'???');
        $value = $this->_encodeHeader($value);
        $this->_storeHeader($name, $value, $append);
    }


    /**
     * Return all Mail Headers as a string. If a boundary is
     * given, a multipart-header is generated with a mime-type
     * of multipart/alternative or multipart/mixed depending on
     * the MailParts in this ZMail object.
     *
     * @param String $boundary
     * @return String
     */
    protected function _getHeaders($boundary=null)
    {
        $out = '';

        foreach($this->_headers AS $header) {
            $out .= $header[0] . ': ' . $header[1] . Zend_Mime::LINEEND;
        }

        if ($boundary) {
            // Build Multipart Mail
            if ($this->_hasAttachments) {
                $type = 'multipart/mixed';
            } else if ($this->_hasTextBody && $this->_hasHtmlBody) {
                $type = 'multipart/alternative';
            } else {
                $type = 'multipart/mixed';
            }

            $out .= 'Content-Type: ' . $type . '; charset="' . $this->_charset . '";'
                  . Zend_Mime::LINEEND
                  . "\t" . 'boundary="' .$boundary. '"' . Zend_Mime::LINEEND
                  . 'MIME-Version: 1.0' . Zend_Mime::LINEEND;
        }

        return $out;
    }


    /**
     * returns the sender of the mail
     *
     * @return string
     */
    public function getFrom()
    {
        return $this->_from;
    }

    /**
     * Sends a Multipart eMail using the given Transport
     *
     * @param Zend_Mail_Transport_Interface $transport
     */
    protected function _sendMultiPart(Zend_Mail_Transport_Interface $transport)
    {
        $mime = new Zend_Mime($this->_mimeBoundary);
        $this->setMime($mime);
        $body = $this->generateMessage();
        $headers = $this->_getHeaders($mime->boundary());
        $this->_mimeBoundary = $mime->boundary();  // if no boundary was set before, set the used boundary now
        $transport->sendMail($this, $body, $headers);
    }

    /**
     * Sends a single part message using a given transport
     *
     * @param Zend_Mail_Transport_Interface $transport
     */
    protected function _sendSinglePart(Zend_Mail_Transport_Interface $transport)
    {
        $headers = $this->_getHeaders() . $this->getPartHeaders(0);
        $body = $this->generateMessage();
        $this->_mimeBoundary = null; // singlepart - no boundary used...
        $transport->sendMail($this, $body, $headers);
    }


    /**
     * Send this mail using the given transport
     *
     * @param Zend_Mail_Transport_Interface $transport
     */
    protected function _sendMail(Zend_Mail_Transport_Interface $transport)
    {
        if (count($this->_parts)>1) {
            $this->_sendMultiPart($transport);
        } else if (count($this->_parts)==1) {
            $this->_sendSinglePart($transport);
        } else {
            throw new Zend_Mail_Exception('Empty Mail cannot be sent');
        }
    }


    /**
     * Sends this email using the given transport or a previously
     * set DefaultTransport or the internal mail function if no
     * default transport had been set.
     *
     * @param Zend_Mail_Transport_Interface $transport
     * @return void
     */
    public function send($transport=null)
    {
        if ($transport === null) {
            if (! self::$_defaultTransport instanceof Zend_Mail_Transport_Interface) {
                $transport = new Zend_Mail_Transport_Sendmail();
            } else {
                $transport = self::$_defaultTransport;
            }
        }

        $this->_sendMail($transport);
    }

}
