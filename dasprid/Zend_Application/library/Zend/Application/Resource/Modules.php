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
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Module bootstrapping resource
 *
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Application_Resource_Modules
{
    /**
     * Initialize modules
     * 
     * @return void
     */
    public function init()
    {
        $bootstrap = $this->getBootstrap();
        $bootstrap->bootstrap('frontcontroller');
        $front = $bootstrap->frontController;

        $modules = $front->getControllerDirectory();
        $default = $front->getDefaultModule();
        foreach (array_keys($modules) as $module) {
            if ($module == $default) {
                continue;
            }

            $path = $front->getModuleDirectory($module);
            $bootstrapPath  = $path . '/Bootstrap.php';
            $bootstrapClass = ucfirst($module) . '_Bootstrap';
            if (file_exists($bootstrapPath)) {
                include_once $bootstrapPath;
                if (!class_exists($bootstrapClass, false)) {
                    throw new Zend_Application_Resource_Exception('Bootstrap file found for module "' . $module . '" but bootstrap class "' . $bootstrapClass . '" not found');
                }
                $moduleBootstrap = new $bootstrapClass($this);
                $moduleBootstrap->bootstrap();
            }
        }
    }
}
