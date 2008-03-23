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
 * @version    $Id: $
 */

/**
 * @category   Zend
 * @package    Zend_Build
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Build_Factory
{
    /**
     * DEFAULT_PROFILE_PATH
     *
     * @const string
     */    
    const DEFAULT_PROFILE_PATH = './';

    /**
     * DEFAULT_PROFILE_NAME
     *
     * @const string
     */
    const DEFAULT_PROFILE_NAME = 'zf-project.xml';

    /**
     * Returns the correct project object for the specified project profile
     *
     * @param  string $profileFilePath
     * @param  string $profileFileName
     * @return Zend_Build_Resource_Project
     */
    public static function makeProject($profileFilePath = self::DEFAULT_PROFILE_PATH, $profileFileName = self::DEFAULT_PROFILE_NAME)
    {
        return Zend_Build_Resource_Project::getConfigurable(new Zend_Config_Xml($profileFilePath . $profileFileName));
    }

    /**
     * Returns the correct action as specified by the manifest files
     *
     * @param  Zend_Config $config
     * @return Object
     */
    public static function makeAction(Zend_Config $config)
    {
        return _make();
    }

    /**
     * Returns the correct resource as specified by the manifest files
     * 
     * @param  Zend_Config $config
     * @return Object
     */
    public static function makeResource(Zend_Config $config)
    {
        return _make(self::MF_RESOURCE_TYPE);
    }

    /**
     * _make
     *
     * @param  string $type
     * @param  string $name
     * @throws Zend_Build_Exception
     * @return Zend_Config
     */
    private static function _make($type, $name)
    {
        /**
         * @see Zend_Build_Manifest
         */
        require_once('Zend/Build/Manifest.php');
        $config = Zend_Build_Manifest::getInstance()->getContext($type, $name);
        if (!is_set($config)) {
            /**
             * @see Zend_Build_Exception
             */
            require_once 'Zend/Build/Exception.php';
            throw new Zend_Build_Exception("Action '$name' not found.");
        }

        return Zend_Build_AbstractConfigurable::getConfigurable($config);
    }
}