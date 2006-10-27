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
 * @subpackage Delicious
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Zend_Http_Client
 */
include_once 'Zend/Http/Client.php';

/**
 * Zend_Json
 */
include_once 'Zend/Json.php';

/**
 * Zend_Service_Exception
 */
include_once 'Zend/Service/Exception.php';

/**
 * Zend_Service_Delicious_SimplePost
 */
include_once 'Zend/Service/Delicious/SimplePost.php';

/**
 * Zend_Service_Delicious_Post
 */
include_once 'Zend/Service/Delicious/Post.php';

/**
 * Zend_Service_Delicious_PostList
 */
include_once 'Zend/Service/Delicious/PostList.php';


/**
 * Zend_Service_Delicious is a concrete implementation of the del.icio.us web service
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Delicious
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Delicious
{
    const API_URI  = 'https://api.del.icio.us';
    const PATH_UPDATE       = '/v1/posts/update';
    const PATH_TAGS         = '/v1/tags/get';
    const PATH_TAG_RENAME   = '/v1/tags/rename';
    const PATH_BUNDLES      = '/v1/tags/bundles/all';
    const PATH_BUNDLE_DELETE= '/v1/tags/bundles/delete';
    const PATH_BUNDLE_ADD   = '/v1/tags/bundles/set';
    const PATH_DATES        = '/v1/posts/dates';
    const PATH_POST_DELETE  = '/v1/posts/delete';
    const PATH_POSTS_GET    = '/v1/posts/get';
    const PATH_POSTS_ALL    = '/v1/posts/all';
    const PATH_POSTS_ADD    = '/v1/posts/add';
    const PATH_POSTS_RECENT = '/v1/posts/recent';

    const JSON_URI     = 'http://del.icio.us';
    const JSON_POSTS   = '/feeds/json/%s/%s';
    const JSON_TAGS    = '/feeds/json/tags/%s';
    const JSON_NETWORK = '/feeds/json/network/%s';
    const JSON_FANS    = '/feeds/json/fans/%s';

    /**
     * Zend_Http_Client instance
     *
     * @var Zend_Http_Client
     */
    protected $_http;
    /**
     * Microtime of last request
     *
     * @var float
     */
    protected static $_lastRequestTime = 0;

    /**
     * Constructs a new del.icio.us Web Services Client
     *
     * @param string $uname Client username
     * @param string $pass  Client password
     * @return Zend_Service_Delicio
     */
    public function __construct($uname = null, $pass = null)
    {
        $this->_http = new Zend_Http_Client();
        $this->setAuth($uname, $pass);
    }
    /**
     * Set client username and password
     *
     * @param string $uname Client user name
     * @param string $pass  Client password
     * @return Zend_Service_Delicio
     */
    public function setAuth($uname, $pass)
    {
        $this->_http->setAuth($uname, $pass);
    }
    /**
     * Get time of the last update
     *
     * @return Zend_Date
     */
    public function getLastUpdate()
    {
        $response = $this->makeRequest(self::PATH_UPDATE);

        $rootNode = $response->documentElement;
        if ($rootNode && $rootNode->nodeName == 'update') {
            /* @todo replace strtotime() with Zend_Date equivalent */
            return new Zend_Date(strtotime($rootNode->getAttribute('time')));
        } else {
            throw new Zend_Service_Delicious_Exception('del.icio.us web service has returned something odd!');
        }

    }
    /**
     * Get all tags.
     * Returnd array contains tags as keys and number of posts in tags as values
     *
     * @return array list of tags
     */
    public function getTags()
    {
        $response = $this->makeRequest(self::PATH_TAGS);

        return self::_xmlResponseToArray($response, 'tags', 'tag', 'tag', 'count');
    }
    /**
     * Rename a tag
     *
     * @param string $old Old tag name
     * @param string $new New tag name
     */
    public function renameTag($old, $new)
    {
        $response = $this->makeRequest(self::PATH_TAG_RENAME, array('old'=>$old,'new'=>$new));

        return self::_evalXmlResult($response);
    }
    /**
     * Get all bundles.
     * Returnd array contains bundles as keys and array of tags as values.
     *
     * @return array list of boundles
     */
    public function getBundles()
    {
        $response = $this->makeRequest(self::PATH_BUNDLES);

        $bundles = self::_xmlResponseToArray($response, 'bundles', 'bundle', 'name', 'tags');
        foreach ($bundles as &$tags) {
            $tags = explode(' ', $tags);
        }
        return $bundles;
    }
    /**
     * Adds a new bundle
     *
     * @param string $bundle Name of new bundle
     * @param array $tags Array of tags separated by spaces
     */
    public function addBundle($bundle, $tags)
    {
        $tags = implode(' ', (array) $tags);
        $response = $this->makeRequest(self::PATH_BUNDLE_ADD, array('bundle' => $bundle, 'tags' => $tags));

        return self::_evalXmlResult($response);
    }
    /**
     * Delete a bundle
     *
     * @param string $bundle Name of bundle to be deleted
     */
    public function deleteBundle($bundle)
    {
        $response = $this->makeRequest(self::PATH_BUNDLE_DELETE, array('bundle' => $bundle));

        return self::_evalXmlResult($response);
    }
    /**
     * Delete a post
     *
     * @param string $url URL of post to be deleted
     */
    public function deletePost($url)
    {
        $response = $this->makeRequest(self::PATH_POST_DELETE, array('url' => $url));

        return self::_evalXmlResult($response);
    }
    /**
     * Get number of posts by date. Returns array where keys are dates
     * and values are numbers of posts.
     *
     * @param string $tag Optional filtering by tag
     * @return array list of dates
     */
    public function getDates($tag = null)
    {
        $parms = array();
        if($tag) $parms['tag'] = $tag;

        $response = $this->makeRequest(self::PATH_DATES, $parms);

        return self::_xmlResponseToArray($response, 'dates', 'date', 'date', 'count');
    }
    /**
     * Get posts matching the arguments. If no date or url is given, most recent date will be used.
     *
     * @param string $tag Optional filtering by tag
     * @param Zend_Date $dt  Optional filtering by date
     * @param string $url Optional filtering by url
     * @return Zend_Service_Delicious_PostList
     */
    public function getPosts($tag = null, $dt = null, $url = null)
    {
        $parms = array();
        if ($tag) $parms['tag'] = $tag;
        if ($url) $parms['url'] = $url;
        if ($dt) {
            if (!$dt instanceof Zend_Date) {
                throw new Zend_Service_Delicious_Exception('Second argument has to be a instance of Zend_Date');
            }
            $parms['dt'] = $dt->get('Y-m-d\TH:i:s\Z');
        }

        $response = $this->makeRequest(self::PATH_POSTS_GET, $parms);

        return $this->_parseXmlPostList($response);
    }
    /**
     * Get all posts
     *
     * @param string $tag Optional filtering by tag
     * @return Zend_Service_Delicious_PostList
     */
    public function getAllPosts($tag = null)
    {
        $parms = array();
        if ($tag) $parms['tag'] = $tag;

        $response = $this->makeRequest(self::PATH_POSTS_ALL, $parms);

        return $this->_parseXmlPostList($response);

    }
    /**
     * Get recent posts
     *
     * @param string $tag   Optional filtering by tag
     * @param string $count Maximal number of posts to be returned (default 15)
     * @return Zend_Service_Delicious_PostList
     */
    public function getRecentPosts($tag = null, $count = 15)
    {
        $parms = array();
        if ($tag) $parms['tag'] = $tag;
        if ($count) $parms['count'] = $count;

        $response = $this->makeRequest(self::PATH_POSTS_RECENT, $parms);

        return $this->_parseXmlPostList($response);
    }
    /**
     * Create new post
     *
     * @return Zend_Service_Delicious_Post
     */
    public function createNewPost($title, $url)
    {
        return new Zend_Service_Delicious_Post($this, array('title' => $title, 'url' => $url));
    }
    /**
     * Get posts of some user
     *
     * @param string $user Owner of the posts
     * @param int $count Number of posts (default 15, max. 100)
     * @param string $tag Opional filtering by tag
     * @return Zend_Service_Delicious_PostList
     */
    public function getUserPosts($user, $count = null, $tag = null)
    {
        $parms = array();
        if ($count) $parms['count'] = $count;

        $path = sprintf(self::JSON_POSTS, $user, $tag);
        $res = $this->makeRequest($path, $parms, 'json');

        return new Zend_Service_Delicious_PostList($this, $res);
    }
    /**
     * Get tags of some user
     * Returend array has tags as keys and number of posts as values
     *
     * @param string $user Owner of the posts
     * @param int $atleast include only tags for which there are at least ### number of posts
     * @param int $count Number of tags to get (default all)
     * @param string $sort Order of returned tags ('alpha' || 'count')
     * @return array
     */
    public function getUserTags($user, $atleast = null, $count = null, $sort = 'alpha')
    {
        $parms = array();
        if ($atleast) {
            $parms['atleast'] = $atleast;
        }
        if ($count) {
            $parms['count'] = $count;
        }
        if ($sort) {
            $parms['sort'] = $sort;
        }

        $path = sprintf(self::JSON_TAGS, $user);
        return $this->makeRequest($path, $parms, 'json');
    }
    /**
     * Get network of some user
     *
     * @param string $user Owner of the network
     * @return array
     */
    public function getUserNetwork($user)
    {
        $path = sprintf(self::JSON_NETWORK, $user);
        return $this->makeRequest($path, null, 'json');
    }
    /**
     * Get fans of some user
     *
     * @param string $user
     * @return array
     */
    public function getUserFans($user)
    {
        $path = sprintf(self::JSON_FANS, $user);
        return $this->makeRequest($path, null, 'json');
    }
    /**
     * Handles all GET requests to a web service
     *
     * @param string $path Path
     * @param array $parms Array of GET parameters
     * @param string $type Type of a request xml|json
     * @return DOMDocument response from web service
     */
    public function makeRequest($path, $parms = array(), $type = 'xml')
    {
        settype($parms, 'array');
        // if previous request was made less then 1 sec ago
        // wait until we can make a new request
        $timeDiff = microtime(true) - self::$_lastRequestTime;
        if ($timeDiff < 1) {
            usleep((1 - $timeDiff) * 1000000);
        }

        $this->_http->resetParameters();

        foreach ($parms as $f_parm => $f_value) {
            $this->_http->setParameterGet($f_parm, $f_value);
        }

        switch ($type) {
            case 'xml':
                $this->_http->setUri(self::API_URI.$path);
                break;
            case 'json':
                $this->_http->setUri(self::JSON_URI.$path);
                $this->_http->setParameterGet('raw', true);
                break;
            default:
                throw new Zend_Service_Delicious_Exception('Unknown request type');
        }

        self::$_lastRequestTime = microtime(true);
        $response = $this->_http->request(Zend_Http_Client::GET);

        if (!$response->isSuccessful()) {
            throw new Zend_Service_Delicious_Exception("Http client reported an error: '{$response->getMessage()}'");
        }

        $responseBody = $response->getBody();

        switch ($type) {
            case 'xml':
                $dom = new DOMDocument() ;

                if (!@$dom->loadXML($responseBody)) {
                    throw new Zend_Service_Delicious_Exception('XML Error');
                }

                return $dom;
            case 'json':
                return Zend_Json::decode($responseBody);
        }
    }
    /**
     * Transform XML string to array
     *
     * @param DOMDocument $response
     * @param string $root     Name of root tag
     * @param string $child    Name of children tags
     * @param string $attKey   Attribute of child tag to be used as a key
     * @param string $attValue Attribute of child tag to be used as a value
     * @return array
     */
    private static function _xmlResponseToArray(DOMDocument $response, $root, $child, $attKey, $attValue)
    {
        $rootNode = $response->documentElement;
        $arrOut = array();

        if ($rootNode->nodeName == $root) {
            $childNodes = $rootNode->childNodes;

            for ($i = 0; $i < $childNodes->length; $i++) {
                $currentNode = $childNodes->item($i);
                if($currentNode->nodeName == $child) {
                    $arrOut[$currentNode->getAttribute($attKey)] = $currentNode->getAttribute($attValue);
                }
            }
        } else {
            throw new Zend_Service_Delicious_Exception('del.icio.us web service has returned something odd!');
        }

        return $arrOut;
    }
    /**
     * Constructs Zend_Service_Delicious_PostList from XML response
     *
     * @param DOMDocument $response
     * @return Zend_Service_Delicious_PostList
     */
    private function _parseXmlPostList(DOMDocument $response)
    {
        $rootNode = $response->documentElement;

        if ($rootNode->nodeName == 'posts') {
            return new Zend_Service_Delicious_PostList($this, $rootNode->childNodes);
        } else {
            throw new Zend_Service_Delicious_Exception('del.icio.us web service has returned something odd!');
        }
    }
    /**
     * Evaluates XML response
     *
     * @param DOMDocument $response
     */
    private static function _evalXmlResult(DOMDocument $response)
    {
        $rootNode = $response->documentElement;

        if ($rootNode && $rootNode->nodeName == 'result') {

            if ($rootNode->hasAttribute('code')) {
                $strResponse = $rootNode->getAttribute('code');
            } else {
                $strResponse = $rootNode->nodeValue;
            }

            if ($strResponse != 'done' && $strResponse != 'ok') {
                throw new Zend_Service_Delicious_Exception("del.icio.us web service: '{$strResponse}' ");
            }
        } else {
            throw new Zend_Service_Delicious_Exception('del.icio.us web service has returned something odd!');
        }
    }
}