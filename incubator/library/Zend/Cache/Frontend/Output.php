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
class Zend_Cache_Frontend_Output extends Zend_Cache_Core
{
                 
    /**
     * Constructor
     * 
     * @param array $options associative array of options
     */
    public function __construct($options = array())
    {
        parent::__construct($options);
    }
        
        
    /**
     * Start the cache
     *
     * @param string $id cache id
     * @param array $tags TODO : explain tags
     * @param boolean $doNotTestCacheValidity if set to true, the cache validity won't be tested
     * @return boolean true if the cache is hit (false else)
     */
    public function start($id, $doNotTestCacheValidity = false)
    {
        $data = $this->get($id, $doNotTestCacheValidity);
        if ($data !== false) {
            echo($data);
            return true;
        }
        ob_start();
        ob_implicit_flush(false);
        return false;
    }


    /**
     * Stop the cache
     */
    public function end($tags = array())
    {
        $data = ob_get_contents();
        ob_end_clean();
        $this->save($data, null, $tags);
        echo($data);
    }
             
}

