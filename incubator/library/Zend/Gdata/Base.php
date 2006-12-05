<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Zend/Gdata.php';

/**
 * Gdata Base
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_Base extends Zend_Gdata
{
    const BASE_FEED_URI ='http://www.google.com/base/feeds/snippets';
    const BASE_POST_URI ='http://www.google.com/base/feeds/snippets';

    // Base operations:
    // @todo: attribute queries
    // @todo: attribute types
    // @todo: location queries
    // @todo: date queries
    // @todo: url queries
    // @todo: list of item types 
    // @todo: item types metadata
    // @todo: attribute statistics

    // Base-specific response content:
    // @todo: <gm:item_type>
    // @todo: <gm:attributes>
    // @todo: <gm:attribute>
    // @todo: <gm:value>
    // @todo: querying "content=attributes,meta" statistics
    // @todo: <gm:impressions>
    // @todo: <gm:clicks>
    // @todo: <gm:page_views>
    // @todo: <gm:impressions>

    protected static $defaultTokenName = 'base_token';

    protected $attributeQueryTerms = array();

    /**
     * Create Gdata_Calendar object
     *
     * @param string $email
     * @param string $password
     *
     */
    public function __construct(Zend_Http_Client $client, $key = null)
    {
        parent::__construct($client);
        if ($key != null) {
            $this->setKey($key);
        }
    }

    /**
     * Retreive feed object
     *
     * @return Zend_Feed
     */
    public function getFeed()
    {
        $uri = self::BASE_FEED_URI;
        if (isset($this->params['_entry'])) {
            $uri .= '/' . $this->params['_entry'];
        } else if (isset($this->params['_category'])) {
            $uri .= '/-/' . $this->params['_category'];
        }
        $uri .= $this->getQueryString();
        return parent::getFeed($uri);
    }

    protected function getQueryString()
    {
        $queryArray = array();
        if (isset($this->query)) {
            $bq = $this->query;
            $queryArray[] = $bq;
        }
        foreach (array_keys($this->attributeQueryTerms) as $attributeName) {
            foreach ($this->attributeQueryTerms[$attributeName] as $attributeValue) {
                $queryArray[] = "[$attributeName: $attributeValue]";
            }
        }
        $this->query = implode(' ', $queryArray);
        $queryString = parent::getQueryString();
        if (isset($bq)) {
            $this->query = $bq;
        }
        return $queryString;
    }

    /**
     * POST xml data to Google with authorization headers set
     *
     * @param string $xml
     * @return Zend_Http_Response
     */
    public function post($xml)
    {
        return parent::post($xml, Zend_Gdata_Base::BASE_POST_URI);
    }

    public function addAttributeQuery($attributeName, $attributeValue)
    {
        // @todo: validate attribute name?
        $this->attributeQueryTerms[$attributeName][] = $attributeValue;
    }

    public function unsetAttributeQuery($attributeName = null)
    {
        if ($attributeName == null) {
            $this->attributeQueryTerms = array();
        } else {
            unset($this->attributeQueryTerms[$attributeName]);
        }
    }

    protected function __set($var, $value)
    {
        switch ($var) {
            case 'query':
                $var = 'bq';
                break;
            case 'category':
                $var = '_category';
                // @todo: validate category value
                break;
            case 'entry':
                $var = '_entry';
                // @todo: validate entry value
                break;
            case 'updatedMin':
            case 'updatedMax':
            case 'publishedMin':
            case 'publishedMax':
                // @todo: throw exception for unsupported params?
                break;
            default:
                // other params are handled by the parent
                break;
        }

        parent::__set($var, $value);
    }

    protected function __get($var)
    {
        switch ($var) {
            case 'query':
                $var = 'bq';
                break;
            case 'category':
                $var = '_category';
                break;
            case 'entry':
                $var = '_entry';
                break;
            default:
                // other params are handled by the parent
                break;
        }
        return parent::__get($var);
    }

    protected function __isset($var)
    {
        switch ($var) {
            case 'query':
                $var = 'bq';
                break;
            case 'category':
                $var = '_category';
                break;
            case 'entry':
                $var = '_entry';
                break;
            default:
                // other params are handled by the parent
                break;
        }
        return parent::__isset($var);
    }

    protected function __unset($var)
    {
        switch ($var) {
            case 'query':
                $var = 'bq';
                break;
            case 'category':
                $var = '_category';
                break;
            case 'entry':
                $var = '_entry';
                break;
            default:
                // other params are handled by the parent
                break;
        }
        return parent::__unset($var);
    }

}

