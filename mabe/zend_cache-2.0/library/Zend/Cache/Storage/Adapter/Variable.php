<?php

namespace Zend\Cache\Storage\Adapter;
use \Zend\Cache\Storage\AbstractAdapter;

class Variable extends AbstractAdapter
{

    /**
     * {@inherit}
     */
    protected $_capabilities = array(
        'datatypes' => array(
            'boolean'  => true,
            'integer'  => true,
            'double'   => true,
            'string'   => true,
            'array'    => true,
            'object'   => true,
            'resource' => true,
            'NULL'     => true,
        ),
        'info' => array(
            'mtime'
        ),
    );

    /**
     * Data storage
     *
     * @var array
     */
    protected $_data = array();

    public function __construct($options = array()) {
        parent::__construct($options);

        if (version_compare(PHP_VERSION, '6', '>=')) {
            unset($this->_capabilities['datatypes']['string']);
            $this->_capabilities['datatypes']['binary'] = true;
            $this->_capabilities['datatypes']['unicode'] = true;
        }
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

    public function clear($match = Storage::MATCH_EXPIRED, array $options = array())
    {
        $ns = isset($options['namespace']) ? $options['namespace'] : '';
        foreach ($this->find($mode, $options) as $key) {
            unset($this->_data[$ns][$key]);
        }
    }

    public function find($match = Storage::MATCH_ACTIVE, array $options=array())
    {
        $ns = isset($options['namespace']) ? $options['namespace'] : '';
        if (!isset($this->_data[$ns])) {
            return array();
        }

        $ttl  = isset($options['ttl']) ? $this->_ttl($options['ttl']) : $this->getTtl();
        $tags = isset($options['tags']) ? $this->_normalizeTags($options['tags']) : null;
        $emptyTags = $keys = array();
        foreach ($this->_data[$ns] as $key => &$info) {

            // compare expired / active
            if ( ($mode & \zend\Cache::MATCH_ALL) != \zend\Cache::MATCH_ALL
              && ($mode & \zend\Cache::MATCH_ALL) != 0 ) {

                // if Zend_Cache::MATCH_EXPIRED mode selected don't match active items
                if (($mode & \zend\Cache::MATCH_EXPIRED) == \zend\Cache::MATCH_EXPIRED) {
                    if ( time() <= ($info[1]+$ttl) ) {
                        continue;
                    }

                // if Zend_Cache::MATCH_ACTIVE mode selected don't match expired items
                } else {
                    if ( time() > ($info[1]+$ttl) ) {
                        continue;
                    }
                }
            }

            // compare tags
            if ($tags !== null) {
                $tagsStored = isset($info[2]) ? $info[2] : $emptyTags;

                if ( ($mode & \zend\Cache::MATCH_TAGS_OR) == \zend\Cache::MATCH_TAGS_OR ) {
                    $match = (count(array_diff($tags, $tagsStored)) != count($tags));
                } elseif ( ($mode & \zend\Cache::MATCH_TAGS_AND) == \zend\Cache::MATCH_TAGS_AND ) {
                    $match = (count(array_diff($tags, $tagsStored)) == 0);
                }

                // negate
                if ( ($mode & \zend\Cache::MATCH_TAGS_NEGATE) == \zend\Cache::MATCH_TAGS_NEGATE ) {
                    $match = !$match;
                }

                if (!$match) {
                    continue;
                }
            }

            $keys[] = $key;
        }

        return  $keys;
    }

    public function status(array $options=array())
    {
        return $this->_getStatusOfPhpMem($options);
    }

}
