<?php

namespace \zend\cache\plugin;
use \zend\cache\adapter\AdapterInterface as AdapterInterface;

class Levels extends PluginAbstract
{

    protected $_adapters = array();

    public function setAdapter(AdapterInterface $adapter)
    {
        parent::setAdapter($adapter);

        // The main adapter if the first adapter
        $this->_adapters[0] = $this->getInnerAdapter();
    }

    // @todo: handle different priorities
    public function appendAdapter(AdapterInterface $adapter)
    {
        $this->_adapters[] = $adapter;
    }

    /**
     * Get minimum capabilities of all append adapters
     *
     * {@inherit}
     */
    public function getCapabilities()
    {
        $capabilities = array();

        foreach ($this->_adapters as $adapter) {
            foreach ($adapter->getCapabilities() as $k => $v) {
                if (!isset($capabilities[$k])) {
                    $capabilities[$k] = $v;
                } elseif ($capabilities[$k] === true) {
                    $capabilities[$k] = $v;
                }
            }
        }

        return $capabilities;
    }

    // on read:  read from all adapters and on hit break iteration and return result
    // on write: write to all adapters (if enough space)

}
