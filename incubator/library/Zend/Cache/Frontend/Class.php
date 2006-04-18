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
class Zend_Cache_Frontend_Class extends Zend_Cache_Core
{
       
    /**
     * Available options
     * 
     * TODO docs
     * 
     * @var array available options
     */
   private $_specificOptions = array(
    	'cachedClass' => null,
    	'cachedObject' => null,
    	'cacheByDefault' => true,
    	'cachedMethods' => array(),
        'nonCachedMethods' => array()
    );
    
    private $_mode = null;
    private $_class = null;
    private $_object = null;
       
    /**
     * Constructor
     * 
     * @param array $options associative array of options
     */
    public function __construct($options = array())
    {
        if ((isset($options['cachedClass'])) and (isset($options['cachedObject']))) {
            Zend_Cache::throwException('cachedClass and cachedObject options are exclusive');
        }
        if ((!isset($options['cachedClass'])) and (!isset($options['cachedObject']))) {
            Zend_Cache::throwException('one of cachedClass or cachedObject option must be set');
        }
        if (isset($options['cachedClass'])) {
            $this->_mode= 'class';
            if (!is_string($options['cachedClass'])) {
                Zend_Cache::throwException('cachedObject option must be a string');
            }
            $this->_class = $options['cachedClass'];
        } else {
            $this->_mode = 'object';
            if (!is_object($options['cachedObject'])) {
                Zend_Cache::throwException('cachedObject option must be an object');
            }
            $this->_object = $options['cachedObject'];
        }
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
    
    public function __call($name, $parameters) 
    {
        // TODO : add some internal tags (to be able to clean a particulier method call or name)
        
        $cacheBool1 = $this->_specificOptions['cacheByDefault'];
        $cacheBool2 = in_array($name, $this->_specificOptions['cachedMethods']);
        $cacheBool3 = in_array($name, $this->_specificOptions['nonCachedMethods']);
        $cache = (($cacheBool1 || $cacheBool2) && (!$cacheBool3));
        if (!$cache) {
            // We do not have not cache
            if ($mode == 'object') {
                return call_user_func_array(array($this->_object, $name), $parameters);
            } else {
                return call_user_func_array(array($this->_class, $name), $parameters);
            }
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
            if ($mode == 'object') {
                $return = call_user_func_array(array($this->_object, $name), $parameters);
            } else {
                $return = call_user_func_array(array($this->_class, $name), $parameters);
            }
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
        return md5($name . serialize($parameters));
    }
                 
}

