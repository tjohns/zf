<?php

class Zend_Service_SlideShare_SlideShow {
	
	const STATUS_QUEUED = 0;
	const STATUS_PROCESSING = 1;
	const STATUS_READY = 2;
	const STATUS_FAILED = 3;
	
	protected $_embedCode;
	protected $_thumbnailUrl;
	protected $_title;
	protected $_description;
	protected $_status;
	protected $_statusDescription;
	protected $_permalink;
	protected $_numViews;
	protected $_slideShowId;
	protected $_slideShowFilename;
	protected $_tags = array();
	protected $_location;
	protected $_transcript;
	
	public function getLocation() {
		return $this->_location;
	}
	
	public function setLocation($loc) {
		$this->_location = (string)$loc;
		return $this;
	}
	
	public function getTranscript() {
		return $this->_transcript;
	}
	
	public function setTranscript($t) {
		$this->_transcript = (string)$t;
		return $this;
	}
	
	public function addTag($tag) {
		$this->_tags[] = (string)$tag;
		return $this;
	}
	
	public function setTags(Array $tags) {
		$this->_tags = $tags;
		return $this;
	}
	
	public function getTags() {
		return $this->_tags;
	}
	
	public function setFilename($file) {
		$this->_slideShowFilename = (string)$file;
		return $this;
	}
	
	public function getFilename() {
		return $this->_slideShowFilename;
	}
	
	public function setId($id) {
		$this->_slideShowId = (string)$id;
		return $this;
	}
	
	public function getId() {
		return $this->_slideShowId;
	}
	
	public function setEmbedCode($code) {
		$this->_embedCode = (string)$code;
		return $this;
	}
	
	public function getEmbedCode() {
		return $this->_embedCode;
	}
	
	public function setThumbnailUrl($url) {
		$this->_thumbnailUrl = (string) $url;
		return $this;
	}
	
	public function getThumbnailUrl() {
		return $this->_thumbnailUrl;
	}
	
	public function setTitle($title) {
		$this->_title = (string)$title;
		return $this;
	}
	
	public function getTitle() {
		return $this->_title;
	}
	
	public function setDescription($desc) {
		$this->_description = (string)$desc;
		return $this;
	}
	
	public function getDescription() {
		return $this->_description;
	}
	
	public function setStatus($status) {
		$this->_status = (int)$status;
		return $this;
	}
	
	public function getStatus() {
		return $this->_status;
	}
	
	public function setStatusDescription($desc) {
		$this->_statusDescription = (string)$desc;
		return $this;
	}
	
	public function getStatusDescription() {
		return $this->_statusDescription;
	}
	
	public function setPermaLink($url) {
		$this->_permalink = (string)$url;
		return $this;
	}
	
	public function getPermaLink() {
		return $this->_permalink;
	}
	
	public function setNumViews($views) {
		$this->_numViews = (int)$views;
		return $this;
	}
	
	public function getNumViews() {
		return $this->_numViews;
	}
}