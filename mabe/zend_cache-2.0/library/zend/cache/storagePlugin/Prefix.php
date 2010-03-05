<?php

namespace zend\cache\storagePlugin;

class Prefix extends StoragePluginAbstract
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
