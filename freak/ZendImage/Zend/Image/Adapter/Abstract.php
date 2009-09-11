<?php
require_once 'Zend/Image/Adapter/Interface.php';

abstract class Zend_Image_Adapter_Abstract
    implements Zend_Image_Adapter_Interface
{

    /*
     * Names of Actions available
     */
    const LINE = 'DrawLine';
    const POLYGON = 'DrawPolygon';
    const ELLIPSE = 'DrawEllipse';
    const ARC = 'DrawArc';
    const ETC = 'MoreToAddHere';

    /**
     * Path of the location of the image
     */
    protected $_imagePath;
    
    protected $_length;
    protected $_height;
    protected $_width;

    public function __construct($config) {
        if(is_array($config)) {
            $this->setOptions($config);
        } else {
            $this->setConfig($config);
        }
    }
    
    /**
     * Set Adapter state from Zend_Config object
     *
     * @param  array $options
     * @return Zend_Image_Adapter_*
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $normalized = ucfirst($key);
            $method = 'set' . $normalized;
            
            switch($key) {
                case 'path':
                    $this->setPath($value);
                    break;
                default:
                    require 'Zend/Image/Exception.php';
                    throw new Zend_Image_Exception("Unknown config parameter specified: '" . $key . "'");
            }
        }

        return $this;
    }

    /**
     * Set Adapter state from Zend_Config object
     *
     * @param  Zend_Config $config
     * @return Zend_Image_Adapter_*
     */
    public function setConfig(Zend_Config $config)
    {
        return $this->setOptions($config->toArray());
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



    public function __call ($action, $arguments)
    {
        $this->apply($action, $arguments[0]);
    }

    /**
     * Get a string containing the image
     *
     * @param string $format (Optional) The format of the image to return
     * @return void
     */
    public function render ($format = 'png')
    {
        return $this->getImage($format);
    }

    public function display ($format = 'png', $sendHeader = true)
    {
        if ($sendHeader) {
            header('Content-type: image/png');
        }
        
        echo $this->render($format);
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

    public function getHeight ()
    {
        return $this->_height;
    }

    public function getWidth ()
    {
        return $this->_width;
    }

    public function getImageLength ()
    {
        return $this->_length; 
    }
}
