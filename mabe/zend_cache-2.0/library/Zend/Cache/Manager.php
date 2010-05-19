<?php

namespace Zend\Cache;
use \Zend\Cache\Storage\Storable;
use \Zend\Cache\InvalidArgumentException as InvalidArgumentException;

class Manager
{

    /**
     * Array of storage instances
     *
     * @var Zend\Cache\Storage\Storable[]
     */
    protected $_storages = array();

    /**
     * Array of ready made configuration templates for lazy
     * loading storages.
     *
     * @var array
     */
    protected $_storageTemplates = array(
        // Simple Common Default
        'default' => array(
            'adapter' => 'Filesystem',
            'plugins' => array(
                'Serialize',       // use default serializer
                'IgnoreUserAbort'  // don't abort on writing
            ),
            // 'options' => array()   // use default options
        ),

        // TODO: convert old templates to the new structure
        // Null Cache (Enforce Null/Empty Values)
        'skeleton' => array(
            'frontend' => array(
                'name'    => null,
                'options' => array(),
            ),
            'backend' => array(
                'name'    => null,
                'options' => array(),
            ),
        ),
        // Static Page HTML Cache
        'page' => array(
            'frontend' => array(
                'name'    => 'Capture',
                'options' => array(
                    'ignore_user_abort' => true,
                ),
            ),
            'backend' => array(
                'name'    => 'Static',
                'options' => array(
                    'public_dir' => '../public',
                ),
            ),
        ),
        // Tag Cache
        'pagetag' => array(
            'frontend' => array(
                'name'    => 'Core',
                'options' => array(
                    'automatic_serialization' => true,
                    'lifetime' => null
                ),
            ),
            'backend' => array(
                'name'    => 'File',
                'options' => array(
                    'cache_dir' => '../cache',
                    'cache_file_umask' => 0644
                ),
            ),
        ),
    );

    /**
     * Array of pattern instances
     *
     * @var Zend\Cache\Pattern\PatternInterface
     */
    protected $_patterns = array();

    /**
     * Array of ready made configuration templates for lazy
     * loading patterns.
     *
     * @var array
     */
    protected $_patternTemplates = array();

    /**
     * Set a new storage for the Cache Manager to contain
     *
     * @param  string $name
     * @param  Zend\Cache\Storage\Storable $storage
     * @return Zend\Cache\Manager
     */
    public function setStorage($name, Storable $storage)
    {
        $this->_storages[$name] = $storage;
        return $this;
    }

    /**
     * Check if the Cache Manager contains the named storage object, or a named
     * configuration template to lazy load the storage object
     *
     * @param string $name
     * @return bool
     */
    public function hasStorage($name)
    {
        return isset($this->_storages[$name]) || $this->hasStorageTemplate($name);
    }

    /**
     * Fetch the named storage object, or instantiate and return a storage object
     * using a named configuration template
     *
     * @param  string $name
     * @return Zend\Cache\Storage\Storable
     * @throws Zend\Cache\InvalidArgumentException if $name desn't exist
     */
    public function getStorage($name)
    {
        if (isset($this->_storages[$name])) {
            return $this->_storages[$name];
        }

        if (!isset($this->_storageTemplates[$name])) {
            throw new InvalidArgumentException("Storage of name '{$name}' doesn't exist");
        }

        $storage = Storage::factory($this->_storageTemplates[$name]);
        $this->_storages[$name] = $storage;

        return $storage;
    }

    /**
     * Set a named configuration template from which a storage object can later
     * be lazy loaded
     *
     * @param  string $name
     * @param  array $options
     * @return Zend\Cache\Manager
     */
    public function setStorageTemplate($name, $options)
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        } elseif (!is_array($options)) {
            throw new InvalidArgumentException(
                'Options passed must be an associative array '
              . 'or an instance of \\Zend\\Config'
            );
        }

        $this->_storageTemplates[$name] = $options;

        return $this;
    }

    /**
     * Check if the named storage template exist.
     *
     * @param  string $name
     * @return bool
     */
    public function hasStorageTemplate($name)
    {
        return isset($this->_storageTemplates[$name]);
    }

    /**
     * Get the named storage template
     *
     * @param  string $name
     * @return array
     * @throws Zend\Cache\InvalidArgumentException if template not exists
     */
    public function getStorageTemplate($name)
    {
        if (!isset($this->_storageTemplates[$name])) {
            throw new InvalidArgumentException("Storage template '{$name}' doesn't exist");
        }

        return $this->_storageTemplates[$name];
    }

    /**
     * Pass an array containing changes to be applied to a named
     * storage template
     *
     * @param  string $name
     * @param  array $options
     * @return Zend\Cache\Manager
     * @throws Zend\Cache\InvalidArgumentException on invalid options format
     *                                             or if no storage templates with $name exist
     */
    public function mergeStorageTemplate($name, $options)
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        } elseif (!is_array($options)) {
            throw new InvalidArgumentException(
                'Options passed must be in an associative array or instance of Zend_Config'
            );
        }

        if (!isset($this->_storageTemplates[$name])) {
            throw new InvalidArgumentException(
                "A storage configuration template does not exist with the name '{$name}'"
            );
        }

        $this->setStorageTemplate($name, array_merge_recursive(
            $this->_storageTemplates[$name],
            $options
        ));

        return $this;
    }

    /**
     * Set a new pattern for the Cache Manager to contain
     *
     * @param  string $name
     * @param  Zend\Cache\Pattern\PatternInterface $pattern
     * @return Zend\Cache\Manager
     */
    public function setPattern($name, Storable $pattern)
    {
        $this->_patterns[$name] = $pattern;
        return $this;
    }

    /**
     * Check if the Cache Manager contains the named pattern object, or a named
     * configuration template to lazy load the pattern object
     *
     * @param string $name
     * @return bool
     */
    public function hasPattern($name)
    {
        return isset($this->_patterns[$name]) || $this->hasPatternTemplate($name);
    }

    /**
     * Fetch the named pattern object, or instantiate and return a pattern object
     * using a named configuration template
     *
     * @param  string $name
     * @return Zend\Cache\Pattern\PatternInterface
     * @throws Zend\Cache\InvalidArgumentException if $name desn't exist
     */
    public function getPattern($name)
    {
        if (isset($this->_patterns[$name])) {
            return $this->_storages[$name];
        }

        if (!isset($this->_patternTemplates[$name])) {
            throw new InvalidArgumentException("Cache pattern of name '{$name}' doesn't exist");
        }

        $pattern = Pattern::factory(
            $this->_patternTemplates[$name]['pattern'],
            $this->_patternTemplates[$name]['options']
        );
        $this->_patterns[$name] = $pattern;

        return $pattern;
    }

    /**
     * Set a named configuration template from which a pattern object can later
     * be lazy loaded
     *
     * @param  string $name
     * @param  array $options
     * @return Zend\Cache\Manager
     */
    public function setPatternTemplate($name, $pattern, $options)
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        } elseif (!is_array($options)) {
            throw new InvalidArgumentException(
                'Options passed must be an associative array '
              . 'or an instance of \\Zend\\Config'
            );
        }

        $this->_patternTemplates[$name] = array(
            'pattern' => $pattern,
            'options' => $options,
        );

        return $this;
    }

    /**
     * Check if the named pattern template exist.
     *
     * @param  string $name
     * @return bool
     */
    public function hasPatternTemplate($name)
    {
        return isset($this->_patternTemplates[$name]);
    }

    /**
     * Get the named pattern template
     *
     * @param  string $name
     * @return array
     * @throws Zend\Cache\InvalidArgumentException if template not exists
     */
    public function getPatternTemplate($name)
    {
        if (!isset($this->_patternTemplates[$name])) {
            throw new InvalidArgumentException("Pattern template '{$name}' doesn't exist");
        }

        return $this->_patternTemplates[$name];
    }

    /**
     * Pass an array containing changes to be applied to a named
     * pattern template
     *
     * @param  string $name
     * @param  array $options
     * @return Zend\Cache\Manager
     * @throws Zend\Cache\InvalidArgumentException on invalid options format
     *                                             or if no pattern templates with $name exist
     */
    public function mergeStorageTemplate($name, $options)
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        } elseif (!is_array($options)) {
            throw new InvalidArgumentException(
                'Options passed must be in an associative array '
              . 'or instance of \\Zend\\Config'
            );
        }

        if (!isset($this->_patternTemplates[$name])) {
            throw new InvalidArgumentException(
                "A pattern configuration template does not exist with the name '{$name}'"
            );
        }

        $this->setPatternTemplate($name, array_merge_recursive(
            $this->_patternTemplates[$name],
            $options
        ));

        return $this;
    }

}
