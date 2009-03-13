<?php

require_once 'Zend/Tool/Framework/Manifest/Metadata.php';

class Zend_Tool_Framework_Manifest_ActionMetadata extends Zend_Tool_Framework_Manifest_Metadata
{
    protected $_type = 'Action';
    protected $_actionName = null;

    public function setActionName($actionName)
    {
        $this->_actionName = $actionName;
        return;
    }
    
    public function getActionName()
    {
        return $this->_actionName;
    }
    
    public function __toString()
    {
        return preg_replace('#\n$#', '', parent::__toString()) 
             . '   (ActionName: ' . $this->_actionName 
             . ')';
          
    }
    
}