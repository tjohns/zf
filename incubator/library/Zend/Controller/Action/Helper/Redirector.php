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
 * @package    Zend_Controller
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */ 


/** Zend_Controller_Action_Exception */
require_once 'Zend/Controller/Action/Exception.php';

/** Zend_Controller_Action_Helper_Abstract */
require_once 'Zend/Controller/Action/Helper/Abstract.php';


/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Zend_Controller_Action
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Controller_Action_Helper_Redirector extends Zend_Controller_Action_Helper_Abstract 
{
    /**
     * HTTP status code for redirects
     * @var int
     */
    protected $_code = 302;

    /**
     * Whether or not calls to _redirect() should exit script execution
     * @var bool
     */
    protected $_exit = true;

    /**
     * Whether or not _redirect() should attempt to prepend the base URL to the 
     * passed URL (if it's a relative URL)
     * @var bool
     */
    protected $_prependBase = true;
    
    
    /**
     * Retrieve HTTP status code to emit on {@link _redirect()} call
     * 
     * @return int
     */
    public function getCode()
    {
        return $this->_code;
    }
    
    
    /**
     * Validate HTTP status redirect code
     * 
     * @param  int $code 
     * @return true
     * @throws Zend_Controller_Action_Exception on invalid HTTP status code
     */
    protected function _checkCode($code)
    {
        if (!is_int($code) || (300 > $code) || (307 < $code)) {
            require_once 'Zend/Controller/Exception.php';
            throw new Zend_Controller_Action_Exception('Invalid redirect HTTP status code (' . $code  . ')');
        }

        return true;
    }
    

    /**
     * Retrieve HTTP status code for {@link _redirect()} behaviour
     * 
     * @param  int $code 
     * @return Zend_Controller_Action_Helper_Redirector
     */
    public function setCode($code)
    {
        $this->_checkCode($code);
        $this->_code = $code;
        return $this;
    }

    
    /**
     * Retrieve flag for whether or not {@link _redirect()} will exit when finished.
     * 
     * @return bool
     */
    public function getExit()
    {
        return $this->_exit;
    }

    
    /**
     * Retrieve exit flag for {@link _redirect()} behaviour
     * 
     * @param  bool $flag 
     * @return Zend_Controller_Action_Helper_Redirector
     */
    public function setExit($flag)
    {
        $this->_exit = ($flag) ? true : false;
        return $this;
    }

    
    /**
     * Retrieve flag for whether or not {@link _redirect()} will prepend the 
     * base URL on relative URLs
     * 
     * @return bool
     */
    public function getPrependBase()
    {
        return $this->_prependBase;
    }

    
    /**
     * Retrieve 'prepend base' flag for {@link _redirect()} behaviour
     * 
     * @param  bool $flag 
     * @return Zend_Controller_Action_Helper_Redirector
     */
    public function setPrependBase($flag)
    {
        $this->_prependBase = ($flag) ? true : false;
        return $this;
    }
    
    
    /**
     * Perform a redirect
     *
     * @param  string $url
     * @param  array $options
     * @return void
     */
    public function goto($url, array $options = null)
    {
        // prevent header injections
        $url = str_replace(array("\n", "\r"), '', $url);

        $exit        = $this->getExit();
        $prependBase = $this->getPrependBase();
        $code        = $this->getCode();
        if (null !== $options) {
            if (isset($options['exit'])) {
                $exit = ($options['exit']) ? true : false;
            }
            if (isset($options['prependBase'])) {
                $prependBase = ($options['prependBase']) ? true : false;
            }
            if (isset($options['code'])) {
                $this->_checkCode($options['code']);
                $code = $options['code'];
            }
        }

        // If relative URL, decide if we should prepend base URL
        if ($prependBase && !preg_match('|^[a-z]+://|', $url)) {
            $request = $this->getRequest();
            if ($request instanceof Zend_Controller_Request_Http) {
                $base = $request->getBaseUrl();
                if (('/' != substr($base, -1)) && ('/' != substr($url, 0, 1))) {
                    $url = $base . '/' . $url;
                } else {
                    $url = $base . $url;
                }
            }
        }

        // Set response redirect
        $response = $this->getResponse();
        
        //@todo this shoudl probabbly change to a header set, and code set (redirect is in this domain, not response)
        $response->setRedirect($url, $code);

        if ($exit) {
            // Close session, if started
            if (isset($_SESSION)) {
                session_write_close();
            }

            $response->sendHeaders();
            exit();
        }
    }
    
    
    /**
     * direct(): Perform helper when called as $this->_helper->redirector($url, $options)
     *
     * @param  string $url
     * @param  array $options
     * @return void
     */
    public function direct($url, array $options = null)
    {
        /*
        if (0 == func_num_args()) {
            throw new Zend_Controller_Action_Exception('Redirector expects at least a URL argument');
        }

        $args = func_get_args();
        $url  = array_shift($args);
        if (0 > count($args)) {
            $options = array_shift($args);
        }
         */

        $this->goto($url, $options);
    }
}
