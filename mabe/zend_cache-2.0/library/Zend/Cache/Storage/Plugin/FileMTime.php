<?php

namespace Zend\Cache\Storage\Plugin;

class FileMTime extends AbstractPlugin
{

    const MODE_AND = 'AND';
    const MODE_OR  = 'OR';

    protected $_masterFiles = array();
    protected $_masterFilesMode = self::MODE_AND;
    protected $_clearStatCache = true;

    /**
     * Buffer of last modification times of master files.
     *
     * @var int[]|null
     */
    protected $_masterFilesMTimeBuffer = null;

    public function setMasterFiles(array $masterFiles)
    {
        $this->_masterFiles = array();
        foreach ($masterFiles as $masterFile) {
            $this->addMasterFile($masterFile);
        }
        return $this;
    }

    public function setMasterFile($masterFile)
    {
        $this->_masterFiles = array();
        $this->addMasterFile($masterFile);
        return $this;
    }

    public function addMasterFile($masterFile)
    {
        $this->_masterFiles[] = (string)$masterFile;
        return $this;
    }

    public function getMasterFiles()
    {
        return $this->_masterFiles;
    }

    public function setMasterFilesMode($mode)
    {
        $mode = strtoupper($mode);
        if ($mode != self::MODE_AND && $mode != self::MODE_OR) {
            throw new InvalidArgumentException("Invalid masterFilesMode '{$mode}'");
        }
        $this->_masterFilesMode = $mode;
    }

    public function getMasterFilesMode()
    {
        return $this->_masterFilesMode;
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

    public function getOptions()
    {
        $options = parent::getOptions();
        $options['masterFiles']             = $this->getMasterFiles();
        $options['masterFilesMode']         = $this->getMasterFilesMode();
        $options['clearStatCache']          = $this->getClearStatCache();
        return $options;
    }

    public function get($key = null, array $options = array())
    {
        if (isset($options['validate']) && !$options['validate']) {
            return $this->getStorage()->get($key, $options);
        }

        $data = $this->getStorage()->get($key, $options);
        if ($data === false) {
            return false;
        }

        $info = $this->info($key, $options);
        return ($info === false) ? false : $data;
    }

    public function getMulti(array $keys, array $options = array())
    {
        if (isset($options['validate']) && !$options['validate']) {
            return $this->getStorage()->getMulti($keys, $options);
        }

        $datas = $this->getStorage()->getMulti($keys, $options);
        $infos = $this->infoMulti($keys, $options);

        foreach ($datas as $key => &$data) {
            if (!isset($infos[$key])) {
                unset($datas[$key]);
            }
        }

        return $datas;
    }

    public function exists($key = null, array $options = array())
    {
        if (isset($options['validate']) && !$options['validate']) {
            return $this->getStorage()->exists($key, $options);
        }

        $info = $this->info($key, $options);
        return ($info === false) ? false : true;
    }

    public function existsMulti(array $keys, array $options = array())
    {
        if (isset($options['validate']) && !$options['validate']) {
            return $this->getStorage()->existsMulti($keys, $options);
        }

        $multi = $this->infoMulti($keys, $options);
        foreach ($multi as &$value) {
            $value = true;
        }

        return $multi;
    }

    public function info($key = null, array $options = array())
    {
        $info = $this->getStorage()->info($key, $options);

        if (isset($options['validate']) && !$options['validate']) {
            return $info;
        }

        if (!isset($info['mtime'])) {
            if ($info === false) {
                return false;
            }

            throw new RuntimeException(
                "Can't detect mtime of item '{$this->lastKey()}': "
              . 'please check if the used storage supports mtime on info'
            );
        }

        if ($this->getClearStatCache()) {
            clearstatcache(false);
        }

        $this->_bufferMasterFilesMTime();
        if (!$this->_checkBufferedMTimes($info['mtime'])) {
            return false;
        }

        return $info;
    }

    public function infoMulti(array $keys, array $options = array())
    {
        $infos = $this->infoMulti($keys, $options);

        if (isset($options['validate']) && !$options['validate']) {
            return $infos;
        }

        if ($this->getClearStatCache()) {
            clearstatcache(false);
        }

        $this->_bufferMasterFilesMTime();

        foreach ($infos as $key => $info) {
            if (!isset($info['mtime'])) {
                throw new RuntimeException(
                    "Can't detect mtime of item {$key}: "
                  . 'please check if the used storage supports mtime on info');
            }

            if (!$this->_checkBufferedMTimes($info['mtime'])) {
                unset($infos[$key]);
            }
        }

        return $infos[$key];
    }

    // TODO: delayed, find, fetch, fetchAll

    protected function _bufferMasterFilesMTime()
    {
        $this->_masterFilesMTimeBuffer = null;

        foreach($this->getMasterFiles() as $masterFile) {
            $masterFileMTime = @filemtime($masterFile);
            if (!$masterFileMTime) {
                $lastErr = error_get_last();
                throw new RuntimeException(
                    "Can\'t detect mtime of masterFile '{$masterFile}': "
                  . $lastErr['message']
                );
            } else {
                $this->_masterFilesMTimeBuffer[] = $masterFileMTime;
            }
        }
    }

    protected function _checkBufferedMTimes($mtime)
    {
        if ($this->_masterFilesMTimeBuffer === null) {
            throw new RuntimeException('No mtimes of master files are buffered');
        }

        if ($this->getMasterFilesMode() == self::MODE_AND) {
            // MODE_AND
            foreach($this->_masterFilesMTimeBuffer as $masterFileMTime) {
                if ($mtime < $masterFileMTime) {
                    return false;
                }
            }

            return true;

        } else {
            // MODE_OR
            foreach($this->_masterFilesMTimeBuffer as $masterFileMTime) {
                if ($mtime >= $masterFileMTime) {
                    return true;
                }
            }

            return false;
        }
    }

}
