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

class Zend_Cache_Frontend_File extends Zend_Cache_Core
{
       
    /**
     * Available options
     * 
     * @var array available options
     */
    static public $availableOptions = array('masterFile'); 
    
    private $_masterFile = null;
    private $_masterFile_mtime = null;
          
    /**
     * Constructor
     * 
     * @param array $options associative array of options
     */
    public function __construct($options = array())
    {
        if (!isset($options['masterFile'])) {
            Zend_Cache::throwException('masterFile option must be set');
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
        parent::__construct($coreOptions);
        if (!($this->_masterFile_mtime = @filemtime($this->_masterFile))) {
            Zend_Cache::throwException('Unable to read masterFile : '.$this->_masterFile);
        }
    }    
    
    /**
     * Set an option
     * 
     * @param string $name name of the option
     * @param mixed $value value of the option
     */
    public function setOption($name, $value)
    {
        if ((!is_string($name)) or (!in_array($name, array_merge(Zend_Cache_Frontend_File::$availableOptions, Zend_Cache_Core::$availableOptions)))) {
            Zend_Cache::throwException("Incorrect option name : $name");
        }
        $property = '_'.$name;
        $this->$property = $value;
    }
    
    public function get($id, $doNotTestCacheValidity = false)
    {
        if (!$doNotTestCacheValidity) {
            if ($this->test($id)) {
                return parent::get($id, true);
            }
            return false;
        }
        return parent::get($id, true);
    }
        
    public function test($id) 
    {
        $lastModified = parent::test($id);
        if ($lastModified) {
            return ($lastModified > $this->_masterFile_mtime);
        }
        return false;
    }
                 
}

