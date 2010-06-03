<?php

namespace Zend\Cache\Storage\Adapter;
use \Zend\Cache\Storage;
use \Zend\Cache\RuntimeException;

class Memory extends AbstractAdapter
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
            'mtime', 'ttl', 'tags'
        ),
    );

    /**
     * Data Array
     *
     * @var array
     */
    protected $_data = array();

    /**
     * The current item count per namespace
     *
     * @var array
     */
    protected $_itemCount = array();

    /**
     * The maximum number of items (per namespace) to store in memory
     *
     * @var int Number of max items or 0 for no limit
     */
    protected $_maxItems = null;

    public function __construct($options = array())
    {
        parent::__construct($options);

        if (version_compare(PHP_VERSION, '6', '>=')) {
            unset($this->_capabilities['datatypes']['string']);
            $this->_capabilities['datatypes']['binary'] = true;
            $this->_capabilities['datatypes']['unicode'] = true;
        }
    }
    
    public function setMaxItems($max)
    {
        $this->_maxItems = (int)$max;
        return $this;
    }

    public function getMaxItems()
    {
        return $this->_maxItems;
    }

    public function set($value, $key = null, array $options = array())
    {
        $key  = $this->_key($key);
        $ns   = isset($options['namespace']) ? $options['namespace'] : $this->getNamespace();
        $tags = isset($options['tags']) ? $this->_tags($options['tags']) : null;

        $exists = isset($this->_data[$ns][$key]);
        $this->_data[$ns][$key] = array($value, time(), $tags);
        if (!$exists) {
            $this->_itemCount[$ns] = isset($this->_itemCount[$ns]) ? $this->_itemCount[$ns] + 1 : 1;
            $this->_handleMaxItems($ns);
        }

        return true;
    }

    public function setMulti(array $keyValuePairs, array $options = array())
    {
        $ns   = isset($options['namespace']) ? $options['namespace'] : $this->getNamespace();
        $tags = isset($options['tags']) ? $this->_tags($options['tags']) : null;
        $time = time();

        foreach ($keyValuePairs as $key => $value) {
            $exists = isset($this->_data[$ns][$key]);
            $this->_data[$ns][$key] = array($value, $time, $tags);
            if (!$exists) {
                $this->_itemCount[$ns] = isset($this->_itemCount[$ns]) ? $this->_itemCount[$ns] + 1 : 1;
            }
        }

        $this->_handleMaxItems($ns);

        return true;
    }

    public function add($value, $key = null, array $options = array())
    {
        $key  = $this->_key($key);
        $ns   = isset($options['namespace']) ? $options['namespace'] : $this->getNamespace();
        $tags = isset($options['tags']) ? $this->_tags($options['tags']) : null;

        if (isset($this->_data[$ns][$key])) {
            throw new RuntimeException("Key '$key' already exists within namespace '$ns'");
        }
        $this->_data[$ns][$key] = array($value, time(), $tags);
        $this->_itemCount[$ns] = isset($this->_itemCount[$ns]) ? $this->_itemCount[$ns] + 1 : 1;
        $this->_handleMaxItems($ns);

        return true;
    }

    public function addMulti(array $keyValuePairs, array $options = array())
    {
        $ns   = isset($options['namespace']) ? $options['namespace'] : $this->getNamespace();
        $tags = isset($options['tags']) ? $this->_tags($options['tags']) : null;
        $time = time();

        foreach ($keyValuePairs as $key => $value) {
            if (isset($this->_data[$ns][$key])) {
                throw new RuntimeException("Key '$key' already exists within namespace '$ns'");
            }
            $this->_data[$ns][$key] = array($value, time(), $tags);
            $this->_itemCount[$ns] = isset($this->_itemCount[$ns]) ? $this->_itemCount[$ns] + 1 : 1;
        }

        $this->_handleMaxItems($ns);

        return true;
    }

    public function replace($value, $key = null, array $options = array())
    {
        $key  = $this->_key($key);
        $ns   = isset($options['namespace']) ? $options['namespace'] : $this->getNamespace();
        $tags = isset($options['tags']) ? $this->_tags($options['tags']) : null;

        if (!isset($this->_data[$ns][$key])) {
            throw new \Exception("Key '$key' doen't exists within namespace '$ns'");
        }
        $this->_data[$ns][$key] = array($value, time(), $tags);

        return true;
    }

    public function replaceMulti(array $keyValuePairs, array $options = array())
    {
        $ns   = isset($options['namespace']) ? $options['namespace'] : $this->getNamespace();
        $tags = isset($options['tags']) ? $this->_tags($options['tags']) : null;
        $time = time();

        foreach ($keyValuePairs as $key => $value) {
            if (!isset($this->_data[$ns][$key])) {
                throw new \Exception("Key '$key' doen't exists within namespace '$ns'");
            }
            $this->_data[$ns][$key] = array($value, time(), $tags);
        }

        return true;
    }

    public function remove($key = null, array $options = array())
    {
        $key = $this->_key($key);
        $ns  = isset($options['namespace']) ? $options['namespace'] : $this->getNamespace();

        if (isset($this->_data[$ns][$key])) {
            unset($this->_data[$ns][$key]);
            $this->_itemCount--;

            // empty namespaces are removed on optimize
        }

        return true;
    }

    public function removeMulti(array $keys, array $options = array())
    {
        $ns  = isset($options['namespace']) ? $options['namespace'] : $this->getNamespace();

        foreach ($keys as $key) {
            if (isset($this->_data[$ns][$key])) {
                unset($this->_data[$ns][$key]);
                $this->_itemCount--;

                // empty namespaces are removed on optimize
            }
        }

        return true;
    }

    public function get($key = null, array $options = array())
    {
        $key = $this->_key($key);
        $ns  = isset($options['namespace']) ? $options['namespace'] : $this->getNamespace();

        if (!isset($this->_data[$ns][$key])) {
            return false;
        }

        if (!isset($options['validate']) || $options['validate']) {
            // check if expired
            $ttl = isset($options['ttl']) ? $this->_ttl($options['ttl']) : $this->getTtl();
            if ( $ttl && time() > ($this->_data[$ns][$key][1] + $ttl) ) {
                return false;
            }
        }

        return $this->_data[$ns][$key][0];
    }

    public function getMulti(array $keys, array $options = array())
    {
        $ns  = isset($options['namespace']) ? $options['namespace'] : $this->getNamespace();
        if (!isset($this->_data[$ns])) {
            return array();
        }

        $ttl = 0;
        if (!isset($options['validate']) || $options['validate']) {
            $ttl = isset($options['ttl']) ? $this->_ttl($options['ttl']) : $this->getTtl();
        }

        $ret = array();
        foreach ($keys as $key) {
            if (isset($this->_data[$ns][$key])) {
                if (!$ttl || time() <= ($this->_data[$ns][$key][1] + $ttl) ) {
                    $ret[$key] = $this->_data[$ns][$key][0];
                }
            }
        }

        return $ret;
    }

    public function exists($key = null, array $options = array())
    {
        $key = $this->_key($key);
        $ns  = isset($options['namespace']) ? $options['namespace'] : $this->getNamespace();

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
        $ns  = isset($options['namespace']) ? $options['namespace'] : $this->getNamespace();

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
            'ttl'   => isset($options['ttl']) ? $this->_ttl($options['ttl']) : $this->getTtl(),
            'tags'  => $this->_data[$ns][$key][2]
        );

        return $info;
    }

    public function getDelayed(array $keys, $select = Storage::SELECT_KEY_VALUE, array $options = array())
    {
        if ($this->_fetchBuffer) {
            throw new RuntimeException('Statement already in use');
        }

        $select   = (int)$select;
        $callback = null;
        if (isset($options['callback'])) {
            $callback = $options['callback'];
            if (!is_callable($callback, false)) {
                throw new Zend_Cache_Exception('Invalid callback');
            }
        }

        $ns  = isset($options['namespace']) ? $options['namespace'] : $this->getNamespace();
        if (!isset($this->_data[$ns])) {
            return true;
        }

        $ttl = 0;
        if (!isset($options['validate']) || $options['validate']) {
            $ttl = isset($options['ttl']) ? $this->_ttl($options['ttl']) : $this->getTtl();
        }

        foreach ($keys as $key) {
            if (isset($this->_data[$ns][$key])) {
                if (!$ttl || time() <= ($this->_data[$ns][$key][1] + $ttl) ) {
                    $data = &$this->_data[$ns][$key];
                    $item = array();
                    if (($select & Storage::SELECT_KEY) == Storage::SELECT_KEY) {
                        $item[0] = $key;
                    }
                    if (($select & Storage::SELECT_VALUE) == Storage::SELECT_VALUE) {
                        $item[1] = &$data[0];
                    }
                    if (($select & Storage::SELECT_TAGS) == Storage::SELECT_TAGS) {
                        $item[2] = &$data[2];
                    }
                    if (($select & Storage::SELECT_MTIME) == Storage::SELECT_MTIME) {
                        $item[3] = &$data[1];
                    }
                    if (($select & Storage::SELECT_ATIME) == Storage::SELECT_ATIME) {
                        $item[4] = null;
                    }
                    if (($select & Storage::SELECT_CTIME) == Storage::SELECT_CTIME) {
                        $item[5] = null;
                    }

                    if ($callback !== null) {
                        $this->_formatFetchItem($item);
                        $callback($item);
                    } else {
                        $this->_fetchBuffer[] = $item;
                    }
                }
            }
        }

        return true;
    }

    public function increment($value, $key = null, array $options = array())
    {
        $key   = $this->_key($key);
        $ns    = isset($options['namespace']) ? $options['namespace'] : $this->getNamespace();
        $value = (int)$value;

        if (isset($this->_data[$ns][$key])) {
            $this->_data[$ns][$key][0]+= $value;
            $this->_data[$ns][$key][1] = time();
        } else {
            $this->_data[$ns][$key] = array($value, time(), null);
            $this->_itemCount++;
        }
    }

    public function decrement($value, $key = null, array $options = array())
    {
        $key   = $this->_key($key);
        $ns    = isset($options['namespace']) ? $options['namespace'] : $this->getNamespace();
        $value = (int)$value;

        if (isset($this->_data[$ns][$key])) {
            $this->_data[$ns][$key][0]-= $value;
            $this->_data[$ns][$key][1] = time();
        } else {
            $this->_data[$ns][$key] = array($value, time(), null);
            $this->_itemCount++;
        }
    }

    public function touch($key = null, array $options = array())
    {
        $key   = $this->_key($key);
        $ns    = isset($options['namespace']) ? $options['namespace'] : $this->getNamespace();

        if (isset($this->_data[$ns][$key])) {
            // update mtime
            $this->_data[$ns][$key][1] = time();
        } else {
            // add an empty item
            $this->_data[$ns][$key] = array('', time(), null);
            $this->_itemCount++;
        }

        return true;
    }

    public function clear($mode = Storage::MATCH_EXPIRED, array $options = array())
    {
        $ns = isset($options['namespace']) ? $options['namespace'] : $this->getNamespace();
        if (!isset($this->_data[$ns])) {
            return true;
        }

        $ttl = 0;
        if (!isset($options['validate']) || $options['validate']) {
            $ttl = isset($options['ttl']) ? $this->_ttl($options['ttl']) : $this->getTtl();
        }

        $mode = (int)$mode;
        if ( ($mode & Storage::MATCH_EXPIRED) != Storage::MATCH_EXPIRED
          && ($mode & Storage::MATCH_ACTIVE) != Storage::MATCH_ACTIVE) {
            $mode = $mode | Storage::MATCH_ACTIVE;
        }

        $tags = isset($options['tags']) ? $this->_tags($options['tags']) : null;

        // clear all
        if (($mode & Storage::MATCH_ALL) == Storage::MATCH_ALL && $tags === null) {
            $this->_data = array();
            $this->_itemCount = 0;
            return true;
        }

        $emptyTags = $keys = array();
        foreach ($this->_data[$ns] as $key => &$item) {

            // compare expired / active
            if ( ($mode & Storage::MATCH_ALL) != Storage::MATCH_ALL
              && ($mode & Storage::MATCH_ALL) != 0 ) {

                // if MATCH_EXPIRED mode selected don't match active items
                if (($mode & Storage::MATCH_EXPIRED) == Storage::MATCH_EXPIRED) {
                    if ( time() <= ($item[1]+$ttl) ) {
                        continue;
                    }

                // if MATCH_ACTIVE mode selected don't match expired items
                } else {
                    if ( time() > ($item[1]+$ttl) ) {
                        continue;
                    }
                }
            }

            // compare tags
            if ($tags !== null) {
                $tagsStored = isset($item[2]) ? $item[2] : $emptyTags;

                if ( ($mode & Storage::MATCH_TAGS_OR) == Storage::MATCH_TAGS_OR ) {
                    $match = (count(array_diff($tags, $tagsStored)) != count($tags));
                } elseif ( ($mode & Storage::MATCH_TAGS_AND) == Storage::MATCH_TAGS_AND ) {
                    $match = (count(array_diff($tags, $tagsStored)) == 0);
                }

                // negate
                if ( ($mode & Storage::MATCH_TAGS_NEGATE) == Storage::MATCH_TAGS_NEGATE ) {
                    $match = !$match;
                }

                if (!$match) {
                    continue;
                }
            }

            unset($this->_data[$ns][$key]);
            $this->_itemCount--;
        }

        return  true;
    }

    public function find($mode = Storage::MATCH_ACTIVE, $select = Storage::SELECT_KEY_VALUE, array $options=array())
    {
        if ($this->_fetchBuffer) {
            throw new RuntimeException('Statement already in use');
        }

        $select = (int)$select;

        $ns = isset($options['namespace']) ? $options['namespace'] : $this->getNamespace();
        if (!isset($this->_data[$ns])) {
            return true;
        }

        $ttl = 0;
        if (!isset($options['validate']) || $options['validate']) {
            $ttl = isset($options['ttl']) ? $this->_ttl($options['ttl']) : $this->getTtl();
        }

        $mode = (int)$mode;
        if ( ($mode & Storage::MATCH_EXPIRED) != Storage::MATCH_EXPIRED
          && ($mode & Storage::MATCH_ACTIVE) != Storage::MATCH_ACTIVE) {
            $mode = $mode | Storage::MATCH_ACTIVE;
        }

        $tags = isset($options['tags']) ? $this->_tags($options['tags']) : null;
        $emptyTags = $keys = array();
        foreach ($this->_data[$ns] as $key => &$data) {

            // compare expired / active
            if (($mode & Storage::MATCH_ALL) != Storage::MATCH_ALL) {
                // if MATCH_EXPIRED mode selected don't match active items
                if (($mode & Storage::MATCH_EXPIRED) == Storage::MATCH_EXPIRED) {
                    if ( time() <= ($data[1]+$ttl) ) {
                        continue;
                    }

                // if MATCH_ACTIVE mode selected don't match expired items
                } else {
                    if ( time() > ($data[1]+$ttl) ) {
                        continue;
                    }
                }
            }

            // compare tags
            if ($tags !== null) {
                $tagsStored = isset($data[2]) ? $data[2] : $emptyTags;

                if ( ($mode & Storage::MATCH_TAGS_OR) == Storage::MATCH_TAGS_OR ) {
                    $match = (count(array_diff($tags, $tagsStored)) != count($tags));
                } elseif ( ($mode & Storage::MATCH_TAGS_AND) == Storage::MATCH_TAGS_AND ) {
                    $match = (count(array_diff($tags, $tagsStored)) == 0);
                }

                // negate
                if ( ($mode & Storage::MATCH_TAGS_NEGATE) == Storage::MATCH_TAGS_NEGATE ) {
                    $match = !$match;
                }

                if (!$match) {
                    continue;
                }
            }

            $item = array();
            if (($select & Storage::SELECT_KEY) == Storage::SELECT_KEY) {
                $item[0] = $key;
            }
            if (($select & Storage::SELECT_VALUE) == Storage::SELECT_VALUE) {
                $item[1] = $data[0];
            }
            if (($select & Storage::SELECT_TAGS) == Storage::SELECT_TAGS) {
                $item[2] = $data[2];
            }
            if (($select & Storage::SELECT_MTIME) == Storage::SELECT_MTIME) {
                $item[3] = $data[1];
            }
            if (($select & Storage::SELECT_ATIME) == Storage::SELECT_ATIME) {
                $item[4] = null;
            }
            if (($select & Storage::SELECT_CTIME) == Storage::SELECT_CTIME) {
                $item[5] = null;
            }

            $this->_fetchBuffer[] = &$item;
        }

        return  true;
    }

    public function status(array $options=array())
    {
        return $this->_statusOfPhpMem();
    }

    public function optimize(array $options = array())
    {
        // delete empty namespaces
        foreach ($this->_itemCount as $ns => $cnt) {
            if (!$cnt) {
                unset($this->_data[$ns], $this->_itemCount[$ns]);
            }
        }

        return true;
    }

    protected function _handleMaxItems($ns)
    {
        $maxItems = $this->getMaxItems();
        if ($maxItems && $this->_itemCount[$ns] > $maxItems) {
            array_shift($this->_data[$ns]);
            $this->_itemCount[$ns]--;
        }
    }

}
