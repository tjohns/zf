<?php
require_once '../../../Setup.php';

require_once 'Zend/Image/Action/DrawArc.php';
$arc = new Zend_Image_Action_DrawArc();
$arc->setHeight(400)
    ->setWidth(400)
    ->setCutoutStart(90)
    ->setCutoutEnd(360)
    ->filled(false);     

$image = Zend_Image::factory('../../../_data/white_400_400.png');
$image->drawLine($arc);
$image->display();
