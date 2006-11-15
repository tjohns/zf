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
	 * Array that contains parameters being used by the webservice
	 * @var array
	 */
	protected $params;
	
	
	//////////////////////////////////////////////////////////
	///////////////////  CORE METHODS  ///////////////////////
	//////////////////////////////////////////////////////////


    /**
     * Zend_Service_Audioscrobbler Constructor, setup character encoding
     */
    public function __construct()
    {
    	$this->set('version', '1.0');

        iconv_set_encoding('output_encoding', 'UTF-8');
        iconv_set_encoding('input_encoding', 'UTF-8');
        iconv_set_encoding('internal_encoding', 'UTF-8');

        try {
            $this->_rest = new Zend_Service_Rest();
            $this->_rest->setUri('http://ws.audioscrobbler.com');
        } catch (Zend_Http_Client_Exception $e) {
            throw $e;
        }
   }

	/**
	* Generic get action for a particular field
	* @param string $key object to be retrieved
	*/
	
	public function get($field)
	{
		if (array_key_exists($field, $this->params)) {
			return $this->params[$field];
		} else {
			return FALSE;
		}
	}

	/**
	 * Generic set action for a field in the parameters being used
	 *
	 * @param string $field field to set
	 * @param string $val value to set in the field
	 */
	
	public function set($field, $value)
	{
		$this->params[$field] = urlencode($value);
	}
	
	/**
	*
	* Private method that queries REST service and returns SimpleXML response set
	* @param string $service name of Audioscrobbler service file we're accessing
	* @param string $params parameters that we send to the service if needded
	* @return SimpleXML result set
	*/
	private function getInfo($service, $params = NULL)
	{
		$service = (string) $service;
		$params = (string) $params;

		try {   
            $request = $this->_rest->restGet($service, $params);
            
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

	//////////////////////////////////////////////////////////
	///////////////////////  USER  ///////////////////////////
	//////////////////////////////////////////////////////////

	/**
	* Utility function to get Audioscrobbler profile information (eg: Name, Gender)
	* @return array containing information
	*/
	public function userGetProfileInformation()
	{
	    $service = "/{$this->get('version')}/user/{$this->get('user')}/profile.xml";
		return $this->getInfo($service);
	}

	/**
	 * Utility function get this user's 50 most played artists
	 * @return array containing info
	*/
	public function userGetTopArtists()
	{
	    $service = "/{$this->get('version')}/user/{$this->get('user')}/topartists.xml";
		return $this->getInfo($service);
	}

	/**
	 * Utility function to get this user's 50 most played albums
	 * @return SimpleXML object containing result set
	*/
	public function userGetTopAlbums()
	{
	    $service = "/{$this->get('version')}/user/{$this->get('user')}/topalbums.xml";
		return $this->getInfo($service);
	}

	/**
	 * Utility function to get this user's 50 most played tracks
	 * @return SimpleXML object containing resut set
	*/
	public function userGetTopTracks()
	{
		$service = "/{$this->get('version')}/user/{$this->get('user')}/toptracks.xml";
		return $this->getInfo($service);
	}

	/**
	 * Utility function to get this user's 50 most used tags
	 * @return SimpleXML object containing result set
	 */
	public function userGetTopTags()
	{
	    $service = "/{$this->get('version')}/user/{$this->get('user')}/tags.xml";
		return $this->getInfo($service);
	}

	/**
	 * Utility function that returns the user's top tags used most used on a specific artist
	 * @return SimpleXML object containing result set
	 *
	 */
	public function userGetTopTagsForArtist()
	{
	    $service = "/{$this->get('version')}/user/{$this->get('user')}/artisttags.xml";
	    $params = "artist={$this->get('artist')}";
		return $this->getInfo($service, $params);
	}

	/**
	 * Utility function that returns this user's top tags for an album
	 * @return SimpleXML object containing result set
	 *
	 */
	public function userGetTopTagsForAlbum()
	{
		$service = "/{$this->get('version')}/user/{$this->get('user')}/albumtags.xml";
		$params = "artist={$this->get('artist')}&album={$this->get('album')}";
		return $this->getInfo($service, $params);
	}

	/**
	 * Utility function that returns this user's top tags for a track
	 * @return SimpleXML object containing result set
	 *
	 */
	public function userGetTopTagsForTrack()
    {
        $service = "/{$this->get('version')}/user/{$this->get('user')}/tracktags.xml";
        $params = "artist={$this->get('artist')}&track={$this->get('track')}";
        return $this->getInfo($service, $params);
    }

	/**
	 * Utility function that retrieves this user's list of friends
	 * @return SimpleXML object containing result set
	 */
	public function userGetFriends()
    {
        $service = "/{$this->get('version')}/user/{$this->get('user')}/friends.xml"; 
		return $this->getInfo($service);
	}

	/**
	 * Utility function that returns a list of people with similar listening preferences to this user
	 * @return SimpleXML object containing result set
	 *
	 */
	public function userGetNeighbours()
    {
        $service = "/{$this->get('version')}/user/{$this->get('user')}/neighbours.xml";
		return $this->getInfo($service);
	}

	/**
	 * Utility function that returns a list of the 10 most recent tracks played by this user
	 * @return SimpleXML object containing result set
	 *
	 */
	public function userGetRecentTracks()
	{
		$service = "/{$this->get('version')}/user/{$this->get('user')}/friends.xml";
        return $this->getInfo($service);
	}

	/**
	 * Utility function that returns a list of the 10 tracks most recently banned by this user
	 * @return SimpleXML object containing result set
	 *
	 */
	public function userGetRecentBannedTracks()
	{
		$service = "/{$this->get('version')}/user/{$this->get('user')}/recentbannedtracks.xml";
        return $this->getInfo($service);
	}

	/**
	 * Utility function that returns a list of the 10 tracks most recently loved by this user
	 * @return SimpleXML object containing result set
	 *
	 */
	public function userGetRecentLovedTracks()
	{
        $service = "/{$this->get('version')}/user/{$this->get('user')}/recentlovedtracks.xml";
        return $this->getInfo($service);
    }

	/**
	 * Utility function that returns a list of dates of available weekly charts for a this user
	 * Should actually be named userGetWeeklyChartDateList() but we have to follow audioscrobbler's naming
	 * @return SimpleXML object containing result set
	 *
	 */
	public function userGetWeeklyChartList()
    {
        $service = "/{$this->get('version')}/user/{$this->get('user')}/weeklychartlist.xml";
        return $this->getInfo($service);
    }


	/**
	 * Utility function that returns weekly album chart data for this user
	 * @return SimpleXML object containing result set
	 *
	 * @param integer $from optional UNIX timestamp for start of date range
	 * @param integer $to optional UNIX timestamp for end of date range
	 */
	public function userGetWeeklyAlbumChart($from = NULL, $to = NULL)
	{
        $params = "";
        
        if ($from != NULL && $to != NULL) {
            $from = (int)$from;
            $to = (int)$to;
            $params = "from={$from}&to={$to}";
        }
        
        $service = "/{$this->get('version')}/user/{$this->get('user')}/weeklyalbumchart.xml";
		return $this->getInfo($service, $params);
	}

	/**
	 * Utility function that returns weekly artist chart data for this user
	 * @return SimpleXML object containing result set
	 *
	 * @param integer $from optional UNIX timestamp for start of date range
	 * @param integer $to optional UNIX timestamp for end of date range
	 */
	public function userGetWeeklyArtistChart($from = NULL, $to = NULL)
	{
        $params = "";
        
        if ($from != NULL && $to != NULL) {
            $from = (int)$from;
            $to = (int)$to;
            $params = "from={$from}&to={$to}";
        }
        
        $service = "/{$this->get('version')}/user/{$this->get('user')}/weeklyartistchart.xml";
		return $this->getInfo($service, $params);
	}

	/**
	 * Utility function that returns weekly track chart data for this user
	 * @return SimpleXML object containing result set
	 *
	 * @param integer $from optional UNIX timestamp for start of date range
	 * @param integer $to optional UNIX timestamp for end of date range
	 */
	public function userGetWeeklyTrackChart($from = NULL, $to = NULL)
	{
        $params = "";
        
        if ($from != NULL && $to != NULL) {
            $from = (int)$from;
            $to = (int)$to;
            $params = "from={$from}&to={$to}";
        }
        
        $service = "/{$this->get('version')}/user/{$this->get('user')}/weeklytrackchart.xml";
		return $this->getInfo($service, $params);
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
		$service = "/{$this->get('version')}/artist/{$this->get('artist')}/similar.xml";
		return $this->getInfo($service);
	}

	/**
	 * Utility function that returns a list of this artist's top listeners
	 * @return SimpleXML object containing result set
	 *
	 */
	public function artistGetTopFans()
	{
		$service = "/{$this->get('version')}/artist/{$this->get('artist')}/fans.xml";
		return $this->getInfo($service);
	}

	/**
	 * Utility function that returns a list of this artist's top-rated tracks
	 * @return SimpleXML object containing result set
	 *
	 */
	public function artistGetTopTracks()
	{
		$service = "/{$this->get('version')}/artist/{$this->get('artist')}/toptracks.xml";
		return $this->getInfo($service);
	}

	/**
	 * Utility function that returns a list of this artist's top-rated albums
	 * @return SimpleXML object containing result set
	 *
	 */
	public function artistGetTopAlbums()
	{
		$service = "/{$this->get('version')}/artist/{$this->get('artist')}/topalbums.xml";
		return $this->getInfo($service);
	}

	/**
	 * Utility function that returns a list of this artist's top-rated tags
	 * @return SimpleXML object containing result set
	 *
	 */
	public function artistGetTopTags()
	{
		$service = "/{$this->get('version')}/artist/{$this->get('artist')}/toptags.xml";
		return $this->getInfo($service);
	}

    //////////////////////////////////////////////////////////
	///////////////////////  ALBUM  //////////////////////////
	//////////////////////////////////////////////////////////
	
	public function albumGetInfo()
	{
	    $service = "/{$this->get('version')}/album/{$this->get('artist')}/{$this->get('album')}/info.xml";
	    return $this->getInfo($service);
	}

    //////////////////////////////////////////////////////////
	///////////////////////  TRACKS //////////////////////////
	//////////////////////////////////////////////////////////
	
	public function trackGetTopFans()
	{
	    $service = "/{$this->get('version')}/track/{$this->get('artist')}/{$this->get('track')}/fans.xml";
	    return $this->getInfo($service);
	}
	
	public function trackGetTopTags()
	{
	    $service = "/{$this->get('version')}/track/{$this->get('artist')}/{$this->get('track')}/toptags.xml";
	    return $this->getInfo($service);
	}
	
    //////////////////////////////////////////////////////////
	///////////////////////  TAGS   //////////////////////////
	//////////////////////////////////////////////////////////	
	
	public function tagGetTopTags()
	{
	    $service = "/{$this->get('version')}/tag/toptags.xml";
	    return $this->getInfo($service);
	}
	
	public function tagGetTopArtists()
	{
	    $service = "/{$this->get('version')}/tag/{$this->get('tag')}/topartists.xml";
	    return $this->getInfo($service);
	}

    public function tagGetTopTracks()
    {
        $service = "/{$this->get('version')}/tag/{$this->get('tag')}/toptracks.xml";
        return $this->getInfo($service);
    }
    
    //////////////////////////////////////////////////////////
	/////////////////////// GROUPS  //////////////////////////
	//////////////////////////////////////////////////////////
	
	public function groupGetWeeklyChartList()
	{
	    $service = "/{$this->get('version')}/group/{$this->get('group')}/weeklychartlist.xml";
	    return $this->getInfo($service);
	}
	
	public function groupGetWeeklyArtistChartList($from = NULL, $to = NULL)
	{
	    
	    if ($from != NULL && $to != NULL) {
	        $from = (int)$from;
	        $to = (int)$to;
	        $params = "from={$from}&$to={$to}";
	    } else {
	        $params = "";
	    }
	    
	    $service = "/{$this->get('version')}/group/{$this->get('group')}/weeklyartistchart.xml";
	    return $this->getInfo($service, $params);
	}
	
	public function groupGetWeeklyTrackChartList($from = NULL, $to = NULL)
	{
	    if ($from != NULL && $to != NULL) {
	        $from = (int)$from;
	        $to = (int)$to;
	        $params = "from={$from}&to={$to}";
	    } else {
	        $params = "";
	    }
	    
	    $service = "/{$this->get('version')}/group/{$this->get('group')}/weeklytrackchart.xml";
	    return $this->getInfo($service, $params);
	}
	
	public function groupGetWeeklyAlbumChartList($from = NULL, $to = NULL)
	{
	    if ($from != NULL && $to != NULL) {
	        $from = (int)$from;
	        $to = (int)$to;
	        $params = "from={$from}&to={$to}";
	    } else {
	        $params = "";
	    }
	    
	    $service = "/{$this->get('version')}/group/{$this->get('group')}/weeklyalbumchart.xml";
	    return $this->getInfo($service, $params);
	}
	
}
?>
