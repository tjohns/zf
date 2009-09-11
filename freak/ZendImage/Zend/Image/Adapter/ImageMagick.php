<?php

require_once 'Zend/Image/Adapter/Abstract.php';
require_once 'Zend/Loader.php';

class Zend_Image_Adapter_ImageMagick extends Zend_Image_Adapter_Abstract {

    const FILTER_UNDEFINED = Imagick::FILTER_UNDEFINED;
    const FILTER_GAUSSIAN = Imagick::FILTER_GAUSSIAN;

    /**
     *
     * @var Imagick
     */
    protected $_handle;

    public static function isAvailable() {
        return class_exists('Imagick');
    }

    /**
     * Applies an action on the image
     *
     * @param Zend_Image_Action_Abstract $object The object that is applied on the image
     */
    public function apply($object) {
        $name = __CLASS__. '_Action_' . $object->getName ();
        Zend_Loader::loadClass ( $name );

        if (! $this->_handle) {
            $this->_loadHandle();
        }

        $actionObject = new $name();
        $actionObject->perform($this, $object);
    }

    /**
     * Sets the path of an image to the adapter
     *
     * @param string $path The path to the image
     */
    public function setPath($path) {
        $this->_path = $path;
        $this->_loadHandle();
    }

    /**
     *
     * @param string $format Image format
     * @return Imagick
     */
    public function getImage($format = 'png') {
        $this->_handle->setImageFormat($format);
        return $this->_handle;
    }

    /**
     *
     * @return Imagick
     */
    public function getHandle() {
        return $this->_handle;
    }

    /**
     *
     */
    protected function _loadHandle() {

        $this->_handle = new Imagick($this->_path);
    }

    /**
     *
     * @return int height in pixels
     */
    public function getHeight() {
        return $this->_handle->getImageHeight();
    }

    /**
     *
     * @return int width in pixels
     */
    public function getWidth() {
        return $this->_handle->getImageWidth();
    }

    /**
     *
     * @return int length in bytes
     */
    public function getImageLength() {
        return $this->_handle->getImageLength();
    }
    
}