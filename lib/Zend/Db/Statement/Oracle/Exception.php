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
 * @package    Zend_Db
 * @subpackage Statement
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */


/**
 * Zend_Db_Statement_Exception
 */
require_once 'Zend/Db/Statement/Exception.php';


/**
 * @package    Zend_Db
 * @subpackage Statement
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class Zend_Db_Statement_Oracle_Exception extends Zend_Db_Statement_Exception {
   protected $message = 'Unknown exception';
   protected $code = 0;

   function __construct($error = null, $code = 0) {
       if (is_array($error)) {
            if (!isset($error['offset'])) {
                $this->message = $error['code']." ".$error['message'];
            } else {
                $this->message = $error['code']." ".$error['message']." ";
                $this->message .= substr($error['sqltext'], 0, $error['offset']);
                $this->message .= "*";
                $this->message .= substr($error['sqltext'], $error['offset']);
            }
            $this->code = $error['code'];
       }
       if (!$this->code && $code) {
           $this->code = $code;
       }
   }
}

/* vim: set et fdm=syntax syn=php ft=php: */

