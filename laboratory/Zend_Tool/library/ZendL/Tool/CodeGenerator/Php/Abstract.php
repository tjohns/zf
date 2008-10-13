<?php

require_once 'ZendL/Tool/CodeGenerator/Abstract.php';

abstract class ZendL_Tool_CodeGenerator_Php_Abstract extends ZendL_Tool_CodeGenerator_Abstract
{
    
    protected $_sourceContent = null;
    
    protected $_isSourceDirty = true;
    
    protected $_indentation = 4;
    
    public function setSourceContent($sourceContent)
    {
        $this->_sourceContent = $sourceContent;
        return $this;
    }
    
    public function getSourceContent()
    {
        return $this->_sourceContent;
    }
    
    public function setSourceDirty($isSourceDirty = true)
    {
        $this->_isSourceDirty = ($isSourceDirty) ? true : false;
        return $this;
    }
    
    public function isSourceDirty()
    {
        return $this->_isSourceDirty;
    }
    
    public function setIndentation($indentation)
    {
        $this->_indentation = $indentation;
        return $this;
    }
    
    public function getIndentation()
    {
        return $this->_indentation;
    }
    
}
