<?php
require_once '../../../Setup.php';

require_once 'Zend/Image/Action/DrawLine.php';
$line = new Zend_Image_Action_DrawLine();
$line->from(10,15)
	 ->to(new Zend_Image_Point(50,125))
	 ->setFilled(true);
	 
$image = new Zend_Image('../../../_data/white_400_400.png');
$image->drawLine($line);
$image->display();
