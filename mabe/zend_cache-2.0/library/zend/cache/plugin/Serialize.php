<?php

namespace zend\cache\plugin;
use \zend\Serializer as Serializer;
use \zend\serializer\adapter\AdapterInterface as SerializerAdapterInterface;

class Serialize extends PluginAbstract
{

    /**
     * Serializer adapter
     *
     * @var \zend\serializer\adapter\AdapterInterface
     */
    protected $_serializer = null;

    public function getSerializer()
    {
        if ($this->_serializer === null) {
            Serializer::getDefaultAdapter();
        }

        return $this->_serializer;
    }

    public function setSerializer(SerializerAdapterInterface $serializer)
    {
        $this->_serializer = $serializer;
    }

    public function getCapabilities()
    {
        $capabilities = $this->_innerAdapter->getCapabilities();
        $capabilities['serialize'] = true;
        return $capabilities;
    }

    // un-/serialize data

}
