<?php
require_once '../../../Setup.php';

/** Usecase 1 **/

$options = array(
	'thickness' => 5,
	'filled' => true,
	'startX' => 10,
	'startY' => 15,
	'endX' => 50,
	'endY' => 125);

$image = new Zend_Image('../../../_data/white_400_400.png');
$image->drawLine($options);
$image->display();
