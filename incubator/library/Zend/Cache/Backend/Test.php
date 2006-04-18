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
 * @package    Zend_Cache
 * @subpackage Backend
 * @copyright  Copyright (c) 2006 Fabien MARTY, Mislav MAROHNIC
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */

 
/**
 * Zend_Cache_Backend_Interface
 */
require_once 'Zend/Cache/Backend/Interface.php';


/**
 * @package    Zend_Cache
 * @subpackage Backend
 * @copyright  Copyright (c) 2006 Fabien MARTY, Mislav MAROHNIC
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class Zend_Cache_Backend_Test implements Zend_Cache_Backend_Interface 
{
    
    // ------------------
    // --- Properties ---
    // ------------------
       
    /**
     * Available options
     * 
     * @var array available options
     */
    private $_options = array();
  
    /**
     * Frontend or Core directives
     * 
     * @var array directives
     */
    private $_directives = array();
    
    /**
     * TODO : docs
     */
    private $_log = array();
    
    /**
     * TODO : docs
     */
    private $_index = 0;
    
    
    // ----------------------
    // --- Public methods ---
    // ----------------------
    
    /**
     * Constructor
     * 
     * @param array $options associative array of options
     */
    public function __construct($options = array())
    {      
        $this->_addLog('construct', array($options));
    }
    
    /**
     * Set the frontend directives
     * 
     * @param array $directives assoc of directives
     */
    public function setDirectives($directives)
    {
        $this->_addLog('setDirectives', array($directives));
    } 
    
    /**
     * Test if a cache is available for the given id and (if yes) return it (false else)
     * 
     * For this test backend only, if $id == 'false', then the method will return false
     * if $id == 'serialized', the method will return a serialized array
     * ('foo' else)
     * 
     * @param string $id cache id
     * @param boolean $doNotTestCacheValidity if set to true, the cache validity won't be tested
     * @return string cached datas (or false)
     */
    public function get($id, $doNotTestCacheValidity = false) 
    {
        $this->_addLog('get', array($id, $doNotTestCacheValidity));
        if ($id=='false') {
            return false;
        }
        if ($id=='serialized') {
            return serialize(array('foo'));
        }
        return 'foo';
    }
    
    /**
     * Test if a cache is available or not (for the given id)
     * 
     * For this test backend only, if $id == 'false', then the method will return false
     * (123456 else)
     * 
     * @param string $id cache id
     * @return mixed false (a cache is not available) or "last modified" timestamp (int) of the available cache record
     */
    public function test($id)
    {
        $this->_addLog('test', array($id));
        if ($id=='false') {
            return false;
        }
        return 123456;
    }
    
    /**
     * Save some string datas into a cache record
     *
     * For this test backend only, if $id == 'false', then the method will return false
     * (true else)
     *
     * @param string $data datas to cache
     * @param string $id cache id
     * @param array $tags array of strings, the cache record will be tagged by each string entry
     * @return boolean true if no problem
     */
    public function save($data, $id, $tags = array())
    {
        $this->_addLog('save', array($data, $id, $tags));
        if ($id=='false') {
            return false;
        }
        return true;
    }
    
    /**
     * Remove a cache record
     * 
     * For this test backend only, if $id == 'false', then the method will return false
     * (true else)
     * 
     * @param string $id cache id
     * @return boolean true if no problem
     */
    public function remove($id) 
    {
        $this->_addLog('remove', array($id));
        if ($id=='false') {
            return false;
        }
        return true;
    }
    
    /**
     * Clean some cache records
     *
     * For this test backend only, if $mode == 'false', then the method will return false
     * (true else)
     *
     * Available modes are :
     * 'all' (default)  => remove all cache entries ($tags is not used)
     * 'old'            => remove too old cache entries ($tags is not used) 
     * 'matchingTag'    => remove cache entries matching all given tags 
     *                     ($tags can be an array of strings or a single string) 
     * 'notMatchingTag' => remove cache entries not matching one of the given tags
     *                     ($tags can be an array of strings or a single string)    
     * 
     * @param string $mode clean mode
     * @param tags array $tags array of tags
     * @return boolean true if no problem
     */
    public function clean($mode = 'all', $tags = array()) 
    {
        $this->_addLog('clean', array($mode, $tags));
        if ($mode=='false') {
            return false;
        }
        return true;
    }  
    
    /**
     * TODO : doc
     */
    public function getLastLog()
    {
        return $this->_log[$this->_index - 1];
    }
    
    /**
     * TODO : doc
     */
    public function getLogIndex()
    {
        return $this->_index;
    }
    
    /**
     * TODO : doc
     */
    public function getAllLogs()
    {
        return $this->_log;
    }
    
         
    // -----------------------
    // --- Private methods ---
    // -----------------------
    
    /**
     * TODO : doc
     */
    private function _addLog($methodName, $args)
    {
        $this->_log[$this->_index] = array(
            'methodName' => $methodName,
            'args' => $args
        );
        $this->_index = $this->_index + 1;
    }  
    
}
