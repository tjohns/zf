<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Zend_Cache_Backend
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace zend\cache\adapter;
use \zend\Cache as Cache;

/**
 * @package    zend\cache
 * @subpackage zend\cache\adapter
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface AdapterInterface
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
     */
    public function setOptions(array $options);

    /**
     * How does this adapter work and which features are available
     *
     * @todo Keys:
     *   serialize                  boolean   support to serialize data (needed to store non string values)
     *   listing                    boolean   support to list stored ids (needed for find & clear)
     *   tagging                    boolean   support tagging
     *   info_mtime                 boolean   Is the mtime available on info
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
     * @throws \zend\cache\Exception
     */
    public function set($value, $key = null, array $options = array());

    /**
     * Store multiple items.
     *
     * @param array $keyValuePairs
     * @param array $options
     * @return boolean
     * @throws \zend\cache\Exception
     */
    public function setMulti(array $keyValuePairs, array $options = array());

    /**
     * Add an item.
     *
     * @param mixed $value
     * @param string $key
     * @param array $options
     * @return boolean
     * @throws \zend\cache\Exception
     * @throws \zend\cache\AlreadyExistException
     */
    public function add($value, $key = null, array $options = array());

    /**
     * Add multiple items.
     *
     * @param array $keyValuePairs
     * @param array $options
     * @return boolean
     * @throws \zend\cache\Exception
     * @throws \zend\cache\AlreadyExistException
     */
    public function addMulti(array $keyValuePairs, array $options = array());

    /**
     * Replace an item.
     *
     * @param mixed $value
     * @param string $key
     * @param array $options
     * @return boolean
     * @throws \zend\cache\Exception
     * @throws \zend\cache\NotFoundException
     */
    public function replace($value, $key = null, array $options = array());

    /**
     * Replace multiple items.
     *
     * @param array $keyValuePairs
     * @param array $options
     * @return boolean
     * @throws \zend\cache\Exception
     * @throws \zend\cache\NotFoundException
     */
    public function replaceMulti(array $keyValuePairs, array $options = array());

    /**
     * Remove an item.
     *
     * @param string $key
     * @param array $options
     * @return boolean True if the item has been removed or wasn't exists, false on failure
     * @throws \zend\cache\Exception
     */
    public function remove($key = null, array $options = array());

    /**
     * Remove multiple items.
     *
     * @param array $keys
     * @param array $options
     * @return boolean True if the items has been removed or wasn't exists, false on failure
     * @throws \zend\cache\Exception
     */
    public function removeMulti(array $keys, array $options = array());

    /* get[Multi] & exists[Multi] & info[Multi] */

    /**
     * Get an item.
     *
     * @param string $key
     * @param array $options
     * @return mixed Data on success and false on failure
     * @throws \zend\cache\Exception
     * @throws \zend\cache\NotFoundException
     */
    public function get($key = null, array $options = array());

    /**
     * Get multiple items.
     *
     * @param array $keys
     * @param array $options
     * @return array Assoziative array of existing cache ids and its data
     * @throws \zend\cache\Exception
     */
    public function getMulti(array $keys, array $options = array());

    /**
     * Test if an item exists.
     *
     * @param string $key
     * @param array $options
     * @return boolean
     * @throws \zend\cache\Exception
     */
    public function exists($key = null, array $options = array());

    /**
     * Test multiple items.
     *
     * @param array $keys
     * @param array $options
     * @return array Assoziative array of existing cache ids
     * @throws \zend\cache\Exception
     */
    public function existsMulti(array $keys, array $options = array());

    /**
     * Get information of an item.
     *
     * @param string $key
     * @param array $options
     * @return array|boolean Information if a cached item or false on failure
     * @throws \zend\cache\Exception
     * @throws \zend\cache\NotFoundException
     */
    public function info($key = null, array $options = array());

    /**
     * Get information of multiple items.
     *
     * @param array $keys
     * @param array $options
     * @return array Assoziative array of existing cache ids and its information
     * @throws \zend\cache\Exception
     */
    public function infoMulti(array $keys, array $options = array());

    /* getDelayed & fetch & fechAll */

    /**
     * Request multiple items.
     *
     * @param array $keys
     * @param array $options
     * @return boolean true on success or fale on failure
     * @throws \zend\cache\Exception
     */
    public function getDelayed(array $keys, array $options = array());

    /**
     * Retrieve the next result from the last request.
     *
     * @return array|boolean The item as key value pair or false
     * @throws \zend\cache\Exception
     */
    public function fetch();

    /**
     * Retrieve all the remaining results from the last request.
     *
     * @return array|boolean Items as key value pairs or false
     * @throws \zend\cache\Exception
     */
    public function fetchAll();

    /* increment[Multi] & decrement[Multi] */

    /**
     * Increment an item.
     *
     * @param string $key
     * @param int $value
     * @param array $options
     * @return boolean True on success or false on failure
     * @throws \zend\cache\Exception
     * @throws \zend\cache\NotFoundException
     */
    public function increment($value, $key = null, array $options = array());

    /**
     * Increment multiple items.
     *
     * @param array $keyValuePairs
     * @param array $options
     * @return boolean True on success or false on failure
     * @throws \zend\cache\Exception
     * @throws \zend\cache\NotFoundException
     */
    public function incrementMulti(array $keyValuePairs, array $options = array());

    /**
     * Decrement an item.
     *
     * @param string $key
     * @param int $value
     * @param array $options
     * @return boolean True on success or false on failure
     * @throws \zend\cache\Exception
     * @throws \zend\cache\NotFoundException
     */
    public function decrement($value, $key = null, array $options = array());

    /**
     * Decrement multiple items.
     *
     * @param array $keyValuePairs
     * @param array $options
     * @return boolean True on success or false on failure
     * @throws \zend\cache\Exception
     * @throws \zend\cache\NotFoundException
     */
    public function decrementMulti(array $keyValuePairs, array $options = array());

    /* find & clear */

    /**
     * Find items by matching flag.
     *
     * @param int $match
     * @param array $options
     * @return array Found keys
     * @throw \zend\cache\Exception
     */
    public function find($match = Cache::MATCH_ACTIVE, array $options = array());

    /**
     * Clear items by matching flag.
     *
     * @param int $match
     * @param array $options
     * @return boolean True on success or false on failure
     * @throw \zend\cache\Exception
     */
    public function clear($match = Cache::MATCH_EXPIRED, array $options = array());

    /**
     * Get adapter status information.
     *
     * @param array $options
     * @return array|boolean Status information as an array or false on failure
     * @throw \zend\cache\Exception
     */
    public function status(array $options);

    /**
     * Optimize adapter storage.
     *
     * @param array $options
     * @return boolean true on success or false on failure
     * @throws \zend\cache\Exception
     */
    public function optimize(array $options = array());

    /**
     * Get the last used key
     *
     * @return string|null
     */
    public function lastKey();

}
