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
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Zend_Service_Rest
 */
require_once 'Zend/Service/Rest.php';

/**
 * Zend_Service_Exception
 */
require_once 'Zend/Service/Exception.php';

/**
 * @package    Zend_Service
 * @subpackage Audioscrobbler
 * @copyright  Copyright (c) 2005-2006 Zend Technologies Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd 	New BSD License
 * @author 	   Chris Hartjes chartjes@littlehart.net (ZCE # 901167)
 * @author 	   Derek Martin  derek@geekunity.com (ZCE # 901168)
 */
class Zend_Service_Audioscrobbler
{
    /**
     * Zend_Service_Rest Object
     *
     * @var Zend_Service_Rest
     */
    protected $_rest;

    /**
     * AudioScrobbler web service version to access
     *
     * @var float
     */
    public $version;

    /**
     *  Last.fm username
     *
     * @var string
     */
    public $user;

    /**
     *  Artist name
     *
     * @var string
     */
    public $artist;

    /**
     *  Album name
     *
     * @var string
     */
    public $album;

    /**
     *  Track name
     *
     * @var string
     */
    public $track;

    /**
     *  Tag name
     *
     * @var string
     */
    public $tag;

    /**
     *  Group name
     *
     * @var string
     */
    public $group;

    /**
     *  Forum id
     *
     * @var string
     */
    public $forum;

    /**
     *  From date timestamp
     *
     * @var string
     */
    public $from_date;

    /**
     *  To date timestamp
     *
     * @var string
     */
    public $to_date;


	//////////////////////////////////////////////////////////
	///////////////////  CORE METHODS  ///////////////////////
	//////////////////////////////////////////////////////////


    /**
     * Zend_Service_Audioscrobbler Constructor, setup character encoding
     */
    public function __construct()
    {
    	$this->version = '1.0';

        iconv_set_encoding('output_encoding', 'UTF-8');
        iconv_set_encoding('input_encoding', 'UTF-8');
        iconv_set_encoding('internal_encoding', 'UTF-8');

        $this->_rest = new Zend_Service_Rest();
        $this->_rest->setUri('http://ws.audioscrobbler.com');
    }


	/**
	* Sets the Audioscrobbler web service version
	* @param float
	*/
    public function setVersion($version)
    {
    	$this->version = (float) trim($version);
    }

	/**
	* Sets the Audioscrobbler username to grab data about
	* @param string
	*/
    public function setUser($user)
    {
    	$this->user = urlencode(trim($user));
    }

	/**
	* Sets the artist to grab data about
	* @param string
	*/
	public function setArtist($artist)
	{
		$this->artist = urlencode(trim($artist));
	}

	/**
	* Sets the album to grab data about
	* @param strubg
	*/
	public function setAlbum($album)
	{
		$this->album = urlencode(trim($album));
	}

	/**
	* Sets the track name to grab data about
	* @param string
	*/
	public function setTrack($track)
	{
		$this->track = urlencode(trim($track));
	}

	/**
	* Sets the lower timestamp boundary for data retrieval
	* @param date
	*/
	public function setFromDate($date)
	{
		$this->from_date = $this->_setDate($date);
	}

	/**
	* Sets the upper timestamp boundary for data retrieval
	* @param date
	*/
	public function setToDate($date)
	{
		$this->to_date = $this->_setDate($date);
	}

	/**
	* When given a date or timestamp, always returns a timestamp
	* @return timestamp
	*/
	private function _setDate($date)
	{
		$date = trim($date);

		if(strchr($date,'-') || strchr($date,'/') || strchr($date,'\\') || strchr($date,' '))
		{
			return strtotime($date);
		}
		else
		{
			return $date;
		}
	}

	/**
	* Sets the tag to grab data about
	* @param string
	*/
	public function setTag($tag)
	{
		$this->tag = urlencode(trim($tag));
	}

	/**
	* Sets the name of the forum to grab data about
	* @param string
	*/
	public function setForum($forum)
	{
		$this->forum = (int) trim($forum);
	}

	/**
	* Sets the group to grab data about
	* @param string
	*/
	public function setGroup($group)
	{
		$this->group = urlencode(trim($group));
	}

	/**
	*
	* Private method that queries REST service and returns SimpleXML response set
	* @param string $dir directory that service lives under
	* @param string $service name of Audioscrobbler service file we're accessing
	* @return SimpleXML result set
	*/
	private function _getInfo($dir, $service)
	{
		$service = (string) $service;
		$dir = (string) $dir;

		try {
			$request = $this->_rest->restGet("/{$this->version}/{$dir}/{$service}");

			if ($request->isSuccessful()) {
				$response = simplexml_load_string($request->getBody());
				return $response;
			} else {
				if ($request->getBody() == 'No such path') {
					throw new Zend_Service_Exception('Could not find: ' . $dir);
				} else if ($request->getBody() == 'No user exists with this name.') {
					throw new Zend_Service_Exception('No user exists with this name.');
				} else {
					throw new Zend_Service_Exception('The REST service ' . $service . ' returned the following status code: ' . $request->getStatus());
				}
			}
		}
		catch (Zend_Service_Exception $e) {
			throw ($e);
		}
	}

	/**
	* Wrapper function for _getInfo() that returns info from the User Data category
	* @param string $service Audioscrobbler service being accessed
	* @param array $params Required parameters, as associative array
	* @return _getInfo() result
	*/

	protected function _getInfoByUser($service, $params=false)
	{
		$this->_verifyRequiredParams('user',$params);
		$dir = 'user/'.$this->user;
		return $this->_getInfo($dir, $service);
	}

	/**
	* Wrapper function that calls _getInfo() for results from the Artist category
	* @param string $service Audioscrobbler service being accessed
	* @param array $params Required parameters, as associative array
	* @return _getInfo() result
	*/
	protected function _getInfoByArtist($service, $params=false)
	{
		$this->_verifyRequiredParams('artist',$params);
		$dir = 'artist/'.$this->artist;
		return $this->_getInfo($dir, $service);
	}

	/**
	* Wrapper function that calls _getInfo() for results from the Track category
	* @param string $service Audioscrobbler service being accessed
	* @param array $params Required parameters, as associative array
	* @return _getInfo() result
	*/
	protected function _getInfoByTrack($service, $params=false)
	{
		$this->_verifyRequiredParams('track',$params);
		$dir = 'track/'.$this->artist.'/'.$this->track;
		return $this->_getInfo($dir, $service);
	}

	/**
	* Wrapper function that calls _getInfo() for results from the Tag category
	* @param string $service Audioscrobbler service being accessed
	* @param array $params Required parameters, as associative array
	* @return _getInfo() result
	*/
	protected function _getInfoByTag($service, $params=false)
	{
		$this->_verifyRequiredParams('tag',$params);
		$dir = 'tag';
		if($this->tag!='') $dir.= '/'; //because OverallTopTags has no subdir
		$dir .= $this->tag;

		return $this->_getInfo($dir, $service);
	}

	/**
	* Wrapper function that calls _getInfo() for results from the Group category
	* @param string $service Audioscrobbler service being accessed
	* @param array $params Required parameters, as associative array
	* @return _getInfo() result
	*/
	protected function _getInfoByGroup($service, $params=false)
	{
		$this->_verifyRequiredParams('group',$params);
		$dir = 'group/'.$this->group;
		return $this->_getInfo($dir, $service);
	}

	/**
	* Wrapper function that calls _getInfo() for results from the Forum category
	* @param string $service Audioscrobbler service being accessed
	* @param array $params Required parameters, as associative array
	* @return _getInfo() result
	*/
	protected function _getInfoByForum($service, $params=false)
	{
		$this->_verifyRequiredParams('forum',$params);
		$dir = 'forum/'.$this->forum;
		return $this->_getInfo($dir, $service);
	}

	/**
	 * Ensures that calls to rest service contain all required parameters
	 *
	 * @param ustring $category
	 * @param uarray $params
	 */
	protected function _verifyRequiredParams($category,$params)
	{
		if(!isset($this->$category) || $this->$category=='')
		{
			throw new Zend_Service_Exception('Required ->set'.ucwords($category).'("'.$category.'");');
		}

		if($params!=false)
		{
			foreach($params as $key => $value)
			{
				throw new Zend_Service_Exception('Required ->set'.ucwords($key).'("'.$value.'");');
			}
		}
	}

	//////////////////////////////////////////////////////////
	///////////////////////  USER  ///////////////////////////
	//////////////////////////////////////////////////////////

	/**
	* Utility function to get Audioscrobbler profile information (eg: Name, Gender)
	* @return array containing information
	*/
	public function userGetProfileInformation()
	{
		return $this->_getInfoByUser('profile.xml');
	}

	/**
	 * Utility function get this user's 50 most played artists
	 * @return array containing info
	*/
	public function userGetTopArtists()
	{
		return $this->_getInfoByUser('topartists.xml');
	}

	/**
	 * Utility function to get this user's 50 most played albums
	 * @return SimpleXML object containing result set
	*/
	public function userGetTopAlbums()
	{
		return $this->_getInfoByUser('topalbums.xml');
	}

	/**
	 * Utility function to get this user's 50 most played tracks
	 * @return SimpleXML object containing resut set
	*/
	public function userGetTopTracks()
	{
		return $this->_getInfoByUser('toptracks.xml');
	}

	/**
	 * Utility function to get this user's 50 most used tags
	 * @return SimpleXML object containing result set
	 */
	public function userGetTopTags()
	{
		return $this->_getInfoByUser('tags.xml');
	}

	/**
	 * Utility function that returns the user's top tags used most used on a specific artist
	 * @return SimpleXML object containing result set
	 *
	 */
	public function userGetTopTagsForArtist()
	{
		$required = array('artist'=>'artist name');
		return $this->_getInfoByUser('artisttags.xml?artist='.urlencode($this->artist),$required);
	}

	/**
	 * Utility function that returns this user's top tags for an album
	 * @return SimpleXML object containing result set
	 *
	 */
	public function userGetTopTagsForAlbum()
	{
		$required = array('artist'=>'artist name','album'=>'album name');
		return $this->_getInfoByUser('albumtags.xml?artist='.urlencode($this->artist).'&album='.urlencode($this->album),$required);
	}

	/**
	 * Utility function that returns this user's top tags for a track
	 * @return SimpleXML object containing result set
	 *
	 */
	public function userGetTopTagsForTrack()
	{
		$required = array('artist'=>'artist name','track'=>'track name');
		return $this->_getInfoByUser('tracktags.xml?artist='.urlencode($this->artist).'&track='.urlencode($this->track),$required);
	}

	/**
	 * Utility function that retrieves this user's list of friends
	 * @return SimpleXML object containing result set
	 */
	public function userGetFriends()
	{
		return $this->_getInfoByUser('friends.xml');
	}

	/**
	 * Utility function that returns a list of people with similar listening preferences to this user
	 * @return SimpleXML object containing result set
	 *
	 */
	public function userGetNeighbours()
	{
		return $this->_getInfoByUser('neighbours.xml');
	}

	/**
	 * Utility function that returns a list of the 10 most recent tracks played by this user
	 * @return SimpleXML object containing result set
	 *
	 */
	public function userGetRecentTracks()
	{
		return $this->_getInfoByUser('recenttracks.xml');
	}

	/**
	 * Utility function that returns a list of the 10 tracks most recently banned by this user
	 * @return SimpleXML object containing result set
	 *
	 */
	public function userGetRecentBannedTracks()
	{
		return $this->_getInfoByUser('recentbannedtracks.xml');
	}

	/**
	 * Utility function that returns a list of the 10 tracks most recently loved by this user
	 * @return SimpleXML object containing result set
	 *
	 */
	public function userGetRecentLovedTracks()
	{
		return $this->_getInfoByUser('recentlovedtracks.xml');
	}

	/**
	 * Utility function that returns recent journal entries by this user
	 * @return SimpleXML object containing result set
	 *
	 */
	public function userGetRecentJournals()
	{
		return $this->_getInfoByUser('journals.rss');
	}

	/**
	 * Utility function that returns a list of dates of available weekly charts for a this user
	 * Should actually be named userGetWeeklyChartDateList() but we have to follow audioscrobbler's naming
	 * @return SimpleXML object containing result set
	 *
	 */
	public function userGetWeeklyChartList()
	{
		return $this->_getInfoByUser('weeklychartlist.xml');
	}

	/**
	 * Utility function that returns the most recent weekly artist chart for this user
	 * @return SimpleXML object containing result set
	 *
	 */
	public function userGetRecentWeeklyArtistChart()
	{
		return $this->_getInfoByUser('weeklyartistchart.xml');
	}

	/**
	 * Utility function that returns the most recent weekly album chart for this user
	 * @return SimpleXML object containing result set
	 *
	 */
	public function userGetRecentWeeklyAlbumChart()
	{
		return $this->_getInfoByUser('weeklyalbumchart.xml');
	}

	/**
	 * Utility function that returns the most recent weekly track chart for this user
	 * @return SimpleXML object containing result set
	 *
	 */
	public function userGetRecentWeeklyTrackChart()
	{
		return $this->_getInfoByUser('weeklytrackchart.xml');
	}

	/**
	 * Utility function that returns this user's weekly artist chart for a specific date range
	 * Use userGetWeeklyChartList() to get a list of valid dates
	 * @return SimpleXML object containing result set
	 *
	 */
	public function userGetPreviousWeeklyArtistChart()
	{
		$required = array('FromDate'=>'1114965332','ToDate'=>'1115570132');
		return $this->_getInfoByUser('weeklyartistchart.xml?from='.$this->from_date.'&to='.$this->to_date, $required);
	}

	/**
	 * Utility function that returns this user's weekly album chart for a specific date range
	 * @return SimpleXML object containing result set
	 *
	 */
	public function userGetPreviousWeeklyAlbumChart()
	{
		$required = array('FromDate'=>'1114965332','ToDate'=>'1115570132');
		return $this->_getInfoByUser('weeklyalbumchart.xml?from='.$this->from_date.'&to='.$this->to_date, $required);
	}

	/**
	 * Utility function that returns this user's weekly album chart for specific date range
	 * @return SimpleXML object containing result set
	 *
	 */
	public function userGetPreviousWeeklyTrackChart()
	{
		$required = array('FromDate'=>'1114965332', 'ToDate'=>'1115570132');
		return $this->_getInfoByUser('weeklytrackchart.xml?from='.$this->from_date.'&to='.$this->to_date. $required);
	}

	//////////////////////////////////////////////////////////
	///////////////////////  ARTIST  /////////////////////////
	//////////////////////////////////////////////////////////

	/**
	 * Public functions for retrieveing artist-specific information
	 *
	 */


	/**
	 * Utility function that returns a list of artists similiar to this artist
	 * @return SimpleXML object containing result set
	 *
	 */
	public function artistGetRelatedArtists()
	{
		return $this->_getInfoByArtist('similar.xml');
	}

	/**
	 * Utility function that returns a list of this artist's top listeners
	 * @return SimpleXML object containing result set
	 *
	 */
	public function artistGetTopFans()
	{
		return $this->_getInfoByArtist('fans.xml');
	}

	/**
	 * Utility function that returns a list of this artist's top-rated tracks
	 * @return SimpleXML object containing result set
	 *
	 */
	public function artistGetTopTracks()
	{
		return $this->_getInfoByArtist('toptracks.xml');
	}

	/**
	 * Utility function that returns a list of this artist's top-rated albums
	 * @return SimpleXML object containing result set
	 *
	 */
	public function artistGetTopAlbums()
	{
		return $this->_getInfoByArtist('topalbums.xml');
	}

	/**
	 * Utility function that returns a list of this artist's top-rated tags
	 * @return SimpleXML object containing result set
	 *
	 */
	public function artistGetTopTags()
	{
		return $this->_getInfoByArtist('toptags.xml');
	}

	//////////////////////////////////////////////////////////
	/////////////////////////  TRACK  ////////////////////////
	//////////////////////////////////////////////////////////

	/**
	 * Utility function that returns a list of this track's top listeners
	 * @return SimpleXML object containing result set
	 *
	 */
	public function trackGetTopFans()
	{
		return $this->_getInfoByTrack('fans.xml');
	}

	/**
	 * Utility function that returns a list of tags most applied to this track
	 * @return SimpleXML object containing result set
	 *
	 */
	public function trackGetTopTags()
	{
		return $this->_getInfoByTrack('toptags.xml');
	}

	//////////////////////////////////////////////////////////
	/////////////////////////  TAGS  /////////////////////////
	//////////////////////////////////////////////////////////

	/**
	 * public functions for retrieving tag-specific information
	 */

	/**
	 * Utility function that returns a list of the site's overall most used tags
	 * @return SimpleXML object containing result set
	 *
	 */
	public function tagGetOverallTopTags()
	{
		return $this->_getInfoByTag('toptags.xml');
	}

	/**
	 * Utility function that returns a list of artists this tag was most applied to
	 * @return SimpleXML object containing result set
	 *
	 */
	public function tagGetTopArtists()
	{
		return $this->_getInfoByTag('topartists.xml');
	}

	/**
	 * Utility function that returns a list of albums to which this tag was most applied
	 * @return SimpleXML object containing result set
	 *
	 */
	public function tagGetTopAlbums()
	{
		return $this->_getInfoByTag('topalbums.xml');
	}
	/**
	 * Utility function that returns a list of tracks to which this tag was most applied
	 * @return SimpleXML object containing result set
	 *
	 */
	public function tagGetTopTracks()
	{
		return $this->_getInfoByTag('toptracks.xml');
	}

	//////////////////////////////////////////////////////////
	///////////////////////  GROUPS  /////////////////////////
	//////////////////////////////////////////////////////////

	/**
	 * public functions for retrieving group-specific information
	 */


	/**
	 * Utility function that returns a a list of recent journal posts by members of this group
	 * @return SimpleXML object containing result set
	 *
	 */
	public function groupGetRecentJournals()
	{
		return $this->_getInfoByGroup('journals.rss');
	}

	/**
	 * Utility function that gets list of dates of available weekly charts for this group
	 * @return SimpleXML object containing result set
	 *
	 */
	public function groupGetWeeklyChart()
	{
		return $this->_getInfoByGroup('weeklychartlist.xml');
	}

	/**
	 * Utility function that gets the most recent weekly artist chart for this group
	 * @return SimpleXML object containing result set
	 *
	 */
	public function groupGetRecentWeeklyArtistChart()
	{
		return $this->_getInfoByGroup('weeklyartistchart.xml');
	}

	/**
	 * Utility function that gets the most recent album chart for this group
	 * @return SimpleXML object containing result set
	 *
	 */
	public function groupGetRecentWeeklyAlbumChart()
	{
		return $this->_getInfoByGroup('weeklyalbumchart.xml');
	}

	/**
	 * Utility function that gets the most recent track chart for this group
	 * @return SimpleXML object containing result set
	 *
	 */
	public function groupGetRecentWeeklyTrackChart()
	{
		return $this->_getInfoByGroup('weeklytrackchart.xml');
	}

	/**
	 * Utility function that gets this group's weekly artist chart by date
	 * Use groupGetWeeklyChartList() to get a list of valid dates
	 * @return SimpleXML object containing result set
	 *
	 */
	public function groupGetPreviousWeeklyArtistChart()
	{
		$required = array('FromDate'=>'1114965332','ToDate'=>'1115570132');
		return $this->_getInfoByGroup('weeklyartistchart.xml?from='.$this->from_date.'&to='.$this->to_date, $required);
	}

	/**
	 * Utility function that gets this group's weekly album chart by date
	 * Use groupGetWeeklyChartList() to get a list of valid dates
	 * @return SimpleXML object containing result set
	 *
	 */
	public function groupGetPreviousWeeklyAlbumChart()
	{
		$required = array('FromDate'=>'1114965332','ToDate'=>'1115570132');
		return $this->_getInfoByGroup('weeklyalbumchart.xml?from='.$this->from_date.'&to='.$this->to_date, $required);
	}

	/**
	 * Utility function that gets this group's weekly track chart by date
	 * Use groupGetWeeklyChartList() to get a list of valid dates
	 * @return SimpleXML object containing result set
	 *
	 */
	public function groupGetPreviousWeeklyTrackChart()
	{
		$required = array('FromDate'=>'1114965332','ToDate'=>'1115570132');
		return $this->_getInfoByGroup('weeklytrackchart.xml?from='.$this->from_date.'&to='.$this->to_date, $required);
	}

	//////////////////////////////////////////////////////////
	//////////////////////  FORUM  ///////////////////////////
	//////////////////////////////////////////////////////////

	/**
	 * Utility function that gets an RSS feed of recent forum posts in a specific forum
	 * @return SimpleXML object containing result set
	 *
	 */
	public function forumGetRecentPosts()
	{
		return $this->_getInfoByForum('posts.rss');
	}

}
?>