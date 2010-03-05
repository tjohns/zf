<?php

namespace zend\cache\storagePlugin;
use \zend\cache\InvalidArgumentException as InvalidArgumentException;

class ExceptionHandler extends StoragePluginAbstract
{

    protected $_exceptionHandler;

    public function __construct(array $options)
    {
        parent::__construct($options);

        if (!$this->_exceptionHandler) {
            throw new InvalidArgumentException("Missing option 'exceptionHandler'");
        }
    }

    public function setExceptionHandler($callback)
    {
        if (!is_callable($callback, true)) {
            throw new InvalidArgumentException('Callback isn\'t callable');
        }
        $this->_exceptionHandler = $callback;
    }

    public function getExceptionHandler()
    {
        return $this->_exceptionHandler;
    }

    // catch all exceptions and call callback
    // don't re-throw exceptions

}
