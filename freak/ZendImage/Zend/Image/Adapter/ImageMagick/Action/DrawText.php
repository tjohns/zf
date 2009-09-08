<?php
require_once 'Zend/Image/Color.php';

class Zend_Image_Adapter_ImageMagick_Action_DrawText {

    /**
     * Draws some text on the handle
     *
     * @param Zend_Image_Adapter_ImageMagick $adapter Adapter
     * @param Zend_Image_Action_DrawText $textObject The object that with all info
     */
    public function perform($adapter, Zend_Image_Action_DrawText $textObject) { // As of ZF2.0 / PHP5.3, this can be made static.
    
        $handle = $adapter->getHandle();

        $color = new ImagickPixel('#000000');// . $textObject->getColor());

        $draw = new ImagickDraw();
        $draw->annotation($textObject->getOffsetX(), $textObject->getOffsetY(), $textObject->getText());

        $handle->drawImage($draw);

        return $handle;
    }
}
