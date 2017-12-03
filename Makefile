# This file is licensed under the Affero General Public License version 3 or
# later. See the COPYING file.
# @author Bernhard Posselt <dev@bernhard-posselt.com>
# @copyright Bernhard Posselt 2016

app_name=$(notdir $(CURDIR))
build_tools_directory=$(CURDIR)/build/tools
build_source_directory=$(CURDIR)/build/source
source_build_directory=$(CURDIR)/build/artifacts/source
source_package_name=$(source_build_directory)/$(app_name)
appstore_build_directory=$(CURDIR)/build/artifacts/appstore
appstore_package_name=$(appstore_build_directory)/$(app_name)
cert_directory=$(HOME)/.nextcloud/certificates
npm=$(shell which npm 2> /dev/null)
composer=$(shell which composer 2> /dev/null)

all: build

# Fetches the PHP and JS dependencies and compiles the JS. If no composer.json
# is present, the composer step is skipped, if no package.json or js/package.json
# is present, the npm step is skipped
.PHONY: build
build:
ifneq (,$(wildcard $(CURDIR)/composer.json))
	make composer
endif
ifneq (,$(wildcard $(CURDIR)/package.json))
	make npm
endif
ifneq (,$(wildcard $(CURDIR)/js/package.json))
	make npm
endif

# Installs and updates the composer dependencies. If composer is not installed
# a copy is fetched from the web
.PHONY: composer
composer:
ifeq (, $(composer))
	@echo "No composer command available, downloading a copy from the web"
	mkdir -p $(build_tools_directory)
	curl -sS https://getcomposer.org/installer | php
	mv composer.phar $(build_tools_directory)
	php $(build_tools_directory)/composer.phar install --prefer-dist
	php $(build_tools_directory)/composer.phar update --prefer-dist
else
	composer install --prefer-dist
	composer update --prefer-dist
endif

# Installs npm dependencies
.PHONY: npm
npm:
ifeq (,$(wildcard $(CURDIR)/package.json))
	cd js && $(npm) install && $(npm) run build
else
	$(npm) install && $(npm) run build
endif

# Removes the appstore build
.PHONY: clean
clean:
	rm -rf ./build

# Same as clean but also removes dependencies installed by composer, bower and
# npm
.PHONY: distclean
distclean: clean
	rm -rf vendor
	rm -rf node_modules
	rm -rf js/vendor
	rm -rf js/node_modules

# Builds the source and appstore package
.PHONY: dist
dist: source appstore

# Builds the source package
.PHONY: source
source:
	rm -rf $(source_build_directory)
	mkdir -p $(source_build_directory)
	tar cvzf $(source_package_name).tar.gz ../$(app_name) \
	--exclude-vcs \
	--exclude="../$(app_name)/build" \
	--exclude="../$(app_name)/js/node_modules" \
	--exclude="../$(app_name)/node_modules" \
	--exclude="../$(app_name)/*.log" \
	--exclude="../$(app_name)/js/*.log" \

# Builds the source package for the app store, ignores php and js tests
.PHONY: appstore
appstore: npm
	rm -rf $(appstore_build_directory)
	rm -rf $(build_source_directory)
	mkdir -p $(appstore_build_directory)
	mkdir -p $(build_source_directory)

	rsync -a \
	--exclude="build" \
	--exclude="tests" \
	--exclude="Makefile" \
	--exclude="*.log" \
	--exclude="phpunit*xml" \
	--exclude="composer.*" \
	--exclude="js/node_modules" \
	--exclude="js/tests" \
	--exclude="js/test" \
	--exclude="js/*.log" \
	--exclude="js/package.json" \
	--exclude="js/bower.json" \
	--exclude="js/karma.*" \
	--exclude="js/protractor.*" \
	--exclude="package.json" \
	--exclude="bower.json" \
	--exclude="karma.*" \
	--exclude="protractor.*" \
	--exclude=".*" \
	--exclude="js/.*" \
	--exclude="l10n/.tx" \
	./ $(build_source_directory)/$(app_name)

	tar cvzf $(appstore_package_name).tar.gz --directory="$(build_source_directory)" $(app_name)

	@if [ -f $(cert_directory)/$(app_name).key ]; then \
		echo "Signing package..."; \
		openssl dgst -sha512 -sign $(cert_directory)/$(app_name).key $(appstore_build_directory)/$(app_name).tar.gz | openssl base64; \
	fi

.PHONY: test
test: composer npm
	$(CURDIR)/vendor/phpunit/phpunit/phpunit --coverage-clover clover.xml -c phpunit.xml
	$(CURDIR)/vendor/phpunit/phpunit/phpunit -c phpunit.integration.xml
	$(npm) run test
