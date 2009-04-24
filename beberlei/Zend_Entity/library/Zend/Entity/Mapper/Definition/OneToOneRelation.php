<?php

class Zend_Entity_Mapper_Definition_OneToOneRelation extends Zend_Entity_Mapper_Definition_AbstractRelation
{
    /**
     * Compile OneToOne Relation Element
     *
     * @param Zend_Entity_Mapper_Definition_Entity $entityDef
     * @param Zend_Entity_Resource_Interface $map
     */
    public function compile(Zend_Entity_Mapper_Definition_Entity $entityDef, Zend_Entity_Resource_Interface $map)
    {
        parent::compile($entityDef, $map);

        if($this->getForeignKey() == null) {
            $foreignDef = $map->getDefinitionByEntityName($this->getClass());
            $this->setForeignKey($foreignDef->getPrimaryKey()->getColumnName());
        }
    }
}