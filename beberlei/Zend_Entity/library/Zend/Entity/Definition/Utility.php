<?php
/**
 * Mapper
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 * 
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to kontakt@beberlei.de so we can send you a copy immediately.
 *
 * @category   Zend
 * @category   Zend_Entity
 * @copyright  Copyright (c) 2009 Benjamin Eberlei
 * @license    New BSD License
 */

class Zend_Entity_Definition_Utility
{
    /**
     * PluginLoader for Definitions
     *
     * @var Zend_Loader_PluginLoader
     */
    private static $definitionLoader = null;

    /**
     * @var array
     */
    private static $definitionClassNames = array();

    /**
     * Get PluginLoader for Definitions
     *
     * @var Zend_Loader_PluginLoader
     */
    public static function getDefinitionLoader()
    {
        if(self::$definitionLoader == null) {
            self::$definitionLoader = new Zend_Loader_PluginLoader(array(), 'Zend_Entity_Definition');
            self::$definitionLoader->addPrefixPath('Zend_Entity_Definition', 'Zend/Entity/Definition');
        }
        return self::$definitionLoader;
    }

    /**
     * Set Definition Loader
     *
     * @param Zend_Loader_PluginLoader
     * @return void
     */
    public static function setDefinitionLoader(Zend_Loader_PluginLoader $loader=null)
    {
        self::$definitionLoader = $loader;
    }

    /**
     * Load a Definition via the DefinitionLoader Utility
     *
     * @param  string $definitionShortname
     * @param  string $propertyName
     * @param  array $options
     * @return Zend_Entity_Definition_Property_Interface
     */
    public static function loadDefinition($definitionShortname, $propertyName, $options=array())
    {
        if(!isset(self::$definitionClassNames[$definitionShortname])) {
            $definitionLoader = self::getDefinitionLoader();
            $class = $definitionLoader->load($definitionShortname);

            if(!class_exists($class)) {
                require_once "Zend/Entity/Exception.php";
                throw new Zend_Entity_Exception(
                    "Definition Class ".$class." resolved from ".$definitionShortname." does not exist."
                );
            }

            self::$definitionClassNames[$definitionShortname] = $class;
            $definition = new $class($propertyName, $options);

            if(!($definition instanceof Zend_Entity_Definition_Property_Abstract) &&
                !($definition instanceof Zend_Entity_Definition_Table)) {
                require_once "Zend/Entity/Exception.php";
                throw new Zend_Entity_Exception(
                    "Definition ".$definitionShortname." resolved to class ".$class." which does not implement ".
                    "the definition property interface."
                );
            }
        } else {
            $class = self::$definitionClassNames[$definitionShortname];
            $definition = new $class($propertyName, $options);
        }
        return $definition;
    }
}