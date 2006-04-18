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
 * Zend_Mail_Abstract
 */
require_once 'Zend/Mail/Abstract.php';

/**
 * Zend_Mail_Message
 */
require_once 'Zend/Mail/Message.php';

/**
 * Zend_Mail_Exception
 */
require_once 'Zend/Mail/Exception.php';


/**
 * @package    Zend_Mail
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class Zend_Mail_Mbox extends Zend_Mail_Abstract 
{
    /** @todo docblock */
    private $_fh;
    
    /** @todo docblock */
    private $_positions;
    
    
    /**
     * Count messages all messages in current box
     * Flags are not supported (exceptions is thrown)
     * 
     * @param  int $flags           filter by flags
     * @throws Zend_Mail_Exception
     * @return int                  number of messages
     */
    public function countMessages($flags = null) 
    {
        if ($flags) {
            throw new Zend_Mail_Exception('mbox does not support flags');
        }
        return count($this->_positions) - 1;
    }
    
    
    /**
     * Get a list of messages with number and size
     *
     * @param  int        $id  number of message
     * @return int|array      size of given message of list with all messages as array(num => size)
     */
    public function getSize($id = 0) 
    {
        if ($id) {
            return $id == 1 
                ? $this->_positions[0] 
                : $this->_positions[$id - 1] - $this->_positions[$id - 2];
        }
    
        $result = array();
        $lastPos = 0;
        foreach ($this->_positions as $num => $pos) {
            $result[$num + 1] = $pos - $lastPos;
            $lastPos = $pos;
        }
        
        return $result;
    }
    
    
    /** @todo docblock */
    private function _goto($id) 
    {
        fseek($this->_fh, $id == 0 ? 0 : $this->_positions[$id - 1]);
        fgets($this->_fh); // consume first line (mbox marker)
        
        return $this->_positions[$id];
    }
    
    
    /**
     * Get a message with headers and body
     *
     * @param  int $id            number of message
     * @return Zend_Mail_Message
     */
    public function getMessage($id) 
    {
        // @todo error handling
        $endPos = $this->_goto($id);
        
        $message = '';
        while (ftell($this->_fh) < $endPos) {
            $message .= fgets($this->_fh);
        }
        
        return new Zend_Mail_Message($message);        
    }
    
    
    /**
     * Get a message with only header and $bodyLines lines of body
     *
     * @param  int $id            number of message
     * @param  int $bodyLines     also retrieve this number of body lines
     * @return Zend_Mail_Message 
     */
    public function getHeader($id, $bodyLines = 0) 
    {
        // @todo error handling!
        $endPos = $this->_goto($id);
        
        $inHeader = true;
        $message = '';
        while (ftell($this->_fh) < $endPos && ($inHeader || $bodyLines--)) {
            $line = fgets($this->_fh);
            if ($inHeader && !trim($line)) {
                if (!$bodyLines) {
                    break;
                } else {
                    $inHeader = false;
                }
            }
            $message .= $line;
        }
        
        if (!$inHeader) {
            return new Zend_Mail_Message($message);        
        } else {
            return new Zend_Mail_Message('', $message);        
        }
    }


    /**
     * Create instance with parameters
     * Supported parameters are:
     *   - filename filename of mbox file
     *
     * @param  $params              array mail reader specific parameters
     * @throws Zend_Mail_Exception
     */
    public function __construct($params) 
    {
        if (!isset($params['filename']) /* || Zend::isReadable($params['filename']) */) {
            throw new Zend_Mail_Exception('no valid filename given in params');
        }
        
        $this->_fh = fopen($params['filename'], 'r');
        if (!$this->_fh) {
            throw new Zend_Mail_Exception('cannot open mbox file');
        }
        
        $line = fgets($this->_fh);
        if (strpos($line, 'From ') !== 0) {
            throw new Zend_Mail_Exception('file is not a valid mbox format');
            fclose($this->_fh);
        }
        
        while (($line = fgets($this->_fh)) !== false) {
            if (strpos($line, 'From ') === 0) {
                $this->_positions[] = ftell($this->_fh) - strlen($line);
            }   
        }
        
        $this->_positions[] = ftell($this->_fh);
        
        $this->_has['top'] = true;
    }
    
    
    /**
     * Close resource for mail lib. If you need to control, when the resource
     * is closed. Otherwise the destructor would call this.
     *
     * @return void
     */
    public function close() 
    {
        fclose($this->_fh);
    }
    
    
    /**
     * Waste some CPU cycles doing nothing.
     *
     * @return void
     */
    public function noop() 
    {
        return true;
    }
    
    
    /**
     * @todo docblock
     */
    public function removeMessage($id) 
    {
        throw new Zend_Mail_Exception('mbox is read-only');
    }

}

?>