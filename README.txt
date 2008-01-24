Welcome to the Zend Framework 1.5 Preview Release! This release showcases features
that will be introduced in the final 1.5 release. The intent of this release is to
give our users access to the latest features in Zend Framework and to improve the
overall quality of the production release through the feedback we recieve. In
particular, *your* feedback could help make the Zend Framework 1.5 production release
a success, so please post your comments and questions to the appropriate mailing list
and bring bugs to our attention by creating an issue in our issue tracker:

http://framework.zend.com/issues.

RELEASE INFORMATION
---------------
Zend Framework Preview Release 1.5 (revision [INSERT REV NUMBER HERE]). Released on
[INSERT DATE HERE].

NEW FEATURES
------------

* New Zend_Form component with support for AJAX-enabled form elements
* New action and view helpers for automating and facilitating AJAX requests and
  alternate response formats
* Infocard, OpenID, and LDAP authentication adapters
* Support for complex Lucene searches, including fuzzy, date-range, and wildcard
  queries
* Support for Lucene 2.1 index file format
* Partial, Placeholder, Action, and Header view helpers for advanced view composition
  and rendering
* New Zend_Layout component for automating and facilitating site layouts
* UTF-8 support for PDF documents
* New Technorati, SlideShare, and Remember the Milk web services

ENHANCEMENTS AND BUGFIXES
-------------------------

* New Zend_TimeSync component supporting the Network Time Protocol (NTP)
* Improved performance of Zend_Translate with new caching option
* addRoute(), addRoutes(), addConfig(), removeRoute(), removeDefaultRoutes() methods
  of Zend_Controller_Router_Rewrite now support method chaining
* Yahoo web service supports Yahoo! Site Explorer and video searches
* Database adapter for Firebird/Interbase
* Query modifiers for fetch and find methods in Zend_Db_Table
* 'init' hook to modify initialization behaviour in subclasses Zend_Db_Table, Rowset,
  and Row
* Support for HTTP CONNECT requests in Zend_Http_Client
* Support for PHP's hash() for read/write control in Zend_Cache
* Zend_Cache_Backend_File may be configured to call ignore_user_abort() to maintain
  cache data integrity
* Timezone in Zend_Date may be set by locale
* Zend_Cache can now use custom frontend and backend classes

A detailed list of all features and bug fixes in this release may be found at:

http://framework.zend.com/issues/secure/IssueNavigator.jspa?mode=hide&requestId=10661
.

INTENDED USE
------------

The code in this release provides a 'sneak peek' at features to be released in the
upcoming Zend Framework 1.5 release and is not intended for production use. Please be
aware that the API's introduced in this release may not be final, and that the Zend
Framework team does not guarantee backwards compatibility to this release in future
releases of Zend Framework. Some of the code included in the library folder for this
release is currently under development in the incubator repository. Inclusion in this
release for such components and features does not necessarily imply inclusion for a
production release of Zend Framework. We strongly discourage the use of this preview
release in production environments or for projects that could not easily be
refactored to work with future releases of Zend Framework. We strongly recommend
evaluating these features for use in future projects, however, and would appreciate
any feedback.

SYSTEM REQUIREMENTS
-------------------

Zend Framework requires PHP 5.1.4 or later. Please see our reference guide for more
detailed system requirements:

http://framework.zend.com/manual/en/requirements.html

INSTALLATION
------------

Please see /INSTALL.txt.

QUESTIONS AND FEEDBACK
----------------------

Online documentation can be found at http://framework.zend.com/manual. Questions that
are not addressed in the manual should be directed to the appropriate mailing list:

http://framework.zend.com/wiki/x/GgE#ContributingtoZendFramework-
Subscribetotheappropriatemailinglists

If you find code in this release behaving in an unexpected manner or contrary to its
documented behavior, please create an issue in the Zend Framework issue tracker at:

http://framework.zend.com/issues

If you have not done so already, you must email cla@zend.com with your issue tracker
username requesting issue posting privileges.
If you would like to be notified of new releases- including the production release of
Zend Framework 1.5- you can subscribe to the fw-announce mailing list by sending a
blank message to fw-announce-subscribe@lists.zend.com.

LICENSE
-------

The files in this archive are released under the Zend Framework license. You can find
a copy of this license in /LICENSE.txt.

ACKNOWLEDGEMENTS
----------------

The Zend Framework team would like to thank all the contributors to the Zend
Framework project, our corporate sponsor (Zend Technologies), and you- the Zend
Framework user. Please visit us sometime soon at http://framework.zend.com. Now have
at it!
