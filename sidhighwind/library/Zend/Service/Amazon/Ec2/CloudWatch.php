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

require_once 'Zend/Service/Amazon/Ec2/Abstract.php';

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
class Zend_Service_Amazon_Ec2_CloudWatch extends Zend_Service_Amazon_Ec2_Abstract
{
    /**
     * The HTTP query server
     */
    protected $_ec2Endpoint = 'monitoring.amazonaws.com';

    /**
     * The API version to use
     */
    protected $_ec2ApiVersion = '2009-05-15';

    /**
     * XML Namespace for the CloudWatch Stuff
     */
    protected $_xmlNamespace = 'http://monitoring.amazonaws.com/doc/2009-05-15/';


    public function getMetricStatistics()
    {
        $params = array();
    }

    /**
     * Return the Metrics that are aviable for your current monitored servers
     *
     * @param string $nextToken     This call returns a list of up to 500 valid metrics
     *                              for which there is recorded data available to a you and
     *                              a NextToken string that can be used to query for the next
     *                              set of results
     * @return array
     */
    public function listMetrics($nextToken = null)
    {
        $params = array();
        $params['Action'] = 'ListMetrics';
        if(!empty($nextToken)) {
            $params['NextToken'] = $nextToken;
        }

        $response = $this->sendRequest($params);
        $response->setNamespace($this->_xmlNamespace);

        $xpath  = $response->getXPath();
        $nodes = $xpath->query('//ec2:ListMetricsResult/ec2:Metrics/ec2:member');

        $return = array();
        foreach ($nodes as $node) {
            $item = array();

            $item['MeasureName']    = $xpath->evaluate('string(ec2:MeasureName/text())', $node);
            $item['Namespace']      = $xpath->evaluate('string(ec2:Namespace/text())', $node);
            $item['Deminsions']['name']     = $xpath->evaluate('string(ec2:Dimensions/ec2:member/ec2:Name/text())', $node);
            $item['Deminsions']['value']    = $xpath->evaluate('string(ec2:Dimensions/ec2:member/ec2:Value/text())', $node);

            if(empty($item['Deminsions']['name'])) {
                $item['Deminsions'] = array();
            }

            $return[] = $item;
            unset($item, $node);
        }

        return $return;
    }
}