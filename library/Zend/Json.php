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
 * @package    Zend_Json
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Class for encoding to and decoding from JSON.
 *
 * @category   Zend
 * @package    Zend_Json
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Json
{
    /**
     * How objects should be encoded -- arrays or as StdClass
     */
    const TYPE_ARRAY  = 0;
    const TYPE_OBJECT = 1;

    /**
     * Decodes the given $encodedValue string which is
     * encoded in the JSON format
     *
     * @param string $encodedValue Encoded in JSON format
     * @param int $objectDecodeType Optional; flag indicating how to decode
     * objects. See {@link ZJsonDecoder::decode()} for details.
     * @return mixed
     */
    static public function decode($encodedValue, $objectDecodeType = null)
    {
        // @todo Zend::loadClass()
        include_once 'Zend/Json/Decoder.php';
        return Zend_Json_Decoder::decode($encodedValue, $objectDecodeType);
    }


    /**
     * Encode the mixed $valueToEncode into the JSON format
     *
     * NOTE: Object should not contain cycles; the JSON format
     * does not allow object reference.
     *
     * NOTE: Only public variables will be encoded
     *
     * @param mixed $valueToEncode
     * @return string JSON encoded object
     */
    static public function encode($valueToEncode)
    {
        // @todo Zend::loadClass()
        include_once 'Zend/Json/Encoder.php';
    	return Zend_Json_Encoder::encode($valueToEncode);
    }
}

