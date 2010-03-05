<?php

namespace zend\cache\storagePlugin;

class MTime extends StoragePluginAbstract
{

    public function __construct($options)
    {
        parent::__construct($options);

        // @todo: throw exception if storage can't store arrays
    }

    // On some storages the last modification time isn't accessable
    // This plugin stores data as an array like $data = array($data, time()) to make it accessable

}
