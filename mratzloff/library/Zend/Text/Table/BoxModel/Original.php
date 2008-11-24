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
 * @version   $Id: Interface.php 12529 2008-11-10 21:05:43Z dasprid $
 */

/**
 * @see Zend_Text_Table_BoxModel_Interface
 */
require_once 'Zend/Text/Table/BoxModel/Interface.php';

/**
 * Original box model.  Includes padding in content width calculations.  This 
 * is currently the default; however, Css will become the default as of 2.0.
 *
 * @category  Zend
 * @package   Zend_Text_Table
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Text_Table_BoxModel_Original implements Zend_Text_Table_BoxModel_Interface
{
    public function getCellWidth($contentWidth, $padding)
    {
        return $contentWidth;
    }
}