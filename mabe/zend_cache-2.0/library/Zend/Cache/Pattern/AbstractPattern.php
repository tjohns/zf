<?php

namespace Zend\Cache\Pattern;
use \Zend\Options;
use \Zend\Cache\Storage;
use \Zend\Cache\Storage\Adaptable;

abstract class AbstractPattern implements PatternInterface
{

    /**
     * The storage adapter
     *
     * @var Zend\Cache\Storage\Adaptable
     */
    protected $_storage;

    public function __construct($options)
    {
        Options::setConstructorOptions($this, $options);

        if (!$this->_storage) {
            throw InvalidArgumentException("Missing option 'storage'");
        }
    }

    public function setOptions(array $options)
    {
        Options::setOptions($this, $options);
        return $this;
    }

    public function getOptions()
    {
        return array(
            'storage' => $this->getStorage(),
        );
    }

    /**
     * Get cache storage
     *
     * return Zend\Cache\Storage\Adaptable
     */
    public function getStorage()
    {
        return $this->_storage;
    }

    /**
     * Set cache storage
     *
     * @param Zend\Cache\Storage\Adaptable|array|string $storage
     * @return Zend\Cache\Pattern\PatternInterface
     */
    public function setStorage($storage)
    {
        if (is_array($storage)) {
            $storage = Storage::factory($storage);
        } elseif (is_string($storage)) {
            $storage = Storage::adapterFactory($storage);
        } elseif ( !($storage instanceof Adaptable) ) {
            throw new InvalidArgumentException(
                'The storage must be an instanceof Zend\Cache\Storage\Adaptable '
              . 'or an array passed to Zend\Cache\Storage::factory '
              . 'or simply the name of the storage adapter'
            );
        }

        $this->_storage = $storage;
        return $this;
    }

}
