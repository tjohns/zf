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
class Zend_Mail_List implements Countable, ArrayAccess, Iterator //, IteratorAggregate
{
    protected $_iterationPos = 0;
    protected $_iterationMax = 0;
    protected $_mailReader;
    
    /**
     * public constructor
     */
    public function __construct(Zend_Mail_Abstract $mailReader)
    {
    	$this->_mailReader = $mailReader;
    	$this->rewind();
    } 

    /**
     * Countable::count()
     */
     public function count()
     {
        return $this->_mailReader->countMessages();
     }
     
     /**
      * ArrayAccess::offsetExists()
      */
     public function offsetExists($id) 
     {
        try {
            if($this->_mailReader->getHeader($id)) {
                return true;
            }
        } catch(Exception $e) {}
        return false;
     }

     /**
      * ArrayAccess::offsetGet()
      */
     public function offsetGet($id) 
     {
        return $this->_mailReader->getMessage($id);
     }
     
     /**
      * ArrayAccess::offsetSet()
      */
     public function offsetSet($id, $value) 
     {
        throw new Exception('not supported');
     }
     
     /**
      * ArrayAccess::offsetUnset()
      */
     public function offsetUnset($id) 
     {
        return $this->_mailReader->removeMessage($id);
     }
     
     /**
      * IteratorAggregate::getIterator()
      */
     public function getIterator() 
     {
        $iterator = clone $this;
        $iterator->rewind();
        return $iterator;
     }
     
     /**
      * Iterator::rewind()
      */
     public function rewind() 
     {
        $this->_iterationMax = $this->_mailReader->countMessages();
        $this->_iterationPos = 1;
     }
     
     /**
      * Iterator::current()
      */
     public function current() 
     {
        return $this->_mailReader->getSize($this->_iterationPos);
     }
     
     /**
      * Iterator::key()
      */
     public function key() 
     {
        return $this->_iterationPos;
     }
     
     /**
      * Iterator::next()
      */
     public function next() 
     {
        ++$this->_iterationPos;
     }
     
     /**
      * Iterator::valid()
      */
     public function valid() 
     {
        return $this->_iterationPos && $this->_iterationPos <= $this->_iterationMax;
     }
}

?>