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
 * @subpackage Akismet
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Service_Abstract */
require_once 'Zend/Service/Abstract.php';

/** Zend_Service_Exception */
require_once 'Zend/Service/Exception.php';

/**
 * Gravatar service implementation
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Gravatar
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Gravatar extends Zend_Service_Abstract 
{
    /**
     * Base URI for the HTTP client
     */
    const API_URI = 'http://www.gravatar.com';

    /**
     * Query path for the HTTP client
     */
    const PATH_AVATAR = 'avatar.php';

    /**
     * Email adress used to make query
     *
     * @var string
     */
    protected $_email;

    /**
     * Default query parameters
     *
     * @var array
     */
    protected $_params = array(
        'rating'  => 'G',
        'size'    => '80',
        'default' => 'http://www.gravatar.com/avatar.php',
        'border'  => '',
    );

    /**
     * Construct a new Gravatar Web Service Client
     *
     * @param string $email
     * @param array $params
     */
    public function __construct($email = '', $params = null)
    {
    	$this->_email = $email;

    	if (null !== $params) {
    	    $this->setParams($params);
    	}

    	Zend_Service_Gravatar::getHttpClient()->setConfig(array('maxredirects' => 0));
    }

    /**
     * Set parameters
     *
     * @param array $params
     */
    public function setParams($params)
    {
    	$this->_params = array_merge($this->_params, $params);
    	return $this;
    }
    
    /**
     * Get params
     *
     * @return array Array of parameters
     */
    public function getParams()
    {
    	return $this->_params;
    }

    /**
     * Set an e-mail address
     *
     * @param string $email
     */
    public function setEmail($email)
    {
    	$this->_email = $email;
    	return $this;
    }

    /**
     * Get an e-mail adress
     * 
     * @return string E-mail address
     */
    public function getEmail()
    {
    	return $this->_email;
    }

    /**
     * Check if an avatar for e-mail adress is valid
     * 
     * Returns TRUE when avatar exists (succesful request without redirect)
     * Returns FALSE when avatar doesn't exist (succesful request with redirect)
     * Throws exception when something goes wrong, e.g. unsuccesful request.
     *
     * @return boolean
     * @throws Zend_Service_Exception when response is other than successful or redirect
     */
    public function isValid()
    {
        $client = self::getHttpClient();

        $client->setUri($this->getUri());
        $client->setMethod(Zend_Http_Client::GET);

        $response = $client->request();

        if ($response->isSuccessful()) {
            return true;
        }

        if ($response->isRedirect()) {
            return false;
        }

        throw new Zend_Service_Exception('HTTP ' . $response->getStatus());
    }

    /**
     * Get URI of gravatar image.
     *
     * @return string URI of gravatar image
     */
    public function getUri() {

	    return self::API_URI . '/' 
	         . self::PATH_AVATAR . '?' 
	         . http_build_query($this->_getQuery(), null, '&amp;');
    }

    /**
     * Get generated Gravatar ID
     *
     * @return string Gravatar ID generated using e-mail adress
     */
    public function getGravatarId()
    {
    	return md5($this->_email);
    }

    /**
     * Prepare query parameters
     *
     * @return string
     */
    protected function _getQuery()
    {
    	return array('gravatar_id' => $this->getGravatarId()) + $this->_params;
    }

}