<?php
require_once 'Zend/Image/Adapter/Abstract.php';
require_once 'Zend/Loader.php';

class Zend_Image_Adapter_Gd extends Zend_Image_Adapter_Abstract
{
    /**
     * The handle of this adapter
     *
     * @var object $_handle
     */
	protected $_handle = null;

    /**
     * The path of the image
     *
     * @var string $_path
     */
	protected $_path = null;


//	protected $_imageInfo = null;

    /**
     * The width of the image
     *
     * @var int $_imageWidth
     */
	protected $_imageWidth = null;

    /**
     * The height of the image
     *
     * @var int $_height
     */
	protected $_imageHeight = null;

    /**
     * The type of the image
     *
     * @var string $_imageType
     */
	protected $_imageType = null;

	/**
     * The amount of bits of the image
     *
     * @var int $_imageBits
     */
	protected $_imageBits = null;

    /**
     * The amount of channels of the image
     *
     * @var int $_imageChannels
     */
	protected $_imageChannels = null;

    /**
     * The mime-type of the image
     *
     * @var string $_imageMime
     */
	protected $_imageMime = null;

	/**
	 * The name of the adapter
	 */
	const NAME = 'Gd';

    /**
     * Checks if the GD-library is available
     *
     * @return boolean True if GD is available
     */
	public function isAvailable() {
		return function_exists ( 'gd_info' );
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
			$this->_loadHandle ();
		}

		$actionObject = new $name ( );
		$actionObject->perform ( $this->_handle, $object );

		/**
		 * @todo: remove when save() and get() methods are implemented.
		 */
//		header('Content-type: image/png');
//		imagepng($this->_handle);
	}

    /**
     * Create/load the handle of this adapter
     *
     */
	protected function _loadHandle() {
		if(null!==$this->_path) {
			if(!$this->setImageInfo($this->_path)) {
				throw new Zend_Image_Exception('No valid image was specified.');
			}
			
			$resource = imagecreatefromstring(file_get_contents($this->_path));
			$this->_handle = new Zend_Image_Adapter_Gd_Handle($resource);
		} else {
			throw new Zend_Image_Exception("Could not load handle");
		}
	}

	/**
     * Set the image info on this adapter
     *
     * @param string $path The path of the image of the info requested
     */
	protected function setImageInfo($path) {
		$info = getimagesize ($path);
		if(!$info) {
			return false;
		}
    
		$params = array('width'   => $info[0],
				        'height'  => $info[1],
				        'type'    => $info[2],
				        'bits'    => $info['bits']);
		
		if(isset($info['channels'])) {
			$params['channels'] = $info['channels'];
		}
		
		$this->_handle->setInfo($params);
	}

	/**
	 * Get the image as a string.
	 *
	 * @param string $format (Optional) The format or mimetype to return
	 */
	public function getImage($format='png') {
	   switch ($format) {
            case 'image/png':
            case 'png':
            	ob_start();
                imagepng($this->_handle->getResource());
                return ob_get_flush();
                break;
	   }
	}

	/**
     * Sets the path of an image to the adapter
     *
     * @param string $path The path to the image
     */
	public function setPath($path) {
		$this->_path = $path;
	}
}