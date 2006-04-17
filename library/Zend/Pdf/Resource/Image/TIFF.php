<?php
/**
 * @package Zend_Pdf
 */

/** Zend_Pdf_Font */
require_once 'Zend/Pdf/Resource/Image.php';

/** Zend_Pdf_Const */
require_once 'Zend/Pdf/Const.php';

/** Zend_Pdf_Exception */
require_once 'Zend/Pdf/Exception.php';

/** Zend_Pdf_Element_Numeric */
require_once 'Zend/Pdf/Element/Numeric.php';

/** Zend_Pdf_Element_Name */
require_once 'Zend/Pdf/Element/Name.php';


/**
 * Tiff image
 *
 * @package Zend_Pdf
 */
class Zend_Pdf_Image_TIFF extends Zend_Pdf_Image
{
    /**
     * Object constructor
     *
     * @param string $imageFileName
     * @throws Zend_Pdf_Exception
     */
    public function __construct($imageFileName)
    {
        if (($imageInfo = getimagesize($imageFileName)) === false) {
            throw new Zend_Pdf_Exception('Corrupted image or image doesn\'t exist.');
        }
        if ($imageInfo[2] != IMAGETYPE_TIFF_II && $imageInfo[2] != IMAGETYPE_TIFF_MM) {
            throw new Zend_Pdf_Exception('ImageType is not TIFF');
        }

        parent::__construct();

       /* This needs to be fixed
        switch ($imageInfo['channels']) {
            case 3:
                $colorSpace = 'DeviceRGB';
                break;
            case 4:
                $colorSpace = 'DeviceCMYK';
                break;
            default:
                $colorSpace = 'DeviceGray';
                break;
        }
       */

       /*
       This is a temporary hack - this needs to be read from the tiff file format.
       IMAGICK pecl extension contains imagick_getcolorspace but introducing another
       extension dependency is probably bad.
       */
       $colorSpace = 'DeviceRGB';

        $imageDictionary = $this->_resource->dictionary;
        $imageDictionary->Width            = new Zend_Pdf_Element_Numeric($imageInfo[0]);
        $imageDictionary->Height           = new Zend_Pdf_Element_Numeric($imageInfo[1]);
        $imageDictionary->ColorSpace       = new Zend_Pdf_Element_Name($colorSpace);
//      $imageDictionary->BitsPerComponent = new Zend_Pdf_Element_Numeric($imageInfo['bits']);

       //This is also a temporary hack - Corresponds imagick_getimagedepth
        $imageDictionary->BitsPerComponent = new Zend_Pdf_Element_Numeric(8);

        if (($imageFile = @fopen($imageFileName, 'rb')) === false ) {
            throw new Zend_Pdf_Exception( "Can not open '$imageFileName' file for reading." );
        }
        $byteCount = filesize($imageFileName);
        $this->_resource->value = '';
        while ( $byteCount > 0 && ($nextBlock = fread($imageFile, $byteCount)) != false ) {
            $this->_resource->value .= $nextBlock;
            $byteCount -= strlen($nextBlock);
        }
        fclose($imageFile);
        $this->_resource->skipFilters();
    }
}
