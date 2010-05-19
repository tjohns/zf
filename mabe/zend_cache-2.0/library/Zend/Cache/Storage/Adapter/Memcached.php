<?php

namespace Zend\Cache\Storage\Adapter;
use \Zend\Cache\Storage;
use \Zend\Cache\RuntimeException;
use \Zend\Cache\InvalidArgumentException;

class Memcached extends AbstractAdapter
{

    /**
     * Default Values
     */
    const DEFAULT_HOST    = '127.0.0.1';
    const DEFAULT_PORT    = 11211;
    const DEFAULT_WEIGHT  = 1;

    /**
     * Check of ext/memcached
     *
     * NULL   = not checked or not loaded
     * TRUE   = All tests passed
     * STRING = Thrown error message
     *
     * @var null|boolean|string
     */
    protected static $_extensionCheck = null;

    protected $_servers = array(
        '127.0.0.1:11211' => array(
            'host'   => self::DEFAULT_HOST,
            'port'   => self::DEFAULT_PORT,
            'weight' => self::DEFAULT_WEIGHT
        )
    );

    protected $_persistentId = null;

    protected $_serverFailureLimit = 0;

    protected $_distribution = null; // Memcached::DISTRIBUTION_MODULA

    protected $_libketamaCompatible = false;

    protected $_bufferWrites = false;

    protected $_binaryProtocol = false;

    protected $_noBlock = false;

    protected $_tcpNodelay = false;

    protected $_socketSendSize = null; // default: varies by platform/kernel configuration.

    protected $_socketRecvSize = null; // default: varies by platform/kernel configuration.

    protected $_compression = false;

    protected $_serializer = null; // Memcached::SERIALIZER_PHP

    protected $_hashAlgo = null; // Memcached::HASH_DEFAULT

    protected $_connectTimeout = 1000;

    protected $_retryTimeout = 0;

    protected $_sendTimeout = 0;

    protected $_recvTimeout = 0;

    protected $_pollTimeout = 1000;

    protected $_cacheDnsLookups = false;

    /**
     * Memcached instance
     *
     * @var Memcached|null
     */
    protected $_memcached = null;

    /**
     * The last used namespace.
     * This value is configured in memcached instance as Memcached::OPT_PREFIX_KEY
     *
     * @var string
     */
    protected $_lastNamespace = '';

    /**
     * Given callback on last getDelayed
     *
     * @var callback|null
     */
    protected $_getDelayedCallback = null;

    /**
     * Given select value on last getDelayed
     *
     * @var int
     */
    protected $_getDelayedSelect = null;

    /**
     * Constructor
     *
     * @param array $options associative array of options
     * @return void
     * @throws Zend\Cache\Exception
     */
    public function __construct($options = array())
    {
        // not within static extension check becoause the extension could load dynamicly.
        if (!extension_loaded('memcached')) {
            throw new RuntimeException('ext/memcached not loaded');
        }

        if (self::$_extensionCheck === null) {
            // check ext/memcached version
            if (!in_array('getResultMessage', get_class_methods('Memcached'))) {
                self::$_extensionCheck = 'Need ext/memcached version >= 1.0.0';
            } else {
                self::$_extensionCheck = true;
            }
        }
        if (self::$_extensionCheck !== true) {
            throw new RuntimeException(self::$_extensionCheck);
        }

        // set default options based on Memcached::* constants
        // after checking ext/memcached
        $this->_distribution = \Memcached::DISTRIBUTION_MODULA;
        $this->_serializer   = \Memcached::SERIALIZER_PHP;
        $this->_hashAlgo     = \Memcached::HASH_DEFAULT;

        parent::__construct($options);
    }

    public function __destruct() {
        if ($this->_memcached) {
            // this fixed "zend_mm_heap corrupted" message on exit.
            $this->_memcached = null;
        }
    }

    /**
     * Set multiple servers to the server pool.
     *
     * @param array $servers
     */
    public function setServers(array $servers)
    {
        $this->_normalizeServers($servers);

        if ($this->_memcached) {
            // reinit memcached instance
            $this->_memcached = null;
        }

        $this->_servers = $servers;

        return $this;
    }

    /**
     * Add multiple servers to the server pool.
     *
     * @param array $servers
     */
    public function addServers(array $servers)
    {
        $this->_normalizeServers($servers);

        if ($this->_memcached) {
            $serversArg = array();
            foreach ($servers as $k => $server) {
                if (!isset($this->_servers[$k])) {
                    $serversArg[] = array($server['host'], $server['port'], $server['weight']);
                }
            }
            try {
                $this->_memcached->addServers($serversArg);
            } catch (\MemcachedException $e) {
                throw new RuntimeException($e->getMessage(), 0, $e);
            }
        }

        $this->_servers = array_merge($this->_servers, $servers);

        return $this;
    }

    /**
     * Get the list of the servers in the pool.
     *
     * return array
     */
    public function getServers()
    {
        return $this->_servers;
    }

    /**
     * Set a server to the server pool.
     *
     * @param array $server
     */
    public function setServer(array $server)
    {
        $this->_normalizeServer($server);

        $this->_servers = array();
        if ($this->_memcached) {
            // reinit memcached instance
            $this->_memcached = null;
        }

        $serverKey = $server['host'] . ':' . $server['port'];
        $this->_servers[$serverKey] = $server;

        return $this;
    }

    /**
     * Add a server to the server pool
     *
     * @param array $server
     */
    public function addServer(array $server)
    {
        $this->_normalizeServer($server);

        $serverKey = $server['host'] . ':' . $server['port'];
        if (!isset($this->_servers[$serverKey])) {
            if ($this->_memcached) {
                try {
                    $this->_memcached->addServer($server['host'], $server['port'], $server['weight']);
                } catch (\MemcachedException $e) {
                    throw new RuntimeException($e->getmessage(), 0, $e);
                }
            }
            $this->_servers[$serverKey] = $server;
        }

        return $this;
    }

    protected function _normalizeServerPool(array &$serverPool)
    {
        $normalizedPool = array();
        foreach ($serverPool as $k => $v) {
            $this->_normalizeServer($v);
            if (is_string($k)) {
                list($host, $port) = explode(':', $k, 2);
                if ( ($host = (string)$host) ) {
                    // TODO validate host
                    $v['host'] = $host;
                }
                if ( ($port = (int)$port) ) {
                    // TODO validate port number
                    $v['port'] = $port;
                }
            }

            $serverKey = $v['host'] . ':' . $v['port'];
            $normalizedPool[$serverKey] = $v;
        }

        $serverPool = $normalizedPool;
    }

    protected function _normalizeServer(array &$server)
    {
        $normalizedServer = array();

        if (isset($server['host'])) {
            $normalizedServer['host'] = (string)$server['host'];
            // TODO: validate host
        } else {
            $normalizedServer['host'] = self::DEFAULT_HOST;
        }

        if (isset($server['port'])) {
            $normalizedServer['port'] = (int)$server['port'];
            // TODO validate port number
        } else {
            $normalizedServer['port'] = self::DEFAULT_PORT;
        }

        if (isset($server['weight'])) {
            $normalizedServer['weight'] = (int)$server['weight'];
            // TODO validate weight
        } else {
            $normalizedServer['weight'] = self::DEFAULT_WEIGHT;
        }

        $server = $normalizedServer;
    }

    /**
     * Set the persistent id to use.
     *
     * By default the Memcached instances are destroyed at the end of the request.
     * To create an instance that persists between requests, use persistent_id to specify a unique ID for the instance.
     * All instances created with the same persistent_id will share the same connection.
     *
     * @param string|null $id
     * @return Zend\Cache\Storage\Adapter\Memcached
     */
    public function setPersistentId($id)
    {
        $id = strlen($id = (string)$id) ? $id : null;
        if ($this->_memcached) {
            // reinit memcached
            $this->_memcached = null;
        }
        $this->_persistentId = $id;
        return $this;
    }

    /**
     * Get the used persistent id.
     *
     * By default the Memcached instances are destroyed at the end of the request.
     * To create an instance that persists between requests, use persistent_id to specify a unique ID for the instance.
     * All instances created with the same persistent_id will share the same connection.
     *
     * @param string|null
     */
    public function getPersistentId()
    {
        return $this->_persistentId;
    }

    /**
     * Set server failure limit.
     *
     * Specifies the failure limit for server connection attempts.
     * The server will be removed after this many continuous connection failures.
     *
     * @param int $limit
     * @return Zend\Cache\Storage\Adapter\Memcached
     */
    public function setServerFailureLimit($limit)
    {
        $limit = (int)$limit;
        if ($this->_memcached) {
            $this->_setMemcachedOption(\Memcached::OPT_SERVER_FAILURE_LIMIT, $limit);
        }
        $this->_serverFailureLimit = $limit;
        return $this;
    }

    /**
     * Get server failire limit.
     *
     * Specifies the failure limit for server connection attempts.
     * The server will be removed after this many continuous connection failures.
     *
     * @return int
     */
    public function getServerFailureLimit()
    {
        return $this->_serverFailureLimit;
    }

    /**
     * Set distribution.
     *
     * Specifies the method of distributing item keys to the servers.
     * Currently supported methods are modulo and consistent hashing.
     * Consistent hashing delivers better distribution and allows servers to be added to the cluster
     * with minimal cache losses.
     *
     * @param int $distribution Value of Memcached::DISTRIBUTION_*
     * @return Zend\Cache\Storage\Adapter\Memcached
     */
    public function setDistribution($distribution)
    {
        $distribution = (int)$distribution;
        if ($this->_memcached) {
            $this->_setMemcachedOption(\Memcached::OPT_DISTRIBUTION, $distribution);
        }
        $this->_distribution = $distribution;
        return $this;
    }

    /**
     * Get distribution.
     *
     * Specifies the method of distributing item keys to the servers.
     * Currently supported methods are modulo and consistent hashing.
     * Consistent hashing delivers better distribution and allows servers to be added to the cluster
     * with minimal cache losses.
     *
     * @return int Value of Memcached::DISTRIBUTION_*
     */
    public function getDistribution()
    {
        return $this->_distribution;
    }

    /**
     * Set libketama compatible.
     *
     * Enables or disables compatibility with libketama-like behavior.
     * When enabled, the item key hashing algorithm is set to MD5 and
     * distribution is set to be weighted consistent hashing distribution.
     * This is useful because other libketama-based clients (Python, Ruby, etc.)
     * with the same server configuration will be able to access the keys transparently.
     *
     * INFO: It is highly recommended to enable this option if you want to use consistent hashing,
     *       and it may be enabled by default in future releases.
     *
     * @param boolean $flag
     * @return Zend\Cache\Storage\Adapter\Memcached
     */
    public function setLibketamaCompatible($flag)
    {
        $flag = (bool)$flag;
        if ($this->_memcached) {
            $this->_setMemcachedOption(\Memcached::OPT_LIBKETAMA_COMPATIBLE, $flag);
        }
        $this->_libketamaCompatible = $flag;
        return $this;
    }

    /**
     * Get libketama compatible.
     *
     * Enables or disables compatibility with libketama-like behavior.
     * When enabled, the item key hashing algorithm is set to MD5 and
     * distribution is set to be weighted consistent hashing distribution.
     * This is useful because other libketama-based clients (Python, Ruby, etc.)
     * with the same server configuration will be able to access the keys transparently.
     *
     * @return boolean
     */
    public function getLibketamaCompatible()
    {
        return $this->_libketamaCompatible;
    }

    /**
     * Set buffer writes.
     *
     * Enables or disables buffered I/O. Enabling buffered I/O causes storage commands to "buffer"
     * instead of being sent. Any action that retrieves data causes this buffer to be sent to the
     * remote connection. Quitting the connection or closing down the connection will also cause
     * the buffered data to be pushed to the remote connection.
     *
     * @param boolean $flag
     * @return Zend\Cache\Storage\Adapter\Memcached
     */
    public function setBufferWrites($flag)
    {
        $flag = (bool)$flag;
        if ($this->_memcached) {
            $this->_setMemcachedOption(\Memcached::OPT_BUFFER_WRITES, $flag);
        }
        $this->_bufferWrites = $flag;
        return $this;
    }

    /**
     * Get buffer writes.
     *
     * Enables or disables buffered I/O. Enabling buffered I/O causes storage commands to "buffer"
     * instead of being sent. Any action that retrieves data causes this buffer to be sent to the
     * remote connection. Quitting the connection or closing down the connection will also cause
     * the buffered data to be pushed to the remote connection.
     *
     * @return boolean
     */
    public function getBufferWrites()
    {
        return $this->_bufferWrites;
    }

    /**
     * Enable the use of the binary protocol.
     * Please note that you cannot toggle this option on an open connection.
     *
     * @param boolean $flag
     * @return Zend\Cache\Storage\Adapter\Memcached
     */
    public function setBinaryProtocol($flag)
    {
        $flag = (bool)$flag;
        if ($this->_binaryProtocol !== $flag) {
            $this->_binaryProtocol = (bool)$flag;
            if ($this->_memcached) {
                // reinit memcached instance
                $this->_memcached = null;
            }
        }
        return $this;
    }

    /**
     * Get binary protocol enabled.
     *
     * @return boolean
     */
    public function getBinaryProtocol()
    {
        return $this->_binaryProtocol;
    }

    /**
     * Enables or disables asynchronous I/O.
     * This is the fastest transport available for storage functions.
     *
     * @param boolean $flag
     * @return Zend\Cache\Storage\Adapter\Memcached
     */
    public function setNoBlock($flag)
    {
        $flag = (bool)$flag;
        if ($this->_memcached) {
            $this->_setMemcachedOption(\Memcached::OPT_NO_BLOCK, $flag);
        }
        $this->_noBlock = $flag;
        return $this;
    }

    /**
     * Get asynchronous I/O enabled.
     *
     * @return boolean
     */
    public function getNoBlock()
    {
        return $this->_noBlock;
    }

    /**
     * Enables or disables the no-delay feature for connecting sockets (may be faster in some environments).
     *
     * @param boolean $flag
     * @return Zend\Cache\Storage\Adapter\Memcached
     */
    public function setTcpNodelay($flag)
    {
        $flag = (bool)$flag;
        if ($this->_memcached) {
            $this->_setMemcachedOption(\Memcached::OPT_TCP_NODELAY, $flag);
        }
        $this->_tcpNodelay = $flag;
        return $this;
    }

    /**
     * Get no-delay feature for connecting sockets enabled.
     *
     * @return boolean
     */
    public function getTcpNodelay()
    {
        return $this->_tcpNodelay;
    }

    /**
     * Set the maximum socket send buffer in bytes.
     *
     * @param int $size
     * @return Zend\Cache\Storage\Adapter\Memcached
     */
    public function setSocketSendSize($size)
    {
        $size = (int)$size;
        if ($size <= 0) {
            throw new InvalidArgumentException('The socket send buffer must be greater than 0');
        }
        if ($this->_memcached) {
            $this->_setMemcachedOption(\Memcached::OPT_SOCKET_SEND_SIZE, $size);
        }
        $this->_socketSendSize = $size;
        return $this;
    }

    /**
     * Get the maximum socket send buffer in bytes.
     *
     * NOTE: The default value varies by platform/kernel configuration
     *       and memcached have be initiated before get it.
     *
     * @return int|null
     */
    public function getSocketSendSize()
    {
        if ( $this->_socketSendSize === null
          && $this->_memcached) {
            $socketSendSize = $this->_memcached->getOption(\Memcached::OPT_SOCKET_SEND_SIZE);
            if ($socketSendSize === false) {
                throw new RuntimeExceotion("Can\'t get memcached option 'SocketSendSize'");
            }
            $this->_socketSendSize = $socketSendSize;
        }
        return $this->_socketSendSize;
    }

    /**
     * Set the maximum socket receive buffer in bytes.
     *
     * @param int $size
     * @return Zend\Cache\Storage\Adapter\Memcached
     */
    public function setSocketRecvSize($size)
    {
        $size = (int)$size;
        if ($size <= 0) {
            throw new InvalidArgumentException('The socket receive buffer must be greater than 0');
        }
        if ($this->_memcached) {
            $this->_setMemcachedOption(\Memcached::OPT_SOCKET_RECV_SIZE, $size);
        }
        $this->_socketRecvSize = $size;
        return $this;
    }

    /**
     * Get the maximum socket receive buffer in bytes.
     *
     * NOTE: The default value varies by platform/kernel configuration
     *       and memcached have be initiated before get it.
     *
     * @return int|null
     */
    public function getSocketRecvSize()
    {
        if ( $this->_socketRecvSize === null
          && $this->_memcached) {
            $socketRecvSize = $this->_memcached->getOption(\Memcached::OPT_SOCKET_RECV_SIZE);
            if ($socketRecvSize === false) {
                throw new RuntimeExceotion("Can\'t get memcached option 'SocketRecvSize'");
            }
            $this->_socketRecvSize = $socketRecvSize;
        }
        return $this->_socketRecvSize;
    }

    /**
     * Enables or disables payload compression.
     * When enabled, item values longer than a certain threshold (currently 100 bytes)
     * will be compressed during storage and decompressed during retrieval transparently.
     *
     * @param boolean $flag
     * @return Zend\Cache\Storage\Adapter\Memcached
     */
    public function setCompression($flag)
    {
        $flag = (bool)$flag;
        if ($this->_memcached) {
            $this->_setMemcachedOption(\Memcached::OPT_COMPRESSION, $flag);
        }
        $this->_compression = $flag;
        return $this;
    }

    /**
     * Get payload compression enabled.
     *
     * @return boolean
     */
    public function getCompression()
    {
        return $this->_compression;
    }

    /**
     * Specifies the serializer to use for serializing non-scalar values.
     * The valid serializers are supplied via Memcached::SERIALIZER_* constants.
     *
     * Memcached::SERIALIZER_IGBINARY is supported only when memcached is configured with
     * --enable-memcached-igbinary option and the igbinary extension is loaded.
     *
     * @param int $serializer
     * @return Zend\Cache\Storage\Adapter\Memcached
     */
    public function setSerializer($serializer)
    {
        $serializer = (int)$serializer;
        if ($this->_memcached) {
            $this->_setMemcachedOption(\Memcached::OPT_SERIALIZER, $serializer);
        }
        $this->_serializer = $serializer;
        return $this;
    }

    /**
     * Get the specified serializer to use for serializing non-scalar values.
     * The valid serializers are constants of Memcached::SERIALIZER_*.
     *
     * @param int $serializer Value of Memcached::SERIALIZER_* constants.
     */
    public function getSerializer()
    {
        return $this->_serializer;
    }

    /**
     * Specifies the hashing algorithm used for the item keys.
     * The valid values are supplied via Memcached::HASH_* constants.
     * Each hash algorithm has its advantages and its disadvantages.
     * Go with the default if you don't know or don't care.
     *
     * @param int $algo
     * @return Zend\Cache\Storage\Adapter\Memcached
     */
    public function setHashAlgo($algo)
    {
        $algo = (int)$algo;
        if ($this->_memcached) {
            $this->_setMemcachedOption(\Memcached::OPT_HASH, $algo);
        }
        $this->_hashAlgo = $algo;
        return $this;
    }

    /**
     * Get the specified hashing algorithm used for the item keys.
     *
     * @param int $algo
     * @return int Value of Memcached::HASH_* constants
     */
    public function getHashAlgo()
    {
        return $this->_hashAlgo;
    }

    /**
     * In non-blocking mode this set the value of the timeout during socket connection, in milliseconds.
     *
     * @param int $timeout
     * @return Zend\Cache\Storage\Adapter\Memcached
     */
    public function setConnectTimeout($timeout)
    {
        $timeout = (int)$timeout;
        if ($timeout <= 0) {
            throw new InvalidArgumentException('The connect timeout must be grater than 0');
        }
        if ($this->_memcached) {
            $this->_setMemcachedOption(\Memcached::OPT_CONNECT_TIMEOUT, $timeout);
        }
        $this->_connectTimeout = $timeout;
        return $this;
    }

    /**
     * In non-blocking mode this set the value of the timeout during socket connection, in milliseconds.
     *
     * @return int
     */
    public function getConnectTimeout()
    {
        return $this->_connectTimeout;
    }

    /**
     * The amount of time, in seconds, to wait until retrying a failed connection attempt.
     *
     * @param int $timeout
     * @return Zend\Cache\Storage\Adapter\Memcached
     */
    public function setRetryTimeout($timeout)
    {
        $timeout = (int)$timeout;
        if ($timeout < 0) {
            throw new InvalidArgumentException('The retry timeout must be grater or equal than 0');
        }
        if ($this->_memcached) {
            $this->_setMemcachedOption(\Memcached::OPT_RETRY_TIMEOUT, $timeout);
        }
        $this->_retryTimeout = $timeout;
        return $this;
    }

    /**
     * The amount of time, in seconds, to wait until retrying a failed connection attempt.
     *
     * @return int
     */
    public function getRetryTimeout()
    {
        return $this->_retryTimeout;
    }

    /**
     * Socket sending timeout, in microseconds.
     * In cases where you cannot use non-blocking I/O this will allow you to
     * still have timeouts on the sending of data.
     *
     * @param int $timeout
     * @return Zend\Cache\Storage\Adapter\Memcached
     */
    public function setSendTimeout($timeout)
    {
        $timeout = (int)$timeout;
        if ($timeout < 0) {
            throw new InvalidArgumentException('The send timeout must be grater or equal than 0');
        }
        if ($this->_memcached) {
            $this->_setMemcachedOption(\Memcached::OPT_SEND_TIMEOUT, $timeout);
        }
        $this->_sendTimeout = $timeout;
        return $this;
    }

    /**
     * Socket sending timeout, in microseconds.
     *
     * @return int
     */
    public function getSendTimeout()
    {
        return $this->_sendTimeout;
    }

    /**
     * Socket reading timeout, in microseconds.
     * In cases where you cannot use non-blocking I/O this will allow you to
     * still have timeouts on the reading of data.
     *
     * @param int $timeout
     * @return Zend\Cache\Storage\Adapter\Memcached
     */
    public function setRecvTimeout($timeout)
    {
        $timeout = (int)$timeout;
        if ($timeout < 0) {
            throw new InvalidArgumentException('The receive timeout must be grater or equal than 0');
        }
        if ($this->_memcached) {
            $this->_setMemcachedOption(\Memcached::OPT_RECV_TIMEOUT, $timeout);
        }
        $this->_recvTimeout = $timeout;
        return $this;
    }

    /**
     * Socket reading timeout, in microseconds.
     *
     * return int
     */
    public function getRecvTimeout()
    {
        return $this->_recvTimeout;
    }

    /**
     * Timeout for connection polling, in milliseconds.
     *
     * @param int $timeout
     * @return Zend\Cache\Storage\Adapter\Memcached
     */
    public function setPollTimeout($timeout)
    {
        $timeout = (int)$timeout;
        if ($timeout <= 0) {
            throw new InvalidArgumentException('The poll timeout must be grater than 0');
        }
        if ($this->_memcached) {
            $this->_setMemcachedOption(\Memcached::OPT_POLL_TIMEOUT, $timeout);
        }
        $this->_pollTimeout = $timeout;
        return $this;
    }

    /**
     * Timeout for connection polling, in milliseconds.
     *
     * return int
     */
    public function getPollTimeout()
    {
        return $this->_pollTimeout;
    }

    /**
     * Enables or disables caching of DNS lookups.
     *
     * @param boolean $flag
     * @return Zend\Cache\Storage\Adapter\Memcached
     */
    public function setCacheDnsLookups($flag)
    {
        $flag = (bool)$flag;
        if ($this->_memcached) {
            $this->_setMemcachedOption(\Memcached::OPT_CACHE_LOOKUPS, $flag);
        }
        $this->_cacheDnsLookups = $flag;
        return $this;
    }

    /**
     * Get caching of DNS lookups enabled.
     *
     * return boolean
     */
    public function getCacheDnsLookups()
    {
        return $this->_cacheDnsLookups;
    }

    protected function _init() {
        if (!$this->_memcached) {
            try {
                $this->_memcached = new \Memcached($this->getPersistentId());

                // init server pool
                $serversArg = array();
                foreach ($this->getServers() as $server) {
                    $serversArg[] = array(
                         $server['host'],
                         $server['port'],
                         $server['weight']
                    );
                }
                $this->_memcached->addServers($serversArg);

                // init options
                $this->_setMemcachedOption(\Memcached::OPT_DISTRIBUTION, $this->getDistribution());
                $this->_setMemcachedOption(\Memcached::OPT_LIBKETAMA_COMPATIBLE, $this->getLibketamaCompatible());
                $this->_setMemcachedOption(\Memcached::OPT_BUFFER_WRITES, $this->getBufferWrites());
                $this->_setMemcachedOption(\Memcached::OPT_BINARY_PROTOCOL, $this->getBinaryProtocol());
                $this->_setMemcachedOption(\Memcached::OPT_NO_BLOCK, $this->getNoBlock());
                $this->_setMemcachedOption(\Memcached::OPT_TCP_NODELAY, $this->getTcpNodelay());
                $this->_setMemcachedOption(\Memcached::OPT_COMPRESSION, $this->getCompression());
                $this->_setMemcachedOption(\Memcached::OPT_HASH, $this->getHashAlgo());
                $this->_setMemcachedOption(\Memcached::OPT_CONNECT_TIMEOUT, $this->getConnectTimeout());
                $this->_setMemcachedOption(\Memcached::OPT_RETRY_TIMEOUT, $this->getRetryTimeout());
                $this->_setMemcachedOption(\Memcached::OPT_SEND_TIMEOUT, $this->getSendTimeout());
                $this->_setMemcachedOption(\Memcached::OPT_RECV_TIMEOUT, $this->getRecvTimeout());
                $this->_setMemcachedOption(\Memcached::OPT_POLL_TIMEOUT, $this->getPollTimeout());
                $this->_setMemcachedOption(\Memcached::OPT_CACHE_LOOKUPS, $this->getCacheDnsLookups());
                $this->_setMemcachedOption(\Memcached::OPT_SERVER_FAILURE_LIMIT, $this->getServerFailureLimit());
                $this->_setMemcachedOption(\Memcached::OPT_SERIALIZER, $this->getSerializer());

                if ($this->getSocketSendSize() !== null) {
                    $this->_setMemcachedOption(\Memcached::OPT_SOCKET_SEND_SIZE, $this->getSocketSendSize());
                }

                if ($this->getSocketRecvSize() !== null) {
                    $this->_setMemcachedOption(\Memcached::OPT_SOCKET_RECV_SIZE, $this->getSocketRecvSize());
                }
            } catch (\MemcachedException $e) {
                throw new RuntimeException($e->getMessage(), 0, $e);
            }
        }
    }

    public function getCapabilities()
    {
        // TODO
    }

    public function status(array $options=array())
    {
        $this->_init();

        $memSize = 0;
        $memUsed = 0;

        try {
            $stats = $this->_memcached->getStats();
            foreach ($stats as $serverStat) {
                $memSize+= $serverStat['limit_maxbytes'];
                $memUsed+= $serverStat['bytes'];
            }
        } catch (\MemcachedException $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }

        return array(
            'total' => $memSize,
            'free'  => ($memSize - $memUsed)
        );
    }

    public function set($value, $key = null, array $options = array())
    {
        $this->_init();

        $key = $this->_key($key);
        $ttl = isset($options['ttl']) ? $this->_ttl($options['ttl']) : $this->getTtl();
        $ns  = isset($options['namespace']) ? (string)$options['namespace'] : $this->getNamespace();
        if ($ns != $this->_lastNamespace) {
            $this->_setMemcachedOption(\Memcached::OPT_PREFIX_KEY, $ns);
            $this->_lastNamespace = $ns;
        }

        try {
            $rs = $this->_memcached->set($key, $value, $this->_expirationTime($ttl));
            if ($rs === false) {
                throw $this->_exceptionByRsCode(
                    $this->_memcached->getResultCode(),
                    $this->_memcached->getResultMessage()
                );
            }

            return true;
        } catch (\MemcachedException $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
    }

    public function setMulti(array $keyValuePairs, array $options = array())
    {
        $this->_init();

        $ttl = isset($options['ttl']) ? $this->_ttl($options['ttl']) : $this->getTtl();
        $ns  = isset($options['namespace']) ? (string)$options['namespace'] : $this->getNamespace();
        if ($ns != $this->_lastNamespace) {
            $this->_setMemcachedOption(\Memcached::OPT_PREFIX_KEY, $ns);
            $this->_lastNamespace = $ns;
        }

        try {
            $rs = $this->_memcached->setMulti($keyValuePairs, $this->_expirationTime($ttl));
            if ($rs === false) {
                throw $this->_exceptionByRsCode(
                    $this->_memcached->getResultCode(),
                    $this->_memcached->getResultMessage()
                );
            }

            return true;
        } catch (\MemcachedException $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
    }

    public function add($value, $key = null, array $options = array())
    {
        $this->_init();

        $key = $this->_key($key);
        $ttl = isset($options['ttl']) ? $this->_ttl($options['ttl']) : $this->getTtl();
        $ns  = isset($options['namespace']) ? (string)$options['namespace'] : $this->getNamespace();
        if ($ns != $this->_lastNamespace) {
            $this->_setMemcachedOption(\Memcached::OPT_PREFIX_KEY, $ns);
            $this->_lastNamespace = $ns;
        }

        try {
            $rs = $this->_memcached->add($key, $value, $this->_expirationTime($ttl));
            if ($rs === false) {
                $rsCode = $this->_memcached->getResultCode();
                if ($rsCode != \Memcached::RES_NOTSTORED) {
                    throw $this->_exceptionByRsCode(
                        $rsCode,
                        $this->_memcached->getResultMessage()
                    );
                }
            }

            return $rs;
        } catch (\MemcachedException $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
    }

    public function addMulti(array $keyValuePairs, array $options = array())
    {
        $this->_init();

        $ttl = isset($options['ttl']) ? $this->_ttl($options['ttl']) : $this->getTtl();
        $ns  = isset($options['namespace']) ? (string)$options['namespace'] : $this->getNamespace();
        if ($ns != $this->_lastNamespace) {
            $this->_setMemcachedOption(\Memcached::OPT_PREFIX_KEY, $ns);
            $this->_lastNamespace = $ns;
        }

        try {
            $expirationTime = $this->_expirationTime($ttl);
            foreach ($keyValuePairs as $key => $value) {
                $rs = $this->_memcached->add($key, $value, $expirationTime);
                if ($rs === false) {
                    throw $this->_exceptionByRsCode(
                        $this->_memcached->getResultCode(),
                        $this->_memcached->getResultMessage()
                    );
                }
            }

            return true;
        } catch (\MemcachedException $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
    }

    public function replace($value, $key = null, array $options = array())
    {
        $this->_init();

        $key = $this->_key($key);
        $ttl = isset($options['ttl']) ? $this->_ttl($options['ttl']) : $this->getTtl();
        $ns  = isset($options['namespace']) ? (string)$options['namespace'] : $this->getNamespace();
        if ($ns != $this->_lastNamespace) {
            $this->_setMemcachedOption(\Memcached::OPT_PREFIX_KEY, $ns);
            $this->_lastNamespace = $ns;
        }

        try {
            $rs = $this->_memcached->replace($key, $value, $this->_expirationTime($ttl));
            if ($rs === false) {
                $rsCode = $this->_memcached->getResultCode();
                if ($rsCode != \Memcached::RES_NOTFOUND) {
                    throw $this->_exceptionByRsCode(
                        $rsCode,
                        $this->_memcached->getResultMessage()
                    );
                }
            }

            return $rs;
        } catch (\MemcachedException $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
    }

    public function replaceMulti(array $keyValuePairs, array $options = array())
    {
        $this->_init();

        $ttl = isset($options['ttl']) ? $this->_ttl($options['ttl']) : $this->getTtl();
        $ns  = isset($options['namespace']) ? (string)$options['namespace'] : $this->getNamespace();
        if ($ns != $this->_lastNamespace) {
            $this->_setMemcachedOption(\Memcached::OPT_PREFIX_KEY, $ns);
            $this->_lastNamespace = $ns;
        }

        try {
            $expirationTime = $this->_expirationTime($ttl);
            foreach ($keyValuePairs as $key => $value) {
                $rs = $this->_memcached->replace($key, $value, $expirationTime);
                if ($rs === false) {
                    throw $this->_exceptionByRsCode(
                        $this->_memcached->getResultCode(),
                        $this->_memcached->getResultMessage()
                    );
                }
            }

            return true;
        } catch (\MemcachedException $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
    }

    public function remove($key = null, array $options = array())
    {
        $this->_init();

        $key = $this->_key($key);
        $ns  = isset($opts['namespace']) ? (string)$opts['namespace'] : $this->getNamespace();
        if ($ns != $this->_lastNamespace) {
            $this->_setMemcachedOption(\Memcached::OPT_PREFIX_KEY, $ns);
            $this->_lastNamespace = $ns;
        }

        try {
            $rs = $this->_memcached->delete($key);
            if ($rs === false) {
                // Don't throw an excaption if cache id doesn't exists
                $rsCode = $this->_memcached->getResultCode();
                if ($rsCode != \Memcached::RES_NOTFOUND) {
                    throw $this->_exceptionByRsCode(
                        $rsCode,
                        $this->_memcached->getResultMessage()
                    );
                }
            }

            return true;
        } catch (\MemcachedException $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
    }

    public function removeMulti(array $keys, array $options = array())
    {
        $this->_init();

        $ns = isset($opts['namespace']) ? (string)$opts['namespace'] : $this->getNamespace();
        if ($ns != $this->_lastNamespace) {
            $this->_setMemcachedOption(\Memcached::OPT_PREFIX_KEY, $ns);
            $this->_lastNamespace = $ns;
        }

        try {
            foreach ($keys as $key) {
                $rs = $this->_memcached->delete($key);
                if ($rs === false) {
                    // Don't throw an excaption if cache id doesn't exists
                    $rsCode = $this->_memcached->getResultCode();
                    if ($rsCode != \Memcached::RES_NOTFOUND) {
                        throw $this->_exceptionByRsCode(
                            $rsCode,
                            $this->_memcached->getResultMessage()
                        );
                    }
                }
            }

            return true;
        } catch (\MemcachedException $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
    }

    public function get($key = null, array $options = array())
    {
        $this->_init();

        $key = $this->_key($key);
        $ns  = isset($options['namespace']) ? (string)$options['namespace'] : $this->getNamespace();
        if ($ns != $this->_lastNamespace) {
            $this->_setMemcachedOption(\Memcached::OPT_PREFIX_KEY, $ns);
            $this->_lastNamespace = $ns;
        }

        try {
            $rs = $this->_memcached->get($key);
            if ($rs === false) {
                $rsCode = $this->_memcached->getResultCode();
                if ($rsCode != \Memcached::RES_NOTFOUND) {
                    throw $this->_exceptionByRsCode(
                        $rsCode,
                        $this->_memcached->getResultMessage()
                    );
                }
            }

            return $rs;
        } catch (\MemcachedException $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
    }

    public function getMulti(array $keys, array $options = array())
    {
        $this->_init();

        $ns = isset($opts['namespace']) ? (string)$opts['namespace'] : $this->getNamespace();
        if ($ns != $this->_lastNamespace) {
            $this->_setMemcachedOption(\Memcached::OPT_PREFIX_KEY, $ns);
            $this->_lastNamespace = $ns;
        }

        try {
            $rs = $this->_memcached->getMulti($keys);
            if ($rs === false) {
                throw $this->_exceptionByRsCode(
                    $this->_memcached->getResultCode(),
                    $this->_memcached->getResultMessage()
                );
            }

            return $rs;
        } catch (\MemcachedException $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
    }

    public function exists($key = null, array $options = array())
    {
        $this->_init();

        $key = $this->_key($key);
        $ns  = isset($options['namespace']) ? (string)$options['namespace'] : $this->getNamespace();
        if ($ns != $this->_lastNamespace) {
            $this->_setMemcachedOption(\Memcached::OPT_PREFIX_KEY, $ns);
            $this->_lastNamespace = $ns;
        }

        try {
            if ($this->_memcached->get($key) === false) {
                $rsCode = $this->_memcached->getResultCode();
                if ($rsCode != \Memcached::RES_NOTFOUND) {
                    throw $this->_exceptionByRsCode(
                        $rsCode,
                        $this->_memcached->getResultMessage()
                    );
                }

                return false;
            }

            return true;
        } catch (\MemcachedException $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
    }

    public function existsMulti(array $keys, array $options = array())
    {
        $this->_init();

        $ns = isset($options['namespace']) ? (string)$options['namespace'] : $this->getNamespace();
        if ($ns != $this->_lastNamespace) {
            $this->_setMemcachedOption(\Memcached::OPT_PREFIX_KEY, $ns);
            $this->_lastNamespace = $ns;
        }

        try {
            $rs = $this->_memcached->getMulti($keys);
            if ($rs === false) {
                throw $this->_exceptionByRsCode(
                    $this->_memcached->getResultCode(),
                    $this->_memcached->getResultMessage()
                );
            }

            return array_keys($rs);
        } catch (\MemcachedException $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
    }

    public function info($key = null, array $options = array())
    {
        $this->_init();

        $key = $this->_key($key);
        $ns  = isset($opts['namespace']) ? (string)$opts['namespace'] : $this->getNamespace();
        if ($ns != $this->_lastNamespace) {
            $this->_setMemcachedOption(\Memcached::OPT_PREFIX_KEY, $ns);
            $this->_lastNamespace = $ns;
        }

        try {
            $rs = $this->_memcached->get($key);
            if ($rs === false) {
                $rsCode = $this->_memcached->getResultCode();
                if ($rsCode != \Memcached::RES_NOTFOUND) {
                    throw $this->_exceptionByRsCode(
                        $rsCode,
                        $this->_memcached->getResultMessage()
                    );
                }

                return false;
            }

            // there are no information queryable about an item
            return array(/* ??? */);
        } catch (\MemcachedException $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
    }

    public function infoMulti(array $keys, array $options = array())
    {
        $this->_init();

        $ns = isset($options['namespace']) ? (string)$options['namespace'] : $this->getNamespace();
        if ($ns != $this->_lastNamespace) {
            $this->_setMemcachedOption(\Memcached::OPT_PREFIX_KEY, $ns);
            $this->_lastNamespace = $ns;
        }

        try {
            $rs = $this->_memcached->getMulti($keys);
            if ($rs === false) {
                throw $this->_exceptionByRsCode(
                    $this->_memcached->getResultCode(),
                    $this->_memcached->getResultMessage()
                );
            }

            // there are no information queryable about an item
            foreach ($rs as &$item) {
                $item = array();
            }

            return $rs;

        } catch (\MemcachedException $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
    }

    public function getDelayed(array $keys, $select = Storage::SELECT_KEY_VALUE, array $options = array())
    {
        $this->_init();

        $ns = isset($options['namespace']) ? (string)$options['namespace'] : '';
        if ($ns != $this->_lastNamespace) {
            $this->_setMemcachedOption(\Memcached::OPT_PREFIX_KEY, $ns);
            $this->_lastNamespace = $ns;
        }

        try {
            $this->_getDelayedSelect = (int)$select;
            if (isset($opts['callback'])) {
                $this->_getDelayedCallback = $options['callback'];
                $rs = $this->_memcached->getDelayed($keys, false, array($this, '_getDelayedCallback'));
            } else {
                $rs = $this->_memcached->getDelayed($keys, false);
            }

            if ($rs === false) {
                $this->_getDelayedCallback = null;
                $this->_getDelayedSelect   = null;
                throw $this->_exceptionByRsCode(
                    $this->_memcached->getResultCode(),
                    $this->_memcached->getResultMessage()
                );
            }

            return true;
        } catch (\MemcachedException $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
    }

    public function _getDelayedCallback(\Memcached $memcached, array $item)
    {
        unset($item['cas']);
        if (($this->_getDelayedSelect & Storage::SELECT_KEY) != Storage::SELECT_KEY) {
            unset($item['key']);
        }
        if (($this->_getDelayedSelect & Storage::SELECT_VALUE) != Storage::SELECT_VALUE) {
            unset($item['value']);
        }
        call_user_func($this->_getDelayedCallback, $item);
        $this->_getDelayedCallback = null;
        $this->_getDelayedSelect   = null;
    }

    public function fetch($fetchStyle = Storage::FETCH_NUM) {
        if (!$this->_memcached) {
            return false;
        }

        try {
            $item = $this->_memcached->fetch();
            if ($item === false) {
                $rsCode = $this->_memcached->getResultCode();
                if ($rsCode != \Memcached::RES_END) {
                    throw $this->_exceptionByRsCode(
                        $this->_memcached->getResultCode(),
                        $this->_memcached->getResultMessage()
                    );
                }
                return false;
            } else {
                unset($item['cas']);

                if (($this->_getDelayedSelect & Storage::SELECT_KEY) != Storage::SELECT_KEY) {
                    unset($item['key']);
                } elseif ( ($fetchStyle & Storage::FETCH_NUM) == Storage::FETCH_NUM) {
                    $item[0] = $item['key'];
                    unset($item['key']);
                } elseif ( ($fetchStyle & Storage::FETCH_BOTH) == Storage::FETCH_BOTH) {
                    $item[0] = $item['key'];
                }

                if (($this->_getDelayedSelect & Storage::SELECT_VALUE) != Storage::SELECT_VALUE) {
                    unset($item['value']);
                } elseif ( ($fetchStyle & Storage::FETCH_NUM) == Storage::FETCH_NUM) {
                    $item[0] = $item['value'];
                    unset($item['value']);
                } elseif ( ($fetchStyle & Storage::FETCH_BOTH) == Storage::FETCH_BOTH) {
                    $item[0] = $item['value'];
                }

                if ( ($fetchStyle & Storage::FETCH_OBJ) == Storage::FETCH_OBJ) {
                    $item = (object)$item;
                }
            }

            return $item;
        } catch (\MemcachedException $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
    }

    public function fetchAll($fetchStyle = Storage::FETCH_NUM)
    {
        if (!$this->_memcached) {
            return array();
        }

        try {
            $items = $this->_memcached->fetchAll();
            if ($items === false) {
                throw $this->_exceptionByRsCode(
                    $this->_memcached->getResultCode(),
                    $this->_memcached->getResultMessage()
                );
            } else {
                foreach ($items as &$item) {
                    unset($item['cas']);

                    if (($this->_getDelayedSelect & Storage::SELECT_KEY) != Storage::SELECT_KEY) {
                        unset($item['key']);
                    } elseif ( ($fetchStyle & Storage::FETCH_NUM) == Storage::FETCH_NUM) {
                        $item[0] = $item['key'];
                        unset($item['key']);
                    } elseif ( ($fetchStyle & Storage::FETCH_BOTH) == Storage::FETCH_BOTH) {
                        $item[0] = $item['key'];
                    }

                    if (($this->_getDelayedSelect & Storage::SELECT_VALUE) != Storage::SELECT_VALUE) {
                        unset($item['value']);
                    } elseif ( ($fetchStyle & Storage::FETCH_NUM) == Storage::FETCH_NUM) {
                        $item[0] = $item['value'];
                        unset($item['value']);
                    } elseif ( ($fetchStyle & Storage::FETCH_BOTH) == Storage::FETCH_BOTH) {
                        $item[0] = $item['value'];
                    }

                    if ( ($fetchStyle & Storage::FETCH_OBJ) == Storage::FETCH_OBJ) {
                        $item = (object)$item;
                    }
                }
            }

            return $items;
        } catch (\MemcachedException $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
    }

    public function increment($value, $key = null, array $options = array())
    {
        $key = $this->_key($key);

        $this->_init();

        $ns = isset($opts['namespace']) ? (string)$opts['namespace'] : '';
        if ($ns != $this->_lastNamespace) {
            $this->_setMemcachedOption(\Memcached::OPT_PREFIX_KEY, $ns);
            $this->_lastNamespace = $ns;
        }

        try {
            $rs = $this->_memcached->increment($key, $value);
            if ($rs === false) {
                $rsCode = $this->_memcached->getResultCode();
                if ($rsCode != \Memcached::RES_NOTFOUND) {
                    throw $this->_exceptionByRsCode(
                        $rsCode,
                        $this->_memcached->getResultMessage()
                    );
                }

                return false;
            }

            return true;
        } catch (\MemcachedException $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
    }

    public function decrement($value, $key = null, array $options = array())
    {
        $key = $this->_key($key);

        $this->_init();

        $ns = isset($opts['namespace']) ? (string)$opts['namespace'] : '';
        if ($ns != $this->_lastNamespace) {
            $this->_setMemcachedOption(\Memcached::OPT_PREFIX_KEY, $ns);
            $this->_lastNamespace = $ns;
        }

        try {
            $rs = $this->_memcached->decrement($key, $value);
            if ($rs === false) {
                $rsCode = $this->_memcached->getResultCode();
                if ($rsCode != \Memcached::RES_NOTFOUND) {
                    throw $this->_exceptionByRsCode(
                        $rsCode,
                        $this->_memcached->getResultMessage()
                    );
                }

                return false;
            }

            return true;
        } catch (\MemcachedException $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
    }

    public function touch($key = null, array $options = array())
    {
        try {
            $value = $this->get($key, $options);
            if ($value === false) {
                $rsCode = $this->getResultCode();
                if ($rsCode != \Memcached::RES_NOTFOUND) {
                    throw $this->_exceptionByRsCode(
                        $rsCode,
                        $this->_memcached->getResultMessage()
                    );
                }

                // add an empty item
                return $this->add('', $key, $options);
            }

            // rewrite item
            return $this->replace($value, $key, $options);
        } catch (\MemcachedException $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
    }

    public function touchMulti(array $keys, array $options = array())
    {
        $values = $this->getMulti($keys, $options);
        if ($values === false) {
            throw $this->_exceptionByRsCode(
                $this->_memcached->getResultCode(),
                $this->_memcached->getResultMessage()
            );
        }

        $addMulti = null;
        $rplMulti = null;
        foreach ($keys as &$key) {
            if (isset($values[$key])) {
                $rplMulti[$key] = $values[$key];
            } else {
                $addMulti[$key] = '';
            }
        }

        $ret = $rplMulti === null ? true : $this->replaceMulti($rplMulti);
        $ret = $ret && ($addMulti === null ? true : $this->addMulti($addMulti));
        return $ret;
    }

    public function clear($match = Storage::MATCH_EXPIRED, array $options = array())
    {
        $match = (int)$match;
        if ( ($match & Storage::MATCH_EXPIRED) != Storage::MATCH_EXPIRED
          && ($match & Storage::MATCH_ACTIVE) != Storage::MATCH_ACTIVE) {
            $match = $match | Storage::MATCH_ACTIVE;
        }

        try {
            // clear all
            if (($match & Storage::MATCH_ACTIVE) == Storage::MATCH_ACTIVE) {
                $this->_init();
                $rs = $this->_memcached->flush();
                if ($rs === false) {
                    throw $this->_exceptionByRsCode(
                        $this->_memcached->getResultCode(),
                        $this->_memcached->getResultMessage()
                    );
                }
                return true;
            }

            // expired items are automatic cleared
            return true;
        } catch (\MemcachedException $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
    }

    /**
     * Get expiration time by ttl
     *
     * Some storage commands involve sending an expiration value (relative to
     * an item or to an operation requested by the client) to the server. In
     * all such cases, the actual value sent may either be Unix time (number of
     * seconds since January 1, 1970, as an integer), or a number of seconds
     * starting from current time. In the latter case, this number of seconds
     * may not exceed 60*60*24*30 (number of seconds in 30 days); if the
     * expiration value is larger than that, the server will consider it to be
     * real Unix time value rather than an offset from current time.
     *
     * @param int $ttl
     * @return int
     */
    protected function _expirationTime($ttl)
    {
        if ($ttl > 2592000) {
            return time() + $ttl;
        } elseif (!$ttl) {
            return 0;
        } else {
            return $ttl;
        }
    }

    /**
     * Generate exception based of memcached result code
     *
     * @param int $rsCode
     * @param string $msg
     * @return Zend\Cache\Exception
     * @throws Zend\Cache\InvalidArgumentException
     */
    protected function _exceptionByRsCode($rsCode, $msg)
    {
        switch ($rsCode) {
            case \Memcached::RES_SUCCESS:
                throw InvalidArgumentException("The result code {$rsCode} isn't an error");

            // TODO: select exception by result code
            case \Memcached::RES_FAILURE:
            case \Memcached::RES_HOST_LOOKUP_FAILURE:
            case \Memcached::RES_UNKNOWN_READ_FAILURE:
            case \Memcached::RES_PROTOCOL_ERROR:
            case \Memcached::RES_CLIENT_ERROR:
            case \Memcached::RES_SERVER_ERROR:
            case \Memcached::RES_WRITE_FAILURE:
            case \Memcached::RES_DATA_EXISTS:
            case \Memcached::RES_NOTSTORED:
            case \Memcached::RES_NOTFOUND:
            case \Memcached::RES_PARTIAL_READ:
            case \Memcached::RES_SOME_ERRORS:
            case \Memcached::RES_NO_SERVERS:
            case \Memcached::RES_END:
            case \Memcached::RES_ERRNO:
            case \Memcached::RES_BUFFERED:
            case \Memcached::RES_TIMEOUT:
            case \Memcached::RES_BAD_KEY_PROVIDED:
            case \Memcached::RES_CONNECTION_SOCKET_CREATE_FAILURE:
            case \Memcached::RES_PAYLOAD_FAILURE:
                return new RuntimeException($msg);

            default:
                return new RuntimeException($msg);
        }
    }

    /**
     * Set an option on memcached instanze.
     *
     * @param int $k
     * @param mixed $v
     * @throws Zend\Cache\RuntimeException
     */
    protected function _setMemcachedOption($k, $v)
    {
        if (!$this->_memcached) {
            throw new RuntimeException('Memcached not instanziated');
        }

        try {
            $rs = $this->_memcached->setOption($k, $v);
            if ($rs === false) {
                throw new RuntimeException("Can't set memcached option '{$k}' => '{$v}'");
            }
        } catch (\MemcachedException $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
    }

}
