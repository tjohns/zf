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
 * Zend
 */
require_once 'Zend.php';

/**
 * Zend_Date
 */
require_once 'Zend/Date.php';

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
    
    public static $options = array(
        'timeout' => 1
    );
    
    public function __construct($server, $options = array()) {
        $this->_addServer($server);
        $this->setOptions($options);
        
        $this->_current = current($this->_timeservers);
    }
    
    public function setOptions($options = array()) 
    {
        if (!is_array($options)) {
            throw Zend::exception('Zend_TimeSync_Exception', '$options is expected to be an array, ' . gettype($config) . ' given');
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
            
            if (!isset($url['host']) && isset($url['path'])) {
                $url['host'] = $url['path'];
            }
            
            $protocol = (strtolower($url['scheme']) === 'ntp') ? 'udp' : 'tcp';
            if (!isset($url['port']) && $protocol === 'udp') {
                $url['port'] = self::DEFAULT_NTP_PORT;
            } else {
                $url['port'] = self::DEFAULT_SNTP_PORT;
            }
            
            $className = 'Zend_TimeSync_' . ucfirst($url['scheme']);
            Zend::loadClass($className);
                        
            $this->_timeservers[] = new $className($protocol . '://' . $url['host'], $url['port']);
        }
    }
    
    public function getDate($locale = false)
    {
        $timestamp = $this->_current->query();
        if ($timestamp) {
            return new Zend_Date($timestamp, false, $locale);
        } elseif ($this->_current = next($this->_timeservers)) {
            return $this->getDate($locale);
        } else {
            $masterException = Zend::exception('Zend_TimeSync_Exception', 'all the provided servers are bogus');            
            foreach ($this->_timeservers as $key => $server) {
                foreach ($server->exceptions as $index => $exception) {
                    $masterException->add($exception);
                }
            }
            throw $masterException;
        }
    }
}
