<?php

/** Zend_Pdf */
require_once 'Zend/Pdf.php';

/**
 * Zend_Pdf_ImageFactory
 *
 * Helps manage the diverse set of supported image file types.
 *
 * @package    Zend_Pdf
 */
class Zend_Pdf_ImageFactory
{
    public static function factory($filename) {
        if(!is_file($filename)) {
            throw new Zend_Pdf_Exception("Cannot create image resource. File not found.");
        }
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        /* 
         * TODO: Use Zend_Mime not file extension. In the mean time, if you need to
         * use an alternate file extension just spin up the right processor directly.
         */
        switch (strtolower($extension)) {
            case 'tif':
                //Fall through to next case;
            case 'tiff':
                return new Zend_Pdf_Image_TIFF($filename);
                break;
            case 'png':
                return new Zend_Pdf_Image_PNG($filename);
                break;
            case 'jpg':
                //Fall through to next case;
            case 'jpe':
                //Fall through to next case;
            case 'jpeg':
                return new Zend_Pdf_Image_JPEG($filename);
                break;
            default:
                throw new Zend_Pdf_Exception("Cannot create image resource. File extension not known or unsupported type.");
                break;
        }
    }
}
?>
