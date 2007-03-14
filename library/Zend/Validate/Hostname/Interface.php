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
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Zend_Validate_Hostname_Interface 
{
     
    /**
     * Returns UTF-8 characters allowed in DNS hostnames for the specified Top-Level-Domain
     *
     * UTF-8 characters should be written as four character hex strings \x{XXXX}
     * For example é is represented by \x{00E9}
     * 
     * To enable additional UTF-8 characters for a domain ensure the TLD exists in the property
     * Zend_Validate_Hostname::_registeredTlds 
     * The addition of the TLD to this array avoids unecessary file checking 
     * since the majority of domains do not have additional UTF-8 characters
     * 
     * Any build scripts used to generate these strings should be placed within build-tools/ValidateHostname
     *
     * @see http://www.iana.org/cctld/ Country-Code Top-Level Domains (TLDs)
     * @see http://www.columbia.edu/kermit/utf8-t1.html UTF-8 characters
     * @return string
     */
    public function getCharacters();
    
}