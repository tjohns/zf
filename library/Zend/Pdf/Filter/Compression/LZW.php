<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to version 1.0 of the Zend Framework
 * license, that is bundled with this package in the file LICENSE, and
 * is available through the world-wide-web at the following URL:
 * http://www.zend.com/license/framework/1_0.txt. If you did not receive
 * a copy of the Zend Framework license and are unable to obtain it
 * through the world-wide-web, please send a note to license@zend.com
 * so we can mail you a copy immediately.
 *
 * @package    Zend_Pdf
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */

/** Zend_Pdf_Filter_Compression */
require_once 'Zend/Pdf/Filter/Compression.php';


/**
 * LZW stream filter
 *
 * @package    Zend_Pdf
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class Zend_Pdf_Filter_Compression_LZW extends Zend_Pdf_Filter_Compression
{
    /**
     * Get EarlyChange decode param value
     *
     * @param array $params
     * @return integer
     * @throws Zend_Pdf_Exception
     */
    private static function _getEarlyChangeValue(&$params)
    {
        if (isset($params['EarlyChange'])) {
            $earlyChange = $params['EarlyChange'];

            if ($earlyChange != 0  &&  $earlyChange != 1) {
                throw new Zend_Pdf_Exception('Invalid value of \'EarlyChange\' decode param - ' . $earlyChange . '.' );
            }
            return $earlyChange;
        } else {
            return 1;
        }
    }


    /**
     * Encode data
     *
     * @param string $data
     * @param array $params
     * @return string
     * @throws Zend_Pdf_Exception
     */
    public static function encode(&$data, $params = null)
    {
        if ($params != null) {
            $data = self::_applyEncodeParams($data, $params);
        }

        throw new Zend_Pdf_Exception('Not implemented yet');
    }

    /**
     * Decode data
     *
     * @param string $data
     * @param array $params
     * @return string
     * @throws Zend_Pdf_Exception
     */
    public static function decode(&$data, $params = null)
    {
        throw new Zend_Pdf_Exception('Not implemented yet');

        if ($params !== null) {
            return self::_applyDecodeParams($data, $params);
        } else {
            return $data;
        }
    }
}
