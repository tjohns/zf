<?php

namespace Zend\Cache\Storage\Plugin;
use \Zend\Cache\Storage\AbstractPlugin;
use \Zend\Cache\Storage\Storable;
use \Zend\Cache\InvalidArgumentException;

class Tagging extends AbstractPlugin
{

    /**
     * The tag storage
     *
     * @var null|Zend\Cache\Storage\Storable
     */
    protected $_tagStorage = null;

    /**
     * Key-Prefix of key files
     * - all tags of one key "<prefix><key>"
     *
     * @var string
     */
    protected $_key2TagPrefix = 'zf-key2tag-';

    /**
     * Key-Prefix of tag files
     * - all keys of one tag "<prefix><tag>"
     *
     * @var string
     */
    protected $_tag2KeyPrefix = 'zf-tag2key-';

    public function getOptions()
    {
        $options = parent::getOptions();
        $options['tagStorage']    = $this->getTagStorage();
        $options['key2TagPrefix'] = $this->getKey2TagPrefix();
        $options['tag2KeyPrefix'] = $this->getTag2KeyPrefix();
        return $options;
    }

    public function getTagStorage()
    {
        if ($this->_tagStorage === null) {
            return $this;
        }

        return $this->_tagStorage;
    }

    public function setTagStorage(Storable $tagStorage)
    {
        $this->_tagStorage = $tagStorage;
    }

    public function resetTagStorage()
    {
        $this->_tagStorage = null;
    }

    public function getKey2TagPrefix()
    {
        return $this->_key2tagPrefix;
    }

    public function setKey2TagPrefix($prefix)
    {
        $prefix = (string)$prefix;
        if (!$prefix) {
            throw new InvalidArgumentException('The key2tagPrefix can\'t be empty');
        }

        $this->_key2TagPrefix = $prefix;
    }

    public function getTag2KeyPrefix()
    {
        return $this->_tag2KeyPrefix;
    }

    public function setTag2KeyPrefix($prefix)
    {
        $prefix = (string)$prefix;
        if (!$prefix) {
            throw new InvalidArgumentException('The tag2KeyPrefix can\'t be empty');
        }

        $this->_tag2KeyPrefix = $prefix;
    }

    public function getCapabilities()
    {
        $capabilities = $this->getStorage()->getCapabilities();
        $capabilities['tagging'] = true;
        return $capabilities;
    }

    // implement tagging

}
