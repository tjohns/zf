<?php

namespace Zend\Cache\Pattern;

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

}
