<?php

namespace Zend\Cache\Storage\Adapter;
use \Zend\Cache\Storage\AbstractAdapter;

class Filesystem extends AbstractAdapter
{

    /**
     * Directory where to store caching files
     *
     * @var null|string A directory or NULL to use sys_get_tmp_dir
     */
    protected $_cacheDir = null;

    /**
     * Used umask on creating a cache file
     *
     * @var int
     */
    protected $_fileUmask = 066;

    /**
     * Lock files on writing
     *
     * @var boolean
     */
    protected $_fileLocking = true;

    /**
     * Used umask on creating a cache directory
     *
     * @var int
     */
    protected $_dirUmask = 077;

    /**
     * Read control enabled ?
     *
     * If enabled a hash (readControlAlgo) will be saved and check on read.
     *
     * @var boolean
     */
    protected $_readControl = false;

    /**
     * The used hash algorithm if read control is enabled
     *
     * @var unknown_type
     */
    protected $_readControlAlgo = 'crc32';

    /**
     * Call clearstatcache enabled?
     *
     * @var boolean
     */
    protected $_clearStatCache = true;

    /**
     * Directory level:
     * How much sub-directaries should created
     *
     * @var int
     */
    protected $_dirLevel = 0;

    /**
     * Buffer vars
     */
    protected $_lastInfoKey = null;
    protected $_lastInfoNs  = null;
    protected $_lastInfoAll = null;
    protected $_lastInfo    = null;

    public function __construct($options = array())
    {
        parent::__construct($options);

        // set default cache directory
        if ($this->getCacheDir() === null) {
            $this->setCacheDir(sys_get_temp_dir());
        }
    }

    public function getOptions()
    {
        $options = parent::getOptions();
        $options['cacheDir']        = $this->getCacheDir();
        $options['filePerm']        = $this->getFilePerm();
        $options['fileUmask']       = $this->getFileUmask();
        $options['fileLocking']     = $this->getFileLocking();
        $options['dirPerm']         = $this->getDirPerm();
        $options['dirUmask']        = $this->getDirUmask();
        $options['dirLevel']        = $this->getDirLevel();
        $options['readControl']     = $this->getReadControl();
        $options['readControlAlgo'] = $this->getReadControlAlgo();
        $options['clearStatCache']  = $this->getClearStatCache();
        return $options;
    }

    public function setCacheDir($dir)
    {
        if (!$dir || !is_dir($dir)) {
            throw new InvalidArgumentException("Cache directory '{$dir}' not found or not a directoy");
        } elseif (!is_writable($dir) || !is_readable($dir)) {
            throw new InvalidArgumentException("Cache directory '{$dir}' not writable or readable");
        }

        $this->_cacheDir = rtrim(realpath($dir), '\\/');
        return $this;
    }

    public function getCacheDir()
    {
        return $this->_cacheDir;
    }

    public function setFilePerm($perm)
    {
        if (is_string($perm)) {
            $perm = octdec($perm);
        } else {
            $perm = (int)$perm;
        }

        // use umask
        return $this->setFileUmask(~$perm);
    }

    public function getFilePerm()
    {
        return ~$this->getFileUmask();
    }

    public function setFileUmask($umask)
    {
        if (is_string($umask)) {
            $umask = octdec($umask);
        } else {
            $umask = (int)$umask;
        }
        if ((~$umask & 0600) != 0600 ) {
            throw new InvalidArgumentException(
                'Invalid file umask or file permission: '
              . 'need permissions to read and write files by owner'
            );
        }

        $this->_fileUmask = $umask;
        return $this;
    }

    public function getFileUmask()
    {
        return $this->_fileUmask;
    }

    public function setFileLocking($flag)
    {
        $this->_fileLocking = (bool)$flag;
    }

    public function getFileLocking()
    {
        return $this->_fileLocking;
    }

    public function setDirPerm($perm)
    {
        if (is_string($perm)) {
            $perm = octdec($perm);
        } else {
            $perm = (int)$perm;
        }

        // use umask
        return $this->setDirUmask(~$perm);
    }

    public function getDirPerm()
    {
        return ~$this->getDirUmask();
    }

    public function setDirUmask($umask)
    {
        if (is_string($umask)) {
            $umask = octdec($umask);
        } else {
            $umask = (int)$umask;
        }

        if ((~$umask & 0700) != 0700 ) {
            throw new InvalidArgumentException(
                'Invalid directory umask or directory permissions: '
              . 'need permissions to execute, read and write directories by owner'
            );
        }

        $this->_dirUmask = $umask;
        return $this;
    }

    public function getDirUmask()
    {
        return $this->_dirUmask;
    }

    public function setDirLevel($level)
    {
        $level = (int)$level;
        if ($level < 0 || $level > 16) {
            throw new InvalidArgumentException(
                "Directory level '{$level}' have to be between 0 and 16"
            );
        }
        $this->_dirLevel = $level;
        return $this;
    }

    public function getDirLevel($level)
    {
        return $this->_dirLevel;
    }

    public function setReadControl($flag)
    {
        $this->_readControl = (bool)$flag;
    }

    public function getReadControl()
    {
        return $this->_readControl;
    }

    public function setReadControlAlgo($algo)
    {
        $algo = strtolower($algo);

        if ($algo != 'strlen') { // handle strlen as a virtual hash algorithm
            if (!in_array($algo, hash_algos())) {
                throw new InvalidArgumentException('Unsupported hash algorithm: ' . $algo);
            }
        }

        $this->_readControlAlgo = $algo;
        return $this;
    }

    public function getReadControlAlgo()
    {
        return $this->_readControlAlgo;
    }

    public function setClearStatCache($flag)
    {
        $this->_clearStatCache = (bool)$flag;
        return $this;
    }

    public function getClearStatCache()
    {
        return $this->_clearStatCache;
    }

    public function getCapabilities()
    {
        // TODO
    }

    public function set($value, $key = null, array $options = array())
    {
        $key = $this->_key($key);
        $options['namespace'] = isset($options['namespace'])
                              ? (string)$options['namespace'] : '';

        if ($this->getClearStatCache()) {
            clearstatcache();
        }

        $this->_set($value, $key, $options);

        return true;
    }

    public function setMulti(array $keys, array $options = array())
    {
        $options['namespace'] = isset($options['namespace'])
                              ? (string)$options['namespace'] : '';

        if ($this->getClearStatCache()) {
            clearstatcache();
        }

        foreach ($keys as $key) {
            $this->_set($value, $key, $options);
        }

        return true;
    }

    protected function _set($value, $key, array $options)
    {
        $oldUmask = null;

        if ( $this->_lastInfoKey == $key
          && ($ns = $options['namespace']) == $this->_lastInfoNs) {
            $filespec = $this->_lastInfo['filespec'];
            // if lastKeyInfo is available I'm sure that the cache directory exist
        } else {
            $filespec = $this->_getKeyFileSpec($key, $ns);
            if ($this->getDirLevel() > 0) {
                $path = dirname($filespec);
                if (!file_exists($path)) {
                    $oldUmask = umask($this->getDirUmask());
                    if ( !@mkdir($path, 0777, true) ) {
                        // reset umask on exception
                        umask($oldUmask);

                        // throw exception with last error message
                        $lastErr = error_get_last();
                        throw new RuntimeException($lastErr['message']);
                    }
                }
            }
        }

        $info = null;
        if ($this->getReadControl()) {
            $info['hash'] = $this->_hash($data, $this->getReadControlAlgo(), true);
        }

        if (isset($opts['tags']) && $options['tags']) {
            $info['tags'] = $opts['tags'];
        }

        try {
            if ($oldUmask !== null) { // $oldUmask could be defined on set directory_umask
                umask($this->getFileUmask());
            } else {
                $oldUmask = umask($this->getFileUmask());
            }

            if ($info) {
                $this->_putFileContent($filespec . '.ifo', serialize($info));
            } elseif (file_exists($filespec . '.ifo')) {
                if (!@unlink($filespec . '.ifo')) {
                    $lastErr = error_get_last();
                    throw new RuntimeException($lastErr['message']);
                }
            }

            try {
                $this->_putFileContent($filespec . '.dat', $data);

                // buffer cache info array
                // -> this give a boost on enabled write_control
                $this->_lastInfoKey = $key;
                $this->_lastInfoAll = $this->_lastInfo + $info;
                $this->_lastInfo    = array(
                    'filespec' => $filespec,
                    'mtime'    => time(),
                );

            } catch (Exception $e) {
                if ($this->_lastInfoKey == $id) {
                    $this->_lastInfoKey = null;
                }

                // remove info file if writing cache file failed
                if ($info) {
                    if (!@unlink($filespec . '.ifo')) {
                        // throw the new exception with previous exception
                        $lastErr = error_get_last();
                        throw new RuntimeException($lastErr['message'], 0, $e);
                    }
                }

                throw $e;
            }

            // reset file_umask
            umask($oldUmask);

        } catch (Exception $e) {
            // reset umask on exception
            umask($oldUmask);
            throw $e;
        }
    }

    public function replace($value, $key = null, array $options = array())
    {
        $key = $this->_key($key);
        $options['namespace'] = isset($options['namespace'])
                              ? (string)$options['namespace'] : '';

        if ($this->getClearStatCache()) {
            clearstatcache();
        }

        if ( !$this->_exists($key, $options) ) {
            throw new RuntimeException("Key '{$key}' doesn't exist");
        }

        $this->_set($value, $key, $options);

        return true;
    }

    public function replaceMulti(array $keys, array $options = array())
    {
        $options['namespace'] = isset($options['namespace'])
                              ? (string)$options['namespace'] : '';

        if ($this->getClearStatCache()) {
            clearstatcache();
        }

        foreach ($keys as $key) {
            if ( !$this->_exists($key, $options) ) {
                throw new RuntimeException("Key '{$key}' doesn't exist");
            }
            $this->_set($value, $key, $options);
        }

        return true;
    }

    public function add($value, $key = null, array $options = array())
    {
        $key = $this->_key($key);
        $options['namespace'] = isset($options['namespace'])
                              ? (string)$options['namespace'] : '';

        if ($this->getClearStatCache()) {
            clearstatcache();
        }

        if ( $this->_exists($key, $options) ) {
            throw new RuntimeException("Key '{$key}' already exist");
        }

        $this->_set($value, $key, $options);

        return true;
    }

    public function addMulti(array $keys, array $options = array())
    {
        $options['namespace'] = isset($options['namespace'])
                              ? (string)$options['namespace'] : '';

        if ($this->getClearStatCache()) {
            clearstatcache();
        }

        foreach ($keys as $key) {
            if ( $this->_exists($key, $options) ) {
                throw new RuntimeException("Key '{$key}' already exist");
            }

            $this->_set($value, $key, $options);
        }

        return true;
    }

    public function remove($key = null, array $options = array())
    {
        $this->_key($key);
        $options['namespace'] = isset($options['namespace'])
                              ? (string)$options['namespace'] : '';

        // unlink is not affected by clearstatcache
        $this->_remove($key, $options);

        return true;
    }

    public function removeMulti(array $keys, array $options = array())
    {
        $options['namespace'] = isset($options['namespace'])
                              ? (string)$options['namespace'] : '';

        // unlink is not affected by clearstatcache
        foreach ($keys as $key) {
            $this->_remove($key, $options);
        }

        return true;
    }

    protected function _remove($key, array $options)
    {
        $filespec = $this->_getKeyFileSpec($key, $options['namespace']);

        $this->_unlink($filespec . '.dat');
        $this->_unlink($filespec . '.ifo');

        if ($this->_lastInfoKey == $key) {
            $this->_lastInfoKey = null;
        }
    }

    public function get($key = null, array $options = array())
    {
        $key = $this->_key($key);
        $options['ttl']       = isset($options['ttl'])
                              ? (int)$options['ttl'] : 0;
        $options['validate']  = isset($options['validate'])
                              ? (bool)$options['validate'] : true;
        $options['namespace'] = isset($options['namespace'])
                              ? (string)$options['namespace'] : '';

        if ($this->getClearStatCache('clearstatcache')) {
            clearstatcache();
        }

        return $this->_get($key, $options);
    }

    public function getMulti(array $keys, array $options = array())
    {
        $options['ttl']       = isset($options['ttl'])
                              ? (int)$options['ttl'] : 0;
        $options['validate']  = isset($options['validate'])
                              ? (bool)$options['validate'] : true;
        $options['namespace'] = isset($options['namespace'])
                              ? (string)$options['namespace'] : '';

        if ($this->getClearStatCache('clearstatcache')) {
            clearstatcache();
        }

        $getMulti = array();
        foreach ($keys as $key) {
            if ( ($rs = $this->_get($key, $options)) !== false) {
                $getMulti[$key] = $rs;
            }
        }

        return $getMulti;
    }

    protected function _get($key, array $options)
    {
        if ( !$this->_exist($key, $options)
          || !($keyInfo=$this->_getKeyInfo($key, $options)) ) {
            return false;
        }

        try {
            $data = $this->_getFileContent($keyInfo['filespec'] . '.dat');

            if ($this->getReadControl('read_control')) {
                if ( !($info = $this->_readInfoFile($keyInfo['filespec'] . '.ifo'))
                  || !isset($info['hash']) ) {
                    // The cache was written without read_control
                    // -> set invalid
                    return false;
                }

                $hashData = $this->_hash($data, $this->getReadControlAlgo('read_control_algo'), true);
                if ($hashData != $info['hash']) {
                    throw new UnexpectedValueException(
                        'readControl: Stored hash and computed hash don\'t match'
                    );
                }
            }

            return $data;

        } catch (Exception $e) {
            try {
                // remove cache file on exception
                $this->_remove($key, $options);
            } catch (Exception $tmp) {} // do not throw remove exception on this point

            throw $e;
        }
    }

    public function exist($key, array $options = array())
    {
        $key = $this->_key($key);
        $options['ttl']       = isset($options['ttl'])
                              ? (int)$options['ttl'] : 0;
        $options['validate']  = isset($options['validate'])
                              ? (bool)$options['validate'] : true;
        $options['namespace'] = isset($options['namespace'])
                              ? (string)$options['namespace'] : '';

        if ($this->getClearStatCache('clearstatcache')) {
            clearstatcache();
        }

        return $this->_exist($key, $options);
    }

    public function existMulti(array $keys, array $options = array())
    {
        $options['ttl']       = isset($options['ttl'])
                              ? (int)$options['ttl'] : 0;
        $options['validate']  = isset($options['validate'])
                              ? (bool)$options['validate'] : true;
        $options['namespace'] = isset($options['namespace'])
                              ? (string)$options['namespace'] : '';

        if ($this->getClearStatCache('clearstatcache')) {
            clearstatcache();
        }

        $existsList = array();
        foreach ($keys as $key) {
            if ( $this->_exist($key, $options) === true ) {
                $existsList[] = $key;
            }
        }

        return $existsList;
    }

    protected function _exist($key, array $options)
    {
        $keyInfo = $this->_getKeyInfo($id, $options);
        if (!$keyInfo) {
            return false; // missing or corrupted cache data
        }

        if ( !$options['validate'] // no validating
          || !$options['ttl']      // infinite lifetime
          || time() <= ($keyInfo['mtime']+$options['ttl'])  // not expired
        ) {
            return true;
        }

        return false;
    }

    public function info($key = null, array $options = array())
    {
        $key = $this->_key($key);
        $options['namespace'] = isset($options['namespace'])
                              ? (string)$options['namespace'] : '';

        if ($this->getClearStatCache()) {
            clearstatcache();
        }

        if ( $this->_lastInfoKey == $key
          && $this->_lastInfoAll
          && $this->_lastInfoNs == $options['namespace'] ) {
            return $this->_lastInfoAll;
        }

        $this->_lastInfoAll = $this->_info($key, $options);
        return $this->_lastInfoAll;
    }

    protected function _info($key, array $options) {
        $keyInfo = $this->_getKeyInfo($key, $options);
        if (!$keyInfo) {
            return false;
        }

        if ( ($info = $this->_readInfoFile($keyInfo['filespec'] . '.ifo')) ) {
            return $keyInfo + $info;
        }

        return $keyInfo;
    }

    public function find($match = Storage::MATCH_ACTIVE, $select = Storage::SELECT_KEY_VALUE, array $options = array())
    {
        // TODO
    }

    /**
     * Clear items by matching flag.
     *
     * @param int $match
     * @param array $options
     * @return boolean True on success or false on failure
     * @throw Zend\Cache\Exception
     */
    public function clear($match = Storage::MATCH_EXPIRED, array $options = array())
    {
        // TODO
    }

    /**
     * Get adapter status information.
     *
     * @param array $options
     * @return array|boolean Status information as an array or false on failure
     * @throw Zend\Cache\Exception
     */
    public function status(array $options)
    {
        return $this->_statusOfPath($this->getCacheDir('cache_dir'));
    }

    /**
     * Get an array of information about the cache key.
     * NOTE: returns false if cache doesn't hit.
     *
     * @param string $key
     * @param array $options
     * @return array|boolean
     */
    protected function _getKeyInfo($key, array $options)
    {
        if ( $this->_lastInfoKey == $key
          && ($ns = $this->_lastInfoNs) == $options['namespace'] ) {
            return $this->_lastInfo;
        }

        $filespec = $this->_getKeyFileSpec($key, $ns);
        if ( ($filemtime = @filemtime($filespec . '.dat')) === false ) {
            return false;
        }

        $this->_lastInfoKey = $key;
        $this->_lastInfoNs  = $ns;
        $this->_lastInfoAll = null;
        $this->_lastInfo    = array(
            'filespec' => $filespec,
            'mtime'    => $filemtime,
        );

        return $this->_lastInfo;
    }

    /**
     * Get cache file spec
     *
     * @param string $key
     * @return string
     */
    protected function _getKeyFileSpec($key, $ns)
    {
        if ($this->_lastInfoKey == $key && $this->_lastInfoNs == $ns) {
            return $this->_lastInfo['filespec'];
        }

        $path  = $this->getCacheDir();
        $level = $this->getDirLevel();
        if ( $level > 0 ) {
            $hash = md5($key);
            for ($i=0; $i < $level; $i+=2) {
                $path.= DIRECTORY_SEPARATOR . $ns . $hash[$i] . $hash[$i+1];
            }
        }

        return $path . DIRECTORY_SEPARATOR . $ns . $key;
    }

    /**
     * Read info file
     *
     * @param string $file
     * @return array|boolean The info array or false if file wasn't found
     * @throws RuntimeException
     */
    protected function _readInfoFile($file) {
        if ( file_exists($file) ) {
            $info = @unserialize($this->_getFileContent($file));
            if (!is_array($info)) {
               throw new RuntimeException("Invalid info file '{$file}'");
            }
            return $info;
        }

        return false;
    }

    /**
     * Read a complete file
     *
     * @param  string $file File complete path
     * @throws RuntimeException
     */
    protected function _getFileContent($file)
    {
        // if file locking enabled -> file_get_contents can't be used
        if ($this->getFileLocking()) {
            $fp = @fopen($file, 'rb');
            if ($fp === false) {
                $lastErr = error_get_last();
                throw new RuntimeException($lastErr['message']);
            }

            flock($fp, LOCK_SH);
            $result = @stream_get_contents($fp);
            if ($result === false) {
                $lastErr = error_get_last();
                @flock($fp, LOCK_UN);
                @fclose($fp);
                throw new RuntimeException($lastErr['message']);
            }
            flock($fp, LOCK_UN);
            fclose($fp);

        // if file locking disabled -> file_get_contents can be used
        } else {
            $result = @file_get_contents($file, false);
            if ($result === false) {
                $lastErr = error_get_last();
                throw new RuntimeException($lastErr['message']);
            }
        }

        return $result;
    }

    /**
     * Write content to a file
     *
     * @param  string $file  File complete path
     * @param  string $data  Data to write
     * @throws RuntimeException
     */
    protected function _putFileContent($file, $data)
    {
        $flags = FILE_BINARY; // since PHP 6 but already defined as 0 in PHP 5.3
        if ($this->getFileLocking()) {
            $flags = $flags | LOCK_EX;
        }

        if (!@file_put_contents($file, $data, $flags)) {
            $lastErr = error_get_last();
            @unlink($file);
            throw new RuntimeException($lastErr['message']);
        }
    }

    /**
     * Unlink a file
     *
     * @param string $file
     * @throw RuntimeException
     */
    protected function _unlink($file) {
        if (!@unlink($file)) {
            // only throw exception if file still exists after deleting
            if (file_exists($file)) {
                $lastErr = error_get_last();
                throw new RuntimeException($lastErr['message']);
            }
        }
    }

}
