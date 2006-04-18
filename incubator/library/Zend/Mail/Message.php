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
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
 
 
/**
 * Zend_Mime_Decode
 */
require_once 'Zend/Mime/Decode.php';

 
/**
 * @package    Zend_Mail
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class Zend_Mail_Message 
{
    /** @todo docblock */
    protected $_headers;
    
    /** @todo docblock */
    protected $_content;


    /**
     * Public constructor
     *
     * @param string $rawMessage  full message with or without headers
     * @param array  $headers     optional headers already seperated from body
     */
    public function __construct($rawMessage, $headers = null) 
    {
        if($headers) {
            if(is_array($headers)) {
                $this->_headers = $headers;
                $this->_content = $rawMessage;
            } else {
                Zend_Mime_Decode::splitMessage($headers, $this->_headers, $null);
                $this->_content = $rawMessage;
            }
        } else {
            Zend_Mime_Decode::splitMessage($rawMessage, $this->_headers, $this->_content);
        }
    }


    /**
     * Body of message
     *
     * @return string body
     */
    public function getContent() 
    {
        return $this->_content;
    }
    
    
    /**
     * Get all headers
     *
     * @return array headers
     */
    public function getHeaders() 
    {
        $result = array();
        
        foreach ($this->_headers as $name => $value) {
            $result .= $name . ': '. $value . Zend_Mime::LINEEND; 
        }
        
        return $result;
    }
    
    
    /**
     * Getter for mail headers - name is matched in lowercase
     *
     * @param  string $name         header name
     * @throws Zend_Mail_Exception
     * @return string|array         header line or array of headers if header exists more than once
     */
    public function __get($name) 
    {
        $name = strtolower($name);
        if (!isset($this->_headers[$name])) {
            throw new Zend_Mail_Exception("no Header with Name $name found");           
        }
        
        return $this->_headers[$name];
    }
}