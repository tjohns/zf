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
 * PNG image
 *
 * @package Zend_Pdf
 */
class Zend_Pdf_Image_PNG extends Zend_Pdf_Image
{
    const PNG_COMPRESSION_DEFAULT_STRATEGY = 0;
    const PNG_COMPRESSION_FILTERED = 1;
    const PNG_COMPRESSION_HUFFMAN_ONLY = 2;
    const PNG_COMPRESSION_RLE = 3;

    const PNG_FILTER_NONE = 0;
    const PNG_FILTER_SUB = 1;
    const PNG_FILTER_UP = 2;
    const PNG_FILTER_AVERAGE = 3;
    const PNG_FILTER_PAETH = 4;

    const PNG_INTERLACING_DISABLED = 0;
    const PNG_INTERLACING_ENABLED = 1;

    const PNG_CHANNEL_GRAY = 0;
    const PNG_CHANNEL_RGB = 2;
    const PNG_CHANNEL_INDEXED = 3;
    const PNG_CHANNEL_GRAY_ALPHA = 4;
    const PNG_CHANNEL_RGB_ALPHA = 6;

    /**
     * Object constructor
     *
     * @param string $imageFileName
     * @throws Zend_Pdf_Exception
     */
    public function __construct($imageFileName)
    {
        if (($imageFile = @fopen($imageFileName, 'rb')) === false ) {
            throw new Zend_Pdf_Exception( "Can not open '$imageFileName' file for reading." );
        }

        parent::__construct();

        //Check if the file is a PNG
        fseek($imageFile, 1, SEEK_CUR); //First signature byte (%)
        if ('PNG' != fread($imageFile, 3)) {
            throw new Zend_Pdf_Exception('Image is not a PNG');
        }
        fseek($imageFile, 12, SEEK_CUR); //Signature bytes (Includes the IHDR chunk) IHDR processed linerarly because it doesnt contain a variable chunk length
        $wtmp = unpack('Ni',fread($imageFile, 4)); //Unpack a 4-Byte Long
        $width = $wtmp['i'];
        $htmp = unpack('Ni',fread($imageFile, 4));
        $height = $htmp['i'];
        $bits = ord(fread($imageFile, 1)); //Higher than 8 bit depths are only supported in later versions of PDF.
        $color = ord(fread($imageFile, 1));

        if (ord(fread($imageFile, 1)) != Zend_Pdf_Image_PNG::PNG_COMPRESSION_DEFAULT_STRATEGY) {
            //TODO: Add compression conversions
            throw new Zend_Pdf_Exception( "Only the default compression strategy is currently supported." );
        }

        if (ord(fread($imageFile,1)) != Zend_Pdf_Image_PNG::PNG_FILTER_NONE) {
                //TODO: Support PNG Filtering
                throw new Zend_Pdf_Exception( "Filtering methods are not currently supported. " );
        }
        if (ord(fread($imageFile,1)) != Zend_Pdf_Image_PNG::PNG_INTERLACING_DISABLED) {
                //TODO: Support Interlacing
                throw new Zend_Pdf_Exception( "Only non-interlaced images are currently supported." );
        }

        fseek($imageFile, 4, SEEK_CUR); //4 Byte Ending Sequence

        $imageData = '';

        /*
         * The following loop processes PNG chunks. 4 Byte Longs are packed first give the chunk length
         * followed by the chunk signature, a four byte code. IDAT and IEND are manditory in any PNG.
         */
        while($chunkLengthBytes = fread($imageFile, 4)) {
            $chunkLengthtmp         = unpack('Ni', $chunkLengthBytes);
            $chunkLength            = $chunkLengthtmp['i'];
            $chunkType                      = fread($imageFile, 4);
            switch($chunkType) {
                //TODO: Support all PNG chunks
                case 'IDAT': //Image Data
                    $imageData .= fread($imageFile, $chunkLength);
                    fseek($imageFile, 4, SEEK_CUR);
                    break;

                case 'PLTE': //Palette
                    if ($color != Zend_Pdf_Image_PNG::PNG_CHANNEL_INDEXED) {
                        throw new Zend_Pdf_Exception( "Only indexed color PNG's can contain palette entries." );
                    }
                   $paletteData = fread($imageFile, $chunkLength);
                   fseek($imageFile, 4, SEEK_CUR);
                   break;

                case 'tRNS': //Basic (non-alpha channel) transparency. (untested)
                    switch ($color) {
                        case Zend_Pdf_Image_PNG::PNG_CHANNEL_GRAY:
                            $baseColor = unpack('n',fread($imageFile, 2));
                            $transparencyData = array($baseColor['n']);
                            fseek($imageFile, $chunkLength - 2, SEEK_CUR);
                            break;
                        case Zend_Pdf_Image_PNG::PNG_CHANNEL_RGB:
                            $red = unpack('n', fread($imageFile, 2));
                            $green = unpack('n', fread($imageFile, 2));
                            $blue = unpack('n', fread($imageFile, 2));
                            $transparencyData = array($red['n'], $green['n'], $blue['n']);
                            fseek($imageFile, $chunkLength - 6, SEEK_CUR);
                            break;
                        case Zend_Pdf_Image_PNG::PNG_CHANNEL_INDEXED:
                            //TODO: Read "For color type 3 (indexed color), the tRNS chunk contains a series of one-byte alpha values, corresponding to entries in the PLTE chunk"
                            fseek($imageFile, $chunkLength, SEEK_CUR);
                            throw new Zend_Pdf_Exception( "tRNS chunk not yet supported for INDEXED color images.." );
                            break;
                        case Zend_Pdf_Image_PNG::PNG_CHANNEL_GRAY_ALPHA:
                            // Fall through to the next case
                        case Zend_Pdf_Image_PNG::PNG_CHANNEL_RGB_ALPHA:
                            fseek($imageFile, $chunkLength, SEEK_CUR);
                            throw new Zend_Pdf_Exception( "tRNS chunk illegal for Alpha Channel Images" );
                            break;
                    }
                    fseek($imageFile, 4, SEEK_CUR); //4 Byte Ending Sequence
                    break;

                case 'IEND';
                    break 2; //End the loop too

                default:
                    fseek($imageFile, $chunkLength + 4, SEEK_CUR); //Skip the section
                    break;
            }
        }
        switch ($color) {
            case Zend_Pdf_Image_PNG::PNG_CHANNEL_RGB:
                $colorSpace = new Zend_Pdf_Element_Name('DeviceRGB');
                break;
            case Zend_Pdf_Image_PNG::PNG_CHANNEL_GRAY:
                $colorSpace = new Zend_Pdf_Element_Name('DeviceGray');
                break;
            case Zend_Pdf_Image_PNG::PNG_CHANNEL_INDEXED:
               $colorSpace                             = new Zend_Pdf_Element_Array();
               $colorSpace->items[]                    = new Zend_Pdf_Element_Name('Indexed');
               $colorSpace->items[]                    = new Zend_Pdf_Element_Name('DeviceRGB');
               $colorSpace->items[]                    = new Zend_Pdf_Element_Numeric((strlen($paletteData)/3-1));
               $paletteObject                          = $this->_objectFactory->newObject(new Zend_Pdf_Element_String_Binary($paletteData));
               $colorSpace->items[]                    = $paletteObject;
                break;
            case Zend_Pdf_Image_PNG::PNG_CHANNEL_GRAY_ALPHA:
                /* Same problem as RGB+Alpha */
                throw new Zend_Pdf_Exception( "PNGs with GRAYSCALE + Alpha are not yet supported" );
                break;
            case Zend_Pdf_Image_PNG::PNG_CHANNEL_RGB_ALPHA:
                /*
                 * Looked into this, im not sure how exactly, but to do this conversion; we
                 * must remove the alpha channel from the data stream, and place it into a SMask
                 * format with its own stream.
                 *
                 * This is probably the most common and therefore important mode to get working.
                 */
                $colorSpace = new Zend_Pdf_Element_Name('DeviceRGB');
                throw new Zend_Pdf_Exception( "4 Channel RGB+Alpha Images Are Not Supported Yet. " );
                break;
            default:
                throw new Zend_Pdf_Exception( "PNG Corruption: Invalid color space." );
        }

        if(empty($imageData)) {
            throw new Zend_Pdf_Exception( "Corrupt PNG Image. Manditory IDAT chunk not found." );
        }

        fclose($imageFile);

        /* This array adds the Predictor which must be > 10 (15=optimal) for PNG parsing */
        $decodeParms = array();
        $decodeParms['Predictor']        = new Zend_Pdf_Element_Numeric('15');
        $decodeParms['Columns']          = new Zend_Pdf_Element_Numeric($width);
        $decodeParms['Colors']           = new Zend_Pdf_Element_Numeric((($color==2)?(3):(1)));
        $decodeParms['BitsPerComponent'] = new Zend_Pdf_Element_Numeric($bits);

        $imageDictionary = $this->_resource->dictionary;
        $imageDictionary->Width            = new Zend_Pdf_Element_Numeric($width);
        $imageDictionary->Height           = new Zend_Pdf_Element_Numeric($height);
        $imageDictionary->ColorSpace       = $colorSpace;
        $imageDictionary->BitsPerComponent = new Zend_Pdf_Element_Numeric($bits);
        $imageDictionary->Filter           = new Zend_Pdf_Element_Name('FlateDecode');
        $imageDictionary->DecodeParms      = new Zend_Pdf_Element_Dictionary($decodeParms);

        if(!empty($smaskData)) {
               $smaskStream = $this->_objectFactory->newStreamObject($smaskData);
               $smaskStream->dictionary->Filter = new Zend_Pdf_Element_Name('FlateDecode');
               $imageDictionary->SMask = $smaskStream;
        }

        if(!empty($transparencyData)) {
                $imageDictionary->Mask = new Zend_Pdf_Element_Array($transparencyData);
        }

        //Include only the image IDAT section data.
        $this->_resource->value = $imageData;

        //Skip double compression
        $this->_resource->skipFilters();
    }
}
