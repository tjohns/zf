<?php


namespace Zend\Cache\Storage;
use \Zend\Cache\Storage;

interface Storable
{

    /**
     * Constructor
     *
     * @param array|Zend_Config $options
     */
    public function __construct($options);

    /**
     * Set multiple options.
     *
     * @param array $options
     * @return Zend\Cache\Storage\Storable
     */
    public function setOptions(array $options);

    /**
     * Get options
     *
     * @return array
     */
    public function getOptions();

    /**
     * How does this adapter work and which features are available
     *
     *   Key        Datatype   Description
     *   datatypes  array      Supported datatypes for values values:
     *                         - A boolean true means full support
     *                         - A string means the datatype to cast to
     *
     *   info       array      Supported information of the info method:
     *                         - ttl    int    Time to live
     *                         - tags   array  Stored tags
     *                         - mtime  int    Last modification time
     *                         - ctime  int    Time of creation
     *                         - atime  int    Last access time
     *
     * @todo Keys:
     *   listing                    boolean   support to list stored ids (needed for find & clear)
     *   tagging                    boolean   support tagging
     *   read_expired               boolean   support to read expired data
     *   key_disallowed_characters  string    All disallowed characters to use at a cache id
     *   key_max_length             int       Maximum key length
     *   ttl_write                  boolean   Store ttl on write (else the ttl will be handled on read)
     *   ttl_max                    int       max. supported lifetime (0 for infinite lifetime)
     *   ...
     *
     * @return array
     */
    public function getCapabilities();

    /* set[Multi] & add[Multi] & replace[Multi] & remove[Multi] */

    /**
     * Store an item.
     *
     * @param mixed $value
     * @param string $key
     * @param array $options
     * @return boolean
     * @throws Zend\Cache\Exception
     */
    public function set($value, $key = null, array $options = array());

    /**
     * Store multiple items.
     *
     * @param array $keyValuePairs
     * @param array $options
     * @return boolean
     * @throws Zend\Cache\Exception
     */
    public function setMulti(array $keyValuePairs, array $options = array());

    /**
     * Add an item.
     *
     * @param mixed $value
     * @param string $key
     * @param array $options
     * @return boolean
     * @throws Zend\Cache\Exception
     */
    public function add($value, $key = null, array $options = array());

    /**
     * Add multiple items.
     *
     * @param array $keyValuePairs
     * @param array $options
     * @return boolean
     * @throws Zend\Cache\Exception
     */
    public function addMulti(array $keyValuePairs, array $options = array());

    /**
     * Replace an item.
     *
     * @param mixed $value
     * @param string $key
     * @param array $options
     * @return boolean
     * @throws Zend\Cache\Exception
     */
    public function replace($value, $key = null, array $options = array());

    /**
     * Replace multiple items.
     *
     * @param array $keyValuePairs
     * @param array $options
     * @return boolean
     * @throws Zend\Cache\Exception
     */
    public function replaceMulti(array $keyValuePairs, array $options = array());

    /**
     * Remove an item.
     *
     * @param string $key
     * @param array $options
     * @return boolean True if the item has been removed or wasn't exists, false on failure
     * @throws Zend\Cache\Exception
     */
    public function remove($key = null, array $options = array());

    /**
     * Remove multiple items.
     *
     * @param array $keys
     * @param array $options
     * @return boolean True if the items has been removed or wasn't exists, false on failure
     * @throws Zend\Cache\Exception
     */
    public function removeMulti(array $keys, array $options = array());

    /* get[Multi] & exists[Multi] & info[Multi] */

    /**
     * Get an item.
     *
     * @param string $key
     * @param array $options
     * @return mixed Data on success and false on failure
     * @throws Zend\Cache\Exception
     */
    public function get($key = null, array $options = array());

    /**
     * Get multiple items.
     *
     * @param array $keys
     * @param array $options
     * @return array Assoziative array of existing cache ids and its data
     * @throws Zend\Cache\Exception
     */
    public function getMulti(array $keys, array $options = array());

    /**
     * Test if an item exists.
     *
     * @param string $key
     * @param array $options
     * @return boolean
     * @throws Zend\Cache\Exception
     */
    public function exists($key = null, array $options = array());

    /**
     * Test multiple items.
     *
     * @param array $keys
     * @param array $options
     * @return array Assoziative array of existing cache ids
     * @throws Zend\Cache\Exception
     */
    public function existsMulti(array $keys, array $options = array());

    /**
     * Get information of an item.
     *
     * @param string $key
     * @param array $options
     * @return array|boolean Information if a cached item or false on failure
     * @throws Zend\Cache\Exception
     */
    public function info($key = null, array $options = array());

    /**
     * Get information of multiple items.
     *
     * @param array $keys
     * @param array $options
     * @return array Assoziative array of existing cache ids and its information
     * @throws Zend\Cache\Exception
     */
    public function infoMulti(array $keys, array $options = array());

    /* getDelayed & fetch & fechAll */

    /**
     * Request multiple items.
     *
     * @param array $keys
     * @param int   $select
     * @param array $options
     * @return boolean true on success or fale on failure
     * @throws Zend\Cache\Exception
     */
    public function getDelayed(array $keys, $select = Storage::SELECT_KEY_VALUE, array $options = array());

    /**
     * Fetches the next item from result set
     *
     * @param int $fetchStyle
     * @return array|object|boolean The next item or false
     * @throws Zend\Cache\Exception
     */
    public function fetch($fetchStyle = Storage::FETCH_NUM);

    /**
     * Returns an array containing all of the result set items
     *
     * @param int $fetchStyle
     * @return array The result set as array containing all items
     * @throws Zend\Cache\Exception
     */
    public function fetchAll($fetchStyle = Storage::FETCH_NUM);

    /* increment[Multi] & decrement[Multi] */

    /**
     * Increment an item.
     *
     * @param string $key
     * @param int $value
     * @param array $options
     * @return boolean True on success or false on failure
     * @throws Zend\Cache\Exception
     */
    public function increment($value, $key = null, array $options = array());

    /**
     * Increment multiple items.
     *
     * @param array $keyValuePairs
     * @param array $options
     * @return boolean True on success or false on failure
     * @throws Zend\Cache\Exception
     */
    public function incrementMulti(array $keyValuePairs, array $options = array());

    /**
     * Decrement an item.
     *
     * @param string $key
     * @param int $value
     * @param array $options
     * @return boolean True on success or false on failure
     * @throws Zend\Cache\Exception
     */
    public function decrement($value, $key = null, array $options = array());

    /**
     * Decrement multiple items.
     *
     * @param array $keyValuePairs
     * @param array $options
     * @return boolean True on success or false on failure
     * @throws Zend\Cache\Exception
     */
    public function decrementMulti(array $keyValuePairs, array $options = array());

    /* touch[Multi] */

    public function touch($key = null, array $options = array());

    public function touchMulti(array $keys, array $options = array());

    /* find & clear */

    /**
     * Find items by matching flag.
     *
     * @param int $match
     * @param int $select
     * @param array $options
     * @return boolean  true on success or fale on failure
     * @throw Zend\Cache\Exception
     */
    public function find($match = Storage::MATCH_ACTIVE, $select = Storage::SELECT_KEY_VALUE, array $options = array());

    /**
     * Clear items by matching flag.
     *
     * @param int $match
     * @param array $options
     * @return boolean True on success or false on failure
     * @throw Zend\Cache\Exception
     */
    public function clear($match = Storage::MATCH_EXPIRED, array $options = array());

    /**
     * Get adapter status information.
     *
     * @param array $options
     * @return array|boolean Status information as an array or false on failure
     * @throw Zend\Cache\Exception
     */
    public function status(array $options);

    /**
     * Optimize adapter storage.
     *
     * @param array $options
     * @return boolean true on success or false on failure
     * @throws Zend\Cache\Exception
     */
    public function optimize(array $options = array());

    /**
     * Get the last used key
     *
     * @return string|null
     */
    public function lastKey();

}
