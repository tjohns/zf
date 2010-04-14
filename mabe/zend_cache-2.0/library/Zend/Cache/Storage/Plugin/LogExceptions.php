<?php

namespace Zend\Cache\Storage\Plugin;
use \Zend\Cache\Storage;
use \Zend\Cache\Storage\AbstractPlugin;
use \Zend\Log as Logger;

class LogExceptions extends AbstractPlugin
{

    protected $_exceptionLogger = null;
    protected $_throwExceptions = false;
    
    public function __construct()
    {
        
    }
    
    public function setExceptionLogger(Logger $logger)
    {
        $this->_exceptionLogger = $logger;
    }
    
    public function getExceptionLogger()
    {
        if ($this->_exceptionLogger === null) {
            // TODO: autodetect by Zend\Registry
        }
        return $this->_exceptionLogger;
    }
    
    public function setThrowExceptions($flag)
    {
        $this->_throwExceptions = (bool)$flag;
    }
    
    public function getThrowExceptions()
    {
        return $this->_throwExceptions;
    }
    
    // catch exceptions, log they and
    // only re-throw exceptions if throwExceptions is enabled

}
