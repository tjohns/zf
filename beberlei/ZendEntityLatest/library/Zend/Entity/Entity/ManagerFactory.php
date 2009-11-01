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
 * @package    Zend_Entity
 * @subpackage Manager
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Entity Manager Factory which creates Entity Managers with their dependencies.
 *
 * @category   Zend
 * @package    Zend_Entity
 * @subpackage Manager
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Entity_ManagerFactory
{
    /**
     * Create a new Entity Manager
     * 
     * @param  array|Zend_Entity $options
     * @return Zend_Entity_Manager_Interface
     */
    static public function createEntityManager($options=array())
    {
        if($options instanceof Zend_Config) {
            $options = $options->toArray();
        }

        if(!isset($options['mapper'])) {
            if(!isset($options['storageOptions'])) {
                throw new Zend_Entity_Exception("No storage options were given to entity manager factory.");
            }
            $mapper = self::createMapper($options['storageOptions']);

            $options['mapper'] = $mapper;
        }

        if(isset($options['namedQueries']) && is_array($options['namedQueries'])) {
            $namedQueryLoader = new Zend_Loader_PluginLoader();
            foreach($options['namedQueries'] AS $path => $prefix) {
                if(!is_numeric($path)) {
                    $namedQueryLoader->addPrefixPath($path, $prefix);
                }
            }
            $options['namedQueryLoader'] = $namedQueryLoader;
        }

        if(isset($options['metadataDefinitionPath'])) {
            $options["metadataFactory"] = self::createMetadataFactoryFromPath($options['metadataDefinitionPath']);
        } else {
            throw new Zend_Entity_Exception(
                "No details on the metadata definition of the entity manager was given."
            );
        }
        
        return new Zend_Entity_Manager($options);
    }

    /**
     * @param array|Zend_Config $storageOptions
     * @return Zend_Entity_MapperAbstract
     */
    static public function createMapper($storageOptions)
    {
        if($storageOptions instanceof Zend_Config) {
            $storageOptions = $storageOptions->toArray();
        }

        if(!isset($storageOptions['backend'])) {
            throw new Zend_Entity_Exception("No backend name was given for creation of Database Mapper.");
        }
        $storageBackendName = $storageOptions['backend'];

        $mapperInstance = null;
        switch(strtolower($storageBackendName)) {
            case 'db':
                $mapperInstance = new Zend_Db_Mapper_Mapper($storageOptions);
                break;
            default:
                if(!class_exists($storageBackendName)) {
                    throw new Zend_Entity_Exception("Invalid Storage Backend given '".$storageBackendName."'.");
                }
                $mapperInstance = new $storageBackendName($storageOptions);
                break;
        }
        if(!($mapperInstance instanceof Zend_Entity_MapperAbstract)) {
            throw new Zend_Entity_Exception();
        }
        return $mapperInstance;
    }

    /**
     * From a filepath infers which type of metadata factory is being asked for and creates it.
     *
     * A directory hints at Code, a file with php extension at PhpFile. More to follow.
     *
     * @param string $path
     */
    static public function createMetadataFactoryFromPath($path)
    {
        if(is_dir($path)) {
            return new Zend_Entity_MetadataFactory_Code($path);
        } else if(is_file($path)) {
            $ext = array_shift(explode(".", $path));
            switch(strtolower($ext)) {
                case 'php':
                    return new Zend_Entity_MetadataFactory_PhpFile($path);
                default:
                    throw new Zend_Entity_Exception("Unknown file type given, cannot create metadata factory from it.");
            }
        } else {
            throw new Zend_Entity_Exception("Invalid path given, cannot create metadata factory from it.");
        }
    }
}