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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Interface.php 3412 2007-02-14 22:22:35Z darby $
 */

/**
 * @category   Zend
 * @package    Zend_Build_Resource
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Build_AbstractConfigurable implements Zend_Build_Configurable
{
    protected $_config = null;

    /**
     * @see Zend_Build_Configurable::getConfig()
     */
    public function getConfig()
    {
        return $this->_config;
    }
    
    /**
     * @see Zend_Build_Configurable::getConfig()
     */
    public function setConfig(Zend_Config $config)
    {
        $this->_config = $config;
    }
    
    /**
     * @see Zend_Build_Configurable::getConfigurable()
     */
    public function getConfigurable(Zend_Config $config))
    {
        $configurable = new $config->class;
        $configurable->setConfig($config);
        return $resource.configure();
    }
}