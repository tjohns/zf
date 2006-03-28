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
 * JPEG image
 *
 * @package Zend_Pdf
 */
class Zend_Pdf_Image_JPEG extends Zend_Pdf_Image
{
    /**
     * Object constructor
     *
     * @param string $imageFileName
     * @throws Zend_Pdf_Exception
     */
    public function __construct($imageFileName)
    {
        if (!function_exists('gd_info')) {
            throw new Zend_Pdf_Exception('Image extension is not installed.');
        }

        $gd_options = gd_info();
        if (!$gd_options['JPG Support'] ) {
            throw new Zend_Pdf_Exception('JPG support is not configured properly.');
        }

        if (($imageInfo = getimagesize($imageFileName)) === false) {
            throw new Zend_Pdf_Exception('Corrupted image or image doesn\'t exist.');
        }
        if ($imageInfo[2] != IMAGETYPE_JPEG && $imageInfo[2] != IMAGETYPE_JPEG2000) {
            throw new Zend_Pdf_Exception('ImageType is not JPG');
        }

        parent::__construct();

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

        $imageDictionary = $this->_resource->dictionary;
        $imageDictionary->Width            = new Zend_Pdf_Element_Numeric($imageInfo[0]);
        $imageDictionary->Height           = new Zend_Pdf_Element_Numeric($imageInfo[1]);
        $imageDictionary->ColorSpace       = new Zend_Pdf_Element_Name($colorSpace);
        $imageDictionary->BitsPerComponent = new Zend_Pdf_Element_Numeric($imageInfo['bits']);
        if ($imageInfo[2] == IMAGETYPE_JPEG) {
            $imageDictionary->Filter       = new Zend_Pdf_Element_Name('DCTDecode');
        } else if ($imageInfo[2] == IMAGETYPE_JPEG2000){
            $imageDictionary->Filter       = new Zend_Pdf_Element_Name('JPXDecode');
        }

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

