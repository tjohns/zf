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
     * The well-known NTP port number
     */
    const DEFAULT_NTP_PORT = 123;
    
    /**
     * The well-known SNTP port number
     */
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
     * Current timeserver being used
     *
     * @var integer
     */
    protected $_current;
    
    
    /**
     * Allowed timeserver schemes
     *
     * @var array
     */
    protected $_allowedSchemes = array('ntp', 
                                       'sntp');
    
    public function __construct($server, $options = array()) {
        $this->_addServer($server);
    }
    
    protected function _addServer($server)
    {
        if (is_array($server)) {
            foreach ($server as $key => $timeServer) {
                $this->_addServer($timeServer);
            }
        } else {
            $defaultUrl = array('scheme' => self::DEFAULT_TIMESERVER_SCHEME,
                                'port'   => self::DEFAULT_NTP_PORT);
                                
            $url = @array_merge($defaultUrl, @parse_url($server));
            if (!in_array(strtolower($url['scheme']), $this->_allowedSchemes)) {
                $url['scheme'] = self::DEFAULT_TIMESERVER_SCHEME;
            }
            
            if (!isset($url['host']) && isset($url['path'])) {
                $url['host'] = $url['path'];
            }
            
            $protocol  = (strcasecmp($url['scheme'], 'ntp') == 0) ? 'udp' : 'tcp';
            $className = 'Zend_TimeSync_' . ucfirst($url['scheme']);
            
            require_once 'Zend/TimeSync/' . ucfirst($url['scheme']) . '.php';
            
            $this->timeservers[] = new $className($protocol . '://' . $url['host'], $url['port']);
        }
    }
    
    public function getDate($locale = false)
    {}
}
