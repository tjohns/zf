<?php

namespace zend\cache\adapter;
use \zend\Cache as Cache;

class Variable extends AdapterAbstract
{

    protected $_capabilities = array();
    protected $_data = array();

    public function getCapabilities()
    {
        return $this->_capabilities;
    }

    public function set($value, $key = null, array $options = array())
    {
        $key  = $this->_key($key);
        $ns   = isset($options['namespace']) ? $options['namespace'] : '';
        if (isset($options['tags'])) {
            $tags = $this->_tags($options['tags']);
            $this->_data[$ns][$key] = array($value, time(), $tags);
        } else {
            $this->_data[$ns][$key] = array($value, time());
        }
        return true;
    }

    public function add($value, $key = null, array $options = array())
    {
        $key = $this->_key($key);
        $ns  = isset($options['namespace']) ? $options['namespace'] : '';
        if (isset($this->_data[$ns][$key])) {
            throw new \Exception("Key '$key' already exists within namespace '$ns'");
        }
        if (isset($options['tags'])) {
            $tags = $this->_tags($options['tags']);
            $this->_data[$ns][$key] = array($value, time(), $tags);
        } else {
            $this->_data[$ns][$key] = array($value, time());
        }
        return true;
    }

    public function replace($value, $key = null, array $options = array())
    {
        $key = $this->_key($key);
        $ns  = isset($options['namespace']) ? $options['namespace'] : '';
        if (!isset($this->_data[$ns][$key])) {
            throw new \Exception("Key '$key' doen't exists within namespace '$ns'");
        }
        if (isset($options['tags'])) {
            $tags = $this->_tags($options['tags']);
            $this->_data[$ns][$key] = array($value, time(), $tags);
        } else {
            $this->_data[$ns][$key] = array($value, time());
        }
        return true;
    }

    public function remove($key = null, array $options = array())
    {
        $key = $this->_key($key);
        $ns  = isset($options['namespace']) ? $options['namespace'] : '';
        if (isset($this->_data[$ns][$key])) {
            if (count($this->_data[$ns]) > 1) {
                unset($this->_data[$ns][$key]);
            } else {
                // remove namespace if key is the only content
                unset($this->_data[$ns]);
            }
        }

        return true;
    }

    public function get($key = null, array $options = array())
    {
        $key = $this->_key($key);
        $ns  = isset($options['namespace']) ? $options['namespace'] : '';

        if (!isset($this->_data[$ns][$key])) {
            return false;
        }

        // check if expired
        if (!isset($options['validate']) || $options['validate']) {
            $ttl = isset($options['ttl']) ? $this->_ttl($options['ttl']) : $this->getTtl();
            if ( $ttl && time() > ($this->_data[$ns][$key][1] + $ttl) ) {
                return false;
            }
        }

        return $this->_data[$ns][$key][0];
    }

    public function exists($key = null, array $options = array())
    {
        $key = $this->_key($key);
        $ns  = isset($options['namespace']) ? $options['namespace'] : '';

        if (!isset($this->_data[$ns][$key])) {
            return false;
        }

        // check if expired
        if (!isset($options['validate']) || $options['validate']) {
            $ttl = isset($options['ttl']) ? $this->_ttl($options['ttl']) : $this->getTtl();
            if ( $ttl && time() > ($this->_data[$ns][$key][1] + $ttl) ) {
                return false;
            }
        }

        return true;
    }

    public function info($key = null, array $options = array())
    {
        $key = $this->_key($key);
        $ns  = isset($options['namespace']) ? $options['namespace'] : '';

        if (!isset($this->_data[$ns][$key])) {
            return false;
        }

        // check if expired
        if (!isset($options['validate']) || $options['validate']) {
            $ttl = isset($options['ttl']) ? $this->_ttl($options['ttl']) : $this->getTtl();
            if ( $ttl && time() > ($this->_data[$ns][$key][1] + $ttl) ) {
                return false;
            }
        }

        $info = array(
            'mtime' => $this->_data[$ns][$key][1],
            'ttl'   => isset($options['ttl']) ? $this->_ttl($options['ttl']) : $this->getTtl()
        );

        if (isset($this->_data[$ns][$key][2])) {
            $info['tags'] = $this->_data[$ns][$key][2];
        }

        return $info;
    }

    public function increment($value, $key = null, array $options = array())
    {
        $key   = $this->_key($key);
        $ns    = isset($options['namespace']) ? $options['namespace'] : '';
        $value = (int)$value;
        if (isset($this->_data[$ns][$key])) {
            $this->_data[$ns][$key][0]+= $value;
            $this->_data[$ns][$key][1] = time();
        } else {
            $this->_data[$ns][$key] = array($value, time());
        }
    }

    public function decrement($value, $key = null, array $options = array())
    {
        $key   = $this->_key($key);
        $ns    = isset($options['namespace']) ? $options['namespace'] : '';
        $value = (int)$value;
        if (isset($this->_data[$ns][$key])) {
            $this->_data[$ns][$key][0]-= $value;
            $this->_data[$ns][$key][1] = time();
        } else {
            $this->_data[$ns][$key] = array($value, time());
        }
    }

    public function clear($match = Cache::MATCH_EXPIRED, array $options = array())
    {
        $ns = isset($options['namespace']) ? $options['namespace'] : '';
        foreach ($this->find($mode, $options) as $key) {
            unset($this->_data[$ns][$key]);
        }
    }

    public function find($match = Cache::MATCH_ACTIVE, array $options=array())
    {
        $ns = isset($options['namespace']) ? $options['namespace'] : '';
        if (!isset($this->_data[$ns])) {
            return array();
        }

        $keys = array();
        foreach ($this->_data[$ns] as $key => $info) {

            // if MATCHING_ALL mode do not check expired
            if ( ($mode & \zend\Cache::MATCHING_ALL) != \zend\Cache::MATCHING_ALL
              && ($mode & \zend\Cache::MATCHING_ALL) != 0 ) {

                $ttl = isset($options['ttl']) ? $this->_ttl($options['ttl']) : $this->getTtl();

                // if Zend_Cache::MATCHING_EXPIRED mode selected do not remove active data
                if (($mode & \zend\Cache::MATCHING_EXPIRED) == \zend\Cache::MATCHING_EXPIRED) {
                    if ( time() <= ($info[1]+$ttl) ) {
                        continue;
                    }

                // if Zend_Cache::MATCHING_ACTIVE mode selected do not remove expired data
                } else {
                    if ( time() > ($info[1]+$ttl) ) {
                        continue;
                    }
                }
            }

            ////////////////////////////////////////
            // on this time all expire tests match
            ////////////////////////////////////////

            // if one of the tag matching mode is selected
            if (($mode & 070) > 0) {

                // if MATCHING_TAGS mode -> check if all given tags available in current cache
                if (($mode & \zend\Cache::MATCHING_TAGS) == \zend\Cache::MATCHING_TAGS ) {
                    if (!isset($info[2])) {
                        continue;
                    } elseif (count(array_diff($options['tags'], $info[2])) > 0) {
                        continue;
                    }

                // if MATCHING_NO_TAGS mode -> check if no given tag available in current cache
                } elseif( ($mode & \zend\Cache::MATCHING_NO_TAGS) == \zend\Cache::MATCHING_NO_TAGS ) {
                    if (isset($info[2]) && count(array_diff($options['tags'], $info[2])) != count($options['tags'])) {
                        continue;
                    }

                // if MATCHING_ANY_TAGS mode -> check if any given tag available in current cache
                } elseif ( ($mode & \zend\Cache::MATCHING_ANY_TAGS) == \zend\Cache::MATCHING_ANY_TAGS ) {
                    if (!isset($info[2])) {
                        continue;
                    } elseif (count(array_diff($options['tags'], $info[2])) == count($options['tags'])) {
                        continue;
                    }

                }
            }

            ////////////////////////////////////////
            // on this time all tag tests match
            ////////////////////////////////////////

            $keys[] = $key;
        }

        return  $keys;
    }


    public function status(array $options=array())
    {
        return $this->_getStatusOfPhpMem($options);
    }

}

