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
 * @copyright  Copyright (c) 2006 Fabien MARTY, Mislav MAROHNIC
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
 
/**
 * Zend_Cache_Backend_Interface
 */
require_once 'Zend/Cache/Backend/Interface.php';

class Zend_Cache_Backend_File implements Zend_Cache_Backend_Interface 
{
    
    // ------------------
    // --- Properties ---
    // ------------------
       
    /**
     * Available options
     * 
     * @var array available options
     */
    static public $availableOptions = array('lifeTime', 'cacheDir', 'fileLocking', 'readControl', 'readControlType', 'hashedDirectoryLevel', 'hashedDirectoryUmask'); 
  
    /**
     * Directory where to put the cache files
     * (make sure to add a trailing slash)
     *
     * @var string $_cacheDir
     */
    private $_cacheDir = '/tmp/';
    
    /**
     * Enable / disable fileLocking
     *
     * (can avoid cache corruption under bad circumstances)
     *
     * @var boolean $_fileLocking
     */
    private $_fileLocking = true;
    
    /**
     * Enable / disable read control
     *
     * If enabled, a control key is embeded in cache file and this key is compared with the one
     * calculated after the reading.
     *
     * @var boolean $_writeControl
     */
    private $_readControl = true;
    
    /**
     * Type of read control (only if read control is enabled)
     *
     * Available values are :
     * 'md5' for a md5 hash control (best but slowest)
     * 'crc32' for a crc32 hash control (lightly less safe but faster, better choice)
     * 'strlen' for a length only test (fastest)
     *
     * @var string $_readControlType
     */
    private $_readControlType = 'crc32';
    
    /**
     * Nested directory level
     *
     * Set the hashed directory structure level. 0 means "no hashed directory 
     * structure", 1 means "one level of directory", 2 means "two levels"... 
     * This option can speed up Cache_Lite only when you have many thousands of 
     * cache file. Only specific benchs can help you to choose the perfect value 
     * for you. Maybe, 1 or 2 is a good start.
     *
     * @var int $_hashedDirectoryLevel
     */
    private $_hashedDirectoryLevel = 0;
    
    /**
     * Umask for hashed directory structure
     *
     * @var int $_hashedDirectoryUmask
     */
    private $_hashedDirectoryUmask = 0700;
          
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
    }    
    
    /**
     * Set an option
     * 
     * @param string $name name of the option
     * @param mixed $value value of the option
     */
    public function setOption($name, $value)
    {
        if ((!is_string($name)) or (!in_array($name, self::$availableOptions))) {
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
        clearstatcache();
        $file = self::_file($id);
        if (($doNotTestCacheValidity) || (is_null($this->_lifeTime))) {
            Zend_Log::log('skipping testing cache validity for "' . basename($file) . '"', Zend_Log::LEVEL_DEBUG, 'ZF');          
            if (!(@file_exists($file))) {
                // We do not test cache validity but there is no file available
                // so the cache is not hit !
                return false;
            }
        }
        if (!($this->_test($file))) {
            // The cache is not hit !
            return false;
        }
        // There is an available cache file !
        $fp = @fopen($file, "rb");
        if (!$fp) return false;
        if ($this->_fileLocking) @flock($fp, LOCK_SH);
        $length = @filesize($this->_file);
        $mqr = get_magic_quotes_runtime();
        set_magic_quotes_runtime(0);
        if ($this->_readControl) {
            $hashControl = @fread($fp, 32);
            $length = $length - 32;
        } 
        if ($length) {
            $data = @fread($fp, $length);
        } else {
            $data = '';
        }
        set_magic_quotes_runtime($mqr);
        if ($this->_fileLocking) @flock($fp, LOCK_UN);
        @fclose($fp);
		if ($this->_readControl) {
            $hashData = self::_hash($data, $this->_readControlType);
		    if ($hashData != $hashControl) {
                // Problem detected by the read control !
                Zend_Log::log('readControl: stored hash and computed hash do not match', Zend_Log::LEVEL_WARNING, 'ZF');                      // we need to invalidate the corresponding cache
		        $this->_remove($file); 
		        return false;    
            }
        }
        return $data;
    }
    
    /**
     * Test if a cache is available or not (for the given id)
     * 
     * @param string $id cache id
     * @return mixed false (a cache is not available) or "last modified" timestamp (int) of the available cache record
     */
    public function test($id)
    {
        clearstatcache();
        $file = self::_file($id);
        return $this->_test($file);
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
        clearstatcache();
        $file = self::_file($id);
        // TODO : logs !
        $firstTry = true;
        $result = false;
        while (1 == 1) {
            $fp = @fopen($this->_file, "wb");
            if ($fp) {
                // we can open the file, so the directory structure is ok
                if ($this->_fileLocking) @flock($fp, LOCK_EX);
                if ($this->_readControl) {
                    @fwrite($fp, self::_hash($data, $this->_readControlType), 32);
                }
                $len = strlen($data);
                @fwrite($fp, $data, $len);
                if ($this->_fileLocking) @flock($fp, LOCK_UN);
                @fclose($fp);
                $result = true;
                break;
            } else {
                // we can't open the file but it's maybe only the directory structure
                // which has to be built
	            if ($this->_hashedDirectoryLevel==0) break;
                if ((!$firstTry) || ($this->_hashedDirectoryLevel == 0)) {
                    // it's not a problem of directory structure
                    break;
                } 
                // In this case, maybe we just need to create the corresponding directory
	            @mkdir(self::_path(), $this->_hashedDirectoryUmask, true);
            }       
        }
        if ($result) {
            foreach ($tags as $tag) {
                $this->_registerTag($id, $tag);
            }
        }
        return $result;
    }
    
    /**
     * Remove a cache record
     * 
     * @param string $id cache id
     * @return boolean true if no problem
     */
    public function remove($id) 
    {
        return $this->_remove(self::_file($id));
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
        // We use this private method to hide the recursive stuff
        return $this->_clean($this->_cacheDir, $mode, $tags);
    }
    
    // -----------------------
    // --- Private methods ---
    // -----------------------
    
    /**
     * TODO : doc
     */  
    private function _remove($file)
    {
        if (!@unlink($file)) {
            # If we can't remove the file (because of locks or any problem), we will touch 
            # the file to invalidate it
            if (is_null($this->_lifeTime)) return false;
            return @touch($file, time() - 2*abs($this->_lifeTime)); 
        }
        return true;
    }
    
    /**
     * TODO : doc
     */
    private function _test($file)
    {
        if (@file_exists($file)) {
            $filemtime = @filemtime($file);
            if ($filemtime > $this->_refreshTime()) {
                return $filemtime;
            }
        }
        return false;
    }
    
    /**
     * Clean some cache records (private method used for recursive stuff)
     *
     * Available modes are :
     * 'all' (default)  => remove all cache entries ($tags is not used)
     * 'old'            => remove too old cache entries ($tags is not used) 
     * 'matchingTag'    => remove cache entries matching all given tags 
     *                     ($tags can be an array of strings or a single string) 
     * 'notMatchingTag' => remove cache entries not matching one of the given tags
     *                     ($tags can be an array of strings or a single string)    
     * 
     * @param string $dir directory to clean
     * @param string $mode clean mode
     * @param tags array $tags array of tags
     * @return boolean true if no problem
     */
    public function _clean($dir, $mode = 'all', $tags = array()) 
    {
        if (!($dh = opendir($dir))) {
            return false;
        }
        $result = true;
        while ($file = readdir($dh)) {
            if (($file != '.') && ($file != '..')) {
                if (substr($file, 0, 6)=='cache_') {
                    $file2 = $dir . $file;
                    if (is_file($file2)) {
                        switch ($mode) {
                            case 'all':
                                $result = ($result) && ($this->_remove($file2));
                                break;
                            case 'old':
                                // files older than lifeTime get deleted from cache
                                if (!is_null($this->_lifeTime)) {
                                    if ((mktime() - @filemtime($file2)) > $this->_lifeTime) {
                                        $result = ($result) && ($this->_remove($file2));
                                    }
                                }
                                break;
                            case 'matchingTag':
                                $matching = true;
                                $id = self::_fileNameToId($file);
                                if (strlen($id) > 0) {
                                    foreach ($tags as $tag) {
                                        if (!($this->_testTag($id, $tag))) {
                                            $matching = false;
                                            break;
                                        }
                                    }
                                    if ($matching) {
                                        $result = ($result) && ($this->_remove($file2));
                                    }
                                }
                                break;
                            case 'notMatchingTag':
                                $matching = false;
                                $id = self::_fileNameToId($file);
                                if (strlen($id) > 0) {
                                    foreach ($tags as $tag) {
                                        if (!($this->_testTag($id, $tag))) {
                                            $matching = true;
                                            break;
                                        }
                                    }
                                    if ($matching) {
                                        $result = ($result) && ($this->_remove($file2));
                                    }
                                }                               
                                break;
                            default:
                                break;
                        }
                    }
                    if ((is_dir($file2)) and ($this->_hashedDirectoryLevel>0)) {
                        // Recursive call
                        $result = ($result) && ($this->_clean($file2 . '/', $mode, $tags));
                    }
                }
            }
        }
        return $result;  
    }
    
    /**
     * Register a cache id with the given tag
     * 
     * @param string $id cache id
     * @param string $tag tag
     * @return boolean true if no problem
     */
    private function _registerTag($id, $tag) 
    {
        Zend_Log::log("registering tag '$tag' for ID '$id'", Zend_Log::LEVEL_DEBUG, 'ZF');
        return $this->save('1', self::tagCacheId($id, $tag));
    }
    
    /**
     * TODO : doc
     */
    private function _testTag($id, $tag) 
    {
        if ($this->test(self::tagCacheId($id, $tag))) {
           return true;
        }
        return false;
    }
    
    /**
     * Compute & return the refresh time
     * 
     * @return int refresh time (unix timestamp)
     */
    private function _refreshTime() 
    {
        if (is_null($this->_lifeTime)) {
            return null;
        }
        return time() - $this->_lifeTime;
    }
    
    /**
     * Make and return a file name (with path)
     *
     * @param string $id cache id
     * @return string file name (with path)
     */  
    static private function _file($id)
    {
        $fileName = self::_idToFileName($id);
        return self::_path($fileName) . $fileName;
    }
    
    /**
     * TODO : doc
     */
    static private function _path($fileName)
    {
        $root = $this->_cacheDir;
        if ($this->_hashedDirectoryLevel>0) {
            $hash = md5($fileName);
            for ($i=0 ; $i<$this->_hashedDirectoryLevel ; $i++) {
                $root = $root . 'cache_' . substr($hash, 0, $i + 1) . '/';
            }             
        }
        return $root;
    }
    
    /**
     * Make a control key with the string containing datas
     *
     * @param string $data data
     * @param string $controlType type of control 'md5', 'crc32' or 'strlen'
     * @return string control key
     */
    static private function _hash($data, $controlType)
    {
        switch ($controlType) {
        case 'md5':
            return md5($data);
        case 'crc32':
            return sprintf('% 32d', crc32($data));
        case 'strlen':
            return sprintf('% 32d', strlen($data));
        default:
            Zend_Cache::throwException("Incorrect hash function : $controlType");
        }
    }
       
    /**
     * TODO : doc
     */
    static private function _idToFileName($id)
    {
        return "cache_$id";
    }
    
    /**
     * TODO : doc
     */
    static private function _fileNameToId($fileName) 
    {       
        return preg_replace('~^cache_()$~', '$1', $fileName);
    }
    
    /**
     * TODO : doc
     */
    static private function _tagCacheId($id, $tag) {
        return 'internal_' . $id . '---' . $tag;
    }
    
}
