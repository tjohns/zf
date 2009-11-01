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
 * @subpackage MetadataFactory
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Testing Factory that allows external access to the definition map.
 *
 * @uses       Zend_Entity_MetadataFactory_FactoryAbstract
 * @category   Zend
 * @package    Zend_Entity
 * @subpackage MetadataFactory
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Entity_MetadataFactory_Testing extends Zend_Entity_MetadataFactory_FactoryAbstract
{
    /**
     * @var array
     */
    protected $_defMap = array();

    /**
     * Add new definition to testing map.
     *
     * @param Zend_Entity_Definition_Entity $entityDefinition
     */
    public function addDefinition(Zend_Entity_Definition_Entity $entityDefinition)
    {
        $this->_defMap[$entityDefinition->getClass()] = $entityDefinition;
    }

    /**
     * Retrieve an array of all definitions by name.
     *
     * @return array
     */
    public function getDefinitionEntityNames()
    {
        return array_keys($this->_defMap);
    }

    /**
     * Get an Entity Mapper Definition by the name of the Entity
     *
     * @param  string $entityName
     * @return Zend_Entity_Definition_Entity
     */
    public function getDefinitionByEntityName($entityName)
    {
        if(!isset($this->_defMap[$entityName])) {
            require_once "Zend/Entity/Exception.php";
            throw new Zend_Entity_Exception("No Definition for the Entity '".$entityName."' was set.");
        }
        return $this->_defMap[$entityName];
    }

    /**
     * @return string
     */
    public function getCurrentVersionHash()
    {
        $hash = "";
        ksort($this->_defMap);
        foreach($this->_defMap AS $entityName => $entityDef) {
            $hash = md5($hash."-".serialize($entityDef));
        }
        return $hash;
    }
}