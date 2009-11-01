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
 * @package    Zend_Entity
 * @subpackage StateTransformer
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Generic Type Converter for several basic types which can be overwritten by specific platforms/adapters.
 *
 * @category   Zend
 * @package    Zend_Entity
 * @subpackage StateTransformer
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Entity_StateTransformer_TypeConverter
{
    /**
     *
     * @param string $type
     * @param mixed $value
     * @param bool $nullable
     * @return mixed
     */
    public function convertToStorageType($type, $value, $nullable=false)
    {
        if($value === null && $nullable) {
            return $value;
        }

        switch($type) {
            case Zend_Entity_Definition_Property::TYPE_BOOLEAN:
                $value = ($value===true||$value===1)?1:0;
                break;
            case Zend_Entity_Definition_Property::TYPE_INT:
                $value = (int)$value;
                break;
            case Zend_Entity_Definition_Property::TYPE_STRING:
            case Zend_Entity_Definition_Property::TYPE_TEXT:
                $value = (string)$value;
                break;
            case Zend_Entity_Definition_Property::TYPE_FLOAT:
                $value = (float)$value;
                break;
            case Zend_Entity_Definition_Property::TYPE_DATE:
                /* @var datetime $propertyValue */
                $value = $value->format('Y-m-d');
                break;
            case Zend_Entity_Definition_Property::TYPE_DATETIME:
                /* @var datetime $propertyValue */
                $value = $value->format('Y-m-d H:i:s');
                break;
            case Zend_Entity_Definition_Property::TYPE_TIMESTAMP:
                /* @var datetime $propertyValue */
                $value = $value->format('U');
                break;
            case Zend_Entity_Definition_Property::TYPE_ARRAY:
                $value = Zend_Entity_StateTransformer_XmlSerializer::toXml($value);
                break;
            case Zend_Entity_Definition_Property::TYPE_BINARY:
                // hum?
                break;

        }
        return $value;
    }

    /**
     *
     * @param string $type
     * @param mixed $value
     * @param boolean $nullable
     * @return mixed
     */
    public function convertToPhpType($type, $value, $nullable=false)
    {
        if($value === null && $nullable) {
            return $value;
        }

        switch($type) {
            case Zend_Entity_Definition_Property::TYPE_BOOLEAN:
                $value = ((int)$value==1)?true:false;
                break;
            case Zend_Entity_Definition_Property::TYPE_INT:
                $value = (int)$value;
                break;
            case Zend_Entity_Definition_Property::TYPE_STRING:
                $value = (string)$value;
                break;
            case Zend_Entity_Definition_Property::TYPE_FLOAT:
                $value = (float)$value;
                break;
            case Zend_Entity_Definition_Property::TYPE_DATE:
            case Zend_Entity_Definition_Property::TYPE_DATETIME:
                $value = new DateTime($value);
                break;
            case Zend_Entity_Definition_Property::TYPE_TIMESTAMP:
                $value = (int)$value;
                $value = new DateTime('@'.$value);
                break;
            case Zend_Entity_Definition_Property::TYPE_ARRAY:
                $value = Zend_Entity_StateTransformer_XmlSerializer::fromXml($value);
                break;
        }
        return $value;
    }
}