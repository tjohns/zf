<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Measure
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * @category   Zend
 * @package    Zend_Measure
 * @subpackage Zend_Measure_Abstract
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Measure_Abstract //implements Serializable
{

    /**
     * internal plain value in standard unit
     */
    private $_value;


    /**
     * internal original type for this unit
     */
    private $_type;


    /**
     * Returns the internal value
     */
    public function getValue()
    {
        return $this->_value;
    }


    /**
     * Sets the internal value
     */
    protected function setValue($value)
    {
        $this->_value = $value;
    }


    /**
     * Returns the original type
     */
    public function getType()
    {
        return $this->_type;
    }


    /**
     * Sets the original type
     */
    protected function setType($type)
    {
        $this->_type = $type;
    }


    /**
     * Serialize
     */
    public function serialize() {
        return serialize($this);
    }


    /**
     * Throw an exception
     *
     * Note : for performance reasons, the "load" of Zend/Measure/Exception is dynamic
     */
    public static function throwException($message)
    {
        require_once('Zend/Measure/Exception.php');
        throw new Zend_Measure_Exception($message);
    }
}