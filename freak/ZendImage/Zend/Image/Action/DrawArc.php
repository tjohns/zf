<?php
require_once 'Zend/Image/Action/Abstract.php';
require_once 'Zend/Image/Point.php';

class Zend_Image_Action_DrawArc extends Zend_Image_Action_Abstract {
    /*
     * The point to center the arc at
     *
     * @var Zend_Image_Point _pointCenter
     */
	protected $_pointCenter = null;

    /*
     * Determines if the arc is filled
     *
     * @var boolean filled
     */
    protected $_filled = true;

    /*
     * The width of the arc
     *
     * @var int Width
     */
    protected $_width = 0;

    /*
     * The height of the arc
     *
     * @var int Height
     */
    protected $_height = 0;

    /*
     * The height of the arc
     *
     * @var int Height
     */
    protected $_cutoutStart = 0;


    /*
     * The height of the arc
     *
     * @var int Height
     */
    protected $_cutoutEnd = 0;

    /*
     * The color of the line (hex)
     *
     * @var int color
     */
    protected $_color = 000000;

    /*
     * The alpha channel of the arc
     *
     * @var int alphachannel
     */
    protected $_alpha = 127;

	/*
	 * The name of this action
	 */
	const NAME  = 'DrawArc';

	/**
     * Parse the inital options for the line
     *
     * @param  array $options An array with the options of the line
     */
	public function __construct($options=array()) {
		$this->_pointCenter = new Zend_Image_Point();

        foreach($options as $key => $value) {
			switch($key) {
			    case 'filled':
			        $this->filled($value);
			        break;
			    case 'pointCenter':
			        $this->setLocation($value);
			        break;
			    case 'centerX':
			        $this->setCenterX($value);
			        break;
			    case 'centerY':
			        $this->setCenterY($value);
			        break;
			    case 'width':
			        $this->setWidth($value);
			        break;
			    case 'height':
			        $this->setHeight($value);
			        break;
			    case 'cutoutStart':
			        $this->setCutoutStart($value);
			        break;
			    case 'cutoutEnd':
			        $this->setCutoutEnd($value);
			        break;
			    case 'color':
			        $this->setColor($value);
			        break;
			    default:
			         require_once 'Zend/Image/Exception.php';
			         throw new Zend_Image_Exception("Unknown option given: $key");
			         break;
			}
		}
	}

    /**
     * Determine if the arc is filled
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
     * Set the center location of the arc
     *
     * @param Zend_Image_Point|integer $param1 A point or coordinate to center the arc at
     * @param integer $y (Optional)            The Y-coordinate to center the arc at
     * @return this
     */
	public function setLocation($param1,$y = null){
	    if($param1 instanceof Zend_Image_Point) {
	        $this->_pointCenter = $param1;
	    } else {
            $this->_pointCenter->setLocation($param1,$y);
	    }
		return $this;
	}

	/**
     * Set the X-coordinate of the center of the arc
     *
     * @param integer $x The X-coordinate to center the arc at
     * @return this
     */
	public function setCenterX($x) {
	   $this->_pointCenter->setX($x);
	   return $this;
	}

    /**
     * Set the Y-coordinate of the center of the arc
     *
     * @param integer $y The Y-coordinate to center the arc at
     * @return this
     */
	public function setCenterY($y) {
       $this->_pointCenter->setY($y);
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
     * Get the location of the center of the arc
     *
     * @return Zend_Image_Point
     */
    public function getLocation(Zend_Image_Adapter_Abstract $adapter=null) {
        if($adapter!==null) {
            if($this->_pointCenter->getX()===null) {
                $this->_pointCenter->setX($adapter->getWidth()/2);
            }
           
            if($this->_pointCenter->getY()===null) {
                $this->_pointCenter->setY($adapter->getHeight()/2);
            }
        }

        return $this->_pointCenter;
    }

    /**
     * Get the alpha channel of the arc
     *
     * @return int alpha channel
     */
    public function getAlpha() {
       return $this->_alpha;
    }

    /**
     * Set the color of the arc
     *
     * @param string $color The color of the arc
     * @return this
     */
    public function setColor($color) {
       $this->_color = $color;
       return $this;
    }


    /**
     * Get the color of the arc
     *
     * @return string Color
     */
    public function getColor() {
       return $this->_color;
    }

    /**
     * Set the width of the arc
     *
     * @param int $width The width of the arc
     * @return this
     */
    public function setWidth($width) {
        $this->_width = $width;
        return $this;
    }

    /**
     * Get width of the arc
     *
     * @return int Width of the arc
     */
    public function getWidth() {
        return $this->_width;
    }

    /**
     * Set the height of the arc
     *
     * @param int $height The height of the arc
     * @return this
     */
    public function setheight($height) {
        $this->_height = $height;
        return $this;
    }

    /**
     * Get height of the arc
     *
     * @return int height of the arc
     */
    public function getheight() {
        return $this->_height;
    }

    /**
     * Set the cutoutStart of the arc
     *
     * @param int $cutoutStart The cutoutStart of the arc
     * @return this
     */
    public function setcutoutStart($cutoutStart) {
        $this->_cutoutStart = $cutoutStart;
        return $this;
    }

    /**
     * Get cutoutStart of the arc
     *
     * @return int cutoutStart of the arc
     */
    public function getcutoutStart() {
        return $this->_cutoutStart;
    }

    /**
     * Set the cutoutEnd of the arc
     *
     * @param int $cutoutEnd The cutoutEnd of the arc
     * @return this
     */
    public function setcutoutEnd($cutoutEnd) {
        $this->_cutoutEnd = $cutoutEnd;
        return $this;
    }

    /**
     * Get cutoutEnd of the arc
     *
     * @return int cutoutEnd of the arc
     */
    public function getcutoutEnd() {
        return $this->_cutoutEnd;
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
