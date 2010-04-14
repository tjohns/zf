<?php

namespace Zend\Cache\Storage\Plugin;
use \Zend\Cache\Storage;
use \Zend\Cache\Storage\AbstractPlugin;
use \Zend\Cache\InvalidArgumentException;

class HandleExceptions extends AbstractPlugin
{

    protected $_exceptionHandler = null;
    protected $_throwExceptions  = false;

    public function __construct(array $options)
    {
        parent::__construct($options);

        if (!$this->_exceptionHandler) {
            throw new InvalidArgumentException("Missing option 'exceptionHandler'");
        }
    }

    public function getOptions()
    {
        $options = parent::getOptions();
        $options['exceptionHandler'] = $this->getExceptionHandler();
        $options['throwExceptions']  = $this->getThrowExceptions();
        return $options;
    }

    public function setExceptionHandler($callback)
    {
        if (!is_callable($callback, true)) {
            throw new InvalidArgumentException('The exception handler must be callable');
        }
        $this->_exceptionHandler = $callback;
    }

    public function getExceptionHandler()
    {
        return $this->_exceptionHandler;
    }

    public function setThrowExceptions($flag)
    {
        $this->_throwExceptions = (bool)$flag;
    }
    
    public function getThrowExceptions()
    {
        return $this->_throwExceptions;
    }
    
    // catch all exceptions, call callback and
    // only re-throw exceptions if throwExceptions is enabled

}
