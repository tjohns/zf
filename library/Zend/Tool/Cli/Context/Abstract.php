<?php

abstract class Zend_Tool_Cli_Context_Abstract
{

    /**
     * @var unknown_type
     */
    protected $_isShortCircuited = false;
    
    /**
     * @var bool
     */
    protected $_isExecutable = false;
    
    /**
     * @var Zend_Build_Manifest
     */
    protected $_buildManifest = null;
    
    /**
     * @var array
     */
    protected $_arguments = array();

    public function setBuildManifest(Zend_Build_Manifest $buildManifest)
    {
    	$this->_buildManifest = $buildManifest;
    	return $this;
    }
    
    public function setArguments($arguments)
    {
        $this->_arguments = $arguments;
        return $this;
    }
    
    public function getRemainingArguments()
    {
        return $this->_arguments;
    }
    
    public function setExecutable($isExecutable = true)
    {
        $this->_isExecutable = $isExecutable;
        return $this;
    }
    
    public function isExecutable()
    {
        return $this->_isExecutable;
    }

    public function setShortCircuited($isShortCircuited = true)
    {
        $this->_isShortCircuited = $isShortCircuited;
        return $this;
    }
    
    public function isShortCircuited()
    {
        return $this->_isShortCircuited;
    }
    
}

