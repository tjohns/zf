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
 * @package    Entity
 * @subpackage Definition
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * The array property represents a PHP array nested in the containing entity.
 *
 * @uses       Zend_Entity_Definition_AbstractArray
 * @category   Zend
 * @package    Entity
 * @subpackage Definition
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Entity_Definition_Array extends Zend_Entity_Definition_AbstractArray
{
    /**
     * @var Zend_Entity_Definition_Property
     */
    public $mapKey = null;

    /**
     * @var Zend_Entity_Definition_Property
     */
    public $element = null;

    /**
     * Set the property Name of the Map Key
     *
     * This option is only relevant for collection maps, not for lists. It defaults to the primary key.
     *
     * @param string $mapKey
     */
    public function setMapKey($mapKey)
    {
        if(!is_string($mapKey)) {
            require_once "Zend/Entity/Exception.php";
            throw new Zend_Entity_Exception(
                "Map-Key option is required to be a string."
            );
        }
        $this->mapKey = $mapKey;
    }

    /**
     * Get the property name of the map key.
     *
     * @return Zend_Entity_Definition_Property
     */
    public function getMapKey()
    {
        return $this->mapKey;
    }

    /**
     * Set the element property for a map-element collection.
     *
     * @param string $element
     * @return void
     */
    public function setElement($element)
    {
        if(!is_string($element)) {
            require_once "Zend/Entity/Exception.php";
            throw new Zend_Entity_Exception(
                "Element option is required to be a string."
            );
        }
        $this->element = $element;
    }

    /**
     * Get element property
     *
     * @return Zend_Entity_Definition_Property
     */
    public function getElement()
    {
        return $this->element;
    }
}