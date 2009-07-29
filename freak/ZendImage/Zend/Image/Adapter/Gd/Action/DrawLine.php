<?php
require_once 'Zend/Image/Color.php';

class Zend_Image_Adapter_Gd_Action_DrawLine {
    /**
     * Draw a line on the image, returns the GD-handle
     *
     * @param  GD image resource    $handle Image to work on
     * @param  Zend_Image_Action_DrawLine   $lineObject The object containing all settings needed for drawing a line.
     * @return void
     */
	public function perform($handle, Zend_Image_Action_DrawLine $lineObject) { // As of ZF2.0 / PHP5.3, this can be made static.
		$color = Zend_Image_Color::calculateHex($lineObject->color);
		$colorAlphaAlloc = 	imagecolorallocatealpha($handle,
							 				   		$color['red'],
							   						$color['green'],
							   						$color['blue'],
							   						$lineObject->alpha);
		if(( $lineObject->thickness / 2 - 0.5 )==0) {
			imageline($handle, $lineObject->getPointStart()->getX(),
							   $lineObject->getPointStart()->getY(),
							   $lineObject->getPointEnd()->getX(),
							   $lineObject->getPointEnd()->getY(),
							   $colorAlphaAlloc);
		} elseif($lineObject->getPointStart()->getX() == $lineObject->getPointEnd()->getX() ||
				 $lineObject->getPointStart()->getY() == $lineObject->getPointEnd()->getY())
		{
			$x1 = round(min($lineObject->getPointStart()->getX(), $lineObject->getPointEnd()->getX()) - ( $lineObject->thickness / 2 - 0.5 ));
			$y1 = round(min($lineObject->getPointStart()->getY(), $lineObject->getPointEnd()->getY()) - ( $lineObject->thickness / 2 - 0.5 ));
			$x2 = round(max($lineObject->getPointStart()->getX(), $lineObject->getPointEnd()->getX()) + ( $lineObject->thickness / 2 - 0.5 ));
			$y2 = round(max($lineObject->getPointStart()->getY(), $lineObject->getPointEnd()->getY()) + ( $lineObject->thickness / 2 - 0.5 ));
			if($lineObject->filled) {
				imagefilledrectangle($handle, $x1, $y1, $x2, $y2,$colorAlphaAlloc);
			} else {
				imagerectangle($handle, $x1, $y1, $x2, $y2,$colorAlphaAlloc);
			}
		} else {
		    require_once 'Zend/Image/Point.php';
            require_once 'Zend/Image/Action/DrawPolygon.php';
    		require_once 'Zend/Image/Adapter/Gd/Action/DrawPolygon.php';
    		$polygonObject = new Zend_Image_Action_DrawPolygon();

            $slope = ($lineObject->getPointEnd()->getY() - $lineObject->getPointStart()->getY())
                     / ($lineObject->getPointEnd()->getX() - $lineObject->getPointStart()->getX()); // y = ax + b
            $a = ($lineObject->thickness / 2 - 0.5) / sqrt(1 + pow($slope,2));
            $points = array(new Zend_Image_Point(round($lineObject->getPointStart()->getX() - (1+$slope)*$a),
                                                 round($lineObject->getPointStart()->getY() + (1-$slope)*$a)),
                            new Zend_Image_Point(round($lineObject->getPointStart()->getX() - (1-$slope)*$a),
                                                 round($lineObject->getPointStart()->getY() - (1+$slope)*$a)),
                            new Zend_Image_Point(round($lineObject->getPointEnd()->getX() + (1+$slope)*$a),
                                                 round($lineObject->getPointEnd()->getY() - (1-$slope)*$a)),
                            new Zend_Image_Point(round($lineObject->getPointEnd()->getX() + (1-$slope)*$a),
                                                 round($lineObject->getPointEnd()->getY() + (1+$slope)*$a)));

			//Draw polygon
			$polygonObject = new Zend_Image_Action_DrawPolygon(array('points'=>$points));
			$handler = new Zend_Image_Adapter_Gd_Action_DrawPolygon();
			$handle = $handler->perform($handle, $polygonObject);
		}

		return $handle;
	}
}
