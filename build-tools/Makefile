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
# @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
# @license    http://framework.zend.com/license/new-bsd     New BSD License
#

#
# Makefile - build Zend Framework product and documentation
#
# Main targets:
#  dist - produce zip and tarball.
#  zip - produce zip from staged tree.
#  tarball - produce tarball from staged tree.
#  stage - produce staged product tree.
#  export - get files from subversion.
#  docbook - build and stage manual from docbook sources.
#  phpdoc - build and stage API doc using phpDocumentor.
#  clean - remove staged files.
#  clean-docbook - remove temp files under docbook sources.
#  clean-export - remove exported product tree.
# 

# -- parameters you are likely to want to change --

# PHPDOC is the command to run to run phpDocumentor.  On Cygwin,
# run 'php' with the phpdoc script as the argument, in Windows-style
# path format, e.g.  "C:/php/phpdoc".  On UNIX/Linux you can just run
# 'phpdoc' if that script is in your PATH.
PHPDOC=php c:/xampp/php/PhpDocumentor/phpdoc

# ZF_TAG is the subversion tag to use to export the files.
# Typically use 'HEAD' until the tree is tagged for a release
# candidate, then use that tag.
ZF_TAG=4420

# ZF_VERSION is the release version string to build.  This is added
# to the VERSION.txt file, and is also used for the name of the 
# staging directory and the names of the zip and tarball files.
ZF_VERSION=0.9.2-Beta

# For LANG, use '*' if you want to build all manual
# translations.  If you want to build one language, use one of the
# names of language directories, e.g. 'en', 'de', etc.  You also can
# use any file-globbing pattern.
LANG=*

# Locations of the DocBook DTD and XSL stylesheets
DTD_HOST=framework.zend.com
DTD_URL=http://$(DTD_HOST)/docbook/xml/4.4/docbookx.dtd
XSL_HOST=framework.zend.com
XSL_URL=http://$(XSL_HOST)/docbook-xsl/html/chunk.xsl

# -- end of parameters you are likely to want to change --

SVN=svn
SVN_USER=
SVN_PASS=

ZF_SVN_URL=http://framework.zend.com/svn/framework/trunk/
ZF_PRODUCT_NAME=Zend Framework
ZF_PRODUCTNAME=ZendFramework
ZF_PRODUCT=$(ZF_PRODUCTNAME)-$(ZF_VERSION)
ZF_EXPORT_DIR=./$(ZF_PRODUCTNAME)-export-$(ZF_TAG)
STAGE_HOME=stage
ZF_STAGE_DIR=$(STAGE_HOME)/$(ZF_PRODUCT)

ZIP=zip
ZF_ZIPBALL=$(ZF_PRODUCT).zip
TAR=tar
ZF_TARBALL=$(ZF_PRODUCT).tar.gz

API_DOC_TITLE_CORE=Zend Framework API Documentation
API_DOC_TITLE_INCUBATOR=Zend Framework Incubator API Documentation
API_DOC_OUTPUT_STYLE=HTML:frames:DOM/earthli

.PHONY: clean clean-docbook clean-export

dist: export stage zipball tarball

zip: zipball
zipball:
	@echo "== CREATING ZIP ARCHIVE =="
	(cd $(STAGE_HOME) && $(ZIP) -rq $(ZF_ZIPBALL) $(ZF_PRODUCT))

tar: tarball
tarball: 
	@echo "== CREATING TAR.GZ ARCHIVE =="
	(cd $(STAGE_HOME) && $(TAR) -czf $(ZF_TARBALL) $(ZF_PRODUCT))

stage: stage-files stage-docs
	@echo "$(ZF_PRODUCT_NAME) Preview Release $(ZF_VERSION) (build $(ZF_TAG))" > $(ZF_STAGE_DIR)/VERSION.txt

stage-files:
	@echo "== STAGING FILES =="
	-mkdir -p $(ZF_STAGE_DIR)
	@(pwd=`pwd` ; cd $(ZF_EXPORT_DIR) && rsync --quiet --archive --delete --exclude-from="$$pwd/exclude-files" ./ $$pwd/$(ZF_STAGE_DIR))

checkout: export
export:
	@echo "== EXPORTING SVN FILES =="
	[ -f "$(ZF_EXPORT_DIR)/VERSION.txt" ] || $(SVN) export -r $(ZF_TAG) --quiet --force --config-dir /tmp $(ZF_SVN_URL) $(ZF_EXPORT_DIR)

stage-docs: docbook phpdoc

docbook: docbook-core docbook-incubator

docbook-core:
	-mkdir -p $(ZF_STAGE_DIR)/documentation/end-user/core
	@for language in $(ZF_EXPORT_DIR)/documentation/manual/$(LANG)/ ; do \
	    lang=`basename $$language` ; \
	    echo "== BUILDING AND STAGING END-USER DOCUMENTATION [CORE] (language: $$lang) ==" ; \
	    ( cd $$language && autoconf && sh ./configure && $(MAKE) -e ) ; \
	    rsync --archive --delete $$language/html/ $(ZF_STAGE_DIR)/documentation/end-user/core/$$lang ; \
	done

docbook-incubator:
	-mkdir -p $(ZF_STAGE_DIR)/documentation/end-user/incubator
	@for language in $(ZF_EXPORT_DIR)/incubator/documentation/manual/$(LANG)/ ; do \
	    lang=`basename $$language` ; \
	    echo "== BUILDING AND STAGING END-USER DOCUMENTATION [INCUBATOR] (language: $$lang) ==" ; \
	    ( cd $$language ; autoconf ; sh ./configure ; $(MAKE) DTD_URL=$(DTD_URL) XSL_URL=$(XSL_URL) ) ; \
	    rsync --archive --delete $$language/html/ $(ZF_STAGE_DIR)/documentation/end-user/incubator/$$lang ; \
	done

phpdoc: phpdoc-core phpdoc-incubator

phpdoc-core:
	@echo "== BUILDING AND STAGING API DOCUMENTATION (CORE) =="
	-mkdir -p $(ZF_STAGE_DIR)/documentation/api/core
	if [ ! -f $(ZF_STAGE_DIR)/documentation/api/core/index.html -o `find $(ZF_STAGE_DIR)/library -newer $(ZF_STAGE_DIR)/documentation/api/core/index.html 2>/dev/null | wc -l` -gt 0 ] ; then \
	    $(PHPDOC) --quiet off \
	      --target $(ZF_STAGE_DIR)/documentation/api/core \
	      --directory $(ZF_STAGE_DIR)/library \
	      --title "$(API_DOC_TITLE_CORE)" \
	      --output $(API_DOC_OUTPUT_STYLE) ; \
	else \
	    echo "...core API documentation is up to date." ; \
	fi

phpdoc-incubator:
	@echo "== BUILDING AND STAGING API DOCUMENTATION (INCUBATOR) =="
	-mkdir -p $(ZF_STAGE_DIR)/documentation/api/incubator
	if [ ! -f $(ZF_STAGE_DIR)/documentation/api/incubator/index.html -o `find $(ZF_STAGE_DIR)/incubator/library -newer $(ZF_STAGE_DIR)/documentation/api/incubator/index.html 2>/dev/null | wc -l` -gt 0 ] ; then \
	    $(PHPDOC) --quiet off \
	      --target $(ZF_STAGE_DIR)/documentation/api/incubator \
	      --directory $(ZF_STAGE_DIR)/incubator/library \
	      --title "$(API_DOC_TITLE_INCUBATOR)" \
	      --output $(API_DOC_OUTPUT_STYLE) ; \
	else \
	    echo "...incubator API documentation is up to date." ; \
	fi

clean:
	-rm -rf $(ZF_STAGE_DIR)
	-rm -f $(ZF_ZIPBALL)
	-rm -f $(ZF_TARBALL)

clean-docbook:
	for language in $(ZF_EXPORT_DIR)/documentation/manual/$(LANG)/ $(ZF_EXPORT_DIR)/incubator/documentation/manual/$(LANG)/ ; do \
	    rm -rf $$language/autom4te.cache $$language/build/docbook-xsl $$language/html/*.html $$language/html/HTML.manifest $$language/config.* $$language/configure $$language/entities.ent $$language/Makefile ; \
	done

clean-export:
	-rm -rf $(ZF_EXPORT_DIR)
