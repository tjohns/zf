<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage Transport
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Zend_Mail_Transport_Interface
 */
require_once 'Zend/Mail/Transport/Interface.php';


/**
 * Class for sending eMails via the PHP internal mail() function
 *
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage Transport
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Mail_Transport_Sendmail implements Zend_Mail_Transport_Interface
{
    /**
     * Final headers string sent to transport
     * @var string 
     * @access public
     */
    public $header = null;

    /**
     * Recipient list
     * @var string 
     * @access public
     */
    public $recipients = null;

    /**
     * Subject
     * @var string 
     * @access public
     */
    public $subject = null;

    /**
     * Send mail using PHP native mail()
     *
     * @param Zend_Mail $mail 
     * @param string $body 
     * @param array $headers 
     * @param array $to 
     * @access public
     * @return void
     * @throws Zend_Mail_Transport_Exception if missing to addresses or on
     * mail() failure
     */
    public function sendMail(Zend_Mail $mail, $body, $headers, $to)
    {
        $this->recipients = implode(',', $to);
        $this->_prepareHeaders($headers);

        /**
         * @todo error checking
         */
        if (!mail($this->recipients, $this->subject, $body, $this->header)) {
            throw new Zend_Mail_Transport_Exception('Unable to send mail');
        }
    }

    /**
     * Format and fix headers
     *
     * mail() uses its $to and $subject arguments to set the To: and Subject:
     * headers, respectively. This method strips those out as a sanity check to
     * prevent duplicate header entries.
     * 
     * @access protected
     * @param array $headers 
     * @return void
     */
    protected function _prepareHeaders($headers)
    {
        // mail() uses its $to parameter to set the To: header, and the $subject
        // parameter to set the Subject: header. We need to strip them out.
        if (0 === strpos(PHP_OS, 'WIN')) {
            // If the current recipients list is empty, throw an error
            if (empty($this->recipients)) {
                throw new Zend_Mail_Transport_Exception('Missing To addresses');
            }
        } else {
            // All others, simply grab the recipients and unset the To: header
            if (!isset($headers['To'])) {
                throw new Zend_Mail_Transport_Exception('Missing To header');
            }

            $this->recipients = str_replace(Zend_Mime::LINEEND . "\t", '', $headers['To'][1]);
            unset($headers['To']);
        }

        // Build header string and grab subject
        $this->subject = '';
        $this->header  = '';
        foreach ($headers as $header) {
            if ('Subject' == $header[0]) {
                $this->subject = $header[1];
                continue;
            } 

            $this->header .= $header[0] . ': ' . $header[1] . Zend_Mime::LINEEND;
        }

        // Trim headers
        $this->header = trim($this->header);
    }
}

