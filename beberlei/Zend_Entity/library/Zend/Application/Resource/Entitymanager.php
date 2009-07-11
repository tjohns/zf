<?php
/**
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 * It is also available through the world-wide-web at this URL:
 * http://www.opensource.org/licenses/bsd-license.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to kontakt@beberlei.de so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2009 Benjamin Eberlei
 * @license    http://www.opensource.org/licenses/bsd-license.php     New BSD License
 * @author     Benjamin Eberlei (kontakt@beberlei.de)
 */

class Zend_Application_Resource_Entitymanager extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * @var Zend_Entity_Manager_Interface
     */
    protected $_entityManager;

    public function init()
    {
        // Return view so bootstrap will store it in the registry
        return $this->getEntityManager();
    }

    /**
     *
     * @return Zend_Entity_Manager_Interface
     */
    public function getEntityManager()
    {
        if (null === $this->_entityManager) {
            $dbAdapter = $this->getBootstrap()->getResource('Db');
            $options = $this->getOptions();

            if(isset($options['metadataDefinitionPath'])) {
                $resourceMap = new Zend_Entity_MetadataFactory_Code($options['metadataDefinitionPath']);
            } else {
                throw new Zend_Application_Resource_Exception("Entity Manager needs 'metadataDefinitionPath' option.");
            }

            $this->_entityManager = new Zend_Entity_Manager($dbAdapter, array('resource' => $resourceMap));
        }
        return $this->_entityManager;
    }
}