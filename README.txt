Welcome to Zend Framework 1.5 Release Candidate 1! This release is intented to
meet quality guidelines for production use, but additional release candidates may
be necessary to reach the quality standards of a General Availability release.
To help us deliver a stable Zend Framework 1.5 GA release soon, please post your
comments and questions to the appropriate mailing list and bring any bugs to our
attention in the Zend Framework issue tracker:

http://framework.zend.com/issues

RELEASE INFORMATION
---------------

Zend Framework 1.5 Release Candidate 1 (revision [INSERT REV NUMBER HERE]).
Released on [INSERT DATE HERE].

SPECIAL NOTICE FOR LUCENE SEARCH USERS
--------------------------------------

If you are upgrading from a 1.0 ZF release to a 1.5 ZF release and you are using
Zend_Search_Lucene, you should be aware that Zend_Search_Lucene now works
exclusively with Apache Lucene 2.1 index file format. Conversion from the
previous format (1.9) is performed automatically during the first index update
after the ZF 1.5 release is installed. THIS CONVERSION CANNOT BE UNDONE. Please
backup your Lucene index if you plan to rollback to 1.0 versions of Zend
Framework and wish to continue using this index.

NEW FEATURES
------------

* New Zend_Form component with support for AJAX-enabled form elements
* New action and view helpers for automating and facilitating AJAX requests and
  alternate response formats
* Infocard and OpenID authentication adapters
* Support for complex Lucene searches, including fuzzy, date-range, and wildcard
  queries
* Support for Lucene 2.1 index file format
* Partial, Placeholder, Action, and Header view helpers for advanced view
  composition and rendering
* New Zend_Layout component for automating and facilitating site layouts
* UTF-8 support for PDF documents
* New Technorati and SlideShare web services

ENHANCEMENTS AND BUGFIXES
-------------------------

* Zend_Json has been augmented to convert from XML to JSON format
* New Zend_TimeSync component supporting the Network Time Protocol (NTP)
* Improved performance of Zend_Translate with new caching option
* addRoute(), addRoutes(), addConfig(), removeRoute(), removeDefaultRoutes()
  methods of Zend_Controller_Router_Rewrite now support method chaining
* Yahoo web service supports Yahoo! Site Explorer and video searches
* Database adapter for Firebird/Interbase
* Query modifiers for fetch and find methods in Zend_Db_Table
* 'init' hook to modify initialization behaviour in subclasses Zend_Db_Table,
  Rowset, and Row
* Support for HTTP CONNECT requests in Zend_Http_Client
* Support for PHP's hash() for read/write control in Zend_Cache
* Zend_Cache_Backend_File may be configured to call ignore_user_abort() to
  maintain cache data integrity
* Timezone in Zend_Date may be set by locale
* Zend_Cache can now use custom frontend and backend classes

A detailed list of all features and bug fixes in this release may be found at:

http://framework.zend.com/issues/secure/IssueNavigator.jspa?mode=hide&requestId=10661

INTENDED USE
------------

The Zend Framework community does not recommend this release for production use.
Please be aware that the API's introduced in this release may not be final, and
that the Zend Framework team does not guarantee backwards compatibility to this
release in future releases of Zend Framework. Once a release candidate is
sanctioned by the ZF community as a GA release, Zend and the ZF community will 
make every effort to maintain backwards compatibility.

SYSTEM REQUIREMENTS
-------------------

Zend Framework requires PHP 5.1.4 or later. Please see our reference guide for
more detailed system requirements:

http://framework.zend.com/manual/en/requirements.html

INSTALLATION
------------

Please see /INSTALL.txt.

QUESTIONS AND FEEDBACK
----------------------

Online documentation can be found at http://framework.zend.com/manual. Questions
that are not addressed in the manual should be directed to the appropriate
mailing list:

http://framework.zend.com/wiki/x/GgE#ContributingtoZendFramework-
Subscribetotheappropriatemailinglists

If you find code in this release behaving in an unexpected manner or contrary to
its documented behavior, please create an issue in the Zend Framework issue
tracker at:

http://framework.zend.com/issues

If you have not done so already, you must email cla@zend.com with your issue
tracker username requesting issue posting privileges.
If you would like to be notified of new releases- including the general
availability release of Zend Framework 1.5- you can subscribe to the fw-announce
mailing list by sending a blank message to fw-announce-subscribe@lists.zend.com.

LICENSE
-------

The files in this archive are released under the Zend Framework license. You can
find a copy of this license in /LICENSE.txt.

ACKNOWLEDGEMENTS
----------------

The Zend Framework team would like to thank all the contributors to the Zend
Framework project, our corporate sponsor (Zend Technologies), and you- the Zend
Framework user. Please visit us sometime soon at http://framework.zend.com!
