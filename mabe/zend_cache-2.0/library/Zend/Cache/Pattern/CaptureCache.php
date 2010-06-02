<?php

namespace Zend\Cache\Pattern;

// Don't extend AbstractPattern because it implements [set|get]Storage
class CaptureCache implements PatternInterface
{

    /**
     * Public directory
     *
     * @var string
     */
    protected $_publicDir = null;

    /**
     * Lock files on writing
     *
     * @var boolean
     */
    protected $_fileLocking = true;

    /**
     * The index filename
     *
     * @var string
     */
    protected $_indexFilename = 'index.html';

    /**
     * Page identifier
     *
     * @var null|string
     */
    protected $_pageId = null;

    /**
     * Storage for tagging
     *
     * @var null|\Zend\Cache\Storage\Adaptable
     */
    protected $_tagStorage = null;

    /**
     * Cache item key to store tags
     *
     * @var string
     */
    protected $_tagKey = 'ZendCachePatternCaptureCache_Tags';

    /**
     * Tags
     *
     * @var array
     */
    protected $_tags = array();

    public function __construct($options)
    {
        Options::setConstructorOptions($this, $options);
    }

    public function setOptions(array $options)
    {
        Options::setOptions($this, $options);
        return $this;
    }

    public function getOptions()
    {
        return array(
            'publicDir'     => $this->getPublicDir(),
            'fileExtension' => $this->getFileException(),
            'fileLocking'   => $this->getFileLocking(),
            'tagStorage'    => $this->getTagStorage(),
        );
    }

    public function setPublicDir($dir)
    {
        $this->_publicDir = $dir;
        return $this;
    }

    public function getPublicDir()
    {
        return $this->_publicDir;
    }

    public function setIndexFilename($filename)
    {
        $this->_indexFilename = (string)$filename;
        return $this;
    }

    public function getIndexFilename()
    {
        return $this->_indexFilename;
    }

    public function setFileLocking($flag)
    {
        $this->_fileLocking = (boolean)$flag;
        return $this;
    }

    public function getFileLocking()
    {
        return $this->_fileLocking;
    }

    /**
     * Set a storage for tagging
     *
     * @param \Zend\Cache\Storage\Adaptable $storage
     * @return \Zend\Cache\Pattern\CaptureCache
     */
    public function setTagStorage(Adaptable $storage)
    {
        $this->_tagStorage = $storage;
        return $this;
    }

    /**
     * Get the storage for tagging
     *
     * @return null|\Zend\Cache\Storage\Adaptable
     */
    public function getTagStorage()
    {
        return $this->_tagStorage;
    }

    /**
     * Set cache item key to store tags
     *
     * @param $tagKey string
     * @return Zend\Cache\Pattern\CaptureCache
     */
    public function setTagKey($tagKey)
    {
        $tagKey = (string)$tagKey;
        if (!isset($tagKey[0])) { // strlen($tagKey) == 0
            throw new InvalidArgumentException("Invalid tag key '{$tagKey}'");
        }

        $this->_tagKey = $tagKey;
        return $this;
    }

    /**
     * Get cache item key to store tags
     *
     * @return string
     */
    public function getTagKey()
    {
        return $this->_tagKey;
    }

    /**
     * Set tags to store
     *
     * @param array $tags
     * @return Zend\Cache\Pattern\CaptureCache
     */
    public function setTags(array $tags)
    {
        $this->_tags = $tags;
        return $this;
    }

    /**
     * Get tags to store
     *
     * @return array
     */
    public function getTags()
    {
        return $this->_tags;
    }

    /**
     * Start the cache
     *
     * @param string $pageId  Page identifier
     * @param array  $options Options
     * @return boolean false
     */
    public function start($pageId = null, array $options = array())
    {
        if ($this->_pageId !== null) {
            throw new RuntimeException("Capturing already stated with page id '{$this->_pageId}'");
        }

        if (isset($options['tags'])) {
            $this->setTags($options['tags']);
            unset($options['tags']);
        }

        if ($this->getTags() && !$this->getTagStorage()) {
            throw new RuntimeException('Tags are defined but missing a tag storage');
        }

        if (!isset($pageId[0])) { // strlen($pageId) == 0
            $pageId = $this->_detectPageId();
        }

        ob_start(array($this, '_flush'));
        ob_implicit_flush(false);
        $this->_pageId = $pageId;

        return false;
    }

    public function get($pageId = null, array $options = array())
    {
        if (!isset($pageId[0])) { // strlen($pageId) == 0
            $pageId = $this->_detectPageId();
        }

        $file = $this->getPublicDir()
              . DIRECTORY_SEPARATOR . $this->_pageId2Path($pageId)
              . DIRECTORY_SEPARATOR . $this->_pageId2Filename($pageId);

        if (file_exists($file)) {
            $content = @file_get_contents($file);
            if ($content === false) {
                throw new RuntimeException("Failed to read cached pageId '{$pageId}': {$lastErr['message']}");
            }
            return $content;
        }

        return false;
    }

    public function exists($pageId = null, array $options = array())
    {
        if (!isset($pageId[0])) { // strlen($pageId) == 0
            $pageId = $this->_detectPageId();
        }

        $file = $this->getPublicDir()
              . DIRECTORY_SEPARATOR . $this->_pageId2Path($pageId)
              . DIRECTORY_SEPARATOR . $this->_pageId2Filename($pageId);

        return file_exists($file);
    }

    public function remove($pageId = null, array $options = array())
    {
        if (!isset($pageId[0])) {
            $pageId = $this->_detectPageId();
        }

        $file = $this->getPublicDir()
              . DIRECTORY_SEPARATOR . $this->_pageId2Path($pageId)
              . DIRECTORY_SEPARATOR . $this->_pageId2Filename($pageId);

        if (file_exists($file)) {
            if (!@unlink($file)) {
                $lastErr = error_get_last();
                throw new RuntimeException("Failed to remove cached pageId '{$pageId}': {$lastErr['message']}");
            }
        }
    }

    public function clear(/*TODO*/)
    {
        // TODO
    }

    /**
     * Determine the page to save from the request
     *
     * @return string
     */
    protected function _detectPageId()
    {
        return $_SERVER['REQUEST_URI'];
    }

    protected function _pageId2Filename($pageId)
    {
        $filename = basename($pageId);

        if ( !isset($fileName[0]) ) { // strlen($fileName) == 0
            $filename = $this->getIndexFilename();
        }

        return $filename;
    }

    protected function _pageId2Path($pageId)
    {
        $path = rtrim(dirname($pageId), '/');

        // convert requested "/" to the valid local directory separator
        if ('/' != DIRECTORY_SEPARATOR) {
            $path = str_replace('/', DIRECTORY_SEPARATOR, $path);
        }

        return $path;
    }

    /**
     * callback for output buffering
     *
     * @param  string $output Buffered output
     * @return boolean FALSE means original input is sent to the browser.
     */
    protected function _flush($output)
    {
        $this->_save($output);

        // http://php.net/manual/function.ob-start.php
        // -> If output_callback  returns FALSE original input is sent to the browser.
        return false;
    }

    protected function _save($output)
    {
        $path     = $this->_pageId2Path($this->_pageId);
        $fullPath = $this->getPublicDir() . DIRECTORY_SEPARATOR . $path;
        if (!file_exists($fullPath)) {
            $oldUmask = umask($this->getDirectoryUmask());
            if (!@mkdir($fullPath, 0777, true)) {
                $lastErr = error_get_last();
                throw new RuntimeException(
                    "Can't create directory '{$fullPath}': {$lastErr['message']}"
                );
            }
        }

        if ($oldUmask !== null) { // $oldUmask could be set on create directory
            umask($this->getFileUmask());
        } else {
            $oldUmask = umask($this->getFileUmask());
        }
        $file = $path . DIRECTORY_SEPARATOR . $this->_pageId2Filename($this->_pageId);
        $fullFile = $this->getPublicDir() . DIRECTORY_SEPARATOR . $file;
        $this->_putFileContent($fullFile, $output);

        $tagStorage = $this->getTagStorage();
        if ($tagStorage) {
            $tagKey     = $this->getTagKey();
            $tagIndex = $tagStorage->getTagStorage()->get($tagKey);
            if (!$tagIndex) {
                $tagIndex = null;
            }

            if ($this->_tags) {
                $tagIndex[$file] = &$this->_tags;
            } elseif ($tagIndex) {
                unset($tagIndex[$file]);
            }

            if ($tagIndex !== null) {
                $this->getTagStorage()->set($tagIndex, $tagKey);
            }
        }
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
        $flags = FILE_BINARY; // since PHP 6 but defined as 0 in PHP 5.3
        if ($this->getFileLocking()) {
            $flags = $flags | LOCK_EX;
        }

        $put = @file_put_contents($file, $data, $flags);
        if ( $put < strlen((binary)$data) ) {
            $lastErr = error_get_last();
            @unlink($file); // remove old or incomplete written file
            throw new RuntimeException($lastErr['message']);
        }
    }

}
