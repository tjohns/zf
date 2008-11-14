<?php

require_once 'Zend/Tool/CodeGenerator/Php/Abstract.php';

abstract class Zend_Tool_CodeGenerator_Php_Member_Abstract extends Zend_Tool_CodeGenerator_Php_Abstract
{
    const VISIBILITY_PUBLIC    = 'public';
    const VISIBILITY_PROTECTED = 'protected';
    const VISIBILITY_PRIVATE   = 'private';
    
    protected $_isAbstract = false;
    protected $_isStatic   = false;
    protected $_visibility = self::VISIBILITY_PUBLIC;
    protected $_name = null;

    public function setAbstract($isAbstract)
    {
        $this->_isAbstract = ($isAbstract) ? true : false;
        return $this;
    }
    
    public function isAbstract()
    {
        return $this->_isAbstract;
    }
    
    public function setStatic($isStatic)
    {
        $this->_isStatic = ($isStatic) ? true : false;
        return $this;
    }
    
    public function isStatic()
    {
        return $this->_isStatic;
    }    
    
    public function setVisibility($visibility)
    {
        $this->_visibility = $visibility;
        return $this;
    }
    
    public function getVisibility()
    {
        return $this->_visibility;
    }
    
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }
    
    public function getName()
    {
        return $this->_name;
    }
    
    
}