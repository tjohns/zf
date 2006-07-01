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
 * @package    Zend_Cache
 * @subpackage Backend
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
 
 
/**
 * Zend_Cache_Backend_Interface
 */
require_once 'Zend/Cache/Backend/Interface.php';


/**
 * @package    Zend_Cache
 * @subpackage Backend
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
     * - the complete path (filename included) of the SQLITE database
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
        if (!isset($options['cacheDBCompletePath'])) Zend_Cache::throwException('cacheDBCompletePath option has to set');
        $this->_db = @sqlite_open($options['cacheDBCompletePath']);
        if (!($this->_db)) {
            Zend_Cache::throwException("Impossible to open " . $options['cacheDBCompletePath'] . " cache DB file");
        }
    }
    
    /**
     * Destructor
     */
    public function __destruct()
    {
        @sqlite_close($this->_db);
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
            $sql = $sql . " AND (expire=0 OR expire>" . time() . ')';
        }
        $result = @sqlite_query($this->_db, $sql);
        $row = @sqlite_fetch_array($result);
        if ($row) {
            return $row['content'];
        }
        return false;
    }
    
    /**
     * Test if a cache is available or not (for the given id)
     * 
     * @param string $id cache id
     * @return mixed false (a cache is not available) or "last modified" timestamp (int) of the available cache record
     */
    public function test($id)
    {
        $sql = "SELECT lastModified FROM cache WHERE id='$id' AND (expire=0 OR expire>" . time() . ')';
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
        if (!$this->_checkStructureVersion()) {
            $this->_buildStructure();
            if (!$this->_checkStructureVersion()) {
                Zend_Cache::throwException("Impossible to build cache structure in " . $this->_options['cacheDBCompletePath']);
            }
        }    
        $data = @sqlite_escape_string($data);
        $mktime = time();
        if (is_null($this->_directives['lifeTime'])) {
            $expire = 0;
        } else {
            $expire = $mktime + $this->_directives['lifeTime'];
        }
        @sqlite_query($this->_db, "DELETE FROM cache WHERE id='$id'");
        $sql = "INSERT INTO cache (id, content, lastModified, expire) VALUES ('$id', '$data', $mktime, $expire)";
        $res = @sqlite_query($this->_db, $sql);       
        if (!$res) {
            if ($this->_directives['logging']) {
                Zend_Log::log("Zend_Cache_Backend_Sqlite::save() : impossible to store the cache id=$id", Zend_Log::LEVEL_WARNING);
	        }
            return false;
        }
        $res = true;
        foreach ($tags as $tag) {
            $res = $res && $this->_registerTag($id, $tag);
        }
        return $res;
    }
    
    /**
     * Remove a cache record
     * 
     * @param string $id cache id
     * @return boolean true if no problem
     */
    public function remove($id) 
    {
        $res = @sqlite_query($this->_db, "SELECT COUNT(*) AS nbr FROM cache WHERE id='$id'");
        $result1 = @sqlite_fetch_single($res);
        $result2 = @sqlite_query($this->_db, "DELETE FROM cache WHERE id='$id'");
        $result3 = @sqlite_query($this->_db, "DELETE FROM tag WHERE id='$id'"); 
        return ($result1 && $result2 && $result3);       
    }
    
    /**
     * Clean some cache records
     *
     * Available modes are :
     * Zend_Cache::CLEANING_MODE_ALL (default)    => remove all cache entries ($tags is not used)
     * Zend_Cache::CLEANING_MODE_OLD              => remove too old cache entries ($tags is not used) 
     * Zend_Cache::CLEANING_MODE_MATCHING_TAG     => remove cache entries matching all given tags 
     *                                               ($tags can be an array of strings or a single string) 
     * Zend_Cache::CLEANING_MODE_NOT_MATCHING_TAG => remove cache entries not {matching one of the given tags}
     *                                               ($tags can be an array of strings or a single string)      
     * 
     * @param string $mode clean mode
     * @param tags array $tags array of tags
     * @return boolean true if no problem
     */
    public function clean($mode = Zend_Cache::CLEANING_MODE_ALL, $tags = array()) 
    {
        if ($mode==Zend_Cache::CLEANING_MODE_ALL) {
            $res1 = @sqlite_query($this->_db, 'DELETE FROM cache');
            $res2 = @sqlite_query($this->_db, 'DELETE FROM tag');
            return $res1 && $res2;
        }
        if ($mode==Zend_Cache::CLEANING_MODE_OLD) {
            $mktime = time();
            $res1 = @sqlite_query($this->_db, "DELETE FROM tag WHERE id IN (SELECT id FROM cache WHERE expire>0 AND expire<=$mktime)");
            $res2 = @sqlite_query($this->_db, "DELETE FROM cache WHERE expire>0 AND expire<=$mktime");
            return $res1 && $res2;
        }
        if ($mode==Zend_Cache::CLEANING_MODE_MATCHING_TAG) {
            $first = true;
            $ids = array();
            foreach ($tags as $tag) {
                $res = @sqlite_query($this->_db, "SELECT DISTINCT(id) AS id FROM tag WHERE name='$tag'");
                if (!$res) {
                    return false;
                }
                $rows = @sqlite_fetch_all($res, SQLITE_ASSOC);  
                $ids2 = array();
                foreach ($rows as $row) {
                    $ids2[] = $row['id'];
                }
                if ($first) {
                    $ids = $ids2;
                    $first = false;
                } else {
                    $ids = array_intersect($ids, $ids2);
                }
            }
            $result = true;
            foreach ($ids as $id) {
                $result = $result && ($this->remove($id));
            }
            return $result;
        }
        if ($mode==Zend_Cache::CLEANING_MODE_NOT_MATCHING_TAG) {
            $res = @sqlite_query($this->_db, "SELECT id FROM cache");
            $rows = @sqlite_fetch_all($res, SQLITE_ASSOC);    
            $result = true;
            foreach ($rows as $row) {
                $id = $row['id'];
                $matching = false;
                foreach ($tags as $tag) {
                    $res = @sqlite_query($this->_db, "SELECT COUNT(*) AS nbr FROM tag WHERE name='$tag' AND id='$id'");
                    if (!$res) {
                        return false;
                    }
                    $nbr = (int) @sqlite_fetch_single($res);
                    if ($nbr > 0) {
                        $matching = true;
                    }
                }
                if (!$matching) {
                    $result = $result && $this->remove($id);
                }
            }     
            return $result;         
        }     
        return false;
    }
    
    /**
     * PUBLIC METHOD FOR UNIT TESTING ONLY !
     * 
     * Force a cache record to expire
     * 
     * @param string $id cache id
     */
    public function ___expire($id)
    {
        $time = time() - 1;
        @sqlite_query($this->_db, "UPDATE cache SET lastModified=$time, expire=$time WHERE id='$id'");
    }
    
    /**
     * PUBLIC METHOD FOR UNIT TESTING ONLY !
     * 
     * Unlink the database file
     */
    public function ___dropDatabaseFile()
    {
        @sqlite_close($this->_db);
        @unlink($this->_options['cacheDBCompletePath']);
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
        $res = @sqlite_query($this->_db, "DELETE FROM TAG WHERE tag='$tag' AND id='$id'");
        $res = @sqlite_query($this->_db, "INSERT INTO tag (name, id) VALUES ('$tag', '$id')");
        if (!$res) {        
	        if ($this->_directives['logging']) {
	            Zend_Log::log("Zend_Cache_Backend_Sqlite::_registerTag() : impossible to register tag=$tag on id=$id", Zend_Log::LEVEL_WARNING);
	        }
            return false;
        }
        return true;
    }
    
    /**
     * Build the database structure
     */
    private function _buildStructure()
    {
        @sqlite_query($this->_db, 'DROP INDEX tag_id_index');
        @sqlite_query($this->_db, 'DROP INDEX tag_name_index');
        @sqlite_query($this->_db, 'DROP INDEX cache_id_expire_index');
        @sqlite_query($this->_db, 'DROP TABLE version');
        @sqlite_query($this->_db, 'DROP TABLE cache');
        @sqlite_query($this->_db, 'DROP TABLE tag');
        @sqlite_query($this->_db, 'CREATE TABLE version (num INTEGER PRIMARY KEY)');
        @sqlite_query($this->_db, 'CREATE TABLE cache (id TEXT PRIMARY KEY, content BLOB, lastModified INTEGER, expire INTEGER)');
        @sqlite_query($this->_db, 'CREATE TABLE tag (name TEXT, id TEXT)');  
        @sqlite_query($this->_db, 'CREATE INDEX tag_id_index ON tag(id)');
        @sqlite_query($this->_db, 'CREATE INDEX tag_name_index ON tag(name)');
        @sqlite_query($this->_db, 'CREATE INDEX cache_id_expire_index ON cache(id, expire)');
        @sqlite_query($this->_db, 'INSERT INTO version (num) VALUES (1)');        
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
        if (!$row) {
            return false;
        }
        if (((int) $row['num']) != 1) {
            // old cache structure
            if ($this->_directives['logging']) {
                Zend_Log::log('Zend_Cache_Backend_Sqlite::_checkStructureVersion() : old cache structure version detected => the cache is going to be dropped', Zend_Log::LEVEL_WARNING);
	        }
            return false;
        }
        return true;
    }
    
}
