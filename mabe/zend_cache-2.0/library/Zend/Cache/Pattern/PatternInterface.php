<?php

namespace Zend\Cache\Pattern;
use \Zend\Cache\Storage\Storable;

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
     * return Zend\Cache\Storage\Storable
     */
    public function getStorage();

    /**
     * Set internal cache storage
     *
     * @param Zend\Cache\Storage\Storable $storage
     */
    public function setStorage(Storable $storage);

}
