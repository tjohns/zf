<?php

class ZendL_Tool_Rpc_Endpoint_Response
{
    
    protected $_content = null;
    
    public function setContent($content)
    {
        $this->_content = $content;
        return $this;
    }
    
    public function getContent()
    {
        return $this->_content;
    }
    
    public function __toString()
    {
        return (string) $this->_content;
    }
    
}