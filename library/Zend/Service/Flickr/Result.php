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
class Zend_Service_Flickr_Result
{
	/**
     * @var int $id The photo's Flickr ID
     */
	public $id;

	/**
     * @var int $owner The photo owner's NSID.
     */
	public $owner;

	/**
     * @var string $secret A key used in URI construction.
     */
	public $secret;

	/**
     * @var string $server The servername to use for URI construction.
     */
	public $server;

	/**
     * @var string $title The photo's title.
     */
	public $title;

	/**
     * @var bool $ispublic The photo is public.
     */
	public $ispublic;

	/**
     * @var bool $isfriend The photo is visible to you because you are a friend of the owner.
     */
	public $isfriend;

	/**
     * @var bool $isfamily The photo is visible to you because you are family of the owner.
     */
	public $isfamily;

	/**
     * @var string $license The license the photo is available under.
     */
	public $license;

	/**
     * @var string $date_upload  The date the photo was uploaded.
     */
	public $date_upload;

	/**
     * @var string $date_upload  The date the photo was taken.
     */
	public $date_taken;

	/**
     * @var string $owner_name THe screenname of the owner.
     */
	public $owner_name;

	/**
     * @var string $icon_server The server used in assembling icon URLs.
     */
	public $icon_server;

	/**
     * @var Zend_Service_Flickr_Image $Square A 75x75 thumbnail of the image.
     */
	public $Square;

	/**
     * @var Zend_Service_Flickr_Image $Thumbnail a 100 pixel thumbnail of the image.
     */
	public $Thumbnail;

	/**
     * @var Zend_Service_Flickr_Image $Small a 240 pixel version of the image.
     */
	public $Small;

	/**
     * @var Zend_Service_Flickr_Image $Medium a 500 pixel version of the image.
     */
	public $Medium;

	/**
     * @var Zend_Service_Flickr_Image $Large a 640 pixel version of the image.
     */
	public $Large;

	/**
     * @var Zend_Service_Flickr_Image $Original the original image.
     */
	public $Original;

	/**
	 * Original Zend_Service_Flickr object
	 *
	 * @var Zend_Service_Flickr
	 */
	protected $_flickr;

	/**
	 * Parse the Flickr Result
	 *
	 * @param DomElement $image
	 * @param Zend_Service_Flickr $flickr Original Zend_Service_Flickr object with which the request was made
	 */
	function __construct(DomElement $image, Zend_Service_Flickr $flickr)
	{
		$xpath = new DOMXPath($image->ownerDocument);
		$photo_properties = array('id', 'owner', 'secret', 'server', 'title', 'ispublic', 'isfriend', 'isfamily',
		                          'license', 'date_upload', 'date_taken', 'owner_name', 'icon_server');

        foreach($xpath->query('./@*', $image) as $property) {
        	$this->{$property->name} = (string) $property->value;
        }

		$this->_flickr = $flickr;

		foreach($this->_flickr->getImageDetails($this->id) as $k => $v) {
			$this->$k = $v;
		}
	}
}


