all:
	for lang in */. ; do $(MAKE) -f ../Makefile -C "$$lang" build-docs ; done

build-docs:
	autoconf 
	sh ./configure
	$(MAKE)
