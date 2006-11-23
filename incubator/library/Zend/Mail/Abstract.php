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
 * Zend
 */
require_once 'Zend.php';
 
/**
 * @package    Zend_Mail
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
abstract class Zend_Mail_Abstract implements Countable, ArrayAccess, SeekableIterator
{
    /**
     * class capabilities with default values 
     */
    protected $_has = array('folder'   => false,
                            'uniqueid' => false,
                            'delete'   => false,
                            'create'   => false,
                            'top'      => false);
    
    /**
     * current iteration position
     */
    protected $_iterationPos = 0;
    
    /**
     * maximum iteration position (= message count)
     */
    protected $_iterationMax = null;

    /**
     * Getter for has-properties. The standard has properties 
     * are: hasFolder, hasUniqueid, hasDelete, hasCreate, hasTop
     * 
     * The valid values for the has-properties are:
     *   - true if a feature is supported
     *   - false if a feature is not supported
     *   - null is it's not yet known or it can't be know if a feature is supported
     * 
     * @param  string $var  property name
     * @return bool         supported or not
     */
    public function __get($var) 
    {
        if(strpos($var, 'has') === 0) {
            $var = strtolower(substr($var, 3));
            return isset($this->_has[$var]) ? $this->_has[$var] : null;
        }
        
        throw Zend::exception('Zend_Mail_Exception', $var . ' not found');
    }


    /**
     * Get a full list of features supported by the specific mail lib and the server
     *
     * @return array list of features as array(featurename => true|false[|null])
     */
    public function getCapabilities() 
    {
        return $this->_has;
    }

    
    /**
     * Count messages with a flag or all messages in current box/folder
     * Flags might not be supported by all mail libs (exceptions is thrown)
     * 
     * @param  int $flags  filter by flags
     * @throws Zend_Mail_Exception
     * @return int number of messages
     */
    abstract public function countMessages($flags = null);


    /**
     * Get a list of messages with number and size
     *
     * @param  int       $id  number of message
     * @return int|array      size of given message of list with all messages as array(num => size)
     */
    abstract public function getSize($id = 0);
    

    /**
     * Get a message with headers and body
     *
     * @param  $id  int number of message
     * @return Zend_Mail_Message
     */
    abstract public function getMessage($id);

    
    /**
     * Get a message with only header and $bodyLines lines of body
     *
     * @param  int $id            number of message
     * @param  int $bodyLines     also retrieve this number of body lines
     * @return Zend_Mail_Message 
     */
    abstract public function getHeader($id, $bodyLines = 0);

    
    /**
     * Create instance with parameters
     *
     * @param  array $params  mail reader specific parameters
     * @throws Zend_Mail_Exception
     */
    abstract public function __construct($params);
    

    /**
     * Destructor calls close() and therefore closes the resource.
     */
    public function __destruct() 
    {
        $this->close();
    }
        

    /**
     * Close resource for mail lib. If you need to control, when the resource
     * is closed. Otherwise the destructor would call this.
     */
    abstract public function close();

    
    /**
     * Keep the resource alive.
     */
    abstract public function noop();

    /**
     * delete a message from current box/folder
     */
    abstract public function removeMessage($id);

    // interface implementations follows

    /**
     * Countable::count()
     * @internal
     * @return   int
     */
     public function count()
     {
        return $this->countMessages();
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
            if ($this->getHeader($id)) {
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
        return $this->getMessage($id);
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
        throw Zend::exception('Zend_Mail_Exception', 'cannot write mail messages via array access');
     }
     
     
     /**
      * ArrayAccess::offsetUnset()
      * @internal
      * @param    int   $id
      * @return   boolean success 
      */
     public function offsetUnset($id) 
     {
        return $this->removeMessage($id);
     }
     
     
     /**
      * Iterator::rewind()
      *
      * Rewind always gets the new count from the storage. Thus if you use
      * the interfaces and your scripts take long you should use reset()
      * from time to time.
      *
      * @internal
      * @return   void
      */
     public function rewind() 
     {
        $this->_iterationMax = $this->countMessages();
        $this->_iterationPos = 1;
     }
     
     
     /**
      * Iterator::current()
      * @internal
      * @return   Zend_Mail_Message current message
      */
     public function current() 
     {
        return $this->getMessage($this->_iterationPos);
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
        if($this->_iterationMax === null) {
          $this->_iterationMax = $this->countMessages();        
        }
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
        if($this->_iterationMax === null) {
          $this->_iterationMax = $this->countMessages();        
        }

        if ($pos > $this->_iterationMax) {
            throw new OutOfBoundsException('this position does not exist');
        }
        $this->_iterationPos = $pos;
     }

}