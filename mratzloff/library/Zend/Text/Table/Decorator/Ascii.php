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
 * @category  Zend
 * @package   Zend_Text_Table
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 * @version   $Id: Ascii.php 12529 2008-11-10 21:05:43Z dasprid $
 */

/**
 * @see Zend_Text_Table_Border_Ascii
 */
require_once 'Zend/Text/Table/Border/Ascii.php';

/**
 * Deprecated.  Use Zend_Text_Table_Border_Ascii instead.
 *
 * @deprecated Since 1.7.1
 * @category   Zend
 * @package    Zend_Text_Table
 * @uses       Zend_Text_Table_Decorator_Interface
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Text_Table_Decorator_Ascii extends Zend_Text_Table_Border_Ascii
{
    /**
     * Deprecated.  Use Zend_Text_Table_Border_Ascii instead.
     *
     * @deprecated Since 1.7.1
     */
    public function __construct()
    {
        //trigger_error('Zend_Text_Table_Decorator_Ascii has been renamed Zend_Text_Table_Border_Ascii', E_USER_NOTICE);
    }
}