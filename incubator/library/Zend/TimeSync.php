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
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

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
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_TimeSync
{
    /**
     * The well-known NTP and SNTP port numbers
     */
    const DEFAULT_NTP_PORT  = 123;
    const DEFAULT_SNTP_PORT = 37;
    
    /**
     * Set the default timeserver scheme to "ntp". This will be called when no, or
     * an invalid scheme is specified
     */
    const DEFAULT_TIMESERVER_SCHEME = 'ntp';
    
    /**
     * Contains array of timeservers
     *
     * @var array
     */
    private $timeservers = array();
        
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
    
    static $options = array(
        'timeout' => 10
    );
    
    public function __construct($server, $options = array()) {
        $this->_addServer($server);
        $this->setOptions($options);
        
        $this->_current = current($this->timeservers);
    }
    
    public function setOptions($options = array()) 
    {
        if (!is_array($options)) {
            throw new Zend_TimeSync_Exception('$config is expected to be an array, ' . gettype($config) . ' given');
        }
        
        foreach ($options as $key => $value) {
            Zend_TimeSync::$options[strtolower($key)] = $value;
        }
    }
        
    protected function _addServer($server)
    {
        if (is_array($server)) {
            foreach ($server as $key => $timeServer) {
                $this->_addServer($timeServer);
            }
        } else {
            $defaultUrl = array(
                'scheme' => self::DEFAULT_TIMESERVER_SCHEME,
            );
                                
            $url = @array_merge($defaultUrl, @parse_url($server));
            if (!in_array(strtolower($url['scheme']), $this->_allowedSchemes)) {
                $url['scheme'] = self::DEFAULT_TIMESERVER_SCHEME;
            }
            $protocol = (strtolower($url['scheme']) === 'ntp') ? 'udp' : 'tcp';
            if (!isset($url['host']) && isset($url['path'])) {
                $url['host'] = $url['path'];
            }
            if (!isset($url['port']) && $protocol === 'udp') {
                $url['port'] = self::DEFAULT_NTP_PORT;
            } else {
                $url['port'] = self::DEFAULT_SNTP_PORT;
            }
            
            $className = 'Zend_TimeSync_' . ucfirst($url['scheme']);
            require_once 'Zend/TimeSync/' . ucfirst($url['scheme']) . '.php';
            
            $this->timeservers[] = new $className($protocol . '://' . $url['host'], $url['port']);
        }
    }
    
    public function getDate($locale = false)
    {
        $timestamp = $this->_current->query();
        if ($timestamp) {
            return new Zend_Date($timestamp, false, $locale);
        } elseif ($this->_current = next($this->timeservers)) {
            $this->getDate($locale);
        } else {
            throw new Zend_TimeSync_Exception('all servers are bogus');
        }
    }
}
