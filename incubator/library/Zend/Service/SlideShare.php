<?php

require_once 'Zend/Http/Client.php';
require_once 'Zend/Cache.php';

require_once 'Zend/Service/SlideShare/Exception.php';
require_once 'Zend/Service/SlideShare/SlideShow.php';

class Zend_Service_SlideShare {
	
	const SERVICE_ERROR_BAD_APIKEY       = 1;
	const SERVICE_ERROR_BAD_AUTH         = 2;
	const SERVICE_ERROR_MISSING_TITLE    = 3;
	const SERVICE_ERROR_MISSING_FILE     = 4;
	const SERVICE_ERROR_EMPTY_TITLE      = 5;
	const SERVICE_ERROR_NOT_SOURCEOBJ    = 6;
	const SERVICE_ERROR_INVALID_EXT      = 7;
	const SERVICE_ERROR_FILE_TOO_BIG     = 8;
	const SERVICE_ERROR_SHOW_NOT_FOUND   = 9;
	const SERVICE_ERROR_USER_NOT_FOUND   = 10;
	const SERVICE_ERROR_GROUP_NOT_FOUND  = 11;
	const SERVICE_ERROR_MISSING_TAG      = 12;
	const SERVICE_ERROR_DAILY_LIMIT      = 99;
	const SERVICE_ERROR_ACCOUNT_BLOCKED  = 100;
	
	const SERVICE_UPLOAD_URI                  = 'http://www.slideshare.net/api/1/upload_slideshow';
	const SERVICE_GET_SHOW_URI                = 'http://www.slideshare.net/api/1/get_slideshow';
	const SERVICE_GET_SHOW_BY_USER_URI        = 'http://www.slideshare.net/api/1/get_slideshow_by_user';
	const SERVICE_GET_SHOW_BY_TAG_URI         = 'http://www.slideshare.net/api/1/get_slideshow_by_tag';
	const SERVICE_GET_SHOW_BY_GROUP_URI       = 'http://www.slideshare.net/api/1/get_slideshows_from_group';
	
	protected $_apiKey;
	protected $_sharedSecret;
	protected $_username;
	protected $_password;
	protected $_httpclient;
	protected $_cacheobject;
	
	public function setHttpClient(Zend_Http_Client $client) {
		$this->_httpclient = $client;
		return $this;
	}
	
	public function getHttpClient() {

		if(!($this->_httpclient instanceof Zend_Http_Client)) {
			$client = new Zend_Http_Client();
			$client->setConfig(array('maxredirects' => 2,
			                         'timeout' => 5));
			                         
			$this->setHttpClient($client);
		}

		$this->_httpclient->resetParameters();
		return $this->_httpclient;
	}
	
	public function setCacheObject(Zend_Cache_Core $cacheobject) {
		$this->_cacheobject = $cacheobject;
		return $this;
	}
	
	public function getCacheObject() {
		
		if(!($this->_cacheobject instanceof Zend_Cache_Core)) {
			$cache = Zend_Cache::factory('Core', 'File', array('lifetime' => 43200,
			                                                   'automatic_serialization' => true),
			                                             array('cache_dir' => '/tmp'));

			$this->setCacheObject($cache); 
		}
		
		return $this->_cacheobject;
	}
	
	public function getUserName() {
		return $this->_username;
	}
	
	public function setUserName($un) {
		$this->_username = $un;
		return $this;
	}
	
	public function getPassword() {
		return $this->_password;
	}
	
	public function setPassword($pw) {
		$this->_password = (string)$pw;
		return $this;
	}
	
	public function getApiKey() {
		return $this->_apiKey;
	}
	
	public function setApiKey($key) {
		$this->_apiKey = (string)$key;
		return $this;
	}
	
	public function getSharedSecret() {
		return $this->_sharedSecret;
	}
	
	public function setSharedSecret($secret) {
		$this->_sharedSecret = (string)$secret;
		return $this;
	}
	
	function __construct($apikey, $sharedSecret, $username = null, $password = null) {
		$this->setApiKey($apikey)
		     ->setSharedSecret($sharedSecret)
		     ->setUserName($username)
		     ->setPassword($password);
		     
		$this->_httpclient = new Zend_Http_Client();
	}

	public function uploadSlideShow(Zend_SlideShare_SlideShow $ss, $make_src_public = true) {
		
	}
	
	public function getSlideShow($ss_id) {
		$timestamp = time();
		
		$params = array('api_key' => $this->getApiKey(),
		                'ts' => $timestamp,
		                'hash' => sha1($this->getSharedSecret().$timestamp),
		                'slideshow_id' => $ss_id);

		$cache = $this->getCacheObject();
		
		$cache_key = md5($key.$value.$offset.$limit);
		
		if(!$retval = $cache->load($cache_key)) {		                
			$client = $this->getHttpClient();
			
			$client->setUri(self::SERVICE_GET_SHOW_URI);
			$client->setParameterPost($params);
	
			try {
				$response = $client->request('POST');
			} catch(Zend_Http_Client_Exception $e) {
				throw new Zend_Service_SlideShare_Exception("Service Request Failed: {$e->getMessage()}");
			}
			
			$sxe = simplexml_load_string($response->getBody());
			
			if($sxe->getName() == "SlideShareServiceError") {
				$message = (string)$sxe->Message[0];
				list($code, $error_str) = explode(':', $message);
				throw new Zend_Service_SlideShare_Exception(trim($error_str), $code);
			}
			
			if(!$sxe->getName() == 'Slideshows') {
				throw new Zend_Service_SlideShare_Exception('Unknown XML Repsonse Received');
			}
	
			$retval = $this->_slideShowNodeToObject(clone $sxe->Slideshow[0]);
			
			$cache->save($retval, $cache_key);
		}
		
		return $retval;
	}
	
	public function getSlideShowsByUsername($username, $offset = null, $limit = null) {
		return $this->_getSlideShowsByType('username_for', $username, $offset, $limit);
	}
	
	public function getSlideShowsByTag($tag, $offset = null, $limit = null) {
		return $this->_getSlideShowsByType('tag', $tag, $offset, $limit);
	}
	
	public function getSlideShowsByGroup($group, $offset = null, $limit = null) {
		return $this->_getSlideShowsByType('group_name', $group, $offset, $limit);
	}
	
	protected function _getSlideShowsByType($key, $value, $offset = null, $limit = null) {
		
		$key = strtolower($key);
		
		switch($key) {
			case 'username_for':
				$responseTag = 'User';
				$queryUri = self::SERVICE_GET_SHOW_BY_USER_URI;
				break;
			case 'group_name':
				$responseTag = 'Group';
				$queryUri = self::SERVICE_GET_SHOW_BY_GROUP_URI;
				break;
			case 'tag':
				$responseTag = 'Tag';
				$queryUri = self::SERVICE_GET_SHOW_BY_TAG_URI;
				break;
			default:
				throw new Zend_Service_SlideShare_Exception("Invalid SlideShare Query");
		}
		
		$timestamp = time();
		
		$params = array('api_key' => $this->getApiKey(),
		                'ts' => $timestamp,
		                'hash' => sha1($this->getSharedSecret().$timestamp),
		                $key => $value);
		
		if(!is_null($offset)) {
			$params['offset'] = (int)$offset;
		}
		
		if(!is_null($limit)) {
			$params['limit'] = (int)$limit;
		}
		
		$cache = $this->getCacheObject();
		
		$cache_key = md5($key.$value.$offset.$limit);
		
		if(!$retval = $cache->load($cache_key)) {
			
			$client = $this->getHttpClient();
			
			$client->setUri($queryUri);
			$client->setParameterPost($params);
	
			try {
				$response = $client->request('POST');
			} catch(Zend_Http_Client_Exception $e) {
				throw new Zend_Service_SlideShare_Exception("Service Request Failed: {$e->getMessage()}");
			}
			
			$sxe = simplexml_load_string($response->getBody());
			
			if($sxe->getName() == "SlideShareServiceError") {
				$message = (string)$sxe->Message[0];
				list($code, $error_str) = explode(':', $message);
				throw new Zend_Service_SlideShare_Exception(trim($error_str), $code);
			}
			
			if(!$sxe->getName() == $responseTag) {
				throw new Zend_Service_SlideShare_Exception('Unknown or Invalid XML Repsonse Received');
			}
	
			$retval = array();
			
			foreach($sxe->children() as $node) {
				if($node->getName() == 'Slideshow') {
					$retval[] = $this->_slideShowNodeToObject($node);
				}
			}
			
			$cache->save($retval, $cache_key);
		}
		
		return $retval;
	}
	
	protected function _slideShowNodeToObject(SimpleXMLElement $node) {
		
		if($node->getName() == 'Slideshow') {

			$ss = new Zend_Service_SlideShare_SlideShow();
			
			$ss->setId((string)$node->ID);
			$ss->setDescription((string)$node->Description);
			$ss->setEmbedCode((string)$node->EmbedCode);
			$ss->setNumViews((string)$node->Views);
			$ss->setPermaLink((string)$node->Permalink);
			$ss->setStatus((string)$node->Status);
			$ss->setStatusDescription((string)$node->StatusDescription);

			foreach(explode(",", (string)$node->Tags) as $tag) {
				
				if(!in_array($tag, $ss->getTags())) {
					$ss->addTag($tag);
				}
			}
			
			$ss->setThumbnailUrl((string)$node->Thumbnail);
			$ss->setTitle((string)$node->Title);
			$ss->setLocation((string)$node->Location);
			$ss->setTranscript((string)$node->Transcript);

			return $ss;

		}
		
		throw new Zend_Service_SlideShare_Exception("Was not provided the expected XML Node for processing");
	}	
}