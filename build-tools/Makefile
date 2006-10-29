#
# Makefile for building Zend Framework product
#

SVN=/usr/bin/svn
SVN_USER=
SVN_PASS=
ZF_TAG=1415

ZF_SVN_URL=http://framework.zend.com/svn/framework/trunk/
ZF_PRODUCT_NAME=ZendFramework
ZF_PRODUCTNAME=ZendFramework
ZF_VERSION=0.2.0
ZF_PRODUCT=$(ZF_PRODUCTNAME)-$(ZF_VERSION)
ZF_EXPORT_DIR=./$(ZF_PRODUCT)-export-$(SVN_TAG)
STAGE_HOME=stage
ZF_STAGE_DIR=$(STAGE_HOME)/$(ZF_PRODUCT)

ZIP=/usr/bin/zip
ZF_ZIPBALL=$(ZF_PRODUCT).zip
TAR=/usr/bin/tar
ZF_TARBALL=$(ZF_PRODUCT).tar.gz

# for Windows:
PHPDOC=php 'C:\php\phpdoc'

# for non-Windows:
# PHPDOC=php '/usr/local/bin/phpdoc'

.PHONY: clean

dist: checkout stage zip tarball

zip: stage
	@echo "== CREATING ZIP ARCHIVE =="
	(cd $(STAGE_HOME) && $(ZIP) -rq $(ZF_ZIPBALL) ./$(ZF_PRODUCT))

tarball: stage
	@echo "== CREATING TAR.GZ ARCHIVE =="
	(cd $(STAGE_HOME) && $(TAR) -czf $(ZF_TARBALL) ./$(ZF_PRODUCT))

stage: stage-files stage-docs
	@echo "$(ZF_PRODUCT_NAME) Preview Release $(ZF_VERSION) (build $(ZF_TAG))" > $(ZF_STAGE_DIR)/VERSION.txt

stage-files: 
	@echo "== STAGING FILES =="
	-mkdir -p $(ZF_STAGE_DIR)
	(cd $(ZF_EXPORT_DIR) && rsync --quiet --archive --delete --exclude-from="../exclude-files" ./ ../$(ZF_STAGE_DIR))


checkout:
	@echo "== EXPORTING SVN FILES =="
	$(SVN) export -r $(SVN_TAG) --quiet --force --config-dir /tmp $(ZF_SVN_URL) $(ZF_EXPORT_DIR)

stage-docs: build-docbook build-phpdoc
	@echo "== STAGING END-USER DOCUMENTATION (CORE) =="
	for lang in $(ZF_EXPORT_DIR)/documentation/manual/*/ ; \
		do rsync --archive --delete $$lang/html $(ZF_STAGE_DIR)/documentation/end-user/core/`basename $$lang` ; \
	done
	@echo "== STAGING END-USER DOCUMENTATION (INCUBATOR) =="
	-for lang in $(ZF_EXPORT_DIR)/incubator/documentation/manual/*/ ; \
		do rsync --archive --delete $$lang/html $(ZF_STAGE_DIR)/documentation/end-user/incubator/`basename $$lang` ; \
	done

build-docbook: 
	@echo "== BUILDING END-USER DOCUMENTATION (CORE) =="
	-mkdir -p $(ZF_STAGE_DIR)/documentation/end-user/core
	cd $(ZF_EXPORT_DIR)/documentation/manual && $(MAKE) 
	@echo "== BUILDING END-USER DOCUMENTATION (INCUBATOR) =="
	-mkdir -p $(ZF_STAGE_DIR)/documentation/end-user/incubator
	cd $(ZF_EXPORT_DIR)/incubator/documentation/manual && $(MAKE) 

build-phpdoc: 
	@echo "== BUILDING AND STAGING API DOCUMENTATION (CORE) =="
	-mkdir -p $(ZF_STAGE_DIR)/documentation/api/core
	if [ ! -f $(ZF_STAGE_DIR)/documentation/api/core/index.html -o `find $(ZF_STAGE_DIR)/library -newer $(ZF_STAGE_DIR)/documentation/api/core/index.html 2>/dev/null | wc -l` -gt 0 ] ; then \
		$(PHPDOC) --quiet off --target $(ZF_STAGE_DIR)/documentation/api/core --directory $(ZF_STAGE_DIR)/library ; \
	else \
		echo "...core API documentation is up to date." ; \
	fi
	@echo "== BUILDING AND STAGING API DOCUMENTATION (INCUBATOR) =="
	-mkdir -p $(ZF_STAGE_DIR)/documentation/api/incubator
	if [ ! -f $(ZF_STAGE_DIR)/documentation/api/incubator/index.html -o `find $(ZF_STAGE_DIR)/incubator/library -newer $(ZF_STAGE_DIR)/documentation/api/incubator/index.html 2>/dev/null | wc -l` -gt 0 ] ; then \
		$(PHPDOC) --quiet off --target $(ZF_STAGE_DIR)/documentation/api/incubator --directory $(ZF_STAGE_DIR)/incubator/library ; \
	else \
		echo "...incubator API documentation is up to date." ; \
	fi

clean:
	-rm -rf $(ZF_STAGE_DIR)
	-rm -f $(ZF_ZIPBALL)
	-rm -f $(ZF_TARBALL)
