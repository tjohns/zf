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

class Zend_Entity_Mapper_Persister_EntityCascade extends Zend_Entity_Mapper_Persister_Simple
{
    /**
     * Save entity into persistence based on the persisters scope
     *
     * @param  Zend_Entity_Interface $entity
     * @param  Zend_Entity_Manager $entityManager
     * @return void
     */
    public function save(Zend_Entity_Interface $entity, Zend_Entity_Manager $entityManager)
    {
        if(count($this->_relations) > 0) {
            $entityState = $entity->getState();
            foreach($this->_relations AS $relation) {
                switch($relation->getCascade()) {
                    case Zend_Entity_Mapper_Definition_Property::CASCADE_ALL:
                    case Zend_Entity_Mapper_Definition_Property::CASCADE_SAVE:
                        $propertyName  = $relation->getPropertyName();
                        $relatedObject = $entityState[$propertyName];
                        $entityManager->save($relatedObject);
                        break;
                }
            }
        }
        parent::save($entity, $entityManager);
    }

    /**
     * Remove entity from persistence based on the persisters scope
     *
     * @param  Zend_Entity_Interface $entity
     * @param  Zend_Entity_Manager $entityManager
     * @return void
     */
    public function delete(Zend_Entity_Interface $entity, Zend_Entity_Manager $entityManager)
    {
        parent::delete($entity, $entityManager);

        if(count($this->_relations) > 0) {
            $entityState = $entity->getState();
            foreach($this->_relations AS $relation) {
                switch($relation->getCascade()) {
                    case Zend_Entity_Mapper_Definition_Property::CASCADE_ALL:
                    case Zend_Entity_Mapper_Definition_Property::CASCADE_DELETE:
                        $propertyName  = $relation->getPropertyName();
                        $relatedObject = $entityState[$propertyName];
                        $entityManager->delete($relatedObject);
                        break;
                }
            }
        }
    }
}