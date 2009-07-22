<?php
require_once 'Zend/Image/Action/Abstract.php';
require_once 'Zend/Image/Point.php';

class Zend_Image_Action_DrawLine extends Zend_Image_Action_Abstract {
    /*
     * The point to start the line at
     *
     * @var Zend_Image_Point _poinstStart
     */
	protected $_pointStart = null;

    /*
     * The point to end the line at
     *
     * @var Zend_Image_Point _poinstEnd
     */
	protected $_pointEnd = null;

	/*
     * The thickness of the line
     *
     * @var integer thickness
     */
	public $thickness = 1;

	/*
     * Determines if the line is filled
     *
     * @var boolean filled
     */
	public $filled = true;

    /*
     * The color of the line (hex)
     *
     * @var int color
     */
	public $color = 000000;

	/*
     * The alpha layer of this line
     *
     * @var int alpha
     */
	public $alpha = 0;

	/*
	 * The name of this action
	 */
	const NAME  = 'DrawLine';

	/**
     * Parse the inital options for the line
     *
     * @param  array $options An array with the options of the line
     */
	public function __construct($options=array()) {
		$this->_pointStart = new Zend_Image_Point();
        $this->_pointEnd = new Zend_Image_Point();

        foreach($options as $key => $value) {
			switch($key) {
				case 'thickness':
					$this->setThickness($value);
					break;
				case 'startX':
				    $this->setStartX($value);
				    break;
				case 'startY':
				    $this->setStartY($value);
				    break;
				case 'endX':
				    $this->setEndX($value);
				    break;
				case 'endY':
				    $this->setEndY($value);
				    break;
/*				default:
					$this->$key = $value;
*/			}
		}
	}

	/**
     * Set the thickness of the line
     *
     * @param  integer $thickness The thickness
     * @return this
     */
	public function setThickness($thickness) {
		$this->thickness = $thickness ;
		return $this;
	}

	/**
     * Determine if the line is filled
     *
     * @param  boolean $isFilled
     * @return this
     */
	public function setFilled($isFilled=true) {
		$this->filled = $isFilled;
		return $this;
	}

	/**
     * Set the starting coordinates of the line
     *
     * @param Zend_Image_Point|integer $param1 A point or coordinate to start at
     * @param integer $y (Optional)            The Y-coordinate to start at
     * @return this
     */
	public function from($param1,$y = null){
	    if($param1 instanceof Zend_Image_Point) {
	        $this->_pointStart = $param1;
	    } else {
            $this->_pointStart->setLocation($param1,$y);
	    }
		return $this;
	}

	/**
     * Set the ending coordinates of the line
     *
     * @param Zend_Image_Point|integer $param1 A point or coordinate to end at
     * @param integer $y (Optional)            The Y-coordinate to end at
     * @return this
     */
	public function to($param1,$y=null){
        if($param1 instanceof Zend_Image_Point) {
            $this->_pointEnd = $param1;
        } else {
        	$this->_pointEnd->setLocation($param1,$y);
        }
		return $this;
	}

	/**
     * Set the starting X-coordinate of the line
     *
     * @param integer $x The X-coordinate to start at
     * @return this
     */
	public function setStartX($x) {
	   $this->_pointStart->setX($x);
	   return $this;
	}

    /**
     * Set the starting Y-coordinate of the line
     *
     * @param integer $y The Y-coordinate to start at
     * @return this
     */
	public function setStartY($y) {
       $this->_pointStart->setY($y);
	   return $this;
	}

    /**
     * Set the ending X-coordinate of the line
     *
     * @param integer $x The X-coordinate to end at
     * @return this
     */
	public function setEndX($x) {
        $this->_pointEnd->setX($x);
        return $this;
	}

    /**
     * Set the ending Y-coordinate of the line
     *
     * @param integer $y The Y-coordinate to end at
     * @return this
     */
	public function setEndY($y) {
        $this->_pointEnd->setY($y);
        return $this;
	}

	/**
     * Set the alpha channel
     *
     * @param integer $alpha The alpha channel
     * @return this
     */
	public function setAlpha($alpha) {
		$this->alpha = $alpha;
		return $this;
	}

    /**
     * Set the coordinates of the line
     *
     * @param integer $xStart   The X-coordinate to start at
     * @param integer $yStart   The Y-coordinate to start at
     * @param integer $xEnd     The X-coordinate to end at
     * @param integer $yEnd     The Y-coordinate to end at
     * @return this
     */
	public function setCoords($xStart, $yStart, $xEnd, $yEnd) {
        $this->_pointStart->setLocation($xStart, $yStart);
        $this->_pointEnd->setLocation($xEnd, $yEnd);
        return $this;
	}

    /**
     * Get the starting point
     *
     * @return Starting point
     */
	public function getPointStart() {
	   return $this->_pointStart;
	}

    /**
     * Get the ending point
     *
     * @return ending point
     */
    public function getPointEnd() {
	   return $this->_pointEnd;
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
