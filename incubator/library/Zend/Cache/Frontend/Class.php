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
 * @package    Zend_Cache
 * @subpackage Frontend
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
 
/**
 * Zend_Cache_Core
 */
require_once 'Zend/Cache/Core.php';


/**
 * @package    Zend_Cache
 * @subpackage Frontend
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Cache_Frontend_Class extends Zend_Cache_Core
{
       
    /**
     * Available options
     * 
     * ====> (string) cachedClass :
     * - if set to a class name, we will cache an abstract class and will use only static calls
     * - 'cachedClass' or 'cachedObject' has to be set (but not both !)
     * 
     * ====> (mixed) cachedObject :
     * - if set to an object, we will cache this object methods
     * - 'cachedClass' or 'cachedObject' has to be set (but not both !)
     * 
     * ====> (boolean) cacheByDefault : 
     * - if true, method calls will be cached by default
     * 
     * ====> (array) cachedMethods :
     * - an array of method names which will be cached (even if cacheByDefault = false)
     * 
     * ====> (array) nonCachedMethods :
     * - an array of method names which won't be cached (even if cacheByDefault = true)
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
    
    /**
     * Caching mode : 'class' or 'object'
     *
     * @var string
     */
    private $_mode = null;
    
    /**
     * The name of the cached abstract class (if mode == 'class')
     * 
     * @var string
     */
    private $_class = null;
    
    /**
     * The cached object (if mode == 'object')
     * 
     * @var mixed
     */
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
                parent::setOption($name, $value);
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
    
    /**
     * Main method : call the specified method or get the result from cache
     * 
     * @param string $name method name
     * @param array $parameters method parameters
     * @return mixed result
     */
    public function __call($name, $parameters) 
    {
        $cacheBool1 = $this->_specificOptions['cacheByDefault'];
        $cacheBool2 = in_array($name, $this->_specificOptions['cachedMethods']);
        $cacheBool3 = in_array($name, $this->_specificOptions['nonCachedMethods']);
        $cache = (($cacheBool1 || $cacheBool2) && (!$cacheBool3));
        if (!$cache) {
            // We do not have not cache
            if ($this->_mode == 'object') {
                return call_user_func_array(array($this->_object, $name), $parameters);
            }
            return call_user_func_array(array($this->_class, $name), $parameters);
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
            if ($this->_mode == 'object') {
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
    
    /**
     * Make a cache id from the method name and parameters
     * 
     * @param string $name method name
     * @param array $parameters method parameters
     * @return string cache id
     */        
    private function _makeId($name, $parameters) 
    {
        return md5($name . serialize($parameters));
    }
                 
}
