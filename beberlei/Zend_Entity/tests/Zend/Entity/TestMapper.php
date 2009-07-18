<?php

class Zend_Entity_TestMapper extends Zend_Entity_Mapper_Mapper
{
    /**
     * @param Zend_Entity_Mapper_Loader_Interface $loader
     */
    public function setLoader(Zend_Entity_Mapper_Loader_Interface $loader)
    {
        $this->_loader = $loader;
    }

    /**
     * @param Zend_Entity_Mapper_Persister_Interface $persister
     */
    public function setPersister(Zend_Entity_Mapper_Persister_Interface $persister)
    {
        $this->_persister = $persister;
    }
}