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
 * @todo       Countable interface is PHP 5.1 only and should be removed
 */
class Zend_Mail_List implements Countable, ArrayAccess, SeekableIterator
{
    /** @todo docblock */
    protected $_iterationPos = 0;
    
    /** @todo docblock */
    protected $_iterationMax = 0;
    
    /** @todo docblock */
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
        /** @todo should catch specific exception hierarchy, not Exception */
        try {
            if ($this->_mailReader->getHeader($id)) {
                return true;
            }
        } catch(Exception $e) {}
        return false;
     }


     /**
      * ArrayAccess::offsetGet()
      * @internal
      * @param    int $id
      * @todo     document return type
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
      * @throws   Exception @todo Zend_Mail_Exception
      * @return   void
      */
     public function offsetSet($id, $value) 
     {
        /** 
         * @todo better description 
         * @todo throw Zend_Mail_Exception instead
         */
        throw new Exception('not supported');
     }
     
     
     /**
      * ArrayAccess::offsetUnset()
      * @internal
      * @param    int   $id
      * @return   mixed 
      * @todo     document return type
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
      * @return   mixed
      * @todo     document return type
      */
     public function current() 
     {
        return $this->_mailReader->getMessage($this->_iterationPos);
     }
     
     
     /**
      * Iterator::key()
      * @internal
      * @return   mixed
      * @todo     document return type
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
            /** 
             * @todo throw Zend_Mail_Exception 
            */
            // OutOfBoundsException would be the right one here, but it seems like it was added after PHP 5.0.4
            throw new Exception('Out of Bounds');
        }
        $this->_iterationPos = $pos;
     }
}