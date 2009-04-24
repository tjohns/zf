<?php
/**
 * Mapper
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 * 
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to kontakt@beberlei.de so we can send you a copy immediately.
 *
 * @category   Zend
 * @category   Zend_Entity
 * @copyright  Copyright (c) 2009 Benjamin Eberlei
 * @license    New BSD License
 */

abstract class Zend_Entity_Mapper_Definition_Property_Abstract implements Zend_Entity_Mapper_Definition_Property_Interface
{
    /**
     * @var string
     */
    protected $propertyName;

    /**
     * Construct a property and call existing methods for all options if present.
     *
     * @param string $propertyName
     * @param array $options
     */
    public function __construct($propertyName, $options=array())
    {
        $this->setPropertyName($propertyName);
        if(is_array($options)) {
            foreach($options AS $k => $v) {
                $method = "set".ucfirst($k);
                if(method_exists($this, $method)) {
                    call_user_func_array(array($this, $method), array($v));
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getPropertyName()
    {
        return $this->propertyName;
    }

    /**
     * @param string $propertyName
     */
    public function setPropertyName($propertyName)
    {
        $this->propertyName = $propertyName;
    }
}