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
 * @package    Zend_Service
 * @subpackage Technorati
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * TODO: phpdoc
 * 
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Technorati
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Technorati_Utils
{
    /**
     * TODO: phpdoc
     */
    public static function setUriHttp($input) {
        /**
         * @see Zend_Uri
         */
        require_once 'Zend/Uri.php';
              
        if ($input instanceof Zend_Uri_Http || $input === null) {
            $uri = $input;
        }
        else {
            try {
                $uri = Zend_Uri::factory((string) $input);
            }
            catch (Exception $e) {
                throw new Zend_Service_Technorati_Exception($e);
            }
        }

        /**
         * @todo Should this method return a value or set the value?
         */
        return $uri;
    }
    
    public static function xpathQueryAndSet() {}
    
    public static function xpathQueryAndSetIf() {}
    
    public static function xpathQueryAndSetUnless() {}
}
