<?php

// ***************************************************
// * THIS PHP SCRIPT IS ONLY AN UNTESTED FIRST DRAFT * 
// ***************************************************
// => it's really impossible that it works now !
// => a lot of work is needed 


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
 * @copyright  Copyright (c) 2006 Fabien MARTY, Mislav MAROHNIC
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
 
/**
 * Zend_Cache_Backend_Interface
 */
require_once 'Zend/Cache/Backend/Interface.php';

class Zend_Cache_Backend_Sqlite implements Zend_Cache_Backend_Interface 
{
    
    // ------------------
    // --- Properties ---
    // ------------------
       
    /**
     * Available options
     * 
     * @var array available options
     */
    static public $availableOptions = array('lifetime', 'cacheDBCompletePath'); 
  
    /**
     * Directory where to put the cache files
     * (make sure to add a trailing slash)
     *
     * @var string $_cacheDir
     */
    private $_cacheDBCompletePath = '/tmp/zend_cache.db';
    
    /**
     * DB ressource 
     * 
     * @var mixed $_db
     */
    private $_db = null;
    
    /**
     * Cache lifetime (in seconds)
     *
     * If null, the cache is valid forever.
     *
     * @var int $_lifeTime
     */
    private $_lifeTime = 3600;
    
    // ----------------------
    // --- Public methods ---
    // ----------------------
    
    /**
     * Constructor
     * 
     * @param string $backend backend name
     * @param array $options associative array of options
     */
    public function __construct($options = array())
    {      
        while (list($name, $value) = each($options)) {
            $this->setOption($name, $value);
        }
        $this->_db = sqlite_open($this->_cacheDBCompletePath);
        if (!($this->_db)) {
            Zend_Cache::throwException("Impossible to open " . $this->_cacheDBCompletePath . " cache DB file");
        }
        if (!$this->_checkStructureVersion()) {
            $this->_buildStructure();
            if (!$this->_checkStructureVersion()) {
                Zend_Cache::throwException("Impossible to build cache structure in " . $this->_cacheDBCompletePath);
            }
        }    
    }    
    
    /**
     * Set an option
     * 
     * @param string $name name of the option
     * @param mixed $value value of the option
     */
    public function setOption($name, $value)
    {
        if ((!is_string($name)) or (!in_array($name, Zend_Cache_Backend_Sqlite::$availableOptions))) {
            Zend_Cache::throwException("Incorrect option name : $name");
        }
        $property = '_'.$name;
        $this->$property = $value;
    }  
    
    /**
     * Test if a cache is available for the given id and (if yes) return it (false else)
     * 
     * @param string $id cache id
     * @param boolean $doNotTestCacheValidity if set to true, the cache validity won't be tested
     * @return string cached datas (or false)
     */
    public function get($id, $doNotTestCacheValidity = false) 
    {
        $sql = "SELECT content FROM cache WHERE id='$id'";
        if (!$doNotTestCacheValidity) {
            $sql = $sql . " AND (expire=0 OR expire>" . mktime() . ')';
        }
        $result = sqlite_query($this->_db, $sql);
        $row = sqlite_fetch_array($result);
        return $row['content'];
    }
    
    /**
     * Test if a cache is available or not (for the given id)
     * 
     * @param string $id cache id
     * @return mixed false (a cache is not available) or "last modified" timestamp (int) of the available cache record
     */
    public function test($id)
    {
        $sql = "SELECT lastModified AS nbr FROM cache WHERE id='$id' AND (expire=0 OR expire>" . mktime() . ')';
        $result = sqlite_query($this->_db, $sql);
        $row = sqlite_fetch_array($result);
        if (isset($row['lastModified'])) {
            return ((int) $row['lastModified']);
        }
        return false;
    }
    
    /**
     * Save some string datas into a cache record
     *
     * Note : $data is always "string" (serialization is done by the 
     * core not by the backend)
     *
     * @param string $data datas to cache
     * @param string $id cache id
     * @param array $tags array of strings, the cache record will be tagged by each string entry
     * @return boolean true if no problem
     */
    public function save($data, $id, $tags = array())
    {
        $data = sqlite_espace_string($data);
        $mktime = mktime();
        if (is_null($this->_lifetime)) {
            $expire = 0;
        } else {
            $expire = $mktime + $this->_lifetime();
        }
        $sql = "INSERT INTO cache (id, content, lastModified, expire) VALUES ('$id', '$data', $mktime, $expire)";
        @sqlite_query($sql);       
        while (list(, $tag) = each($tags)) {
            $this->_registerTag($this->_id, $tag);
        }
        // TODO : return false if a problem is detected when the insert is done
        return true;
    }
    
    /**
     * Remove a cache record
     * 
     * @param string $id cache id
     * @return boolean true if no problem
     */
    public function remove($id) 
    {
        $sql1 = "DELETE FROM cache WHERE id='$id'";
        $sql2 = "DELETE FROM tags WHERE id='$id'";
        @sql_query($sql1);
        @sql_query($sql2);        
    }
    
    /**
     * Clean some cache records
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
        // Use case structure
        if ($mode=='all') {
            @sqlite_query('DELETE FROM cache');
            @sqlite_query('DELETE FROM tag');
        }
        if ($mode=='old') {
            $mktime = mktime();
            @sqlite_query("DELETE FROM tag WHERE id IN (SELECT id FROM cache WHERE expire>0 AND expire<=$mktime)");
            @sqlite_query("DELETE FROM cache WHERE expire>0 AND expire<=$mktime");
        }
        if ($mode=='matchingTag') {
            // TODO
        }
        if ($mode=='notMatchingTag') {
            // TODO
        }
    }
    
    // -----------------------
    // --- Private methods ---
    // -----------------------
    
    /**
     * Register a cache id with the given tag
     * 
     * @param string $id cache id
     * @param string $tag tag
     * @return boolean true if no problem
     */
    private function _registerTag($id, $tag) {
        Zend_Log::log("registering tag '$tag' for ID '$id'", Zend_Log::LEVEL_DEBUG, 'ZF');
        @sqlite_query("INSERT INTO tag (name, id) VALUES ('$tag', '$id')");
    }
    
    private function _buildStructure()
    {
        @sqlite_query('DROP TABLE version');
        @sqlite_query('DROP TABLE cache');
        @sqlite_query('DROP TABLE tag');
        @sqlite_query('CREATE TABLE version (num INTEGER PRIMARY KEY)');
        @sqlite_query('CREATE TABLE cache (id TEXT PRIMARY KEY, content BLOB, lastModified INTEGER, expire INTEGER)');
        @sqlite_query('CREATE TABLE tag (name TEXT PRIMARY KEY, id TEXT)');  
        //TODO : index on id     
    }
    
    private function _checkStructureVersion()
    {
        $result = sqlite_query($this->_db, "SELECT num FROM version");
        $row = sqlite_fetch_array($result);
        if (!$row) {
            return false;
        }
        if (((int) $row['num']) != 1) {
            // old cache structure
            return false;
        }
        return true;
    }
    
}
