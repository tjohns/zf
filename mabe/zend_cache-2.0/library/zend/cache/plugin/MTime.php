<?php

namespace zend\cache\plugin;

class MTime extends PluginAbstract
{

    public function __construct($options)
    {
        parent::__construct($options);

        // @todo: throw exception if adapter can't store arrays
    }

    // On some adapters the last modification time isn't accessable
    // This plugin stores data as an array like $data = array($data, time()) to make it accessable

}
