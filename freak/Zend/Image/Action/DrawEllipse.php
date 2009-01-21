<?php
require_once 'Zend/Image/Action/Abstract.php';
require_once 'Zend/Image/Point.php';

class Zend_Image_Action_DrawEllipse extends Zend_Image_Action_Abstract {
    /**
     * The point to start the ellipse at
     *
     * @var Zend_Image_Point _location
     */
	protected $_location = null;

    /**
     * The width of the ellipse
     *
     * @var integer width
     */
    public $_width = null;

    /**
     * The height of the ellipse
     *
     * @var integer height
     */
    public $_height = null;

	/**
     * Determines if the ellipse is filled
     *
     * @var boolean filled
     */
	public $_filled = true;

    /**
     * The color of the ellipse (hex)
     *
     * @var int color
     */
	public $_color = 000000;

	/**
     * The alpha layer of this ellipse
     *
     * @var int alpha
     */
	public $_alpha = 0;

	/**
	 * The name of this action
	 */
	const NAME  = 'DrawEllipse';

	/**
     * Parse the inital options for the ellipse
     *
     * @param  array $options An array with the options of the ellipse
     */
	public function __construct($options=array()) {
		$this->_location = new Zend_Image_Point();
        foreach($options as $key => $value) {
			switch($key) {
				case 'filled':
					$this->filled($value);
					break;
				case 'color':
				    $this->setColor($value);
				    break;
				case 'alpha':
				    $this->setAlpha($value);
				    break;
				case 'startX':
				    $this->setX($value);
				    break;
				case 'startY':
				    $this->setY($value);
				    break;
				case 'location':
				    $this->setLocation($value);
				    break;
				case 'width':
				    $this->setWidth($value);
				    break;
				case 'height':
				    $this->setHeight($value);
				    break;
				default:
				    require_once 'Zend/Image/Exception.php';
				    throw new Zend_Image_Exception('Invalid option recognized.');
				    break;
		  }
	   }
	}

	/**
     * Determine if the ellipse is filled
     *
     * @param  boolean $isFilled (Optional)
     * @return this|_isFilled
     */
    public function filled($isFilled=null) {
        if(null===$isFilled) {
            return $this->_filled;
        }
        $this->_filled = (bool)$isFilled;
        return $this;
    }

    /**
     * Set the width of the ellipse
     *
     * @param int $width the width of the ellipse
     * @return this
     */
    public function setWidth($width) {
        $this->_width = $width;
        return $this;
    }

    /**
     * Set the height of the ellipse
     *
     * @param int $height The height of the ellipse
     * @return this
     */
    public function setHeight($height) {
        $this->_height = $height;
        return $this;
    }

    /**
     * Get the color of the ellipse
     *
     * @return int color
     */
    public function getColor() {
        return $this->_color;
    }

    /**
     * Set the color of the ellipse
     *
     * @param string $color Color of the ellipse
     * @return this
     */
    public function setColor($color) {
        $this->_color = $color;
        return $this;
    }

	/**
     * Set the coordinates of the ellipse
     *
     * @param Zend_Image_Point|integer $param1 A point or coordinate of the ellipse
     * @param integer $y (Optional)            The Y-coordinate of the ellipse
     * @return this
     */
	public function setLocation($param1,$y = null){
	    if($param1 instanceof Zend_Image_Point) {
	        $this->_location = $param1;
	    } else {
            $this->_location->setLocation($param1,$y);
	    }
		return $this;
	}

	/**
     * Get the location of the ellipse
     *
     * @return location
     */
    public function getLocation() {
        return $this->_location;
    }


	/**
     * Set the starting X-coordinate of the line
     *
     * @param integer $x The X-coordinate to start at
     * @return this
     */
	public function setX($x) {
	   $this->_location->setX($x);
	   return $this;
	}

    /**
     * Set the starting Y-coordinate of the line
     *
     * @param integer $y The Y-coordinate to start at
     * @return this
     */
	public function setY($y) {
       $this->_location->setY($y);
	   return $this;
	}

 	/**
     * Set the alpha channel
     *
     * @param integer $alpha The alpha channel
     * @return this
     */
	public function setAlpha($alpha) {
		$this->_alpha = $alpha;
		return $this;
	}

	    /**
     * Get the alpha channel of the ellipse
     *
     * @return integer alpha channel
     */
    public function getAlpha() {
        return $this->_alpha;
    }

    /**
     * Get the width of the ellipse
     *
     * @return integer Width
     */
    public function getWidth() {
        return $this->_width;
    }

    /**
     * Get the height of the ellipse
     *
     * @return integer height
     */
    public function getHeight() {
        return $this->_height;
    }

    /**
     * Get the name of this action
     *
     * @return self::NAME
     */
    public function getName() {
		return self::NAME;
	}
}
