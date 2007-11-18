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
 * @package    Zend_Service
 * @subpackage Technorati
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * @see Zend_Service_Technorati_Exception
 */
require_once 'Zend/Service/Technorati/Exception.php';

/** 
 * @see Zend_Service_Technorati_Author 
 */
require_once 'Zend/Service/Technorati/Author.php';

/**
 * @see Zend_Service_Technorati_Weblog
 */
require_once 'Zend/Service/Technorati/Weblog.php';


/**
 * TODO: phpdoc
 * 
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Technorati
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Technorati
{
    /** Base Technorati API URI */
    const URI_BASE = 'http://api.technorati.com';
    
    /**
     * TODO: extract all API paths and store them into constants
     */
    

    /**
     * Your Technorati API key
     *
     * @var     string
     * @access  protected
     */
    protected $_apiKey;

    /**
     * Zend_Rest_Client instance
     *
     * @var     Zend_Rest_Client
     * @access  protected
     */
    protected $_restClient;


    /**
     * Zend_Service_Technorati Constructor, setup character encoding
     *
     * @param   string $apiKey  Your Technorati API key
     */
    public function __construct($apiKey)
    {
        iconv_set_encoding('output_encoding', 'UTF-8');
        iconv_set_encoding('input_encoding', 'UTF-8');
        iconv_set_encoding('internal_encoding', 'UTF-8');

        $this->_apiKey = $apiKey;
    }


    /**
     * Cosmos Query
     *
     * Cosmos lets you see what blogs are linking to a given URL.
     * On the Technorati site, you can enter a URL in the searchbox and
     * it will return a list of blogs linking to it.
     * The API version allows more features and gives you a way
     * to use the cosmos on your own site.
     *
     * Query options include:
     *
     * 'type'       => (link|weblog)
     *      optional - A value of link returns the freshest links referencing your target URL.
     *      A value of weblog returns the last set of unique weblogs referencing your target URL.
     * 'limit'      => (int)
     *      optional - adjust the size of your result from the default value of 20
     *      to between 1 and 100 results.
     * 'start'      => (int)
     *      optional - adjust the range of your result set.
     *      Set this number to larger than zero and you will receive
     *      the portion of Technorati's total result set ranging from start to start+limit.
     *      The default start value is 1.
     * 'current'    => (yes|no)
     *      optional - the default setting of true
     *      Technorati returns links that are currently on a weblog's homepage.
     *      Set this parameter to false if you would like to receive all links
     *      to the given URL regardless of their current placement on the source blog.
     * 'claim'      => (int)
     *      optional - the default setting of 0 returns no user information
     *      about each weblog included in the result set when available.
     *      Set this parameter to 1 to include Technorati member data
     *      in the result set when a weblog in your result set
     *      has been successfully claimed by a member of Technorati.
     * 'highlight'  => (int)
     *      optional - the default setting of 1
     *      highlights the citation of the given URL within the weblog excerpt.
     *      Set this parameter to 0 to apply no special markup to the blog excerpt.
     *
     * @param   string $url     Target URL. Prefixes http:// and www. are optional.
     * @param   array $options  Additional parameters to refine your query
     * @return  Zend_Service_Technorati_CosmosResultSet Cosmos resultset
     * @link    http://technorati.com/developers/api/cosmos.html Technorati API: Cosmos Query reference
     */
    public function cosmos($url, $options = null)
    {
        static $defaultOptions = array( 'type'      => 'link',
                                        'start'     => 1,
                                        'limit'     => 20,
                                        'current'   => 'yes',
                                        'format'    => 'xml',
                                        'claim'     => 0,
                                        'highlight' => 1,
                                        );

        $options['url'] = $url;

        $options = $this->_prepareOptions($options, $defaultOptions);
        $this->_validateCosmos($options);

        // make request
        $restClient = $this->getRestClient();
        $restClient->getHttpClient()->resetParameters();
        $response = $restClient->restGet('/cosmos', $options);
        self::_checkResponseErrors($response);

        $dom = new DOMDocument();
        $dom->loadXML($response->getBody());
        self::_checkErrors($dom);

        /** 
         * @see Zend_Service_Technorati_CosmosResultSet 
		 */
		require_once 'Zend/Service/Technorati/CosmosResultSet.php';
        return new Zend_Service_Technorati_CosmosResultSet($dom);
    }

    /**
     * Search Query
     *
     * TODO: phpdoc
     * 
     * @todo    function :)
     *
     * @param   string  $query      Set this to the words you are searching for.
     *                              Separate words with '+' as usual.
     * @param   array   $options    Additional parameters to refine your query
     * @return  Zend_Service_Technorati_SearchResultSet Search resultset
     * @link    http://technorati.com/developers/api/search.html Technorati API: Search Query reference
     * /
    public function search($query, $options = null)
    {
    } */

    /**
     * Tag Query
     * 
     * TODO: phpdoc
     *
     * @todo    function :)
     *
     * @param   string  $tag        Set this to the tag term you are searching posts for.
     * @param   array   $options    Additional parameters to refine your query
     * @return  Zend_Service_Technorati_TagResultSet Tag resultset
     * @link    http://technorati.com/developers/api/tag.html Technorati API: Tag Query reference
     * /
    public function tag($tag, $options = null)
    {
    } */
    
    /**
     * TODO: missing functions
     * 
     * Search
     * - http://technorati.com/developers/api/dailycounts.html
     * - http://technorati.com/developers/api/toptags.html
     * Blog information
     * - http://technorati.com/developers/api/bloginfo.html
     * - http://technorati.com/developers/api/blogposttags.html
     */

    /**
     * GetInfo Query
     * 
     * The getinfo query tells you things that Technorati knows about a member.
     * 
     * The returned info is broken up into two sections: 
     * The first part describes some information that the user wants 
     * to allow people to know about him- or herself. 
     * The second part of the document is a listing of the weblogs 
     * that the user has successfully claimed and the information 
     * that Technorati knows about these weblogs.
     *
     * @param   String $username    Set this to the Technorati user name you are searching for.
     * @return  Zend_Service_Technorati_GetInfoResult GetInfo result
     * @link    http://technorati.com/developers/api/getinfo.html Technorati API: GetInfo reference
     */
    public function getInfo($username) {
        static $defaultOptions = array('format' => 'xml');

        $options['username'] = $username;

        $options = $this->_prepareOptions($options, $defaultOptions);
        $this->_validateGetInfo($options);

        // make request
        $restClient = $this->getRestClient();
        $restClient->getHttpClient()->resetParameters();
        $response = $restClient->restGet('/getinfo', $options);
        self::_checkResponseErrors($response);

        $dom = new DOMDocument();
        $dom->loadXML($response->getBody());
        self::_checkErrors($dom);

        /** 
         * @see Zend_Service_Technorati_GetInfoResult
         */
        require_once 'Zend/Service/Technorati/GetInfoResult.php';
        return new Zend_Service_Technorati_GetInfoResult($dom);
    }
    
    /**
     * KeyInfo Query
     *
     * The keyinfo query provides information on daily usage of an API key. 
     * Key Info Queries do not count against a key's daily query limit.
     * 
     * A day is defined as 00:00-23:59 Pacific time.
     * 
     * @return  Zend_Service_Technorati_KeyInfoResult KeyInfo result
     * @link    http://developers.technorati.com/wiki/KeyInfo Technorati API: Key Info reference
     */
    public function keyInfo()
    {
        static $defaultOptions = array();

        $options = $this->_prepareOptions(array(), $defaultOptions);
        // you don't need to validate this request
        // because key is the only mandatory element 
        // and it's already set in _prepareOptions()

        // now search for keyinfo
        $restClient = $this->getRestClient();
        $restClient->getHttpClient()->resetParameters();
        $response = $restClient->restGet('/keyinfo', $options);
        self::_checkResponseErrors($response);

        $dom = new DOMDocument();
        $dom->loadXML($response->getBody());
        self::_checkErrors($dom);

        /** 
         * @see Zend_Service_Technorati_KeyInfoResult
         */
        require_once 'Zend/Service/Technorati/KeyInfoResult.php';		
        return new Zend_Service_Technorati_KeyInfoResult($dom, $this->_apiKey);
    }


    /**
     * Return Technorati API key
     *
     * @return string   Technorati API key
     */
    public function getApiKey()
    {
        return $this->_apiKey;
    }

    /**
     * Returns a reference to the REST client
     *
     * If the reference hasn't being inizialized yet,
     * then a new Zend_Rest_Client instance is created.
     *
     * @return Zend_Rest_Client
     */
    public function getRestClient()
    {
        if (is_null($this->_restClient)) {
            /**
             * @see Zend_Rest_Client
             */
            require_once 'Zend/Rest/Client.php';
            $this->_restClient = new Zend_Rest_Client(self::URI_BASE);
        }

        return $this->_restClient;
    }

    /**
     * Set Technorati API key
     *
     * @param   string $value   Technorati API Key
     * @return  void
     * @link    http://technorati.com/developers/apikey.html How to get your Technorati API Key
     * @todo    Should this function validate the key?
     */
    public function setApiKey($value)
    {
        $this->_apiKey = $value;
    }


    /**
     * Validate Cosmos Options
     *
     * @param   array   $options
     * @return  void
     * @throws  Zend_Service_Technorati_Exception
     */
    protected function _validateCosmos($options)
    {
        static $validOptions = array('key', 'url',
            'type', 'limit', 'start', 'current', 'format', 'claim', 'highlight');

        if (!is_array($options)) {
            /**
             * @see Zend_Service_Technorati_Exception
             */
            throw new Zend_Service_Technorati_Exception('Options must be specified as an array');
        }

        // Validate keys in the $options array
        $this->_compareOptions($options, $validOptions);

        /**
         * @see Zend_Filter_Int
         */
        require_once 'Zend/Filter/Int.php';
        $filterInt = new Zend_Filter_Int();

        /**
         * @see Zend_Validate_Between
         */
        require_once 'Zend/Validate/Between.php';
        $validateBetween = new Zend_Validate_Between(1, 100, true);

        /**
         * @see Zend_Validate_GreaterThan
         */
        require_once 'Zend/Validate/GreaterThan.php';
        $validateGreaterThan = new Zend_Validate_GreaterThan(0);


        // Validate url (required)
        if (empty($options['url'])) {
            throw new Zend_Service_Technorati_Exception('Cosmos query requires an "url" option');
        }

        // Validate type (optional)
        if (isset($options['type'])) {
            $this->_validateInArray('type',
                                    $options['type'],
                                    array('link', 'weblog'));
        }

        // Validate limit (optional)
        if (isset($options['limit']) && !$validateBetween->setMin(1)->setMax(100)->isValid($options['limit'])) {
            /**
             * @see Zend_Service_Technorati_Exception
             */
            throw new Zend_Service_Technorati_Exception($options['limit'] . 
                        ' is not valid for the "limit" option');
        }

        // Validate start (optional)
        if (isset($options['start']) && !$validateGreaterThan->isValid($options['start'])) {
            /**
             * @see Zend_Service_Technorati_Exception
             */
            throw new Zend_Service_Technorati_Exception($options['start'] . 
                        ' is not valid for the "start" option');
        }

        // Validate current (optional)
        if (isset($options['current'])) {
            $tmp = $filterInt->filter($options['current']);
            $options['current'] = $tmp ? 'yes' : 'no';
        }

        // Validate format (optional)
        $this->_validateOptionFormat($options);
        
        // Validate claim (optional)
        if (isset($options['claim'])) {
            $options['claim'] = $filterInt->filter($options['highlight']);
        }

        // Validate highlight (optional)
        if (isset($options['highlight'])) {
            $options['highlight'] = $filterInt->filter($options['highlight']);
        }
    }

    /**
     * Validate GetInfo Options
     *
     * @param   array   $options
     * @return  void
     * @throws  Zend_Service_Technorati_Exception
     */
    protected function _validateGetInfo($options)
    {
        static $validOptions = array('key', 'username',
            'format');

        if (!is_array($options)) {
            /**
             * @see Zend_Service_Technorati_Exception
             */
            throw new Zend_Service_Technorati_Exception('Options must be specified as an array');
        }

        // Validate keys in the $options array
        $this->_compareOptions($options, $validOptions);

        // Validate username (required)
        if (empty($options['username'])) {
            throw new Zend_Service_Technorati_Exception('GetInfo query requires "username" option');
        }

        // Validate format (optional)
        $this->_validateOptionFormat($options);
    }    
    
    /**
     * Validate Search Options
     * 
     * TODO: phpdoc
     *
     * @param   array   $options
     * @return  void
     * @throws  Zend_Service_Technorati_Exception
     * @todo    develop function
     * /
    protected function _validateSearch($options)
    {
    } */

    /**
     * Validate Tag Options
     * 
     * TODO: phpdoc
     *
     * @param   array   $options
     * @return  void
     * @throws  Zend_Service_Technorati_Exception
     * @todo    develop function
     * /
    protected function _validateTag($options)
    {
    } */

    /**
     * Validate that an option is in a given array
     *
     * @param   string $name    Option name
     * @param   string $value   Option value
     * @param   array $array    Array in which to check for the option
     * @return  void
     * @throws  Zend_Service_Technorati_Exception
     */
    protected function _validateInArray($name, $value, $array)
    {
        if (!in_array($value, $array)) {
            /**
             * @see Zend_Service_Technorati_Exception
             */
            throw new Zend_Service_Technorati_Exception("Invalid value for $name");
        }
    }
    
    /**
     * Validate "format" option
     * 
     * Be aware that Zend_Service_Technorati supports only XML as format value.
     * 
     * @param   array $options
     * @return  void
     * @throws  Zend_Service_Technorati_Exception if format value != XML
     */
    private function _validateOptionFormat($options) {
        if (isset($options['format']) && $options['format'] != 'xml') {
            /**
             * @see Zend_Service_Technorati_Exception
             */
            throw new Zend_Service_Technorati_Exception($options['format'] . 
                        ' is not valid for the "format" option,' .
                        ' Zend_Service_Technorati supports only "xml"');
        }
    }
    
    /**
     * Check Result for errors
     *
     * @param   DomDocument $dom    API response DOM document
     * @return  void
     * @throws  Zend_Service_Technorati_Exception
     * @link    http://technorati.com/developers/api/error.html Technorati API: Error response
     */
    protected static function _checkErrors(DomDocument $dom)
    {
        $xpath = new DOMXPath($dom);

        if ($xpath->query("/tapi/document/result/error")->length >= 1) {
        	// @todo improve xpath expression
            $error = $xpath->query("//error/text()")->item(0)->data;
            /**
             * @see Zend_Service_Technorati_Exception
             */
            throw new Zend_Service_Technorati_Exception($error);
        }
    }

    /**
     * Check ReST response for errors
     *
     * @param   Zend_Http_Response  $response   ReST response
     * @return  void
     * @throws  Zend_Service_Technorati_Exception
     */
    protected static function _checkResponseErrors(Zend_Http_Response $response)
    {
        if ($response->isError()) {
            /**
             * @see Zend_Service_Technorati_Exception
             */
            throw new Zend_Service_Technorati_Exception(
                        'Invalid response status code (' .
                        'HTTP/' . $response->getVersion() .
                        $response->getStatus() . ' ' . $response->getStatus() . ')');
        }
    }

    /**
     * Checks whether the user options are valid
     *
     * @param   array $options        User options
     * @param   array $validOptions   Valid options
     * @return  void
     * @throws  Zend_Service_Technorati_Exception
     */
    protected function _compareOptions($options, $validOptions)
    {
        $difference = array_diff(array_keys($options), $validOptions);
        if ($difference) {
            /**
             * @see Zend_Service_Technorati_Exception
             */
            throw new Zend_Service_Technorati_Exception(
                        'The following parameters are invalid: ' . 
                        implode(',', $difference));
        }
    }

    /**
     * Prepare options for the request
     *
     * @param   array $options        User options
     * @param   array $defaultOptions Default options
     * @return  array Merged array of user and default/required options.
     */
    protected function _prepareOptions($options, $defaultOptions)
    {
        $options['key'] = $this->_apiKey;
        $options = array_merge($defaultOptions, $options);
        return $options;
    }
}
