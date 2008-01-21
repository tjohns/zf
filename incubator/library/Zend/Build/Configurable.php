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
 * @package    Zend_Build
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

interface Zend_Build_Configurable 
{ 
    /**
     * Configure the component with Config object set previously
     */ 
    public function configure(); 
 
    /**
     * Set the Config object on this object
     *
     * @param Zend_Config $config Configuration object
     */
    public function setConfig(Zend_Config $config);
    
    /**
     * Get configuration object
     *
     * @return Zend_Config|void
     */ 
    public function getConfig(); 
 
    /**
     * Instantiate component from configuration object
     *
     * @param Zend_Config $config  Configuration object
     * @return object     Instance of the configured component
     */ 
    public static function getConfigurable(); 
} 