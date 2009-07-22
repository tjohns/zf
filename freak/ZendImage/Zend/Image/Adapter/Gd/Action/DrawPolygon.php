<?php
require_once 'Zend/Image/Color.php';

class Zend_Image_Adapter_Gd_Action_DrawPolygon {

    /**
     * Draws a polygon on the handle
     *
     * @param GD-object $handle The handle on which the polygon is drawn
     * @param Zend_Image_Action_DrawPolygon $polygonObject The object that with all info
     */
    public function perform($handle, Zend_Image_Action_DrawPolygon $polygonObject) { // As of ZF2.0 / PHP5.3, this can be made static.

	    $color = Zend_Image_Color::calculateHex($polygonObject->color);
		$colorAlphaAlloc = 	imagecolorallocatealpha($handle,
							 				   		$color['red'],
							   						$color['green'],
							   						$color['blue'],
							   						$polygonObject->alpha);

        $points = $this->_parsePoints($polygonObject->getPoints());

        if($polygonObject->filled) {
            imagefilledpolygon($handle, $points, count($points)/2, $colorAlphaAlloc);
        } else {
            imagepolygon($handle, $points, count($points)/2, $colorAlphaAlloc);
        }

		return $handle;
	}

    /**
     * Parse the points to something the GD library understands
     *
     * @param array $points An array filled with instances of Zend_Image_Point
     * @return An array with coordinates
     */
	protected function _parsePoints($points) {
        $out = array();
        foreach($points as $point) {
           $out[] = $point->getX();
           $out[] = $point->getY();
        }
        return $out;
	}
}
