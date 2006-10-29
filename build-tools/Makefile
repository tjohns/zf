#
# Makefile for building Zend Framework product
#

SVN=/usr/bin/svn
SVN_USER=
SVN_PASS=
SVN_TAG=HEAD

ZF_SVN_URL=http://framework.zend.com/svn/framework/trunk/
ZF_VERSION=0.2.0
ZF_PRODUCT_NAME=ZendFramework
ZF_PRODUCT=$(ZF_PRODUCT_NAME)-$(ZF_VERSION)
ZF_EXPORT_DIR=./$(ZF_PRODUCT)-export
STAGE_HOME=stage
ZF_STAGE_DIR=$(STAGE_HOME)/$(ZF_PRODUCT)

ZIP=/usr/bin/zip
ZF_ZIPBALL=$(ZF_PRODUCT).zip
TAR=/usr/bin/tar
ZF_TARBALL=$(ZF_PRODUCT).tar.gz

PHPDOC=php 'C:\php\phpdoc'
# PHPDOC=php '/usr/local/bin/phpdoc'

dist: checkout stage zip tarball

zip: stage
	(cd $(STAGE_HOME) && $(ZIP) -rq $(ZF_ZIPBALL) ./$(ZF_PRODUCT))

tarball: stage
	(cd $(STAGE_HOME) && $(TAR) -czf $(ZF_TARBALL) ./$(ZF_PRODUCT))

stage: init-docbook
	(cd $(ZF_EXPORT_DIR) && rsync --quiet --archive \
		--exclude-from="../exclude-files" \
		--exclude="documentation" \
		--exclude="incubator/documentation" \
		--exclude="exclude-files" \
		./ ../$(ZF_STAGE_DIR))

checkout:
	$(SVN) export -r $(SVN_TAG) --quiet --force --config-dir /tmp $(ZF_SVN_URL) $(ZF_EXPORT_DIR)

stage-docs: build-docbook build-phpdoc

build-docbook: init-docbook
	cd $(ZF_EXPORT_DIR)/documentation/manual && $(MAKE) 
	-for lang in $(ZF_EXPORT_DIR)/documentation/manual/*/ ; \
		do cp -r $$lang/html $(ZF_STAGE_DIR)/documentation/end-user/core/`basename $$lang` ; \
	done
	cd $(ZF_EXPORT_DIR)/incubator/documentation/manual && $(MAKE) 
	-for lang in $(ZF_EXPORT_DIR)/incubator/documentation/manual/*/ ; \
		do cp -r $$lang/html $(ZF_STAGE_DIR)/documentation/end-user/incubator/`basename $$lang` ; \
	done

init-docbook:
	-mkdir -p $(ZF_STAGE_DIR)/documentation/end-user/core
	-mkdir -p $(ZF_STAGE_DIR)/documentation/end-user/incubator

build-phpdoc: init-phpdoc
	$(PHPDOC) --quiet on \
		--target $(ZF_STAGE_DIR)/documentation/api/core \
		--directory $(ZF_EXPORT_DIR)/library
	$(PHPDOC) --quiet on \
		--target $(ZF_STAGE_DIR)/documentation/api/incubator \
		--directory $(ZF_EXPORT_DIR)/incubator/library

init-phpdoc:
	-mkdir -p $(ZF_STAGE_DIR)/documentation/api/core
	-mkdir -p $(ZF_STAGE_DIR)/documentation/api/incubator

clean:
	-rm -rf $(ZF_STAGE_DIR)
	-rm -f $(ZF_ZIPBALL)
	-rm -f $(ZF_TARBALL)
