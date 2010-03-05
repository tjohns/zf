<?php

namespace zend\cache;
use \zend\Options as Options;

class CallbackCache
{

    /**
     * Cache adapter
     *
     * @var \zend\cache\adapter\AdapterInterface
     */
    protected $_adapter;

    public function __construct($options)
    {
        Options::setConstructorOptions($this, $options);

        if (!$this->_adapter) {
            throw InvalidArgumentException('Missing option "adapter"');
        }
    }

    public function setOptions(array $options)
    {
        Options::setOptions($this, $options);
    }

    public function getAdapter()
    {
        return $this->_adapter;
    }

    public function setAdapter(adapter\AdapterInterface $adapter)
    {
        $this->_adapter = $adapter;
    }

    public function call($callback, array $args=array(), $adapterOptions=array())
    {

    }

}
