#
# Zend Framework
#
# LICENSE
#
# This source file is subject to the new BSD license that is bundled
# with this package in the file LICENSE.txt.
# It is also available through the world-wide-web at this URL:
# http://framework.zend.com/license/new-bsd
# If you did not receive a copy of the license and are unable to
# obtain it through the world-wide-web, please send an email
# to license@zend.com so we can send you a copy immediately.
#
# @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
# @license    http://framework.zend.com/license/new-bsd     New BSD License
#

#
# Makefile - build Zend Toolbar XPI extension
#
# Main targets:
#  all   - Create XPI extension.
#  clean - Remove staged files.
#

VERSION = 1.8.0-dev
DIST    = dist
XPI     = zend_toolbar.xpi

all: xpi

xpi: dist-dir install.rdf 
	zip -r $(DIST)/$(XPI) * -x $(DIST) -x Makefile -x install.rdf.in -x "*.svn/*"

dist-dir:
	mkdir -p $(DIST)
	rm -f $(DIST)/$(XPI)

install.rdf:
	sed 's/@VERSION@/$(VERSION)/' install.rdf.in > install.rdf

clean:
	rm -f install.rdf
	rm -rf $(DIST)
