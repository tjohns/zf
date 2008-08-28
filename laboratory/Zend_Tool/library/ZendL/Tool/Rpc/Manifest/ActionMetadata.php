<?php

require_once 'ZendL/Tool/Rpc/Manifest/Metadata.php';

class ZendL_Tool_Rpc_Manifest_ActionMetadata extends ZendL_Tool_Rpc_Manifest_Metadata
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
}