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

/** Zend_Pdf_Filter */
require_once 'Zend/Pdf/Filter.php';


/**
 * ASCII85 stream filter
 *
 * @package    Zend_Pdf
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
abstract class Zend_Pdf_Filter_Compression extends Zend_Pdf_Filter
{
    /**
     * Get Predictor decode param value
     *
     * @param array $params
     * @return integer
     * @throws Zend_Pdf_Exception
     */
    private static function _getPredictorValue(&$params)
    {
        if (isset($params['Predictor'])) {
            $predictor = $params['Predictor'];

            if ($predictor != 1   &&  $predictor != 2   &&
                $predictor != 10  &&  $predictor != 11  &&   $predictor != 12  &&
                $predictor != 13  &&  $predictor != 14  &&   $predictor != 15) {
                throw new Zend_Pdf_Exception('Invalid value of \'Predictor\' decode param - ' . $predictor . '.' );
            }
            return $predictor;
        } else {
            return 1;
        }
    }

    /**
     * Get Colors decode param value
     *
     * @param array $params
     * @return integer
     * @throws Zend_Pdf_Exception
     */
    private static function _getColorsValue(&$params)
    {
        if (isset($params['Colors'])) {
            $colors = $params['Colors'];

            if ($colors != 1  &&  $colors != 2  &&  $colors != 3  &&  $colors != 4) {
                throw new Zend_Pdf_Exception('Invalid value of \'Color\' decode param - ' . $colors . '.' );
            }
            return $colors;
        } else {
            return 1;
        }
    }

    /**
     * Get BitsPerComponent decode param value
     *
     * @param array $params
     * @return integer
     * @throws Zend_Pdf_Exception
     */
    private static function _getBitsPerComponentValue(&$params)
    {
        if (isset($params['BitsPerComponent'])) {
            $bitsPerComponent = $params['BitsPerComponent'];

            if ($bitsPerComponent != 1  &&  $bitsPerComponent != 2  &&
                $bitsPerComponent != 4  &&  $bitsPerComponent != 8  &&
                $bitsPerComponent != 16 ) {
                throw new Zend_Pdf_Exception('Invalid value of \'BitsPerComponent\' decode param - ' . $bitsPerComponent . '.' );
            }
            return $bitsPerComponent;
        } else {
            return 8;
        }
    }

    /**
     * Get Columns decode param value
     *
     * @param array $params
     * @return integer
     */
    private static function _getColumnsValue(&$params)
    {
        if (isset($params['Columns'])) {
            return $params['Columns'];
        } else {
            return 1;
        }
    }


    /**
     * Convert stream data according to the filter params set before encoding.
     *
     * @param string $data
     * @param array $params
     * @return string
     * @throws Zend_Pdf_Exception
     */
    protected static function _applyEncodeParams(&$data, $params) {
        $predictor        = self::_getPredictorValue($params);
        $colors           = self::_getColorsValue($params);
        $bitsPerComponent = self::_getBitsPerComponentValue($params);
        $columns          = self::_getColumnsValue($params);

        if ($predictor == 1) {
            return $data;
        }


        throw new Zend_Pdf_Exception('Unknown prediction algorithm - ' . $predictor . '.' );
    }

    /**
     * Convert stream data according to the filter params set after decoding.
     *
     * @param string $data
     * @param array $params
     * @return string
     */
    protected function _applyDecodeParams(&$data, $params) {
        $predictor        = self::_getPredictorValue($params);
        $colors           = self::_getColorsValue($params);
        $bitsPerComponent = self::_getBitsPerComponentValue($params);
        $columns          = self::_getColumnsValue($params);

        $bitsPerSample    = $bitsPerComponent*$colors;
        $byetsPerRow      = ceil($bitsPerSample*$columns/8);
        $output           = '';
        $offset           = 0;

        /** None prediction */
        if ($predictor == 1) {
            return $data;
        }

        /** TIFF Predictor 2 */
        if ($predictor == 2) {
            throw new Zend_Pdf_Exception('Not implemented yet' );
        }

        // PNG prediction functions also insert algorithm tag for each row
        $rows = ceil(strlen($data)/($byetsPerRow + 1));

        /** PNG prediction (Sub on all rows) */
        if ($predictor == 12) {
            $lastRow = array();
            for ($count = 0; $count < $rows; $count++) {
                if (ord($data{$offset++}) != 0x02) {
                    throw new Zend_Pdf_Exception(sprintf('Wrong algorithm tag. Offset - 0x%08X. Must be 0x02 (PNG Sub prediction) instead of 0x%02X.', --$offset, $data{$offset}));
                }

                for ($count2 = 0; $count2 < $byetsPerRow  &&  $offset < strlen($data); $count2++) {
                    if ($count == 0) {
                        $newByte = ord($data{$offset++});
                    } else {
                        $newByte = (ord($data{$offset++}) +  $lastRow[$count2]) & 0xFF;
                    }
                    $lastRow[$count2] = $newByte;
                    $output .= chr($newByte);
                }
            }
            return $output;
        }

        throw new Zend_Pdf_Exception('Unknown prediction algorithm - ' . $predictor . '.' );
    }
}
