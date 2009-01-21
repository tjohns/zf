<?php
require_once 'Zend/Image/Action/Abstract.php';
require_once 'Zend/Image/Point.php';

class Zend_Image_Action_DrawText extends Zend_Image_Action_Abstract {
    /**
     * Position of the text
     *
     * @var integer _position
     */
	protected $_position = null;

    /**
     * The color of the line (hex)
     *
     * @var int color
     */
    protected $_color = 000000;

    /**
     * The alpha channel of the arc
     *
     * @var int alphachannel
     */
    protected $_alpha = 127;

    /**
     * The text that needs to be drawn
     *
     * @var string Text
     */
    protected $_text = '';

    /**
     * The size of the text that is drawn
     *
     * @var int size
     */
    protected $_size = 10;

    /**
     * The font to use
     *
     * @var string Path to font
     */
    protected $_font = 'Zend/Image/Fonts/CaslonRoman.ttf';

    /**
     * The amount of degrees the text needs to be rotated
     *
     * @var int rotation
     */
    protected $_rotation = 0;

    /**
     * X-Offset
     *
     * @var int offsetX
     */
    protected $_offsetX = 0;

    /**
     * Y-offset
     *
     * @var int offsetY
     */
    protected $_offsetY = 0;

	/**
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
			    case 'offsetX':
			        $this->setOffsetX($value);
			        break;
			    case 'offsetY':
			        $this->setOffsetY($value);
			        break;
			    case 'text':
			        $this->setText($value);
			        break;
			    case 'size':
			        $this->setSize($value);
			        break;
			    case 'position':
			        $this->setPosition($value);
			        break;
                case 'font':
                    $this->setFont($value);
                    break;
                case 'alpha':
                    $this->setAlpha($value);
                    break;
                case 'rotation':
                    $this->setRotation($value);
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
     * Set the position of the text
     *
     * @param string $position The position of the text
     * @return this
     * @todo implement check given value = valid
     */
	public function setPosition($position){
        $this->_position = $position;
		return $this;
	}

	/**
     * Set the X-offset of the text
     *
     * @param integer $x The X-offset
     * @return this
     */
	public function setOffsetX($x) {
	   $this->_offsetX = $x;
	   return $this;
	}

	/**
	 * Get the X-offset of the text
	 *
	 * @return integer offsetX
	 */
	public function getOffsetX() {
	    return $this->_offsetX;
	}

    /**
     * Set the Y-offset of the text
     *
     * @param integer $y The Y-offset
     * @return this
     */
    public function setOffsetY($y) {
       $this->_offsetY = $y;
       return $this;
    }

    /**
     * Get the Y-offset of the text
     *
     * @return integer offsetY
     */
    public function getOffsetY() {
        return $this->_offsetY;
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
     * Get the position of the text
     *
     * @return integer Position
     */
    public function getPosition() {
       return $this->_position;
    }

    /**
     * Get the alpha channel of the text
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
     * Set the rotation of the text
     *
     * @param int $rotation The rotation of the text
     * @return this
     */
    public function setRotation($rotation) {
        $this->_rotation = (int) $rotation;
        return $this;
    }

    /**
     * Get the rotation of the text
     *
     * @return integer rotation of the text
     */
    public function getRotation() {
        return $this->_rotation;
    }

    /**
     * Set the font to use for the text
     *
     * @param string $fontPath Path to font
     * @throw Zend_Image_Exception
     * @return this
     */
     public function setFont($fontPath) {
        if(!file_exists($fontPath)) {
            require_once 'Zend/Image/Exception.php';
            throw new Zend_Image_Exception('Incorrect font');
        }
        $this->_font = $fontPath;
    }

    /**
     * Get the path of the font
     *
     * @return string path of font
     */
    public function getFont() {
        return $this->_font;
    }

    /**
     * Set the text to print on the image
     *
     * @param string $text Text to print
     * @return this
     */
    public function setText($text) {
        $this->_text = (string)$text;
        return $this;
    }

    /**
     * Get the text that will be printed
     *
     * @return string text to be printed on image
     */
    public function getText() {
        return $this->_text;
    }

    /**
     * Set the size of the text
     *
     * @param string $size of text
     * @return this
     */
    public function setSize($size) {
        $this->_size = (int) $size;
        return $this;
    }

    /**
     * Get the size of the text
     *
     * @return int size of the text
     */
    public function getSize() {
        return $this->_size;
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
