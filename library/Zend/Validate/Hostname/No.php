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
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * @see Zend_Validate_Hostname_Interface
 */
require_once 'Zend/Validate/Hostname/Interface.php';


/**
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Validate_Hostname_No implements Zend_Validate_Hostname_Interface 
{
    
    /**
     * Returns UTF-8 characters allowed in DNS hostnames for the specified Top-Level-Domain
     *
     * @see http://www.norid.no/domeneregistrering/idn/idn_nyetegn.en.html Norway (.NO)
     * @see /build-tools/ValidateHostname/generateNo.php Build file 
     * @return string
     */
    public function getCharacters()
    {
        return  '\x{00E0}\x{00E1}\x{00E4}-\x{00EA}\x{00F1}-\x{00F4}\x{00F6}\x{00F8}\x{00FC}\x{010D}' . 
                '\x{0111}\x{014B}\x{0144}\x{0161}\x{0167}\x{017E}';
    }
    
}