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
 * @package    Zend_Db
 * @subpackage Mapper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Handle persistence of collections
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Mapper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Mapper_Persister_Collection
{
    /**
     * @var Zend_Entity_Definition_Collection
     */
    protected $_collectionDef = null;

    /**
     * @param Zend_Entity_Definition_Collection $collectionDef
     */
    public function __construct(Zend_Entity_Definition_Collection $collectionDef)
    {
        $this->_collectionDef = $collectionDef;
    }

    /**
     * @todo Optimize the delete statement by using only one call with an IN() reference.
     * @todo Optionally optimize this by using batch inserts on MySQL and Postgres
     * @param mixed $ownerId
     * @param Zend_Entity_Collection_Interface $collection
     * @param Zend_Entity_Manager_Interface $entityManager
     */
    public function persist($ownerId, $collection, $entityManager)
    {
        $db = $entityManager->getMapper()->getAdapter();
        $identityMap = $entityManager->getIdentityMap();
        $collectionDef = $this->_collectionDef;

        $key = $collectionDef->key;
        $foreignKey = $collectionDef->relation->columnName;
        $relatedClass = $collectionDef->relation->class;
        foreach($collection->__ze_getAdded() AS $relatedEntity) {
            if(!($relatedEntity instanceof $relatedClass)) {
                throw new Zend_Entity_InvalidEntityException(
                    "Related entity '".get_class($relatedEntity)."' is not of the type '".$collectionDef->relation->class."'."
                );
            }

            $db->insert($collectionDef->table, array(
                $key => $ownerId,
                $foreignKey => $identityMap->getPrimaryKey($relatedEntity),
            ));
        }
        foreach($collection->__ze_getRemoved() AS $relatedEntity) {
            if(!($relatedEntity instanceof $relatedClass)) {
                throw new Zend_Entity_InvalidEntityException(
                    "Related entity '".get_class($relatedEntity)."' is not of the type '".$collectionDef->relation->class."'."
                );
            }

            $db->delete($collectionDef->table,
                $db->quoteIdentifier($key)." = ".$db->quote($ownerId)." AND ".
                $db->quoteIdentifier($foreignKey)." = ".$db->quote($identityMap->getPrimaryKey($relatedEntity))
            );
        }
    }
}