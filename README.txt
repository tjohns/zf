Welcome to the Zend Framework 1.9 Release! 

RELEASE INFORMATION
---------------
Zend Framework 1.9 Beta 1 Release ([INSERT REV NUM HERE]).
Released on July 23, 2009.

NEW FEATURES
------------
* Zend_Rest_Route, Zend_Rest_Controller, and
  Zend_Controller_Plugin_PutHandler, which aid in providing RESTful
  resources via the MVC layer.

* Zend_Feed_Reader, which provides a common API to RSS and Atom feeds,
  as well as extensions to each format, caching, and a slew of other
  functionality.

* Zend_Queue and Zend_Service_Amazon_Sqs, which provide the ability to
  use local and remote messaging and queue services for offloading
  asynchronous processes.

* Zend_Db_Table updates to allow using Zend_Db_Table as a concrete
  class by passing it one or more table definitions via the
  constructor.

* Zend_Test_PHPUnit_Db, which provides Zend_Db support for PHPUnit's
  DBUnit support, allowing developers to do functional and integration
  testing against databases using data fixtures.

* Annotation processing support for Zend_Pdf, as well as performance
  improvements.

* Zend_Dojo custom build layer support.

* Numerous Zend_Ldap improvements.

* Zend_Log_Writer_Syslog, a Zend_Log writer for writing to your system
  log.

* Several new view helpers, including Zend_View_Helper_BaseUrl.

* PHP 5.3 compatibility, including support for new features in the
  mysqli extension.

A detailed list of all features and bug fixes in this release may be found at:

http://framework.zend.com/changelog/1.9.0b1

SYSTEM REQUIREMENTS
-------------------

Zend Framework requires PHP 5.2.4 or later. Please see our reference
guide for more detailed system requirements:

http://framework.zend.com/manual/en/requirements.html

INSTALLATION
------------

Please see INSTALL.txt.

QUESTIONS AND FEEDBACK
----------------------

Online documentation can be found at http://framework.zend.com/manual.
Questions that are not addressed in the manual should be directed to the
appropriate mailing list:

http://framework.zend.com/wiki/display/ZFDEV/Mailing+Lists

If you find code in this release behaving in an unexpected manner or
contrary to its documented behavior, please create an issue in the Zend
Framework issue tracker at:

http://framework.zend.com/issues

If you would like to be notified of new releases, you can subscribe to
the fw-announce mailing list by sending a blank message to
fw-announce-subscribe@lists.zend.com.

LICENSE
-------

The files in this archive are released under the Zend Framework license.
You can find a copy of this license in LICENSE.txt.

ACKNOWLEDGEMENTS
----------------

The Zend Framework team would like to thank all the contributors to the Zend
Framework project, our corporate sponsor, and you, the Zend Framework user.
Please visit us sometime soon at http://framework.zend.com.
