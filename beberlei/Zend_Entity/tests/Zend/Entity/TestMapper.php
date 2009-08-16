<?php

class Zend_Entity_TestMapper extends Zend_Entity_Mapper_Mapper
{
    /**
     * @param Zend_Entity_Mapper_Loader_LoaderAbstract $loader
     */
    public function setLoader(Zend_Entity_Mapper_Loader_LoaderAbstract $loader)
    {
        $this->_loader = $loader;
    }

    /**
     * @param Zend_Entity_Mapper_Persister_Interface $persister
     */
    public function setPersister($class, Zend_Entity_Mapper_Persister_Interface $persister)
    {
        $this->_persister[$class] = $persister;
    }
}