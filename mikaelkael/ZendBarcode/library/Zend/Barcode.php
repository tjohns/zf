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
 * @package    Zend_Barcode
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Class for generate Barcode
 *
 * @category   Zend
 * @package    Zend_Barcode
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Barcode
{

    /**
     * Factory for Zend_Barcode_... classes.
     *
     * First argument may be a string containing the base of the adapter class
     * name, e.g. 'int25' corresponds to class Zend_Image_Barcode_Int25.  This
     * is case-insensitive.
     *
     * First argument may alternatively be an object of type Zend_Config.
     * The barcode class base name is read from the 'barcode' property.
     * The barcode config parameters are read from the 'params' property.
     *
     * Second argument is optional and may be an associative array of key-value
     * pairs.  This is used as the argument to the barcode constructor.
     *
     * If the first argument is of type Zend_Config, it is assumed to contain
     * all parameters, and the second argument is ignored.
     *
     * @param  mixed $barcode         String name of base barcode class, or Zend_Config object.
     * @param  mixed $renderer        String name of base barcode class, or Zend_Config object.
     * @param  mixed $barcodeConfig   OPTIONAL; an array or Zend_Config object with barcode parameters.
     * @param  mixed $rendererConfig  OPTIONAL; an array or Zend_Config object with barcode parameters.
     * @return Zend_Barcode
     * @throws Zend_Barcode_Exception
     */
    public static function factory($barcode, $renderer, $barcodeConfig = array (), $rendererConfig = array (), $automaticRenderError = true)
    {
        /*
         * Convert Zend_Config argument to plain string
         * barcode name and separate config object.
         */
        if ($barcode instanceof Zend_Config) {
            if (isset($barcode->barcode->params)) {
                $barcodeConfig = $barcode->barcode->params->toArray();
            }
            if (isset($barcode->barcode->barcode)) {
                $barcode = (string) $barcode->barcode->barcode;
            } else {
                $barcode = null;
            }
            if (isset($barcode->renderer->params)) {
                $rendererConfig = $barcode->renderer->params->toArray();
            }
            if (isset($barcode->renderer->barcode)) {
                $renderer = (string) $barcode->renderer->barcode;
            } else {
                $renderer = null;
            }
        }
        if ($automaticRenderError) {
            try {
                $barcode = self::_makeBarcode($barcode, $barcodeConfig);
                $renderer = self::_makeRenderer($renderer, $rendererConfig);
            } catch (Exception $e) {
                $barcode = self::_makeBarcode('error', array('text' => $e->getMessage()));
                $renderer = self::_makeRenderer($renderer, array());
            }
        } else {
            $barcode = self::_makeBarcode($barcode, $barcodeConfig);
            $renderer = self::_makeRenderer($renderer, $rendererConfig);
        }
        return $renderer->setBarcode($barcode);
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $barcode
     * @param unknown_type $barcodeConfig
     * @return Zend_Barcode_Object
     */
    public static function _makeBarcode($barcode, $barcodeConfig = array ())
    {
        if ($barcode instanceof Zend_Barcode_Object) {
            return $barcode;
        }
        /*
         * Convert Zend_Config argument to plain string
         * barcode name and separate config object.
         */
        if ($barcode instanceof Zend_Config) {
            if (isset($barcode->params)) {
                $barcodeConfig = $barcode->params->toArray();
            }
            if (isset($barcode->barcode)) {
                $barcode = (string) $barcode->barcode;
            } else {
                $barcode = null;
            }
        }
        /*
         * Verify that barcode parameters are in an array.
         */
        if (! is_array($barcodeConfig)) {
            /**
             * @see Zend_Barcode_Exception
             */
            require_once 'Zend/Barcode/Exception.php';
            throw new Zend_Barcode_Exception('Barcode parameters must be in an array or a Zend_Config object');
        }
        /*
         * Verify that an barcode name has been specified.
         */
        if (! is_string($barcode) || empty($barcode)) {
            /**
             * @see Zend_Barcode_Exception
             */
            require_once 'Zend/Barcode/Exception.php';
            throw new Zend_Barcode_Exception('Barcode name must be specified in a string');
        }
        /*
         * Form full barcode class name
         */
        $barcodeNamespace = 'Zend_Barcode_Object';
        if (isset($barcodeConfig['barcodeNamespace'])) {
            $barcodeNamespace = $barcodeConfig['barcodeNamespace'];
        }
        $barcodeName = strtolower($barcodeNamespace . '_' . $barcode);
        $barcodeName = str_replace(' ', '_', ucwords(str_replace('_', ' ', $barcodeName)));
        /*
         * Load the barcode class.  This throws an exception
         * if the specified class cannot be loaded.
         */
        require_once 'Zend/Loader.php';
        @Zend_Loader::loadClass($barcodeName);
        /*
         * Create an instance of the barcode class.
         * Pass the config to the barcode class constructor.
         */
        $bcAdapter = new $barcodeName($barcodeConfig);
        /*
         * Verify that the object created is a descendent of the abstract barcode type.
         */
        if (! $bcAdapter instanceof Zend_Barcode_Object) {
            /**
             * @see Zend_Image_Barcode_Exception
             */
            require_once 'Zend/Barcode/Exception.php';
            throw new Zend_Barcode_Exception("Barcode class '$barcodeName' does not extend Zend_Barcode_Object");
        }
        return $bcAdapter;
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $renderer
     * @param unknown_type $rendererConfig
     * @return Zend_Barcode_Renderer
     */
    public static function _makeRenderer($renderer, $rendererConfig = array ())
    {
        if ($renderer instanceof Zend_Barcode_Renderer) {
            return $renderer;
        }
        /*
         * Convert Zend_Config argument to plain string
         * barcode name and separate config object.
         */
        if ($renderer instanceof Zend_Config) {
            if (isset($renderer->params)) {
                $rendererConfig = $renderer->params->toArray();
            }
            if (isset($renderer->barcode)) {
                $renderer = (string) $renderer->barcode;
            } else {
                $renderer = null;
            }
        }
        /*
         * Verify that barcode parameters are in an array.
         */
        if (! is_array($rendererConfig)) {
            /**
             * @see Zend_Barcode_Exception
             */
            require_once 'Zend/Barcode/Exception.php';
            throw new Zend_Barcode_Exception('Barcode parameters must be in an array or a Zend_Config object');
        }
        /*
         * Verify that an barcode name has been specified.
         */
        if (! is_string($renderer) || empty($renderer)) {
            /**
             * @see Zend_Barcode_Exception
             */
            require_once 'Zend/Barcode/Exception.php';
            throw new Zend_Barcode_Exception('Barcode name must be specified in a string');
        }
        /*
         * Form full barcode class name
         */
        $rendererNamespace = 'Zend_Barcode_Renderer';
        if (isset($rendererConfig['barcodeNamespace'])) {
            $rendererNamespace = $rendererConfig['barcodeNamespace'];
        }
        $rendererName = strtolower($rendererNamespace . '_' . $renderer);
        $rendererName = str_replace(' ', '_', ucwords(str_replace('_', ' ', $rendererName)));
        /*
         * Load the barcode class.  This throws an exception
         * if the specified class cannot be loaded.
         */
        require_once 'Zend/Loader.php';
        @Zend_Loader::loadClass($rendererName);
        /*
         * Create an instance of the barcode class.
         * Pass the config to the barcode class constructor.
         */
        $rdrAdapter = new $rendererName($rendererConfig);
        /*
         * Verify that the object created is a descendent of the abstract barcode type.
         */
        if (! $rdrAdapter instanceof Zend_Barcode_Renderer) {
            /**
             * @see Zend_Barcode_Exception
             */
            require_once 'Zend/Barcode/Exception.php';
            throw new Zend_Barcode_Exception("Barcode class '$rendererName' does not extend Zend_Barcode_Object");
        }
        return $rdrAdapter;
    }

    /**
     * Enter description here...
     *
     * @param string | Zend_Barcode_Object | array | Zend_Config $barcode
     * @param string | Zend_Barcode_Renderer $renderer
     * @param array | Zend_Config $barcodeConfig
     * @param array | Zend_Config $rendererConfig
     */
    public static function render($barcode, $renderer, $barcodeConfig = array (), $rendererConfig = array ())
    {
        self::factory($barcode, $renderer, $barcodeConfig, $rendererConfig)->render();
    }

    /**
     * Enter description here...
     *
     * @param string | Zend_Barcode_Object | array | Zend_Config $barcode
     * @param string | Zend_Barcode_Renderer $renderer
     * @param array | Zend_Config $barcodeConfig
     * @param array | Zend_Config $rendererConfig
     * @return mixed
     */
    public static function draw($barcode, $renderer, $barcodeConfig = array (), $rendererConfig = array ())
    {
        self::factory($barcode, $renderer, $barcodeConfig, $rendererConfig)->draw();
    }

    /**
     * Proxy for setBarcodeFont of Zend_Barcode_Object
     * @param string $font
     * @eturn void
     */
    public static function setBarcodeFont($font)
    {
        require_once 'Zend/Barcode/Object.php';
        Zend_Barcode_Object::setBarcodeFont($font);
    }
}