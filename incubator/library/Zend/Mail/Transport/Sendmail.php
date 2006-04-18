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
 * @subpackage Transport
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */

/**
 * Zend_Mail_Transport_Interface
 */
require_once 'Zend/Mail/Transport/Interface.php';


/**
 * Class for sending eMails via the PHP internal mail() function
 *
 * @package    Zend_Mail
 * @subpackage Transport
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class Zend_Mail_Transport_Sendmail implements Zend_Mail_Transport_Interface
{
    public function sendMail(Zend_Mail $mail, $body, $headers)
    {
        /**
         * @todo error checking
         */
        mail(join(',', $mail->getRecipients()), $mail->getSubject(), $body, $headers);
    }
}
