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
class Zend_Service_Amazon_Ec2_Instance_Windows extends Zend_Service_Amazon_Ec2_Abstract
{
    public function bundle()
    {}

    public function cancelBundle($bundleId)
    {
        $params = array();
        $params['Action'] = 'CancelBundleTask';
        $params['BundleId'] = $bundleId;

        $response = $this->sendRequest($params);

        $xpath = $response->getXPath();

        $return = array();
        $return['instanceId'] = $xpath->evaluate('string(//ec2:bundleInstanceTask/ec2:instanceId/text())');
        $return['bundleId'] = $xpath->evaluate('string(//ec2:bundleInstanceTask/ec2:bundleId/text())');
        $return['state'] = $xpath->evaluate('string(//ec2:bundleInstanceTask/ec2:state/text())');
        $return['startTime'] = $xpath->evaluate('string(//ec2:bundleInstanceTask/ec2:startTime/text())');
        $return['updateTime'] = $xpath->evaluate('string(//ec2:bundleInstanceTask/ec2:updateTime/text())');
        $return['progress'] = $xpath->evaluate('string(//ec2:bundleInstanceTask/ec2:progress/text())');
        $return['storage']['s3']['bucket'] = $xpath->evaluate('string(//ec2:bundleInstanceTask/ec2:storage/ec2:S3/ec2:bucket/text())');
        $return['storage']['s3']['prefix'] = $xpath->evaluate('string(//ec2:bundleInstanceTask/ec2:storage/ec2:S3/ec2:prefix/text())');


        return $return;
    }

    public function describeBundle($bundleId)
    {
        $params = array();
        $params['Action'] = 'DescribeBundleTasks';

        if(is_array($bundleId) && !empty($bundleId)) {
            foreach($bundleId as $k=>$name) {
                $params['bundleId.' . ($k+1)] = $name;
            }
        } elseif($bundleId) {
            $params['bundleId.1'] = $bundleId;
        }

        $response = $this->sendRequest($params);

        $xpath = $response->getXPath();

        $items = $xpath->evaluate('//ec2:bundleInstanceTasksSet/ec2:item');
        $return = array();

        foreach($items as $item) {
            $i = array();
            $i['instanceId'] = $xpath->evaluate('string(ec2:instanceId/text())', $item);
            $i['bundleId'] = $xpath->evaluate('string(ec2:bundleId/text())', $item);
            $i['state'] = $xpath->evaluate('string(ec2:state/text())', $item);
            $i['startTime'] = $xpath->evaluate('string(ec2:startTime/text())', $item);
            $i['updateTime'] = $xpath->evaluate('string(ec2:updateTime/text())', $item);
            $i['progress'] = $xpath->evaluate('string(ec2:progress/text())', $item);
            $i['storage']['s3']['bucket'] = $xpath->evaluate('string(ec2:storage/ec2:S3/ec2:bucket/text())', $item);
            $i['storage']['s3']['prefix'] = $xpath->evaluate('string(ec2:storage/ec2:S3/ec2:prefix/text())', $item);

            $return[] = $i;
            unset($i);
        }


        return $return;
    }

    protected function _s3UploadPolicySig($bucketName, $prefix, $expireInMinutes = 1440)
    {
        $arrParams = array();
        $arrParams['expiration'] = gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", (time() + ($expireInMinutes * 60)));
        $arrParmss['conditions'][] = array('bucket' => $bucketName);
        $arrParmss['conditions'][] = array('acl' => 'ec2-bundle-read');
        $arrParmss['conditions'][] = array('starts-with', '$key', $prefix);
    }
}
