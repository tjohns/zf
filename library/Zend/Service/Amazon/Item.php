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
class Zend_Service_Amazon_Item
{
    public $ASIN;
    public $DetailPageURL;
    public $SalesRank;
    public $SmallImage;
    public $MediumImage;
    public $LargeImage;
    public $Subjects;
    public $OfferSummary;
    public $Offers;
    public $CustomerReviews;
    public $SimilarProducts;
    public $Accessories;
    public $Tracks;
    public $ListmaniaLists;
    public $PromotionalTag;
    private $_xml;


    /**
     * Parse the given <Item> element
     *
     * @param DomElement $dom
     */
    function __construct(DomElement $dom)
    {
    	$xpath = new DOMXPath($dom->ownerDocument);
    	$xpath->registerNamespace('az', 'http://webservices.amazon.com/AWSECommerceService/2005-10-05');
    	$this->ASIN = $xpath->query('./az:ASIN/text()', $dom)->item(0)->data;

    	$result = $xpath->query('./az:DetailPageURL/text()', $dom);
    	if ($result->length == 1) {
    		$this->DetailPageURL = $result->item(0)->data;
    	}

    	$result = $xpath->query('./az:ItemAttributes/az:*/text()', $dom);
    	if ($result->length >= 1) {
            foreach ($result as $v) {
                if ($v->parentNode->tagName == 'ListPrice') {
                    $this->ListPrice = (int) $v->data;
                    $this->ListPriceCurrency = (string) $xpath->query('./az:itemAttributes/CurrenyCode/text()', $dom)->item(0)->data;
                } else {
                    if (isset($this->{$v->parentNode->tagName})) {
                        $this->{$v->parentNode->tagName} = array($this->{$v->parentNode->tagName}, (string) $v->data);
                    } else {
                        $this->{$v->parentNode->tagName} = (string) $v->data;
                    }
                }
            }
        }

        foreach(array('SmallImage', 'MediumImage', 'LargeImage') as $im) {
        	$result = $xpath->query("./az:imageSets/ImageSet[position() = 1]/$im", $dom);
            if ($result->length == 1) {
                $this->$im = new Zend_Service_Amazon_Image($result->item(0));
            }
        }

        $result = $xpath->query('./az:SalesRank/text()', $dom);
    	if ($result->length == 1) {
    		$this->SalesRank = (int) $result->item(0)->data;
    	}

    	$result = $xpath->query('./az:CustomerReviews/*', $dom);
    	if ($result->length >= 1) {
            foreach ($result as $review) {
                $this->CustomerReviews[] = new Zend_Service_Amazon_CustomerReview($review);
                $this->AverageRating = (float) $xpath->query('./az:CustomerReviews/AverageRating/text()', $dom)->item(0)->data;
                $this->TotalReviews = (int) $xpath->query('./az:CustomerReviews/TotalReviews/text()', $dom)->item(0)->data;
            }
        }

        $result = $xpath->query('./az:EditorialReviews/*', $dom);
    	if ($result->length == 1) {
            foreach ($result->childNodes as $r) {
                $this->EditorialReviews[] = new Zend_Service_Amazon_EditorialReview($r);
            }
        }

        $result = $xpath->query('./az:SimilarProducts/*', $dom);
    	if ($result->length == 1) {
            foreach ($result->childNodes as $r) {
                $this->SimilarProducts[] = new Zend_Service_Amazon_SimilarProduct($r);
            }
        }

        $result = $xpath->query('./az:ListmaniaLists/*', $dom);
    	if ($result->length == 1) {
            foreach ($result->childNodes as $r) {
                $this->ListmaniaLists[] = new Zend_Service_Amazon_ListmaniaList($r);
            }
        }

        $result = $xpath->query('./az:Tracks/az:Disc', $dom);
        if ($result->length > 1) {
			foreach ($result as $disk) {
                foreach ($xpath->query('./*/text()', $disk) as $t) {
                    $this->Tracks[$disk->getAttribute('number')] = (string) $t->data;
                }
            }
        } else if ($result->length == 1) {
            foreach ($xpath->query('./*/text()', $result->item(0)) as $t) {
                $this->Tracks[] = (string) $t->firstChild->data;
            }
        }

        $result = $xpath->query('./az:Offers', $dom);
        if ($result->length > 1) {
        	$this->Offers = new Zend_Service_Amazon_OfferSet($dom);
        }

        $result = $xpath->query('./az:Accessories/*', $dom);
        if ($result->length > 1) {
            foreach ($result->childNodes as $r) {
                $this->Accessories[] = new Zend_Service_Amazon_Accessories($r);
            }
        }

        $this->xml = $dom;
    }


    /**
     * Return the Items original XML
     *
     * @return string
     */
    function asXML()
    {
    	return $this->dom->ownerDocument->saveXML($this->dom);
    }
}

