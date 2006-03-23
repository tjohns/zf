<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to version 1.0 of the Zend Framework
 * license, that is bundled with this package in the file LICENSE, and
 * is available through the world-wide-web at the following URL:
 * http://www.zend.com/license/framework/1_0.txt. If you did not receive
 * a copy of the Zend Framework license and are unable to obtain it
 * through the world-wide-web, please send a note to license@zend.com
 * so we can mail you a copy immediately.
 *
 * @package    Zend_Service
 * @subpackage Flickr
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */


/**
 * @package    Zend_Service
 * @subpackage Flickr
 * @copyright  Copyright (c) 2005-2006 Zend Technologies Inc. (http://www.zend.com)
 * @license    Zend Framework License version 1.0
 */
class Zend_Service_Flickr_Image
{
    /**
     * @var string $uri the URI of the image
     */
    public $uri;

    /**
     * @var string $clickUri the URI for linking to the photo on Flickr
     */
    public $clickUri;

    /**
     * @var string $height the height of the image in pixels
     */
    public $height;

    /**
     * @var string $width the width of the image in pixels
     */
    public $width;

    /**
     * Parse given Flickr Image element
     *
     * @param DomElement $image
     */
    public function __construct(DomElement $image)
    {
        $this->uri      = (string) $image->getAttribute('source');
        $this->clickUri = (string) $image->getAttribute('url');
        $this->height   = (int) $image->getAttribute('height');
        $this->width    = (int) $image->getAttribute('width');
    }
}

