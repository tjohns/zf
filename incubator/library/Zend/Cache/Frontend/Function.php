<?php

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to version 1.0 of the Zend Framework
 * license, that is bundled with this package in the file LICENSE, and
 * is available through the world-wide-web at the following URL:
 * http://www.zend.com/license/framework/1_0.txt. If you did not receive
 * a copy of the Zend Framework license and are unable to obtain it
 * through the world-wide-web, please send a note to license@zend.com
 * so we can mail you a copy immediately.
 *
 * @package    Zend_Cache
 * @subpackage Frontend
 * @copyright  Copyright (c) 2006 Fabien MARTY, Mislav MAROHNIC
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */

 
/**
 * Zend_Cache_Core
 */
require_once 'Zend/Cache/Core.php';


/**
 * @package    Zend_Cache
 * @subpackage Frontend
 * @copyright  Copyright (c) 2006 Fabien MARTY, Mislav MAROHNIC
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class Zend_Cache_Frontend_Function extends Zend_Cache_Core
{
       
    /**
     * This frontend specific options
     * 
     * TODO : docs
     * 
     * @var array options
     */
    private $_specificOptions = array(
    	'cacheByDefault' => true, 
    	'cachedFunctions' => array(),
        'nonCachedFunctions' => array()
    ); 
           
    /**
     * Constructor
     * 
     * @param array $options associative array of options
     */
    public function __construct($options = array())
    {
        while (list($name, $value) = each($options)) {
            $this->setOption($name, $value);
        }
        $this->setOption('automaticSerialization', true);
    }    
    
    /**
     * Set an option
     * 
     * @param string $name name of the option
     * @param mixed $value value of the option
     */
    public function setOption($name, $value)
    {
        if (is_string($name)) {
            if (array_key_exists($name, $this->_options)) {
            	// This is a Core option
                parent::setOptions($name, $value);
                return;
            }
            if (array_key_exists($name, $this->_specificOptions)) { 
        		// This a specic option of this frontend
                $this->_specificOptions[$name] = $value;
                return;
            }
        } 
        Zend_Cache::throwException("Incorrect option name : $name");
    }
    
    public function call($name, $parameters) 
    {
        // TODO : add some internal tags (to be able to clean a particulier function call or name)
        
        $cacheBool1 = $this->_specificOptions['cacheByDefault'];
        $cacheBool2 = in_array($name, $this->_specificOptions['cachedFunctions']);
        $cacheBool3 = in_array($name, $this->_specificOptions['nonCachedFunctions']);
        $cache = (($cacheBool1 || $cacheBool2) && (!$cacheBool3));
        if (!$cache) {
            // We do not have not cache
            return call_user_func_array($name, $parameters);
        }
        
        $id = $this->_makeId($name, $parameters);
        if ($this->test($id)) {
            // A cache is available
            $result = $this->get($id);
            $output = $result[0];
            $return = $result[1];
        } else {
            // A cache is not available 
            ob_start();
            ob_implicit_flush(false);
            $return = call_user_func_array($name, $parameters);
            $output = ob_get_contents();
            ob_end_clean();
            $data = array($output, $return);
            $this->save($data);
        }
        echo $output;
        return $return;
    }
        
    private function _makeId($name, $parameters) 
    {
        if (!is_string($name)) {
            Zend_Cache::throwException('Incorrect function name');
        }
        return md5($name . serialize($parameters));
    }
                 
}

