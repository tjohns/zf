<?php
require_once '../../../Setup.php';

require_once 'Zend/Image/Action/DrawArc.php';
$arc = new Zend_Image_Action_DrawArc();
//$arc->setLocation(new Zend_Image_Point(100,50))
$arc->setHeight(100)
    ->setWidth(150)
    ->setCutoutStart(90)
    ->setCutoutEnd(360);     

$image = new Zend_Image('../../../_data/white_400_400.png');
$image->drawLine($arc);
$image->display();
