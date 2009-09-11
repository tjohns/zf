<?php
require_once 'Zend/Image/Color.php';

class Zend_Image_Adapter_ImageMagick_Action_DrawLine {
/**
 * Draw a line on the image, returns the GD-handle
 *
 * @param  Zend_Image_Adapter_ImageMagick image resource    $handle Image to work on
 * @param  Zend_Image_Action_DrawLine   $lineObject The object containing all settings needed for drawing a line.
 * @return void
 */
    public function perform(Zend_Image_Adapter_ImageMagick $adapter,
        Zend_Image_Action_DrawLine $lineObject) {

        $handle = $adapter->getHandle();

        $draw = new ImagickDraw();

        $color = new ImagickPixel('#' . 'ff0000');//$lineObject->color);

        $draw->setFillColor($color);

        $draw->setStrokeWidth($lineObject->thickness);

        $draw->setStrokeColor('#00ff00');

        if(1 == $lineObject->thickness) {

            $draw->line($lineObject->getPointStart()->getX(),
                $lineObject->getPointStart()->getY(),
                $lineObject->getPointEnd()->getX(),
                $lineObject->getPointEnd()->getY());

        } else {
            require_once 'Zend/Image/Point.php';
            require_once 'Zend/Image/Action/DrawPolygon.php';
            require_once 'Zend/Image/Adapter/Gd/Action/DrawPolygon.php';
            $polygonObject = new Zend_Image_Action_DrawPolygon();

        }


        $handle->drawImage($draw);

    }

}