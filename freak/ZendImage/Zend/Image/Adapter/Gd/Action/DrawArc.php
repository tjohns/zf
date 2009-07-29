<?php
require_once 'Zend/Image/Adapter/Gd/Action/Abstract.php';
require_once 'Zend/Image/Color.php';

class Zend_Image_Adapter_Gd_Action_DrawArc
    extends Zend_Image_Adapter_Gd_Action_Abstract
{

    /**
     * Draws an arc on the handle
     *
     * @param GD-object $handle The handle on which the ellipse is drawn
     * @param Zend_Image_Action_DrawEllipse $ellipseObject The object that with all info
     */
    public function perform(Zend_Image_Adapter_Gd $adapter,
        Zend_Image_Action_DrawArc $arcObject)
    { 

	    $color = Zend_Image_Color::calculateHex($arcObject->getColor());
		$colorAlphaAlloc = 	imagecolorallocatealpha($adapter->getHandle(),
							 				   		$color['red'],
							   						$color['green'],
							   						$color['blue'],
							   						127-$arcObject->getAlpha());

        if(!$arcObject->filled()) {
            $style = 6;
        } else {
            $style = 0;
        }
        
        $location = $arcObject->getLocation($adapter);
        imagefilledarc($adapter->getHandle(),
                       $location->getX(),
                       $location->getY(),
                       $arcObject->getWidth(),
                       $arcObject->getHeight(),
                       $arcObject->getCutoutStart()-90,
                       $arcObject->getCutoutEnd()-90,
                       $colorAlphaAlloc,
                       $style);
                       
	}
}
