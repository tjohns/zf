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
 * @subpackage Amazon
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */


/**
 * @package    Zend_Service
 * @subpackage Amazon
 * @copyright  Copyright (c) 2005-2006 Zend Technologies Inc. (http://www.zend.com)
 * @license    Zend Framework License version 1.0
 */
class Zend_Service_Amazon_Offer
{
    /**
     * Parse the given Offer element
     *
     * @param DomElement $dom
     */
    public function __construct(DomElement $dom)
    {
    	$xpath = new DOMXPath($dom->ownerDocument);
    	$xpath->registerNamespace('az', 'http://webservices.amazon.com/AWSECommerceService/2005-10-05');
    	$this->MerchantId = (string) $xpath->query('./az:Merchant/az:MerchantId/text()', $dom)->item(0)->data;
        $this->GlancePage = (string) $xpath->query('./az:Merchant/az:GlancePage/text()', $dom)->item(0)->data;
        $this->Condition = (string) $xpath->query('./az:OfferAttributes/az:Condition/text()', $dom)->item(0)->data;
        $this->OfferListingId = (string) $xpath->query('./az:OfferListing/az:OfferListingId/text()', $dom)->item(0)->data;
        $this->Price = (int) $xpath->query('./az:OfferListing/az:Price/az:Amount/text()', $dom)->item(0)->data;
        $this->CurrencyCode = (string) $xpath->query('./az:OfferListing/az:Price/az:CurrencyCode/text()', $dom)->item(0)->data;
        $this->Availability = (string) $xpath->query('./az:OfferListing/az:Availability/text()', $dom)->item(0)->data;
        $this->IsEligibleForSuperSaverShipping = (bool) $xpath->query('./az:OfferListing/az:isEligibleForSuperSaverShipping/text()', $dom)->item(0)->data;
    }
}
