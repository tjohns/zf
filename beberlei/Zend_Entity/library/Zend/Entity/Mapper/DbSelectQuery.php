<?php

class Zend_Entity_Mapper_DbSelectQuery extends Zend_Entity_Query_AbstractQuery
{
    /**
     * @var Zend_Entity_Mapper_Select
     */
    protected $_select = null;

    protected function _initQuery()
    {
        $this->_select = $this->_mapper->select();
    }

    /**
     *
     * @param  string $class
     * @param  string $operation
     * @param  mixed $value
     * @return Zend_Entity_Mapper_DbSelectQuery
     */
    public function joinInner($class, $operation, $value)
    {
        $db = $this->_entityManager->getAdapter();
        $metadataFactory = $this->_entityManager->getMetadataFactory();
        $foreignDefinition = $metadataFactory->getDefinitionByEntityName($class);

        $intersectTable = $collectionDef->getTable();
        if($foreignDefinition->getTable() !== $collectionDef->getTable()) {

            $foreignPrimaryKey = $foreignDefinition->getPrimaryKey()->getKey();

            $intersectOnLhs = $db->quoteIdentifier($intersectTable.".".$relation->getColumnName());
            $intersectOnRhs = $db->quoteIdentifier($foreignDefinition->getTable().".".$foreignPrimaryKey);
            $intersectOn = $intersectOnLhs." = ".$intersectOnRhs;
        }

        $this->_select->joinInner($foreignTable, $foreignOnClause);

        return $this;
    }

    public function execute()
    {
        
    }
    
    protected function _assembleQuery()
    {
        
    }
}