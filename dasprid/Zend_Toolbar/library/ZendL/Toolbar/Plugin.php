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
 * @category   ZendL
 * @package    ZendL_Toolbar
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * @see Zend_Version
 */
require_once 'Zend/Version.php';

/**
 * @see Zend_Controller_Front
 */
require_once 'Zend/Controller/Front.php';

/**
 * @see Zend_Controller_Plugin_Abstract
 */
require_once 'Zend/Controller/Plugin/Abstract.php';

/**
 * @see Zend_Wildfire_Plugin_FrameworkToolbar
 */
require_once 'Zend/Wildfire/Plugin/FrameworkToolbar.php';


/**
 * Toolbar plugin
 *
 * @category   ZendL
 * @package    ZendL_Toolbar
 * @uses       Zend_Controller_Plugin_Abstract
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZendL_Toolbar_Plugin extends Zend_Controller_Plugin_Abstract
{
    /**
     * Wildfire toolbar plugin
     *
     * @var Zend_Wildfire_Plugin_FrameworkToolbar
     */
    protected $_wildfirePlugin;
    
    /**
     * Create the wildfire plugin 
     * 
     * It is required at this point, so that the Wildfire controller plugin
     * is registered before dispatchLoopShutdown
     *
     * @return void
     */
    public function dispatchLoopStartup()
    {
        $this->_wildfirePlugin = Zend_Wildfire_Plugin_FrameworkToolbar::getInstance();
    }
    
    /**
     * Inject the debug information at the end of the request
     *
     * @return void
     */
    public function dispatchLoopShutdown()
    {
        $data = array(
            'version' => Zend_Version::VERSION,
            'variables' => array(
                'post'    => $_POST,
                'get'     => $_GET,
                'cookie'  => $_COOKIE,
                'session' => (isset($_SESSION))?$_SESSION:null,
                'env'     => $_ENV
            )
        ); 

        $this->_wildfirePlugin->send($data);
    }
}

