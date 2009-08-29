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
     * @param  string $mapperName
     * @param  array  $options
     * @return Zend_Entity_Manager_Interface
     */
    static public function createEntityManager($mapperName, array $options=array())
    {
        if(isset($options['metadataDefinitionPath'])) {
            $metadataFactory = new Zend_Entity_MetadataFactory_Code($options['metadataDefinitionPath']);
        } else {
            throw new Zend_Entity_Exception(
                "No details on the metadata definition of the entity manager was given."
            );
        }

        if(isset($options['metadataCache'])) {
            $metadataFactory = new Zend_Entity_MetadataFactory_Cache($metadataFactory, $options['metadataCache']);
        }
        $options['metadataFactory'] = $metadataFactory;

        switch($mapperName) {
            case 'Db':
                $mapperName = 'Zend_Db_Mapper_Mapper';
                break;
        }

        if(!class_exists($mapperName)) {
            throw new Zend_Entity_Exception(
                "Mapper Class with name '".$mapperName."' does not exist!"
            );
        }

        $mapperFactoryCallback = array($mapperName, "create");
        if(!is_callable($mapperFactoryCallback)) {
            throw new Zend_Entity_Exception(
                "Mapper '".$mapperName."' has no valid 'create' factory callback."
            );
        }

        $mapper = call_user_func_array($mapperFactoryCallback, array($options));

        if(!($mapper instanceof Zend_Entity_MapperAbstract)) {
            throw new Zend_Entity_Exception();
        }

        $options['mapper'] = $mapper;

        if(isset($options['namedQueries']) && is_array($options['namedQueries'])) {
            $namedQueryLoader = new Zend_Loader_PluginLoader();
            foreach($options['namedQueries'] AS $path => $prefix) {
                if(!is_numeric($path)) {
                    $namedQueryLoader->addPrefixPath($path, $prefix);
                }
            }
            $options['namedQueryLoader'] = $namedQueryLoader;
        }

        return new Zend_Entity_Manager($options);
    }
}