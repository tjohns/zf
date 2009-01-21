<?php
require_once 'Zend/Image/Action/Abstract.php';
require_once 'Zend/Image/Point.php';

class Zend_Image_Action_DrawPolygon extends Zend_Image_Action_Abstract {
	/**
     * Determines if the polygon is filled
     *
     * @var boolean filled
     */
	public $filled = true;

    /**
     * The color of the polygon (hex)
     *
     * @var int color
     */
	public $color = 000000;

	/**
     * The alpha channel of this polygon
     *
     * @var int alpha
     */
	public $alpha = 0;

	/**
	 * The points to which the polygon
	 * needs to be drawn
	 *
	 * @var array points
	 */
	protected $_points = array();

	const NAME  = 'DrawPolygon';

    /**
     * Parse the inital options for the polygon
     *
     * @param  array $options An array with the options of the polygon
     */
	public function __construct($options=array()) {
        foreach($options as $key => $value) {
			switch($key) {
				case 'thickness':
					$this->setThickness($value);
					break;
				case 'points':
				    $this->addPoints($value);
				    break;
			}
		}
	}

    /**
     * Determine if the polygon is filled
     *
     * @param  boolean $isFilled
     * @return this
     */
	public function setFilled($isFilled=true) {
		$this->filled = (bool)$isFilled;
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
     * Add points that need to be part of the polygon
     *
     * @param array $points
     * @return this
     */
	public function addPoints(array $points) {
	    foreach($points as $point) {
	       $this->addPoint($point);
	    }
	    return $this;
	}

    /**
     * Add a point that needs to be part of the polygon
     *
     * @param Zend_Image_Point|array $point The point
     * @return this
     */
	public function addPoint($point) {
	    if($point instanceof Zend_Image_Point) {
	       $this->_points[] = $point;
	    } elseif(is_array($point)) {
            $this->_points[] = new Zend_Image_Point($point[0],$point[1]);
        } else {
            require_once 'Zend/Exception.php';
            throw new Zend_Exception('A point can only be an array, or an instanceof Zend_Image_Point');
        }
	}

	/**
     * Get the points on which the polygon is drawn
     *
     * @return the points of the polygon
     */
	public function getPoints() {
	   return $this->_points;
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
