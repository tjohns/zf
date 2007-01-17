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
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * @see Zend_Validate_Interface
 */
require_once 'Zend/Validate/Interface.php';

/**
 * @see Zend_Validate_Hostname
 */
require_once 'Zend/Validate/Hostname.php';


/**
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Validate_EmailAddress implements Zend_Validate_Interface
{
    /**
     * Array of validation failure messages
     *
     * @var array
     */
    protected $_messages = array();
    
    /**
     * Local object for validating the hostname part of an email address
     *
     * @var Zend_Validate_Hostname
     */
    protected $_hostnameValidator = null;
    
    /**
     * Sets validator options
     *
     * @todo Not sure what $domainLocalAllowed is intended for - ask Darby!
     * @param boolean $domainLocalAllowed
     */
    public function __construct($domainLocalAllowed = false)
    {
        /**
          * @todo ZF-42 Check what hostnames are allowed via RFC 2822
          */
        // Instantiate Zend_Validate_Hostname allowing for all hostnames at present
        $this->_hostnameValidator = new Zend_Validate_Hostname(); 
    }

    /**
     * @todo for ZF 0.8
     */    
    public function getDomainLocalAllowed()
    {}

    /**
     * @todo for ZF 0.8
     */    
    public function setDomainLocalAllowed($domainLocalAllowed)
    {}

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value is a valid email address
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        $this->_messages = array();
        
        // Split email address up
        if (preg_match('/^([^@]+)@([^@]+)$/', $value, $matches)) {
            $localPart	= $matches[1];
            $hostname 	= $matches[2];
            
           /**
             * @todo ZF-42 check isHostname against RFC spec
             * @todo ZF-42 implement basic MX check on hostname via dns_get_record()
             */
            // Match hostname part
            $hostnameResult = $this->_hostnameValidator->isValid($hostname);
            if (!$hostnameResult) {
                $this->_messages[] = "'$hostname' is not a valid hostname for email address '$value'";
                
                // Get messages from hostnameValidator
                foreach ($this->_hostnameValidator->getMessages() as $message) {
                    $this->_messages[] = $message;
                }
            }
            
            // First try to match the local part on the common dot-atom format
            $localResult = false;
            
            // Dot-atom characters are:
            // ALPHA / DIGIT / and "!", "#", "$", "%", "&", "'", "*", "+", 
            // "-", "/", "=", "?", "^", "_", "`", "{", "|", "}", "~", "."
            // Dot character "." must be surrounded by other non-dot characters
            $dotAtom = '[a-zA-Z0-9\x21\x23\x24\x25\x26\x27\x2a\x2b\x2d\x2f\x3d';
            $dotAtom .= '\x3f\x5e\x5f\x60\x7b\x7c\x7d\x7e\x2e]';
            if ( (preg_match('/^' . $dotAtom . '+$/', $localPart)) && 
                 (strpos($localPart, '.') !== 0) && 
                 (strrpos($localPart, '.') !== strlen($localPart) - 1) ) {
                $localResult = true;
            }
            
            /**
             * @todo ZF-42 check Quoted-string character class
             */ 
            // If not matched, try quoted string format
            if (!$localResult) {
                // Quoted-string characters are:
                // Any US-ASCII characters except "\" or double-quote "
                
                // DQUOTE *([FWS] qcontent) [FWS] DQUOTE
                $quoted = '\x22[^\x5c\x22]+\x22';
                
                if (preg_match('/^' . $quoted . '$/', $localPart)) {
                    $localResult = true;
                }
            }
            
            /**
             * @todo ZF-42 check character class, dummy if else statement below to populate error messages
             */ 
            // If not matched, try obsolete format
            if (!$localResult) {
                if (true === 0) {
                    
                } else {
                    $this->_messages[] = "'$localPart' is not a valid local-part according to RFC 2822 for email address '$value'";
                }
            }
            
            // If both parts valid, return true
            if ($localResult && $hostnameResult) {
                return true;
            } else {
                return false;
            }
            
        } else {
            $this->_messages[] = "'$value' is not in the valid email address format local-part@hostname";
            return false;
        }
    }


    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns array of validation failure messages
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->_messages;
    }
    
}
