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
 * @package    Zend_TimeSync
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Zend
 */
require_once 'Zend.php';

/**
 * Zend_Date
 */
require_once 'Zend/Date.php';

/**
 * Zend_TimeSync_Exception
 */
require_once 'Zend/TimeSync/Exception.php';

/**
 * @category   Zend
 * @package    Zend_TimeSync
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_TimeSync implements IteratorAggregate
{
    /**
     * The well-known NTP and SNTP port numbers
     */
    const DEFAULT_NTP_PORT  = 123;
    const DEFAULT_SNTP_PORT = 37;

    /**
     * Set the default timeserver scheme to "ntp". This will be called 
     * when no, or an invalid scheme is specified
     */
    const DEFAULT_SCHEME = 'ntp';

    /**
     * Contains array of timeservers
     *
     * @var array
     */
    protected $_timeservers = array();

    /**
     * Holds a reference to the current timeserver being used
     *
     * @var object
     */
    protected $_current;

    /**
     * Allowed timeserver schemes
     *
     * @var array
     */
    protected $_allowedSchemes = array(
        'ntp',
        'sntp'
    );

    /**
     * Configuration array, set using the constructor or using
     * ::setOptions() or ::setOption()
     *
     * @var array
     */
    public static $options = array(
        'timeout' => 1
    );

    /**
     * Zend_TimeSync constructor
     *
     * The constructor takes one to two parameters. The first parameter 
     * is $server, which may be a single string representation for a 
     * timeserver, or a structured array for multiple timeservers.
     *
     * Each server must be provided with a valid scheme, and may
     * contain an optional port number. If no port number has been
     * suplied, the default matching port number will be used.
     *
     * Supported schemes are:
     * - ntp
     * - sntp
     *
     * The second parameter is $options, and it is optional. If not
     * specified, default options will be used.
     *
     * @param   mixed $server
     * @param   array $options
     * @return  Zend_TimeSync
     */
    public function __construct($server = array(), $options = array())
    {
        $this->addServer($server);
        $this->setOptions($options);

        $this->_current = reset($this->_timeservers);
    }

    /**
     * getIterator() - return an iteratable object for use in foreach and the like,
     * this completes the IteratorAggregate interface
     *
     * @return ArrayObject
     */
    public function getIterator()
    {
        return new ArrayObject($this->_timeservers);
    }

    /**
     * Adds a timeserver to the server list
     * 
     * The $server parameter may be a string with a single timeserver, or an array 
     * listing multiple timeservers.
     *
     * @param  mixed $server
     * @return void
     * @throws Zend_TimeSync_Exception
     */
    public function addServer($server)
    {
        if (is_array($server)) {
            foreach ($server as $key => $timeServer) {
                $this->_addServer($timeServer);
            }
        } elseif (is_string($server)) {
            $this->_addServer($server);
        } else {
            throw new Zend_TimeSync_Exception('$server should be an array or string, ' . gettype($server) . ' given');
        }
    }

    /**
     * Sets the value for a given option
     * 
     * This will replace any currently defined options.
     *
     * @param   mixed $key
     * @param   mixed $value
     * @return  void
     * @throws  Zend_TimeSync_Exception
     */
    public function setOption($key, $value)
    {
        if ((bool) preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $key)) {
            Zend_TimeSync::$options[$key] = $value;
        } else {
            throw new Zend_TimeSync_Exception("Invalid offset key: '$key'");
        }
    }

    /**
     * Sets the value for the given options
     * 
     * This will replace any currently defined options.
     *
     * @param   array $options
     * @return  void
     * @throws  Zend_TimeSync_Exception
     */
    public function setOptions($options = array())
    {
        if (!is_array($options)) {
            throw new Zend_TimeSync_Exception('$options is expected to be an array, ' . gettype($options) . ' given');
        }

        foreach ($options as $key => $value) {
            $this->setOption($key, $value);
        }
    }

    /**
     * Marks a nameserver as current
     *
     * @param   integer $flag
     * @return  void
     * @throws  Zend_TimeSync_Exception
     */
    public function setCurrent($flag = null)
    {
    	if (array_key_exists($flag, $this->_timeservers)) {
            $this->_current = $this->_timeservers[$flag];
        } else {
            throw new Zend_TimeSync_Exception('$flag does not point to valid timeserver');
        }
    }

    /**
     * Returns the value to the option
     *
     * @param   integer $flag
     * @return  mixed
     * @throws  Zend_TimeSync_Exception
     */
    public function getOption($flag)
    {
    	if (array_key_exists($flag, Zend_TimeSync::$options)) {
            return Zend_TimeSync::$options[$flag];
        } else {
            throw new Zend_TimeSync_Exception('$flag does not point to valid option');
        }
    }

    /**
     * Receive a specified timeservers
     *
     * @param   integer $flag
     * @return  object
     * @throws  Zend_TimeSync_Exception
     */
    public function get($flag = null)
    {
    	if (array_key_exists($flag, $this->_timeservers)) {
            return $this->_timeservers[$flag];
        } else {
            throw new Zend_TimeSync_Exception('$flag does not point to valid timeserver');
        }
    }

    /**
     * Returns the timeserver that is currently set
     *
     * @return object
     * @throws Zend_TimeSync_Exception
     */
    public function getCurrent()
    {
        if (isset($this->_current) && $this->_current !== false) {
            return $this->_current;
        } else {
            throw new Zend_TimeSync_Exception('there is no timeserver set');
        }
    }

    /**
     * Returns information sent/returned from the current timeserver
     * 
     * @return  array
     */
    public function getInfo()
    {
        return $this->getCurrent()->getInfo();   
    }

    /**
     * Query the timeserver list using the fallback mechanism
     *
     * @param   $locale optional locale
     * @return  object
     * @throws  Zend_TimeSync_Exception
     */
    public function getDate($locale = false)
    {
        foreach ($this->_timeservers as $key => $server) {
            $this->_current = $server;
            try {
                return $server->getDate($locale);
            } catch (Zend_TimeSync_Exception $e) {
                if (!isset($masterException)) {
                    $masterException = new Zend_TimeSync_Exception('all timeservers are bogus');
                }
                $masterException->addException($e);
            }
        }

        throw $masterException;
    }

    /**
     * Adds a timeserver object to the timeserver list
     *
     * @param   string $server
     * @return  void
     */
    protected function _addServer($server)
    {
        $urlinfo = @parse_url($server);
        $scheme  = self::DEFAULT_SCHEME;

        foreach ($urlinfo as $key => $value) {
            switch ($key) {
                case 'scheme':
                    if (in_array($value, $this->_allowedSchemes)) {
                        $scheme = strtolower($value);
                    }
                    break;

                case 'path':
                case 'host':
                    $host = $value;
                    break;

                case 'port':
                    $port = $value;
                    break;

                default:
                    break; // break intentionally omitted
            }
        }

        $protocol  = ($scheme == 'ntp') ? 'udp' : 'tcp';
        $className = 'Zend_TimeSync_' . ucfirst($scheme);
        
        if (!isset($port)) {
            if ($scheme == 'ntp') {
                $port = self::DEFAULT_NTP_PORT;
            } elseif ($scheme == 'sntp') {
                $port = self::DEFAULT_SNTP_PORT;
            }
        }

        Zend::loadClass($className);

        $server = new $className($protocol . '://' . $host, $port);
        array_push($this->_timeservers, $server);
    }
}
