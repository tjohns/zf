<?php

class Zend_Entity_TestMapper extends Zend_Db_Mapper_Mapper
{
    /**
     * @param string $fetchMode
     * @param Zend_Db_Mapper_Loader_LoaderAbstract $loader
     */
    public function setLoader($fetchMode, Zend_Db_Mapper_Loader_LoaderAbstract $loader)
    {
        $this->_loaders[$fetchMode] = $loader;
    }

    /**
     * @param Zend_Db_Mapper_Persister_Interface $persister
     */
    public function setPersister($class, Zend_Db_Mapper_Persister_Interface $persister)
    {
        $this->_persister[$class] = $persister;
    }
}