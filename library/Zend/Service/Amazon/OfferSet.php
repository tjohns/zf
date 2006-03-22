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
class Zend_Service_Amazon_OfferSet
{
    /**
     * Parse the given Offer Set Element
     *
     * @param DomElement $dom
     */
    public function __construct(DomElement $dom)
    {
    	$xpath = new DOMXPath($dom->ownerDocument);
    	$xpath->registerNamespace('az', 'http://webservices.amazon.com/AWSECommerceService/2005-10-05');

    	$offer = $xpath->query('./az:OfferSummary', $dom);
        if ($offer->length == 1) {
        	$lowestNewPrice = $xpath->query('./az:OfferSummary/az:LowestNewPrice', $offer);
            if ($lowestNewPrice->length == 1) {
                $this->LowestNewPrice = (int) $xpath->query('./az:OfferSummary/az:LowestNewPrice/az:Amount/text()', $dom)->item(0)->data;
                $this->LowestNewPriceCurrency = (string) $xpath->query('./az:OfferSummary/az:LowestNewPrice/az:CurrencyCode/text()', $dom)->item(0)->data;
            }
            $lowestOldPrice = $xpath->query('./az:OfferSummary/az:LowestNewPrice', $offer);
            if ($lowestOldPrice->length == 1) {
                $this->LowestOldPrice = (int) $xpath->query('./az:OfferSummary/az:LowestOldPrice/az:Amount/text()', $dom)->item(0)->data;
                $this->LowestOldPriceCurrency = (string) $xpath->query('./az:OfferSummary/az:LowestOldPrice/az:CurrencyCode/text()', $dom)->item(0)->data;
            }
            $this->TotalNew = (int) $xpath->query('./az:OfferSummary/az:TotalNew/text()', $dom)->item(0)->data;
            $this->TotalUsed = (int) $xpath->query('./az:OfferSummary/az:TotalUsed/text()', $dom)->item(0)->data;
            $this->TotalCollectible = (int) $xpath->query('./az:OfferSummary/az:TotalCollectible/text()', $dom)->item(0)->data;
            $this->TotalRefurbished = (int) $xpath->query('./az:OfferSummary/az:TotalRefurbished/text()', $dom)->item(0)->data;
        }
        $offers = $xpath->query('./az:Offers/Offer', $dom);
        if ($offers->length >= 1) {
            foreach($offers as $offer) {
                $this->Offers[] = new Zend_Service_Amazon_Offer($offer);
            }
        }
    }
}
