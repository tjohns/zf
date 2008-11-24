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
 * @version   $Id: Column.php 12637 2008-11-14 06:53:07Z ralph $
 */

/**
 * @see Zend_Text_Table_Cell
 */
require_once 'Zend/Text/Table/Cell.php';

/**
 * Deprecated.  Use Zend_Text_Table_Cell instead.
 *
 * @category  Zend
 * @package   Zend_Text_Table
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Text_Table_Column extends Zend_Text_Table_Cell
{
    /**
     * Deprecated.  Use Zend_Text_Table_Cell instead.
     *
     * @deprecated Since 1.7.1
     * @param      string  $content  The content of the column
     * @param      string  $align    The align of the content
     * @param      integer $colSpan  The colspan of the column
     * @param      string  $charset  The encoding of the content
     */
    public function __construct($content = null, $align = null, $colSpan = null, $charset = null)
    {
        //trigger_error('Zend_Text_Table_Column has been renamed Zend_Text_Table_Cell', E_USER_NOTICE);

        parent::__construct();
    }
}