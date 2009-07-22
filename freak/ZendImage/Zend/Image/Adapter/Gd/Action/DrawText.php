<?php
require_once 'Zend/Image/Color.php';

class Zend_Image_Adapter_Gd_Action_DrawText {

    /**
     * Draws some text on the handle
     *
     * @param GD-object $handle The handle on which the ellipse is drawn
     * @param Zend_Image_Action_DrawText $textObject The object that with all info
     */
    public function perform($handle, Zend_Image_Action_DrawText $textObject) { // As of ZF2.0 / PHP5.3, this can be made static.

        $color = Zend_Image_Color::calculateHex($textObject->getColor());
        $colorAlphaAlloc =  imagecolorallocatealpha($handle,
                                                    $color['red'],
                                                    $color['green'],
                                                    $color['blue'],
                                                    127-$textObject->getAlpha());


        return $handle;
    }
}
