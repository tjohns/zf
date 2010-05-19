<?php

namespace Zend\Cache\Storage\Plugin;

class Prefix extends AbstractPlugin
{

    protected $_prefix = 'zf-';

    public function getOptions()
    {
        $options = parent::getOptions();
        $options['prefix'] = $this->getPrefix();
        return $options;
    }

    public function getPrefix()
    {
        return $this->_prefix;
    }

    public function setPrefix($prefix)
    {
        $this->_prefix = (string)$prefix;
    }

    // add prefix to all cache ids

}
