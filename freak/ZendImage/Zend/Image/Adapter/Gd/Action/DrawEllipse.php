<?php
require_once 'Zend/Image/Color.php';

class Zend_Image_Adapter_Gd_Action_DrawEllipse {

    /**
     * Draws an ellipse on the handle
     *
     * @param GD-object $handle The handle on which the ellipse is drawn
     * @param Zend_Image_Action_DrawEllipse $ellipseObject The object that with all info
     */
    public function perform($handle, Zend_Image_Action_DrawEllipse $ellipseObject) { // As of ZF2.0 / PHP5.3, this can be made static.

	    $color = Zend_Image_Color::calculateHex($ellipseObject->getColor());
		$colorAlphaAlloc = 	imagecolorallocatealpha($handle,
							 				   		$color['red'],
							   						$color['green'],
							   						$color['blue'],
							   						$ellipseObject->getAlpha());

        if($ellipseObject->filled()) {
            imagefilledellipse($handle,
                               $ellipseObject->getLocation()->getX(),
                               $ellipseObject->getLocation()->getY(),
                               $ellipseObject->getWidth(),
                               $ellipseObject->getHeight(),
                               $colorAlphaAlloc);
        } else {
                imageellipse($handle,
                               $ellipseObject->getLocation()->getX(),
                               $ellipseObject->getLocation()->getY(),
                               $ellipseObject->getWidth(),
                               $ellipseObject->getHeight(),
                               $colorAlphaAlloc);
        }
		return $handle;
	}
}
