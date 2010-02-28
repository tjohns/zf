<?php

namespace \zend\cache\plugin;

class Prefix extends PluginAbstract
{

    protected $_prefix = 'zf-';

    public function getPrifix()
    {
        return $this->_prefix;
    }

    public function setPrefix($prefix)
    {
        $this->_prefix = (string)$prefix;
    }

    // add prefix to all cache ids

}
