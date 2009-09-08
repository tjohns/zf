<?php

class Zend_Image_Adapter_ImageMagick_Action_Resize {

    public function perform(Zend_Image_Adapter_ImageMagick $adapter,
        Zend_Image_Action_Resize $resize) {

        $handle = $adapter->getHandle();

        $handle->resizeImage($resize->getXAmount(), $resize->getYAmount(), $resize->getFilter(), 1);

    }


}