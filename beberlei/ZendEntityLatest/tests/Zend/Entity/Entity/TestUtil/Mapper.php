<?php

class Zend_Entity_TestUtil_Mapper extends Zend_Entity_MapperAbstract
{
    public $save = array();
    public $delete = array();

    protected function _doLoad($entityManager,$entityName,$keyValue) {
    }
    protected function _doDelete($entity,$entityName,$entityManager) {
        $this->delete[] = array($entityName, $entity);
    }
    public function closeConnection() {
    }
    public function createNativeQuery($sqlQuery,$resultSetMapping,$entityManager) {
    }
    public function refresh($entity,$entityManager) {
    }
    public function getTransaction() {
    }
    public function initializeMappings(Zend_Entity_MetadataFactory_FactoryAbstract $metadataFactory) {
        $this->_mappings = $metadataFactory->transform("Zend_Db_Mapper_Mapping");
    }
    protected function _doSave($entity,$entityName,$entityManager) {
        $this->save[] = array($entityName, $entity);
    }
}