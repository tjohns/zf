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
 * @package    Zend_Layout
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Provide Layout support for MVC applications
 *
 * @category   Zend
 * @package    Zend_Layout
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Layout
{
    /**
     * Set layout script to use
     *
     * If set after disableLayout() called, implicitly re-enables layout.
     * 
     * @param  string $name 
     * @return Zend_Layout
     */ 
    public function setLayout($name) 
    {
    } 
 
    /**
     * Get current layout script
     * 
     * @return string
     */ 
    public function getLayout() 
    {
    } 
 
    /**
     * Disable layout
     *
     * @return void
     */ 
    public function disableLayout() 
    {
    } 
 
    /**
     * Set layout script path
     * 
     * @param  string $path 
     * @return Zend_Layout
     */ 
    public function setLayoutPath($path) 
    {
    } 
 
    /**
     * Get current layout script path
     * 
     * @return string
     */ 
    public function getLayoutPath() 
    {
    } 
 
    /**
     * Set view object
     * 
     * @param  Zend_View_Interface $view
     * @return Zend_Layout
     */ 
    public function setView(Zend_View_Interface $view) 
    {
    } 
 
    /**
     * Get current view object
     * 
     * @return Zend_View_Interface
     */ 
    public function getView() 
    {
    } 
 
    /**
     * Set layout variable
     * 
     * @param  string $key 
     * @param  mixed $value 
     * @return void
     */ 
    public function __set($key, $value) 
    {
    } 
 
    /**
     * Get layout variable
     * 
     * @param  string $key
     * @return mixed
     */ 
    public function __get($key) 
    {
    } 
 
    /**
     * Is a layout variable set?
     *
     * @param  string $key
     * @return bool
     */ 
    public function __isset($key) 
    {
    } 
 
    /**
     * Unset a layout variable?
     *
     * @param  string $key
     * @return void
     */ 
    public function __unset($key) 
    {
    } 
 
    /**
     * Assign one or more layout variables
     * 
     * @param  mixed $spec Assoc array or string key; if assoc array, sets each
     * key as a layout variable
     * @param  mixed $value Value if $spec is a key
     * @return Zend_Layout
     */ 
    public function assign($spec, $value = null) 
    {
    } 
 
    /**
     * Render layout
     *
     * Sets internal script path as last path on script path stack, assigns 
     * layout variables to view, determines layout name using inflector, and 
     * renders layout view script.
     * 
     * @param  mixed $name 
     * @return mixed
     */ 
    public function render($name = null) 
    { 
    } 
 
    /**
     * Constructor
     *
     * Accepts either:
     * - A string path to layouts
     * - An array of options
     * - A Zend_Config object with options
     *
     * Layout script path, either as argument or as key in options, is 
     * required.
     *
     * If useMvc flag is false from options, simply sets layout script path. 
     * Otherwise, also instantiates and registers action helper and controller 
     * plugin.
     * 
     * @param mixed $options 
     * @return void
     */ 
    public function __construct($options = null) 
    { 
    } 
}
