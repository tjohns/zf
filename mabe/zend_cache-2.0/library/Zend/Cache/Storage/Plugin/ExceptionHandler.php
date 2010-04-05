<?php

namespace Zend\Cache\Storage\Plugin;
use \Zend\Cache\Storage\AbstractPlugin;
use \Zend\Cache\InvalidArgumentException;

class ExceptionHandler extends AbstractPlugin
{

    protected $_exceptionHandler;

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
        return $options;
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
