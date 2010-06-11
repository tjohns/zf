<?php

namespace Zend\Cache\Storage\Plugin;

class StoreTimes extends AbstractPlugin
{

    protected $_noAtime = true;
    protected $_noCtime = true;

    public function getOptions()
    {
        $options = parent::getOptions();
        $options['noAtime'] = $this->getNoAtime();
        $options['noCtime'] = $this->getNoCtime();
        return $options;
    }

    public function setNoAtime($flag)
    {
        $this->_noAtime = (bool)$flag;
        return $this;
    }

    public function getNoAtime()
    {
        return $this->_noAtime;
    }

    public function setNoCtime($flag)
    {
        $this->_noCtime = (bool)$flag;
        return $this;
    }

    public function getNoCtime()
    {
        return $this->_noCtime;
    }

    public function getCapabilities()
    {
        $capabilities = $this->getStorage()->getCapabilities();

        if (!$this->getNoCtime()) {
            $capabilities['info'][] = 'ctime';
        }
        if (!$this->getNoAtime()) {
            $capabilities['info'][] = 'atime';
        }

        $capabilities['info'][] = 'mtime';
        $capabilities['info'] = array_unique($capabilities['info']);

        return $capabilities;
    }

    public function set($value, $key = null, array $options = array())
    {
        $time    = time();
        $storage = $this->getStorage();

        if ($this->getNoCtime()) {
            return $storage->set(array($value, $time, $time, $time), $options);
        }

        $info = $this->info($key, $options);

        if (isset($info['ctime'])) {
            return $storage->set(array($value, $info['ctime'], $time, $time), $options);
        } else {
            return $storage->set(array($value, $time, $time, $time), $options);
        }
    }

    public function setMulti(array $keyValuePairs, array $options = array())
    {
        $time      = time();
        $storage   = $this->getStorage();
        $noCtime   = $this->getNoCtime();

        if (!$noCtime) {
            $infoMulti = $this->infoMulti($keyValuePairs, $options);
        }

        foreach ($keyValuePairs as $k => &$v) {
            if (!$noCtime && isset($infoMulti[$k]['ctime'])) {
                $v = array($v, &$infoMulti[$k]['ctime'], &$time, &$time);
            } else {
                $v = array($v, &$time, &$time, &$time);
            }
        }

        return $storage->setMulti($keyValuePairs, $options);
    }

    public function add($value, $key = null, array $options = array())
    {
        $time = time();
        return $this->getStorage()->add(
            array($value, $time, $time, $time),
            $key,
            $options
        );
    }

    public function addMulti(array $keyValuePairs, array $options = array())
    {
        $time = time();
        foreach ($keyValuePairs as &$v) {
            $v = array($v, $time, $time, $time);
        }

        return $this->getStorage()->addMulti($keyValuePairs, $options);
    }

    public function replace($value, $key = null, array $options = array())
    {
        $time    = time();
        $storage = $this->getStorage();

        if ($this->getNoCtime()) {
            return $storage->replace(array($value, $time, $time, $time), $key, $options);
        }

        $info = $this->info($key, $options);
        if ($info === false) {
            return false;
        }

        if (isset($info['ctime'])) {
            return $storage->replace(array($value, $info['ctime'], $time, $time), $key, $options);
        } else {
            return $storage->replace(array($value, $time, $time, $time), $key, $options);
        }
    }

    public function replaceMulti(array $keyValuePairs, array $options = array())
    {
        $time      = time();
        $storage   = $this->getStorage();
        $noCtime   = $this->getNoCtime();

        if (!$noCtime) {
            $infoMulti = $this->infoMulti($keyValuePairs, $options);
        }

        foreach ($keyValuePairs as $k => &$v) {
            if (!$noCtime && isset($infoMulti[$k]['ctime'])) {
                $v = array($v, &$infoMulti[$k]['ctime'], &$time, &$time);
            } else {
                $v = array($v, &$time, &$time, &$time);
            }
        }

        return $storage->replaceMulti($keyValuePairs, $options);
    }

    public function get($key = null, array $options = array())
    {
        $rs = $this->getStorage()->get($key, $options);
        if ($rs !== false && !isset($rs[0])) {
            throw new RuntimeException("Missing value part of return value of key '{$this->lastKey()}'");
        }

        if (!$this->getNoAtime()) {
            // TODO: handle atime
        }

        return $rs[0];
    }

    public function getMulti(array $keys, array $options = array())
    {
        $rs = $this->getStorage()->getMulti($keys, $options);
        if ($rs !== false) {
            $noAtime = $this->getNoAtime();
            foreach ($rs as $key => &$v) {
                if (!isset($v[0])) {
                    throw new RuntimeException("Missing value part of return value of key '{$key}'");
                }
                $v = $v[0];

                if (!$noAtime) {
                    // TODO: handle atime
                }
            }
        }

        return $rs;
    }

    public function info($key = null, array $options = array())
    {
        $storage = $this->getStorage();
        $info = $storage->info($key, $options);

        if ($info !== false) {
            $item = $storage->get($key, $options);
            if ($item === false) {
                $info = false;
            } else {
                if (isset($item[1])) {
                    $info['ctime'] = (int)$item[1];
                }
                if (isset($item[2])) {
                    $info['mtime'] = (int)$item[2];
                }
                if (isset($item[3])) {
                    $info['atime'] = (int)$item[3];
                }
            }
        }

        return $info;
    }

    public function infoMulti(array $keys, array $options = array())
    {
        $infoMulti = $this->getStorage()->infoMulti($keys, $options);
        if ($infoMulti !== false) {
            $getMulti = $this->getStorage()->getMulti($keys, $options);
            if ($getMulti === false) {
                $infoMulti = false;
            } else {
                foreach ($infoMulti as $key => &$info) {
                    if (!isset($get[$key])) {
                        unset($infoMulti[$key]);
                    } else {
                        if (isset($get[$key][1])) {
                            $info['ctime'] = (int)$get[$key][1];
                        }
                        if (isset($get[$key][2])) {
                            $info['mtime'] = (int)$get[$key][2];
                        }
                        if (isset($get[$key][3])) {
                            $info['atime'] = (int)$get[$key][3];
                        }
                    }
                }
            }
        }

        return $infoMulti;
    }

    // TODO: fetch & fetchAll

    public function increment($value, $key = null, array $options = array())
    {
        $get = $this->get($key, $options);
        if ($get !== false) {
            return $this->replace($get + (int)$value, $key, $options);
        } else {
            return $this->add((int)$value, $key,$options);
        }
    }

    public function incrementMulti(array $keyValuePairs, array $options = array())
    {
        $getMulti = $this->getMulti($keyValuePairs, $options);
        $addMulti = null;
        $rplMulti = null;
        foreach ($keyValuePairs as $key => $value) {
            if (!isset($getMulti[$key])) {
                $addMulti[$key] = (int)$value;
            } else {
                $rplMulti[$key] = $getMulti[$key] + (int)$value;
            }
        }

        $ret = $addMulti === null ? true : $this->addMulti($addMulti, $options);
        $ret = $ret && ($rplMulti === null ? true : $this->replaceMulti($rplMulti, $options));
        return $ret;
    }

    public function decrement($value, $key = null, array $options = array())
    {
        $get = $this->get($key, $options);
        if ($get !== false) {
            return $this->replace($get - (int)$value, $key, $options);
        } else {
            return $this->add((int)$value, $key,$options);
        }
    }

    public function decrementMulti(array $keyValuePairs, array $options = array())
    {
        $getMulti = $this->getMulti($keyValuePairs, $options);
        $addMulti = null;
        $rplMulti = null;
        foreach ($keyValuePairs as $key => $value) {
            if (!isset($getMulti[$key])) {
                $addMulti[$key] = (int)$value;
            } else {
                $rplMulti[$key] = $getMulti[$key] - (int)$value;
            }
        }

        $ret = $addMulti === null ? true : $this->addMulti($addMulti, $options);
        $ret = $ret && ($rplMulti === null ? true : $this->replaceMulti($rplMulti, $options));
        return $ret;
    }

    public function touch($key = null, array $options = array())
    {
        $get = $this->get($key, $options);
        if ($get === false) {
            $this->add('', $key, $options);
        } else {
            $this->replace($get, $key, $options);
        }
    }

    public function touchMulti(array $keys, array $options = array())
    {
        $getMulti = $this->getMulti();
        $addMulti = null;
        $rplMulti = null;
        foreach ($keys as $key) {
            if (isset($getMulti[$key])) {
                $rplMulti[$key] = $getMulti[$key];
            } else {
                $addMulti[$key] = '';
            }
        }

        $ret = $rplMulti === null ? true : $this->replaceMulti($rplMulti, $options);
        $ret = $ret && ($rplMulti === null ? true : $this->addMulti($addMulti, $options));
        return $ret;
    }

}
