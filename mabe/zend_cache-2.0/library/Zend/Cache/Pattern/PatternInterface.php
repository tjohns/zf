<?php

namespace Zend\Cache\Pattern;
use \Zend\Cache\Storage\Adaptable;

interface PatternInterface
{

    /**
     * Constructor
     *
     * @param array|Zend_Config $options
     */
    public function __construct($options);

    /**
     * Set pattern options
     *
     * @param array $options
     * @return Zend\Cache\Pattern\PatternInterface
     */
    public function setOptions(array $options);

    /**
     * Get all pattern options
     *
     * return array
     */
    public function getOptions();

    /**
     * Get internal cache storage
     *
     * return Zend\Cache\Storage\Adaptable
     */
    public function getStorage();

    /**
     * Set internal cache storage
     *
     * @param Zend\Cache\Storage\Adaptable $storage
     * @return Zend\Cache\Pattern\PatternInterface
     */
    public function setStorage($storage);

}
