<?php

class Zend_Entity_Null_Mapper extends Zend_Entity_MapperAbstract
{    
    public function closeConnection() {
    }
    protected function _doSave($entity,$entityName,$entityManager) {
    }
    protected function _doLoad($entityManager,$entityName,$keyValue) {
    }
    public function getTransaction() {
    }
    public function createNativeQuery($sqlQuery,$resultSetMapping,$entityManager) {
    }
    protected function _doDelete($entity,$entityName,$entityManager) {
    }
}