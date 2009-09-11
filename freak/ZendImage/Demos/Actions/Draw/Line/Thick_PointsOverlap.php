<?php
require_once '../../../Setup.php';

require_once 'Zend/Image/Action/DrawLine.php';
$line = new Zend_Image_Action_DrawLine();
$line->from(10,15)
	 ->to(new Zend_Image_Point(10,125))
	 ->setFilled(true)
	 ->setThickness(5);
	 
$image = Zend_Image::factory('../../../_data/white_400_400.png');
$image->drawLine($line);
$image->display();
