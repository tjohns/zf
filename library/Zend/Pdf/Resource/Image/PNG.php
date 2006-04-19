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
     * @todo Add compression conversions to support compression strategys other than PNG_COMPRESSION_DEFAULT_STRATEGY.
     * @todo Add pre-compression filtering.
     * @todo Add interlaced image handling.
     * @todo Add support for 16-bit images. Requires PDF version bump to 1.5 at least.
     * @todo Add processing for all PNG chunks defined in the spec. gAMA etc.
     * @todo Fix tRNS chunk support for Indexed Images to a SMask.
     */
    public function __construct($imageFileName)
    {
        if (($imageFile = @fopen($imageFileName, 'rb')) === false ) {
            throw new Zend_Pdf_Exception( "Can not open '$imageFileName' file for reading." );
        }

        parent::__construct();

        //Default way of working with PNGs is to copy compressed data
        $compressed = true;

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
            throw new Zend_Pdf_Exception( "Only the default compression strategy is currently supported." );
        }

        if (ord(fread($imageFile,1)) != Zend_Pdf_Image_PNG::PNG_FILTER_NONE) {
            throw new Zend_Pdf_Exception( "Filtering methods are not currently supported. " );
        }
        if (ord(fread($imageFile,1)) != Zend_Pdf_Image_PNG::PNG_INTERLACING_DISABLED) {
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
                case 'IDAT': //Image Data
                    /*
                     * Reads the actual image data from the PNG file. Since we know at this point that the compression
                     * strategy is the default strategy, we also know that this data is Zip compressed. We will either copy
                     * the data directly to the PDF and provide the correct FlateDecode predictor, or decompress the data
                     * decode the filters and output the data as a raw pixel map.
                     */
                    $imageData .= fread($imageFile, $chunkLength);
                    fseek($imageFile, 4, SEEK_CUR);
                    break;

                case 'PLTE': //Palette
                    $paletteData = fread($imageFile, $chunkLength);
                    fseek($imageFile, 4, SEEK_CUR);
                    break;

                case 'tRNS': //Basic (non-alpha channel) transparency.
                    $trnsData = fread($imageFile, $chunkLength);
                    switch ($color) {
                        case Zend_Pdf_Image_PNG::PNG_CHANNEL_GRAY:
                            $baseColor = ord(substr($trnsData, 1, 1));
                            $transparencyData = array(new Zend_Pdf_Element_Numeric($baseColor), new Zend_Pdf_Element_Numeric($baseColor));
                            break;
                        case Zend_Pdf_Image_PNG::PNG_CHANNEL_RGB:
                            $red = ord(substr($trnsData,1,1));
                            $green = ord(substr($trnsData,3,1));
                            $blue = ord(substr($trnsData,5,1));
                            $transparencyData = array(new Zend_Pdf_Element_Numeric($red), new Zend_Pdf_Element_Numeric($red), new Zend_Pdf_Element_Numeric($green), new Zend_Pdf_Element_Numeric($green), new Zend_Pdf_Element_Numeric($blue), new Zend_Pdf_Element_Numeric($blue));
                            break;
                        case Zend_Pdf_Image_PNG::PNG_CHANNEL_INDEXED:
                            //Find the first transparent color in the index, we will mask that. (This is a bit of a hack. This should be a SMask and mask all entries values).
                            if(($trnsIdx = strpos($trnsData, chr(0))) !== false) {
                                $transparencyData = array(new Zend_Pdf_Element_Numeric($trnsIdx), new Zend_Pdf_Element_Numeric($trnsIdx));
                            }
                            break;
                        case Zend_Pdf_Image_PNG::PNG_CHANNEL_GRAY_ALPHA:
                            // Fall through to the next case
                        case Zend_Pdf_Image_PNG::PNG_CHANNEL_RGB_ALPHA:
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
                if(empty($paletteData)) {
                    throw new Zend_Pdf_Exception( "PNG Corruption: No palette data read for indexed type PNG." );
                }
                $colorSpace = new Zend_Pdf_Element_Array();
                $colorSpace->items[] = new Zend_Pdf_Element_Name('Indexed');
                $colorSpace->items[] = new Zend_Pdf_Element_Name('DeviceRGB');
                $colorSpace->items[] = new Zend_Pdf_Element_Numeric((strlen($paletteData)/3-1));
                $paletteObject = $this->_objectFactory->newObject(new Zend_Pdf_Element_String_Binary($paletteData));
                $colorSpace->items[] = $paletteObject;
                break;
            case Zend_Pdf_Image_PNG::PNG_CHANNEL_GRAY_ALPHA:
                /*
                 * To decode PNG's with alpha data we must create two images from one. One image will contain the Gray data
                 * the other will contain the Gray transparency overlay data. The former will become the object data and the latter
                 * will become the Shadow Mask (SMask).
                 */
                if($bits > 8) {
                    /*
                     * The calculations are slightly different for 16 bit depth images. We will have to correct the math to support 16bit.
                     * Further, to support 16-bit images the PDF output version must be bumped to 1.5.
                     */
                    throw new Zend_Pdf_Exception('Not implemented yet. PNGs with bit depth greater than 8.');
                }
                $colorSpace = new Zend_Pdf_Element_Name('DeviceGray');

                $imageDataTmp = null;
                $imageDataRaw = null;
                $smaskData = null;

                if (extension_loaded('zlib')) {
                    $trackErrors = ini_get( "track_errors");
                    ini_set('track_errors', '1');

                    if (($imageDataRaw = @gzuncompress($imageData)) === false) {
                        ini_set('track_errors', $trackErrors);
                        throw new Zend_Pdf_Exception($php_errormsg);
                    }

                    ini_set('track_errors', $trackErrors);
                } else {
                    throw new Zend_Pdf_Exception('Not implemented yet. Currently zlib support required to use Alpha PNGs');
                }

                $compressed = false;
                $channels = 2; //GA
                $scanLineLength = (ceil(($bits * $channels * $width)/8)) + 1;
                $bytesPerPixel = ceil(($bits * $channels) / 8);

                $pngDataRawDecoded = '';
                $lastScanLineDataDecoded = '';
                //For every scanline (row) the first byte is the filter
                for($scanline = 0, $scanlines = $height; $scanline < $scanlines; $scanline++) {
                    $currentScanLineDataStruct = substr($imageDataRaw, $scanline * $scanLineLength, $scanLineLength);
                    $filter = ord($currentScanLineDataStruct[0]);
                    $currentScanLineData = substr($currentScanLineDataStruct, 1);
                    $currentScanLineDataDecoded = '';
                    switch($filter) {
                        case Zend_Pdf_Image_PNG::PNG_FILTER_NONE:
                            $currentScanLineDataDecoded = $currentScanLineData;
                        break;
                        case Zend_Pdf_Image_PNG::PNG_FILTER_SUB:
                            for($byte = 0, $byteLen = strlen($currentScanLineData); $byte < $byteLen; $byte++) {
                                //See spec @ http://www.w3.org/TR/PNG/#9Filters for function definitions
                                $x = ord($currentScanLineData[$byte]);
                                $a = (($byte<$bytesPerPixel)?(0):(ord($currentScanLineDataDecoded[$byte-$bytesPerPixel])));
                                $currentScanLineDataDecoded .= chr(($x + $a) % 256);
                            }
                        break;
                        case Zend_Pdf_Image_PNG::PNG_FILTER_UP:
                            for($byte = 0, $byteLen = strlen($currentScanLineData); $byte < $byteLen; $byte++) {
                                $x = ord($currentScanLineData[$byte]);
                                $b = ((empty($lastScanLineDataDecoded))?(0):(ord($lastScanLineDataDecoded[$byte])));
                                $currentScanLineDataDecoded .= chr(($x + $b) % 256);
                            }
                        break;
                        case Zend_Pdf_Image_PNG::PNG_FILTER_AVERAGE:
                            for($byte = 0, $byteLen = strlen($currentScanLineData); $byte < $byteLen; $byte++) {
                                $x = ord($currentScanLineData[$byte]);
                                $a = (($byte<$bytesPerPixel)?(0):(ord($currentScanLineDataDecoded[$byte-$bytesPerPixel])));
                                $b = ((empty($lastScanLineDataDecoded))?(0):(ord($lastScanLineDataDecoded[$byte])));
                                $currentScanLineDataDecoded .= chr(($x + floor(($a + $b)/2)) % 256);
                            }
                        break;
                        case Zend_Pdf_Image_PNG::PNG_FILTER_PAETH:
                            for($byte = 0, $byteLen = strlen($currentScanLineData); $byte < $byteLen; $byte++) {
                                $x = ord($currentScanLineData[$byte]);
                                $a = (($byte<$bytesPerPixel)?(0):(ord($currentScanLineDataDecoded[$byte-$bytesPerPixel])));
                                $b = ((empty($lastScanLineDataDecoded))?(0):(ord($lastScanLineDataDecoded[$byte])));
                                $c = ((empty($lastScanLineDataDecoded) || ($byte<$bytesPerPixel))?(0):(ord($lastScanLineDataDecoded[$byte-$bytesPerPixel])));
                                $currentScanLineDataDecoded .= chr(($x + $this->_paethPredictor($a, $b, $c)) % 256);
                            }
                        break;
                    }
                    $lastScanLineDataDecoded = $currentScanLineDataDecoded;
                    $pngDataRawDecoded .= $currentScanLineDataDecoded;
                }

                //Iterate every pixel and copy out gray data and alpha channel (this will be slow)
                for($pixel = 0, $pixelcount = ($width * $height); $pixel < $pixelcount; $pixel++) {
                    $imageDataTmp .= $pngDataRawDecoded[($pixel*2)];
                    $smaskData .= $pngDataRawDecoded[($pixel*2)+1];
                }

                $imageData = $imageDataTmp; //Overwrite image data with the gray channel without alpha
                unset($pngDataRawDecoded, $imageDataTmp); //Allow php to free memory
                break;
            case Zend_Pdf_Image_PNG::PNG_CHANNEL_RGB_ALPHA:
                /*
                 * To decode PNG's with alpha data we must create two images from one. One image will contain the RGB data
                 * the other will contain the Gray transparency overlay data. The former will become the object data and the latter
                 * will become the Shadow Mask (SMask).
                 */
                if($bits > 8) {
                    /*
                     * The calculations are slightly different for 16 bit depth images. We will have to correct the math to support 16bit.
                     * Further, to support 16-bit images the PDF output version must be bumped to 1.5.
                     */
                    throw new Zend_Pdf_Exception('Not implemented yet. PNGs with bit depth greater than 8.');
                }
                $colorSpace = new Zend_Pdf_Element_Name('DeviceRGB');

                $imageDataTmp = null;
                $imageDataRaw = null;
                $smaskData = null;

                if (extension_loaded('zlib')) {
                    $trackErrors = ini_get( "track_errors");
                    ini_set('track_errors', '1');

                    if (($imageDataRaw = @gzuncompress($imageData)) === false) {
                        ini_set('track_errors', $trackErrors);
                        throw new Zend_Pdf_Exception($php_errormsg);
                    }

                    ini_set('track_errors', $trackErrors);
                } else {
                    throw new Zend_Pdf_Exception('Not implemented yet. Currently zlib support required to use Alpha PNGs');
                }

                $compressed = false;
                $channels = 4; //RGBA
                $scanLineLength = (ceil(($bits * $channels * $width)/8)) + 1;
                $bytesPerPixel = ceil(($bits * $channels) / 8);

                $pngDataRawDecoded = '';
                $lastScanLineDataDecoded = '';
                //For every scanline (row) the first byte is the filter
                for($scanline = 0, $scanlines = $height; $scanline < $scanlines; $scanline++) {
                    $currentScanLineDataStruct = substr($imageDataRaw, $scanline * $scanLineLength, $scanLineLength);
                    $filter = ord($currentScanLineDataStruct[0]);
                    $currentScanLineData = substr($currentScanLineDataStruct, 1);
                    $currentScanLineDataDecoded = '';
                    switch($filter) {
                        case Zend_Pdf_Image_PNG::PNG_FILTER_NONE:
                            $currentScanLineDataDecoded = $currentScanLineData;
                        break;
                        case Zend_Pdf_Image_PNG::PNG_FILTER_SUB:
                            for($byte = 0, $byteLen = strlen($currentScanLineData); $byte < $byteLen; $byte++) {
                                //See spec @ http://www.w3.org/TR/PNG/#9Filters for function definitions
                                $x = ord($currentScanLineData[$byte]);
                                $a = (($byte<$bytesPerPixel)?(0):(ord($currentScanLineDataDecoded[$byte-$bytesPerPixel])));
                                $currentScanLineDataDecoded .= chr(($x + $a) % 256);
                            }
                        break;
                        case Zend_Pdf_Image_PNG::PNG_FILTER_UP:
                            for($byte = 0, $byteLen = strlen($currentScanLineData); $byte < $byteLen; $byte++) {
                                $x = ord($currentScanLineData[$byte]);
                                $b = ((empty($lastScanLineDataDecoded))?(0):(ord($lastScanLineDataDecoded[$byte])));
                                $currentScanLineDataDecoded .= chr(($x + $b) % 256);
                            }
                        break;
                        case Zend_Pdf_Image_PNG::PNG_FILTER_AVERAGE:
                            for($byte = 0, $byteLen = strlen($currentScanLineData); $byte < $byteLen; $byte++) {
                                $x = ord($currentScanLineData[$byte]);
                                $a = (($byte<$bytesPerPixel)?(0):(ord($currentScanLineDataDecoded[$byte-$bytesPerPixel])));
                                $b = ((empty($lastScanLineDataDecoded))?(0):(ord($lastScanLineDataDecoded[$byte])));
                                $currentScanLineDataDecoded .= chr(($x + floor(($a + $b)/2)) % 256);
                            }
                        break;
                        case Zend_Pdf_Image_PNG::PNG_FILTER_PAETH:
                            for($byte = 0, $byteLen = strlen($currentScanLineData); $byte < $byteLen; $byte++) {
                                $x = ord($currentScanLineData[$byte]);
                                $a = (($byte<$bytesPerPixel)?(0):(ord($currentScanLineDataDecoded[$byte-$bytesPerPixel])));
                                $b = ((empty($lastScanLineDataDecoded))?(0):(ord($lastScanLineDataDecoded[$byte])));
                                $c = ((empty($lastScanLineDataDecoded) || ($byte<$bytesPerPixel))?(0):(ord($lastScanLineDataDecoded[$byte-$bytesPerPixel])));
                                $currentScanLineDataDecoded .= chr(($x + $this->_paethPredictor($a, $b, $c)) % 256);
                            }
                        break;
                    }
                    $lastScanLineDataDecoded = $currentScanLineDataDecoded;
                    $pngDataRawDecoded .= $currentScanLineDataDecoded;
                }

                //Iterate every pixel and copy out rgb data and alpha channel (this will be slow)
                for($pixel = 0, $pixelcount = ($width * $height); $pixel < $pixelcount; $pixel++) {
                    $imageDataTmp .= $pngDataRawDecoded[($pixel*4)+0] . $pngDataRawDecoded[($pixel*4)+1] . $pngDataRawDecoded[($pixel*4)+2];
                    $smaskData .= $pngDataRawDecoded[($pixel*4)+3];
                }

                $imageData = $imageDataTmp; //Overwrite image data with the RGB channel without alpha
                unset($pngDataRawDecoded, $imageDataTmp); //Allow php to free memory
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
        $decodeParms['Colors']           = new Zend_Pdf_Element_Numeric((($color==Zend_Pdf_Image_PNG::PNG_CHANNEL_RGB || $color==Zend_Pdf_Image_PNG::PNG_CHANNEL_RGB_ALPHA)?(3):(1)));
        $decodeParms['BitsPerComponent'] = new Zend_Pdf_Element_Numeric($bits);

        $imageDictionary = $this->_resource->dictionary;
        $imageDictionary->Width            = new Zend_Pdf_Element_Numeric($width);
        $imageDictionary->Height           = new Zend_Pdf_Element_Numeric($height);
        $imageDictionary->ColorSpace       = $colorSpace;
        $imageDictionary->BitsPerComponent = new Zend_Pdf_Element_Numeric($bits);
        if($compressed) {
            $imageDictionary->Filter       = new Zend_Pdf_Element_Name('FlateDecode');
            $imageDictionary->DecodeParms  = new Zend_Pdf_Element_Dictionary($decodeParms);
        }

        if(!empty($smaskData)) {
            /*
             * Includes the Alpha transparency data as a Gray Image, then assigns the image as the Shadow Mask for the main image data.
             */
            $smaskStream = $this->_objectFactory->newStreamObject($smaskData);
            $smaskStream->dictionary->Type = new Zend_Pdf_Element_Name('XObject');
            $smaskStream->dictionary->Subtype = new Zend_Pdf_Element_Name('Image');
            $smaskStream->dictionary->Width = new Zend_Pdf_Element_Numeric($width);
            $smaskStream->dictionary->Height = new Zend_Pdf_Element_Numeric($height);
            $smaskStream->dictionary->ColorSpace = new Zend_Pdf_Element_Name('DeviceGray');
            $smaskStream->dictionary->BitsPerComponent = new Zend_Pdf_Element_Numeric($bits);
            $imageDictionary->SMask = $smaskStream;
        }

        if(!empty($transparencyData)) {
            //This is experimental and not properly tested.
            $imageDictionary->Mask = new Zend_Pdf_Element_Array($transparencyData);
        }

        //Include only the image IDAT section data.
        $this->_resource->value = $imageData;

        //Skip double compression
        $this->_resource->skipFilters();
    }

    /**
     * Paeth Predictor
     *
     * The Paeth Predictor is used in PNG decompression. This is an implementation
     * of the pseudocode given in the png specification.
     *
     * @param int $a
     * @param int $b
     * @param int $c
     * @return int
     */
    protected function _paethPredictor($a,$b,$c) {
        $p = $a + $b - $c;
        $pa = abs($p - $a);
        $pb = abs($p - $b);
        $pc = abs($p - $c);
        if(($pa <= $pb) && ($pa <= $pc)) {
            return $a;
        } else if($pb <= $pc) {
            return $b;
        } else {
            return $c;
        }
    }
}




