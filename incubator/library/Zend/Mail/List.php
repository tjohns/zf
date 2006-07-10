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
 * @package    Zend_Mail
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class Zend_Mail_List implements Countable, ArrayAccess, SeekableIterator
{
    /**
     * current iteration position
     */
    protected $_iterationPos = 0;
    
    /**
     * maximum iteration position (= message count)
     */
    protected $_iterationMax = 0;
    
    /**
     * mail reading class controlled by Zend_Mail_List
     */
    protected $_mailReader;

    
    /**
     * Public constructor
     *
     * @param Zend_Mail_Abstract $mailReader
     */
    public function __construct(Zend_Mail_Abstract $mailReader)
    {
        $this->_mailReader = $mailReader;
        $this->rewind();
    } 


    /**
     * Countable::count()
     * @internal
     * @return   int
     */
     public function count()
     {
        return $this->_mailReader->countMessages();
     }
     
     
     /**
      * ArrayAccess::offsetExists()
      * @internal
      * @param    int     $id
      * @return   boolean
      */
     public function offsetExists($id) 
     {
        try {
            if ($this->_mailReader->getHeader($id)) {
                return true;
            }
        } catch(Zend_Mail_Exception $e) {}
        
        return false;
     }


     /**
      * ArrayAccess::offsetGet()
      * @internal
      * @param    int $id
      * @return   Zend_Mail_Message message object 
      */
     public function offsetGet($id) 
     {
        return $this->_mailReader->getMessage($id);
     }
     
     
     /**
      * ArrayAccess::offsetSet()
      * @internal
      * @param    id     $id
      * @param    mixed  $value
      * @throws   Zend_Mail_Exception
      * @return   void
      */
     public function offsetSet($id, $value) 
     {
        throw new Zend_Mail_Exception('cannot write mail messages via array access');
     }
     
     
     /**
      * ArrayAccess::offsetUnset()
      * @internal
      * @param    int   $id
      * @return   boolean success 
      */
     public function offsetUnset($id) 
     {
        return $this->_mailReader->removeMessage($id);
     }
     
     
     /**
      * Iterator::rewind()
      * @internal
      * @return   void
      */
     public function rewind() 
     {
        $this->_iterationMax = $this->_mailReader->countMessages();
        $this->_iterationPos = 1;
     }
     
     
     /**
      * Iterator::current()
      * @internal
      * @return   Zend_Mail_Message current message
      */
     public function current() 
     {
        return $this->_mailReader->getMessage($this->_iterationPos);
     }
     
     
     /**
      * Iterator::key()
      * @internal
      * @return   int id of current position
      */
     public function key() 
     {
        return $this->_iterationPos;
     }
     
     
     /**
      * Iterator::next()
      * @internal
      * @return   void
      */
     public function next() 
     {
        ++$this->_iterationPos;
     }
     
     
     /**
      * Iterator::valid()
      * @internal
      * @return   boolean
      */
     public function valid() 
     {
        return $this->_iterationPos && $this->_iterationPos <= $this->_iterationMax;
     }
     
     
     /**
      * SeekableIterator::seek()
      * @internal
      * @param  int $pos
      * @return void
      */
     public function seek($pos)
     {
        if ($pos > $this->_iterationMax) {
            // OutOfBoundsException would be the right one here, but it seems like it was added after PHP 5.0.4
            throw new Zend_Mail_Exception('Out of Bounds');
        }
        $this->_iterationPos = $pos;
     }
     
     /**
      * fallback for mail reader class methods 
      */
     public function __call($method, $params)
     {
        return call_user_func_array(array($this->_mailReader, $method), $params);
     }
}