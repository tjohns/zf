<?php
require_once 'Zend/Loader.php';

class Zend_Image
{
    /**
     * Adapter: GD
     */
    const ADAPTER_GD = 'Gd';
    /**
     * Adapter: Imagemick
     */
    const ADAPTER_IMAGEMAGICK = 'ImageMagick';

    /*
*
     * Names of Actions available
     */
    const LINE = 'DrawLine';
    const POLYGON = 'DrawPolygon';
    const ELLIPSE = 'DrawEllipse';
    const ARC = 'DrawArc';
    const ETC = 'MoreToAddHere';
    
    /**
     * Adapter set to use for image operation
     *
     * @var Zend_Image_Adapter
     */
    protected $_adapter = null;

    /**
     * Path of the location of the image
     */
    protected $_imagePath = null;

    /**
     * Loads the image, if a path is given
     *
     * @param string $path (Optional) Path to the image
     */
    public function __construct ($path = null)
    {
        if (! is_array($path)) {
            $this->setImagePath($path);
        }
    }

    /**
     * Sets the adapter to use
     * Currently only GD is available
     * ImageMagick will follow soon
     *
     * @param string   $adapter (Optional) The adapter to use
     * @param boolean  $check (Optional) If ture, check if the adapter is available
     * @throw Zend_Image_Exception When checked for availability of unavailable adapter
     */
    public function setAdapter ($adapter = null, $check = true)
    {
        if ($adapter) {
            $name = 'Zend_Image_Adapter_' . $adapter;
            Zend_Loader::loadClass($name);
            $this->_adapter = new $name();
        } else {
            /* No adapter was set. Attempt to detect. */
            $check = false;
            $this->setAdapter($this->_detectAdapter());
        }
        
        if ($check && ! $this->_adapter->isAvailable()) {
            require_once 'Zend/Image/Exception.php';
            throw new Zend_Image_Exception("Adapter '$adapter' is not available.");
        }
        
        $this->setImagePath();
        return $this;
    }

    /**
     * Detects the adapter to use.
     * Order: GD, ImageMick
     *
     * @return Available adapter
     */
    protected function _detectAdapter ()
    {
        if (function_exists('gd_info')) {
            return self::ADAPTER_GD;
        } elseif(class_exists('Imagick')) {
            return self::ADAPTER_IMAGEMAGICK;
        }
        
        return null;
    }

    /**
     * Set the path of the image
     *
     * @param string   $path (Optional) The path of the image
     * @throw Zend_Image_Exception if path is set on nonexistent adapter
     */
    public function setImagePath ($path = null)
    {
        if (null !== $path) {
            $this->_imagePath = $path;
            if (null !== $this->_adapter) {
                $this->setImagePath();
            }
        } else {
            if (null === $this->_adapter) {
                require_once 'Zend/Image/Exception.php';
                throw new Zend_Image_Exception('Cannot set image path on an adapter that hasn\'t been set.');
            } elseif (! file_exists($this->_imagePath)) {
                require_once 'Zend/Image/Exception.php';
                throw new Zend_Image_Exception('Image path does not exist.');
            }
            $this->_adapter->setPath($this->_imagePath);
        }
        
        return $this;
    }

    /**
     * Perform an action on the image
     * @param mixed $param1
     * @param array $options Options that will be parsed on to the action
     * @return Zend_Image
     * @todo: use plugin loader.
     */
    public function apply ($param1, $param2 = null)
    {
    	if($param2 instanceof Zend_Image_Action_Abstract ) {
    		$object = $param2;
    	} elseif ($param1 instanceof Zend_Image_Action_Abstract) {
            $object = $param1;
        } else {
            $name = 'Zend_Image_Action_' . ucfirst($param1);
            Zend_Loader::loadClass($name);
            $object = new $name($param2);
        }
        
        if (! $this->_adapter) {
            $this->setAdapter();
        }
        
        $this->_adapter->apply($object);
        return $this;
    }
    
    public function __call($action,$arguments) {
    	$this->apply($action,$arguments[0]);
    }
    
    /**
     * Get a string containing the image
     *
     * @param string $format (Optional) The format of the image to return
     * @return void
     */
    public function render ($format = 'png')
    {
        return $this->_adapter->getImage($format);
    }
    
    public function display($format = 'png',$sendHeader=true) {
    	if($sendHeader) {
   			header('Content-type: image/png');
    	}
    	
    	echo $this->_adapter->getImage($format);
    }

    /**
     * Get a string containing the image
     *
     * @return string The image
     */
    public function __toString ()
    {
		return $this->render();
    }

    public function getHeight(){
        return $this->_adapter->getHeight();
    }

    public function getWidth(){
        return $this->_adapter->getWidth();
    }

    public function getImageLength(){
        return $this->_adapter->getImageLength();
    }
    
}