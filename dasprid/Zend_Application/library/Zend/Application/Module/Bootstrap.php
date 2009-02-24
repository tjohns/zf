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
 * @package    Zend_Application
 * @subpackage Module
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see Zend_Application_Bootstrap_Base
 */
require_once 'Zend/Application/Bootstrap/Base.php';

/**
 * Base bootstrap class for modules
 * 
 * @uses       Zend_Loader_Autoloader_Resource
 * @package    Zend_Application
 * @subpackage Module
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Application_Module_Bootstrap extends Zend_Application_Bootstrap_Base
{
    /**
     * Used auto loader
     *
     * @var Zend_Loader_Autoloader_Resource
     */
    protected $_resourceLoader;

    /**
     * Set module resource loader
     * 
     * @param  Zend_Loader_Autoloader_Resource $loader 
     * @return Zend_Application_Module_Bootstrap
     */
    public function setResourceLoader(Zend_Loader_Autoloader_Resource $loader)
    {
        $this->_resourceLoader = $loader;
        return $this;
    }

    /**
     * Retrieve module resource loader
     * 
     * @return Zend_Loader_Autoloader_Resource
     */
    public function getResourceLoader()
    {
        if (null === $this->_resourceLoader) {
            $class = get_class($this);
            if (preg_match('/^([a-z][a-z0-9]*)_Bootstrap$/i', $class, $matches)) {
                $prefix = $matches[1];
                $r = new ReflectionClass($this);
                $path = $r->getFileName();
                $this->setResourceLoader(new Zend_Application_Module_Autoloader(array(
                    'prefix' => $prefix,
                    'path'   => dirname($path),
                )));
            }
        }
        
        return $this->_resourceLoader;
    }
}
