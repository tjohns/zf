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
 * @copyright  Copyright (c) 2006 Fabien MARTY, Mislav MAROHNIC
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
 
/**
 * Zend_Cache_Core
 */
require_once 'Zend/Cache/Core.php';

class Zend_Cache_Frontend_Class extends Zend_Cache_Core
{
       
    /**
     * Available options
     * 
     * @var array available options
     */
    static public $availableOptions = array('cachedClass', 'cachedObject', 'cacheByDefault', 'cachedMethods', 'nonCachedMethods'); 
    
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
        $coreOptions = $options;
        while (list(, $option) = each(Zend_Cache_Frontend_Class::$availableOptions)) {           
            // we remove frontend specific option
            // TODO : better way with a array_* function ?
            unset($coreOptions[$option]);
        }
        $coreOptions['automaticSerialization'] = true;
        parent::__construct($coreOptions);
    }    
    
    /**
     * Set an option
     * 
     * @param string $name name of the option
     * @param mixed $value value of the option
     */
    public function setOption($name, $value)
    {
        if ((!is_string($name)) or (!in_array($name, array_merge(Zend_Cache_Frontend_Class::$availableOptions, Zend_Cache_Core::$availableOptions)))) {
            Zend_Cache::throwException("Incorrect option name : $name");
        }
        $property = '_'.$name;
        $this->$property = $value;
    }
    
    public function __call($name, $parameters) 
    {
        // TODO : add some internal tags (to be able to clean a particulier method call or name)
        // TODO : deal with cachedMethods and/or cacheBYDefault...
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

