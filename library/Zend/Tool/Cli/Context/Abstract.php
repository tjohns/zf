<?php

abstract class Zend_Tool_Cli_Context_Abstract
{

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

}

