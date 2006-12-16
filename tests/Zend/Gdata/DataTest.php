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
 * @package    Zend_Gdata
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Zend/Gdata/Data.php';

/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_DataTest extends PHPUnit_Framework_TestCase
{

    public function testIsValidTrue()
    {
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::ATTENDEE_STATUS_ACCEPTED, 'attendeeStatus'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::ATTENDEE_STATUS_DECLINED, 'attendeeStatus'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::ATTENDEE_STATUS_INVITED, 'attendeeStatus'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::ATTENDEE_STATUS_TENTATIVE, 'attendeeStatus'));

        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::ATTENDEE_TYPE_OPTIONAL, 'attendeeType'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::ATTENDEE_TYPE_REQUIRED, 'attendeeType'));

        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::COMMENTS_REGULAR, 'comments'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::COMMENTS_REVIEWS, 'comments'));

        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::EVENT_STATUS_CANCELED, 'eventStatus'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::EVENT_STATUS_CONFIRMED, 'eventStatus'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::EVENT_STATUS_TENTATIVE, 'eventStatus'));

        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::LINK_ALTERNATE, 'link'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::LINK_ENCLOSURE, 'link'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::LINK_RELATED, 'link'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::LINK_SELF, 'link'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::LINK_VIA, 'link'));

        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::LINK_ONLINE_LOCATION, 'link#gdata'));

        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::ORDERBY_MODIFICATION_TIME, 'orderby#base'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::ORDERBY_NAME, 'orderby#base'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::ORDERBY_RELEVANCY, 'orderby#base'));

        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::ORDERBY_STARTTIME, 'orderby#calendar'));

        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::PHONE_CAR, 'phoneNumber'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::PHONE_FAX, 'phoneNumber'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::PHONE_GENERAL, 'phoneNumber'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::PHONE_HOME, 'phoneNumber'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::PHONE_INTERNAL_EXTENSION, 'phoneNumber'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::PHONE_MOBILE, 'phoneNumber'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::PHONE_OTHER, 'phoneNumber'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::PHONE_PAGER, 'phoneNumber'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::PHONE_SATELLITE, 'phoneNumber'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::PHONE_VOIP, 'phoneNumber'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::PHONE_WORK, 'phoneNumber'));

        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::PROJ_ATTENDEES_ONLY, 'projection'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::PROJ_BASIC, 'projection'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::PROJ_COMPOSITE, 'projection'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::PROJ_FREE_BUSY, 'projection'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::PROJ_FULL, 'projection'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::PROJ_FULL_NOATTENDEES, 'projection'));

        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::RATING_OVERALL, 'rating'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::RATING_PRICE, 'rating'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::RATING_QUALITY, 'rating'));

        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::STATUS_CANCELED, 'status'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::STATUS_CONFIRMED, 'status'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::STATUS_TENTATIVE, 'status'));

        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::TRANSP_OPAQUE, 'transparency'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::TRANSP_TRANSPARENT, 'transparency'));

        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::VIS_CONFIDENTIAL, 'visibility'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::VIS_DEFAULT, 'visibility'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::VIS_PRIVATE, 'visibility'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::VIS_PRIVATE_MAGIC_COOKIE, 'visibility'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::VIS_PUBLIC, 'visibility'));

        $this->assertTrue(Zend_Gdata_Data::isValid('', 'where'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::WHERE_ALTERNATE, 'where'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::WHERE_PARKING, 'where'));

        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::WHO_ATTENDEE, 'who#event'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::WHO_PERFORMER, 'who#event'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::WHO_ORGANIZER, 'who#event'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::WHO_SPEAKER, 'who#event'));

        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::WHO_BCC, 'who#message'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::WHO_CC, 'who#message'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::WHO_FROM, 'who#message'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::WHO_REPLY_TO, 'who#message'));
        $this->assertTrue(Zend_Gdata_Data::isValid(Zend_Gdata_Data::WHO_TO, 'who#message'));
    }

    public function testIsValidFalse()
    {
        $this->assertFalse(Zend_Gdata_Data::isValid('Mxyzptlk!', 'visibility'));
        $this->assertFalse(Zend_Gdata_Data::isValid(Zend_Gdata_Data::VIS_PUBLIC, 'Mxyzptlk!'));
    }

    public function testGetValues()
    {
        $array = Zend_Gdata_Data::getValues('attendeeStatus');
        $this->assertTrue(is_array($array) && count($array) == 4);

        $array = Zend_Gdata_Data::getValues('attendeeType');
        $this->assertTrue(is_array($array) && count($array) == 2);

        $array = Zend_Gdata_Data::getValues('comments');
        $this->assertTrue(is_array($array) && count($array) == 2);

        $array = Zend_Gdata_Data::getValues('eventStatus');
        $this->assertTrue(is_array($array) && count($array) == 3);

        $array = Zend_Gdata_Data::getValues('link');
        $this->assertTrue(is_array($array) && count($array) == 5);

        $array = Zend_Gdata_Data::getValues('link#gdata');
        $this->assertTrue(is_array($array) && count($array) == 1);

        $array = Zend_Gdata_Data::getValues('orderby#base');
        $this->assertTrue(is_array($array) && count($array) == 3);

        $array = Zend_Gdata_Data::getValues('orderby#calendar');
        $this->assertTrue(is_array($array) && count($array) == 1);

        $array = Zend_Gdata_Data::getValues('phoneNumber');
        $this->assertTrue(is_array($array) && count($array) == 11);

        $array = Zend_Gdata_Data::getValues('projection');
        $this->assertTrue(is_array($array) && count($array) == 6);

        $array = Zend_Gdata_Data::getValues('rating');
        $this->assertTrue(is_array($array) && count($array) == 3);

        $array = Zend_Gdata_Data::getValues('status');
        $this->assertTrue(is_array($array) && count($array) == 3);

        $array = Zend_Gdata_Data::getValues('transparency');
        $this->assertTrue(is_array($array) && count($array) == 2);

        $array = Zend_Gdata_Data::getValues('visibility');
        $this->assertTrue(is_array($array) && count($array) == 5);

        $array = Zend_Gdata_Data::getValues('where');
        $this->assertTrue(is_array($array) && count($array) == 3);

        $array = Zend_Gdata_Data::getValues('who#event');
        $this->assertTrue(is_array($array) && count($array) == 4);

        $array = Zend_Gdata_Data::getValues('who#message');
        $this->assertTrue(is_array($array) && count($array) == 5);
    }

    public function testGetValuesInvalid()
    {
        $this->assertFalse(Zend_Gdata_Data::getValues('Mxyzptlk!'));
    }

}
