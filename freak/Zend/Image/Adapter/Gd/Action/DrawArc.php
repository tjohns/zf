<?php
require_once 'Zend/Image/Color.php';

class Zend_Image_Adapter_Gd_Action_DrawArc {

    /**
     * Draws an arc on the handle
     *
     * @param GD-object $handle The handle on which the ellipse is drawn
     * @param Zend_Image_Action_DrawEllipse $ellipseObject The object that with all info
     */
    public function perform($handle, Zend_Image_Action_DrawArc $arcObject) { // As of ZF2.0 / PHP5.3, this can be made static.

	    $color = Zend_Image_Color::calculateHex($arcObject->getColor());
		$colorAlphaAlloc = 	imagecolorallocatealpha($handle,
							 				   		$color['red'],
							   						$color['green'],
							   						$color['blue'],
							   						127-$arcObject->getAlpha());

        if(!$arcObject->filled()) {
            $style = 6;
        } else {
            $style = 0;
        }
        imagefilledarc($handle,
                       $arcObject->getLocation()->getX(),
                       $arcObject->getLocation()->getY(),
                       $arcObject->getWidth(),
                       $arcObject->getHeight(),
                       $arcObject->getCutoutStart(),
                       $arcObject->getCutoutEnd(),
                       $colorAlphaAlloc,
                       $style);
		return $handle;
	}
}
