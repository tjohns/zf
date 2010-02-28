<?php

namespace \zend\cache\plugin;
use \zend\cache\adapter\AdapterInterface as AdapterInterface;
use \zend\cache\InvalidArgumentException as InvalidArgumentException;

class Tagging extends PluginAbstract
{

    /**
     * Cache adapter to store tags
     *
     * @var \zend\cache\adapter\AdapterInterface
     */
    protected $_tagHandler = null;

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
    protected $_tag2IdPrefix = 'zf-tag2key-';

    public function getTagHandler()
    {
        if ($this->_tagHandler === null) {
            return $this;
        }

        return $this->_taghandler;
    }

    public function setTagHandler(AdapterInterface $tagHandler)
    {
        $this->_tagHandler = $tagHandler;
    }

    public function resetTagHandler()
    {
        $this->_tagHandler = null;
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
        $capabilities = $this->_innerAdapter->getCapabilities();
        $capabilities['tagging'] = true;
        return $capabilities;
    }

    // implement tagging

}
