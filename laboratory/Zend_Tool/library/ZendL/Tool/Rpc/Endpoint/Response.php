<?php

class ZendL_Tool_Rpc_Endpoint_Response
{
    
    protected $_content = null;
    protected $_exception = null;
    
    public function setContent($content)
    {
        $this->_content = $content;
        return $this;
    }
    
    public function getContent()
    {
        return $this->_content;
    }
    
    public function isException()
    {
        return isset($this->_exception);
    }
    
    public function setException(Exception $exception)
    {
        $this->_exception = $exception;
        return $this;
    }
    
    public function getException()
    {
        return $this->_exception;
    }
    
    public function __toString()
    {
        return (string) $this->_content;
    }
    
}