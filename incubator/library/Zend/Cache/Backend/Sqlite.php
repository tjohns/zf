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
class Zend_Cache_Backend_Sqlite implements Zend_Cache_Backend_Interface 
{
    
    // ------------------
    // --- Properties ---
    // ------------------
       
    /**
     * Available options
     * 
     * =====> (string) cacheDBCompletePath :
     * Directory where to put the cache files
     * (make sure to add a trailing slash)
     * 
     * @var array available options
     */
    private $_options = array(
    	'cacheDBCompletePath' => null
    ); 
  
    /**
     * Frontend or Core directives
     * 
     * =====> (int) lifeTime :
     * - Cache lifetime (in seconds)
     * - If null, the cache is valid forever
     * 
     * =====> (int) logging :
     * - if set to true, a logging is activated throw Zend_Log
     * 
     * @var array directives
     */
    private $_directives = array(
        'lifeTime' => 3600,
        'logging' => false
    );  
       
    /**
     * DB ressource 
     * 
     * @var mixed $_db
     */
    private $_db = null;
    
    
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
        if (!is_array($options)) Zend_Cache::throwException('Options parameter must be an array');
        while (list($name, $value) = each($options)) {
            if (!is_string($name) || !array_key_exists($name, $this->_options)) {
                Zend_Cache::throwException("Incorrect option name : $name");
            }
            $this->_options[$name] = $value;
        }
        if (!isset($options['cacheDBCompletePath'])) Zend_Cache::throwException('cacheDbCompletePath option has to set');
        $this->_db = @sqlite_open($options['cacheDBCompletePath']);
        if (!($this->_db)) {
            Zend_Cache::throwException("Impossible to open " . $options['cacheDBCompletePath'] . " cache DB file");
        }
        // TODO : maybe move this into save() method ?
        if (!$this->_checkStructureVersion()) {
            $this->_buildStructure();
            if (!$this->_checkStructureVersion()) {
                Zend_Cache::throwException("Impossible to build cache structure in " . $options['cacheDBCompletePath']);
            }
        }    
    }
        
    /**
     * Set the frontend directives
     * 
     * @param array $directives assoc of directives
     */
    public function setDirectives($directives)
    {
        if (!is_array($directives)) Zend_Cache::throwException('Directives parameter must be an array');
        while (list($name, $value) = each($directives)) {
            if (!is_string($name)) {
                Zend_Cache::throwException("Incorrect option name : $name");
            }
            if (array_key_exists($name, $this->_directives)) {
                $this->_directives[$name] = $value;
            }
        }
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
        $sql = "SELECT lastModified FROM cache WHERE id='$id' AND (expire=0 OR expire>" . mktime() . ')';
        $result = @sqlite_query($this->_db, $sql);
        $row = @sqlite_fetch_array($result);
        if ($row) {
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
        $data = sqlite_escape_string($data);
        $mktime = mktime();
        if (is_null($this->_directives['lifeTime'])) {
            $expire = 0;
        } else {
            $expire = $mktime + $this->_directives['lifeTime'];
        }
        @sqlite_query($this->_db, "DELETE FROM cache WHERE id='$id'");
        $sql = "INSERT INTO cache (id, content, lastModified, expire) VALUES ('$id', '$data', $mktime, $expire)";
        @sqlite_query($this->_db, $sql);       
        while (list(, $tag) = each($tags)) {
            $this->_registerTag($id, $tag);
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
        $result = false;
        $res = @sqlite_query($this->_db, "SELECT COUNT(*) AS nbr FROM cache WHERE id='$id'");
        if ($res) {
            $row = @sqlite_fetch_array($res);  
            if (((int) $row['nbr']) > 0) {
                $result = true; 
            }
        }
        $sql1 = "DELETE FROM cache WHERE id='$id'";
        $sql2 = "DELETE FROM tags WHERE id='$id'";
        @sqlite_query($this->_db, $sql1);
        @sqlite_query($this->_db, $sql2); 
        return $result;       
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
            @sqlite_query($this->_db, 'DELETE FROM cache');
            @sqlite_query($this->_db, 'DELETE FROM tag');
        }
        if ($mode=='old') {
            $mktime = mktime();
            @sqlite_query($this->_db, "DELETE FROM tag WHERE id IN (SELECT id FROM cache WHERE expire>0 AND expire<=$mktime)");
            @sqlite_query($this->_db, "DELETE FROM cache WHERE expire>0 AND expire<=$mktime");
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
        if ($this->_directives['logging']) {
            Zend_Log::log("registering tag '$tag' for ID '$id'", Zend_Log::LEVEL_DEBUG, 'ZF');
        }
        @sqlite_query($this->_db, "INSERT INTO tag (name, id) VALUES ('$tag', '$id')");
    }
    
    /**
     * Build the database structure
     */
    private function _buildStructure()
    {
        @sqlite_query($this->_db, 'DROP TABLE version');
        @sqlite_query($this->_db, 'DROP TABLE cache');
        @sqlite_query($this->_db, 'DROP TABLE tag');
        @sqlite_query($this->_db, 'CREATE TABLE version (num INTEGER PRIMARY KEY)');
        @sqlite_query($this->_db, 'INSERT INTO version (num) VALUES (1)');
        @sqlite_query($this->_db, 'CREATE TABLE cache (id TEXT PRIMARY KEY, content BLOB, lastModified INTEGER, expire INTEGER)');
        @sqlite_query($this->_db, 'CREATE TABLE tag (name TEXT PRIMARY KEY, id TEXT)');  
        //TODO : index on id     
    }
    
    /**
     * Check if the database structure is ok (with the good version)
     * 
     * @return boolean true if ok
     */
    private function _checkStructureVersion()
    {
        $result = @sqlite_query($this->_db, "SELECT num FROM version");
        if (!$result) return false;
        $row = @sqlite_fetch_array($result);
        if (!$row) return false;
        if (((int) $row['num']) != 1) {
            // old cache structure
            return false;
        }
        return true;
    }
    
}
