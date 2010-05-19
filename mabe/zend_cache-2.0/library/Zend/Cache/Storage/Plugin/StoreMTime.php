<?php

namespace Zend\Cache\Storage\Plugin;

class StoreMTime extends AbstractPlugin
{

    public function getCapabilities()
    {
        $capabilities = $this->getStorage()->getCapabilities();
        $capabilities['info'][] = 'mtime';
        $capabilities = array_unique($capabilities['info']);
        return $capabilities;
    }
    
    public function set($value, $key = null, array $options = array())
    {
        return $this->getStorage()->set(
            array($value, time()),
            $key,
            $options
        );
    }
    
    public function setMulti(array $keyValuePairs, array $options = array())
    {
        $now = time();
        foreach ($keyValuePairs as &$v) {
            $v = array($v, $now);
        }
        
        return $this->getStorage()->setMulti($keyValuePairs, $options);
    }
    
    public function add($value, $key = null, array $options = array())
    {
        return $this->getStorage()->add(
            array($value, time()),
            $key,
            $options
        );
    }
    
    public function addMulti(array $keyValuePairs, array $options = array())
    {
        $now = time();
        foreach ($keyValuePairs as &$v) {
            $v = array($v, $now);
        }
        
        return $this->getStorage()->addMulti($keyValuePairs, $options);
    }
    
    public function replace($value, $key = null, array $options = array())
    {
        return $this->getStorage()->replace(
            array($value, time()),
            $key,
            $options
        );
    }
    
    public function replaceMulti(array $keyValuePairs, array $options = array())
    {
        $now = time();
        foreach ($keyValuePairs as &$v) {
            $v = array($v, $now);
        }
        
        return $this->getStorage()->replaceMulti($keyValuePairs, $options);
    }
    
    public function get($key = null, array $options = array())
    {
        $rs = $this->getStorage()->get($key, $options);
        if ($rs !== false && !isset($rs[0])) {
            throw new RuntimeException("Missing value part of return value of key '{$this->lastKey()}'");
        }
        return $rs[0];
    }

    public function getMulti(array $keys, array $options = array())
    {
        $rs = $this->getStorage()->getMulti($keys, $options);
        if ($rs !== false) {
            foreach ($rs as $key => &$v) {
                if (!isset($v[0])) {
                    throw new RuntimeException("Missing value part of return value of key '{$key}'");
                }
                $v = $v[0];
            }
        }
        return $rs;
    }
    
    public function info($key = null, array $options = array())
    {
        $info = $this->getStorage()->info($key, $options);
        if ($info !== false) {
            $get = $this->getStorage()->get($key, $options);
            if ($get === false) {
                $info = false;
            } elseif (!isset($get[1])) {
                throw new RuntimeException("Missing mtime part of return value of key '{$this->lastKey()}'");
            }
            $info['mtime'] = $get[1];
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
                    } elseif (!isset($get[$key][1])) {
                        throw new RuntimeException("Missing mtime part of return value of key '{$key}'");
                    } else {
                        $info['mtime'] = $get[$key][1];
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
    
    // On some storages the last modification time isn't accessable
    // This plugin stores data as an array like $data = array($data, time()) to make it accessable

}
