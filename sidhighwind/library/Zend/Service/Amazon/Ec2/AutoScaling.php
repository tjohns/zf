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
 * @package    Zend_Service_Amazon
 * @subpackage Ec2
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once 'library/Zend/Service/Amazon/Ec2/Abstract.php';

/**
 * An Amazon EC2 interface that allows yout to run, terminate, reboot and describe Amazon
 * Ec2 Instances.
 *
 * @category   Zend
 * @package    Zend_Service_Amazon
 * @subpackage Ec2
 * @copyright  Copyright (c) 22005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Amazon_Ec2_AutoScaling extends Zend_Service_Amazon_Ec2_Abstract
{
    /**
     * The HTTP query server
     */
    protected $_ec2Endpoint = 'autoscaling.amazonaws.com';

    /**
     * The API version to use
     */
    protected $_ec2ApiVersion = '2009-05-15';
}