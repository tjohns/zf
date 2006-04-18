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
 * @package    Zend_Cache
 * @copyright  Copyright (c) 2006 Fabien MARTY, Mislav MAROHNIC
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
abstract class Zend_Cache 
{

    static public $availableFrontends = array('Core', 'Output', 'Class', 'File', 'Function');
    static public $availableBackends = array('File', 'Sqlite');
        
    /**
     * Factory
     * 
     * @param string $frontend frontend name
     * @param string $backend backend name
     * @param array $frontendOptions associative array of options for the corresponding frontend constructor
     * @param array $backendOptions associative array of options for the corresponding backend constructor
     */
    static public function factory($frontend, $backend, $frontendOptions = array(), $backendOptions = array())
    {
        
        // because lowercase will fail
        $frontend = @ucfirst($frontend);
        $backend = @ucfirst($backend);
        
        if (!in_array($frontend, Zend_Cache::$availableFrontends)) {
            Zend_Cache::throwException("Incorrect frontend ($frontend)");
        }
        if (!in_array($backend, Zend_Cache::$availableBackends)) {
            Zend_Cache::throwException("Incorrect backend ($backend)");
        }
        
        // For perfs reasons, with frontend == 'Core', we can interact with the Core itself
        $frontendClass = 'Zend_Cache_' . ($frontend != 'Core' ? 'Frontend_' : '') . $frontend;
        
        $backendClass = 'Zend_Cache_Backend_' . $backend;
        
        // For perfs reasons, we do not use the Zend::loadClass() method
        // (security controls are explicit)
        require_once(str_replace('_', DIRECTORY_SEPARATOR, $frontendClass) . '.php');
        require_once(str_replace('_', DIRECTORY_SEPARATOR, $backendClass) . '.php');
        
        $frontendObject = new $frontendClass($frontendOptions);
        $backendObject = new $backendClass($backendOptions);
        $frontendObject->setBackend($backendObject);
        return $frontendObject;
        
    }     
    
    static public function throwException($msg)
    {
        // For perfs reasons, we use this dynamic inclusion
        require_once('Zend/Cache/Exception.php');
        throw new Zend_Cache_Exception($msg);
    }
    
}
