<?php

class Zend_Entity_Mapper_Persister_CollectionCascade extends Zend_Entity_Mapper_Persister_EntityCascade
{
    protected $_collections = array();

    /**
     * Initialize is called once on each persister to gather information on how to perform the persist operation.
     *
     * @param  Zend_Entity_Mapper_Definition_Entity $entityDef
     * @param  Zend_Entity_Resource_Interface     $defMap
     * @return void
     */
    public function initialize(Zend_Entity_Mapper_Definition_Entity $entityDef, Zend_Entity_Resource_Interface $defMap)
    {
        parent::initialize($entityDef, $defMap);

        foreach($entityDef->getExtensions() AS $extension) {
            // TODO: Inverse relation side is never cascaded!

            switch($extension->getCascade()) {
                case Zend_Entity_Mapper_Definition_Property::CASCADE_ALL:
                case Zend_Entity_Mapper_Definition_Property::CASCADE_SAVE:
                    $this->_collections[] = $extension;
                    break;
            }
        }
    }

    /**
     * Save entity into persistence based on the persisters scope
     *
     * @param  Zend_Entity_Interface $entity
     * @param  Zend_Entity_Manager $entityManager
     * @return void
     */
    public function save(Zend_Entity_Interface $entity, Zend_Entity_Manager $entityManager)
    {
        parent::save($entity, $entityManager);

        if(count($this->_collections) > 0) {
            $db = $entityManager->getAdapter();
            $entityState = $entity->getState();
            foreach($this->_collections AS $colDef) {
                $propertyName = $colDef->getPropertyName();
                if(!isset($entityState[$propertyName])) {
                    continue;
                }
                $collection = $entityState[$propertyName];
                if(!($collection instanceof Zend_Entity_Collection_Interface)) {
                    continue;
                }
                if($collection->wasLoadedFromDatabase() == false) {
                    continue;
                }

                // TODO: Foreign Key or Mapping Table Relationship?

                foreach($collection AS $relatedEntity) {
                    $entityManager->save($relatedEntity);
                }

                foreach($collection->getAdded() AS $addedEntity) {
                    
                }
            }
        }
    }
}