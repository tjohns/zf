<?php

namespace Zend\Cache\Pattern;

// Don't extend AbstractPattern because it implements [set|get]Storage
class CaptureCache implements PatternInterface
{

    public function __construct($options)
    {
        Options::setConstructorOptions($this, $options);
    }

    public function setOptions(array $options)
    {
        Options::setOptions($this, $options);
        return $this;
    }

    public function getOptions()
    {
        return array();
    }

    /*
     * old Zend_Cache_Frontend_Capture
     * and Zend_Cache_Backend_Static
     *
     * From ZF 1.x docs:
     * This backend works in concert with Zend_Cache_Frontend_Capture (the two
     * must be used together) to save the output from requests as static files.
     * This means the static files are served directly on subsequent requests
     * without any involvement of PHP or Zend Framework at all.
     */

}
