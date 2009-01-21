<?php
class Zend_Image_Color {
    /**
     * Calculate the decimal values for each color
     * based on the given hexvalue
     *
     * @param integer $color The color to calculate
     * @return array Decimal values for each color
     */
    public static function calculateHex($color){
//    	$color = 127-$color;
        $c['red']=hexdec(substr($color,0,2));
        $c['green']=hexdec(substr($color,2,2));
        $c['blue']=hexdec(substr($color,4,2));
        return $c;
    }
}